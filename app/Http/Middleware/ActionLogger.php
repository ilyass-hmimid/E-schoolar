<?php

namespace App\Http\Middleware;

use App\Models\LogAction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ActionLogger
{
    protected $sensitiveActions = [
        'login',
        'logout',
        'password/email',
        'password/reset',
        'admin/',
        'professors',
        'students',
        'classes',
        'subjects',
        'grades',
        'absences',
        'payments',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for non-GET requests or specific GET endpoints
        if ($this->shouldLog($request)) {
            $this->logAction($request, $response);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        // Skip logging for these methods
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            // Only log GET requests to sensitive endpoints
            foreach ($this->sensitiveActions as $action) {
                if (str_contains($request->path(), $action)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    protected function logAction(Request $request, $response): void
    {
        $user = $request->user();
        $action = $request->method() . ' ' . $request->path();
        $status = $response->getStatusCode();
        
        // Skip logging successful GET requests to non-sensitive endpoints
        if ($request->isMethod('GET') && $status === 200) {
            $isSensitive = false;
            foreach ($this->sensitiveActions as $sensitive) {
                if (str_contains($request->path(), $sensitive)) {
                    $isSensitive = true;
                    break;
                }
            }
            if (!$isSensitive) {
                return;
            }
        }

        $metadata = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $status,
            'parameters' => $this->filterParameters($request->all()),
        ];

        try {
            // Vérifier si la table log_actions existe
            if (\Schema::hasTable('log_actions')) {
                LogAction::log(
                    action: $action,
                    description: "{$request->method()} request to {$request->path()}",
                    user: $user,
                    metadata: $metadata
                );
            } else {
                \Log::warning('La table log_actions n\'existe pas. Impossible d\'enregistrer l\'action.', [
                    'action' => $action,
                    'user_id' => $user?->id
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement de l\'action dans les logs', [
                'error' => $e->getMessage(),
                'action' => $action,
                'user_id' => $user?->id
            ]);
        }
    }

    protected function filterParameters(array $parameters): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key'];
        
        foreach ($parameters as $key => $value) {
            if (in_array($key, $sensitiveKeys)) {
                $parameters[$key] = '***';
            } elseif (is_array($value)) {
                $parameters[$key] = $this->filterParameters($value);
            }
        }
        
        return $parameters;
    }
}
