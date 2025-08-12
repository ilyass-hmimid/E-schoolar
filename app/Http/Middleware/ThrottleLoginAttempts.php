<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThrottleLoginAttempts
{
    /**
     * Nombre maximal de tentatives autorisées
     */
    protected $maxAttempts = 5;

    /**
     * Délai de blocage en secondes après dépassement du nombre de tentatives
     */
    protected $decayMinutes = 15;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Ne s'applique qu'aux tentatives de connexion
        if (!$this->isLoginAttempt($request)) {
            return $next($request);
        }

        $key = $this->throttleKey($request);
        $attempts = Cache::get($key, 0) + 1;
        
        // Enregistrer la tentative actuelle
        Cache::put($key, $attempts, now()->addMinutes($this->decayMinutes));
        
        // Si le nombre de tentatives dépasse le maximum
        if ($attempts > $this->maxAttempts) {
            $this->logSuspiciousActivity($request);
            
            return response()->json([
                'message' => 'Trop de tentatives de connexion. Veuillez réessayer dans ' . $this->decayMinutes . ' minutes.',
                'retry_after' => now()->addMinutes($this->decayMinutes)->diffForHumans(),
            ], 429);
        }

        $response = $next($request);

        // Si la connexion a réussi, réinitialiser le compteur
        if ($this->loginSucceeded($request, $response)) {
            $this->clearLoginAttempts($request);
        }
        // Si la connexion a échoué, incrémenter le compteur
        elseif ($this->loginFailed($request, $response)) {
            $remainingAttempts = $this->maxAttempts - $attempts;
            
            if ($remainingAttempts > 0) {
                $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);
                $response->headers->set('X-RateLimit-Limit', $this->maxAttempts);
                $response->headers->set('X-RateLimit-Reset', now()->addMinutes($this->decayMinutes)->timestamp);
                
                $response->setContent(json_encode(array_merge(
                    json_decode($response->getContent(), true) ?? [],
                    [
                        'message' => 'Identifiants incorrects. Il vous reste ' . $remainingAttempts . ' tentative(s).',
                        'remaining_attempts' => $remainingAttempts,
                    ]
                )));
            }
        }

        return $response;
    }
    
    /**
     * Vérifie si la requête est une tentative de connexion
     */
    protected function isLoginAttempt(Request $request): bool
    {
        return $request->is('login') && $request->isMethod('post');
    }
    
    /**
     * Vérifie si la tentative de connexion a réussi
     */
    protected function loginSucceeded(Request $request, $response): bool
    {
        return $response->getStatusCode() === 200 && 
               $request->is('login') && 
               $request->isMethod('post') && 
               $response->getContent() && 
               json_decode($response->getContent(), true)['success'] ?? false;
    }
    
    /**
     * Vérifie si la tentative de connexion a échoué
     */
    protected function loginFailed(Request $request, $response): bool
    {
        return $response->getStatusCode() === 422 || 
               ($response->getStatusCode() === 401 && 
                $request->is('login') && 
                $request->isMethod('post'));
    }
    
    /**
     * Génère une clé unique pour le throttling basée sur l'adresse IP et l'email
     */
    protected function throttleKey(Request $request): string
    {
        $email = $request->input('email');
        $ip = $request->ip();
        
        return 'login_attempts:' . sha1($email . '|' . $ip);
    }
    
    /**
     * Réinitialise le compteur de tentatives
     */
    protected function clearLoginAttempts(Request $request): void
    {
        Cache::forget($this->throttleKey($request));
    }
    
    /**
     * Enregistre une activité suspecte dans les logs
     */
    protected function logSuspiciousActivity(Request $request): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $userAgent = $request->userAgent();
        
        Log::warning("Tentative de connexion bloquée pour l'adresse IP: $ip - Email: $email - User-Agent: $userAgent");
        
        // Ici, vous pourriez également envoyer une notification à l'administrateur
        // ou déclencher d'autres actions de sécurité
    }
}
