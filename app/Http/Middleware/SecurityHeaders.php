<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        
        // Ne pas appliquer les en-têtes pour les réponses de redirection
        if ($response->isRedirection()) {
            return $response;
        }
        
        // Ne pas appliquer les en-têtes pour les réponses JSON (API)
        if ($request->expectsJson() || $request->is('api/*')) {
            return $response;
        }
        
        // Headers de sécurité
        $headers = [
            // Protection contre le détournement de clics (Clickjacking)
            'X-Frame-Options' => 'SAMEORIGIN',
            
            // Protection contre le MIME-sniffing
            'X-Content-Type-Options' => 'nosniff',
            
            // Politique de sécurité du contenu (CSP)
            'Content-Security-Policy' => $this->getCspHeader(),
            
            // Politique de référence (Referrer-Policy)
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            
            // Politique de fonctionnalités (Feature-Policy)
            'Permissions-Policy' => $this->getPermissionsPolicyHeader(),
            
            // Protection XSS (obsolète mais toujours utile pour les anciens navigateurs)
            'X-XSS-Protection' => '1; mode=block',
            
            // Politique de transport sécurisé (HSTS)
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            
            // Désactiver la mise en cache du navigateur pour les pages sensibles
            'Cache-Control' => $this->getCacheControlHeader($request, $response),
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        // Ajouter les en-têtes à la réponse
        foreach ($headers as $key => $value) {
            if (!empty($value)) {
                $response->headers->set($key, $value);
            }
        }
        
        return $response;
    }
    
    /**
     * Génère l'en-tête Content-Security-Policy
     */
    private function getCspHeader(): string
    {
        $csp = [];
        
        // Sources autorisées par défaut
        $csp[] = "default-src 'self'";
        
        // Scripts
        $csp[] = "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com";
        
        // Feuilles de style
        $csp[] = "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net";
        
        // Images
        $csp[] = "img-src 'self' data: https: http:";
        
        // Polices
        $csp[] = "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net";
        
        // Connexions (XHR, WebSocket, EventSource, etc.)
        $csp[] = "connect-src 'self' " . config('app.url');
        
        // Frames
        $csp[] = "frame-src 'self' https://www.youtube.com https://player.vimeo.com";
        
        // Médias (audio, vidéo)
        $csp[] = "media-src 'self' https:";
        
        // Objets embarqués (object, embed, applet)
        $csp[] = "object-src 'none'";
        
        // Formulaires
        $csp[] = "form-action 'self'";
        
        // Cadres enfants (iframe, frame)
        $csp[] = "frame-ancestors 'self'";
        
        // Worker
        $csp[] = "worker-src 'self'";
        
        // Manifeste
        $csp[] = "manifest-src 'self'";
        
        // URL de base
        $csp[] = "base-uri 'self'";
        
        return implode('; ', $csp);
    }
    
    /**
     * Génère l'en-tête Permissions-Policy
     */
    private function getPermissionsPolicyHeader(): string
    {
        $policies = [
            'accelerometer=()',
            'ambient-light-sensor=()',
            'autoplay=()',
            'battery=()',
            'camera=()',
            'display-capture=()',
            'document-domain=()',
            'encrypted-media=()',
            'fullscreen=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'midi=()',
            'payment=()',
            'picture-in-picture=()',
            'publickey-credentials-get=()',
            'sync-xhr=()',
            'usb=()',
            'screen-wake-lock=()',
            'web-share=()',
            'xr-spatial-tracking=()',
        ];
        
        return implode(', ', $policies);
    }
    
    /**
     * Détermine l'en-tête Cache-Control approprié
     */
    private function getCacheControlHeader(Request $request, $response): string
    {
        // Désactiver le cache pour les pages d'administration et les formulaires
        if ($request->is('admin/*') || $request->is('profile*') || $request->is('settings*')) {
            return 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
        }
        
        // Pour les autres pages, permettre la mise en cache avec validation
        return 'public, max-age=3600, must-revalidate';
    }
}
