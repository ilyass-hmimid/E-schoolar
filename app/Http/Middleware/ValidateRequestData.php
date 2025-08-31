<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateRequestData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(Request $request, Closure $next, string $rules = null)
    {
        // Si aucune règle n'est spécifiée, passer à la requête suivante
        if (!$rules) {
            return $next($request);
        }

        // Charger les règles de validation depuis le fichier de configuration
        $validationRules = config('validation_rules.' . $rules);
        
        if (!$validationRules) {
            return $next($request);
        }

        // Valider les données de la requête
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                throw new ValidationException($validator);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        return $next($request);
    }
}
