<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
            ]);
        }

        // If no specific role is required, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check for admin role - full access
        if ($user->role === \App\Enums\RoleType::ADMIN) {
            return $next($request);
        }

        // Check for professor role - access to their classes/subjects
        if (in_array('professor', $roles) && $user->role === \App\Enums\RoleType::PROFESSEUR) {
            // Additional check for professor-specific resources
            if ($this->hasProfessorAccess($request, $user)) {
                return $next($request);
            }
        }

        // Check for student role - access to their own data
        if (in_array('student', $roles) && $user->role === \App\Enums\RoleType::ELEVE) {
            if ($this->hasStudentAccess($request, $user)) {
                return $next($request);
            }
        }

        // Check for assistant role - access to specific functionalities
        if (in_array('assistant', $roles) && $user->role === \App\Enums\RoleType::ASSISTANT) {
            if ($this->hasAssistantAccess($request, $user)) {
                return $next($request);
            }
        }

        // Check for other roles using the enum-based role system
        foreach ($roles as $role) {
            // Convert role string to enum value
            try {
                $roleEnum = \App\Enums\RoleType::fromName(strtoupper($role));
                if ($user->role === $roleEnum) {
                    return $next($request);
                }
            } catch (\ValueError $e) {
                // Role not found in enum, continue to next check
                continue;
            }
        }
        
        // Si on arrive ici, l'utilisateur n'a aucun des rôles requis
        abort(403, 'Accès non autorisé pour votre rôle.');
    }
    
    /**
     * Check if professor has access to the requested resource
     */
    protected function hasProfessorAccess(Request $request, $user): bool
    {
        // Allow access to professor dashboard and related pages
        if ($request->is('professor/dashboard*') || 
            $request->is('professor/profile*') ||
            $request->is('professor/classes*') ||
            $request->is('professor/subjects*') ||
            $request->is('professor/students*')) {
            return true;
        }

        // Check for specific class access
        if ($request->routeIs('classes.*') || $request->is('classes/*')) {
            $classId = $request->route('class') ?? $request->class_id;
            if ($classId) {
                return $user->classes()->where('id', $classId)->exists();
            }
        }

        // Check for specific subject access
        if ($request->routeIs('subjects.*') || $request->is('subjects/*')) {
            $subjectId = $request->route('subject') ?? $request->subject_id;
            if ($subjectId) {
                return $user->subjects()->where('id', $subjectId)->exists();
            }
        }

        // Check for specific student access (if professor teaches this student)
        if ($request->routeIs('students.*') || $request->is('students/*')) {
            $studentId = $request->route('student') ?? $request->student_id;
            if ($studentId) {
                return $user->students()->where('users.id', $studentId)->exists();
            }
        }

        return false;
    }

    /**
     * Check if student has access to the requested resource
     */
    protected function hasStudentAccess(Request $request, $user): bool
    {
        // Allow access to student dashboard and related pages
        if ($request->is('student/dashboard*') || 
            $request->is('student/profile*') ||
            $request->is('student/grades*') ||
            $request->is('student/absences*') ||
            $request->is('student/payments*')) {
            return true;
        }

        // Check if student is accessing their own data
        if ($request->routeIs('profile.*') || $request->is('profile*')) {
            $userId = $request->route('user') ?? $request->user_id;
            return !$userId || $userId == $user->id;
        }

        // Check for specific grade access
        if ($request->routeIs('grades.*') || $request->is('grades/*')) {
            $gradeId = $request->route('grade') ?? $request->grade_id;
            if ($gradeId) {
                return $user->grades()->where('id', $gradeId)->exists();
            }
        }

        // Check for specific absence access
        if ($request->routeIs('absences.*') || $request->is('absences/*')) {
            $absenceId = $request->route('absence') ?? $request->absence_id;
            if ($absenceId) {
                return $user->absences()->where('id', $absenceId)->exists();
            }
        }

        // Check for specific payment access
        if ($request->routeIs('payments.*') || $request->is('payments/*')) {
            $paymentId = $request->route('payment') ?? $request->payment_id;
            if ($paymentId) {
                return $user->payments()->where('id', $paymentId)->exists();
            }
        }

        return false;
    }

    /**
     * Récupère les alias pour un rôle donné
     */
    private function getRoleAliases($role)
    {
        // Convertir en chaîne et en minuscules pour la comparaison
        $role = strtolower((string)$role);
        
        // Définition des alias de rôles
        $aliases = [
            // Admin
            '1' => ['admin', 'administrateur', 'administrator'],
            'admin' => ['admin', 'administrateur', 'administrator'],
            'administrateur' => ['admin', 'administrateur', 'administrator'],
            
            // Professeur
            '2' => ['professeur', 'prof', 'teacher'],
            'professeur' => ['professeur', 'prof', 'teacher'],
            'prof' => ['professeur', 'prof', 'teacher'],
            
            // Assistant
            '3' => ['assistant', 'assist', 'aide'],
            'assistant' => ['assistant', 'assist', 'aide'],
            
            // Élève
            '4' => ['eleve', 'etudiant', 'student'],
            'eleve' => ['eleve', 'etudiant', 'student'],
            'etudiant' => ['eleve', 'etudiant', 'student'],
        ];
        
        return $aliases[$role] ?? [$role];
    }
}
