<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Où rediriger les utilisateurs après la connexion.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    
    /**
     * Le nombre maximum de tentatives de connexion autorisées.
     *
     * @var int
     */
    protected $maxAttempts = 5;
    
    /**
     * Le nombre de minutes pendant lesquelles l'utilisateur est bloqué après avoir dépassé le nombre maximum de tentatives.
     *
     * @var int
     */
    protected $decayMinutes = 15;

    /**
     * Créer une nouvelle instance du contrôleur.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Afficher le formulaire de connexion de l'application.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * L'utilisateur a été authentifié.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Vérifier si le compte est actif
        if ($user->status !== 'actif') {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        }
        
        // Mettre à jour la dernière date de connexion
        $user->last_login_at = now();
        $user->save();
        
        // Enregistrer l'activité de connexion
        if (class_exists('App\Events\UserLoggedIn')) {
            event(new \App\Events\UserLoggedIn($user, $request));
        }
        
        // Redirection en fonction du rôle
        switch ($user->role) {
            case 'admin':
                $redirectTo = route('admin.dashboard');
                break;
                
            case 'professeur':
                $redirectTo = route('professeur.dashboard');
                break;
                
            case 'eleve':
                $redirectTo = route('eleve.dashboard');
                break;
                
            default:
                $redirectTo = route('home');
        }
        
        // Vérifier s'il y a une URL de redirection dans la requête
        if ($request->has('redirect')) {
            return redirect($request->input('redirect'));
        }
        
        // Vérifier s'il y a une URL de redirection dans la session
        if ($request->session()->has('url.intended')) {
            return redirect()->intended($redirectTo);
        }
        
        // Rediriger vers le tableau de bord approprié
        return redirect($redirectTo);
    }

    /**
     * Send the response after a failed login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->route('login')
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => ['Email ou mot de passe incorrect'],
            ]);
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('status', 'Vous avez été déconnecté avec succès.');
    }
}
