<?php

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Profile\AvatarController;
use Illuminate\Support\Facades\Session;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::middleware(['guest'])->group(function () {
//     Route::get('/{any}', function () {
//         return view('auth.login');
//     })->where('any', '.*');
// });

Route::get('/', function () {
    // Vérifier si l'utilisateur est authentifié
    if (auth()->check()) {
        // Rediriger l'utilisateur vers la dernière page active ou vers /home par défaut
        $previousUrl = Session::get(auth()->id() . '_url.intended', '/home');
        Session::forget(auth()->id() . '_url.intended'); // Effacer la session après utilisation
        return redirect()->to($previousUrl);
    } else {
        // S'il n'est pas authentifié, enregistrer l'URL précédente dans la session
        $currentUserKey = auth()->id() . '_url.intended';
        Session::put($currentUserKey, url()->previous());
        return view('auth.login');
    }
})->middleware('auth');





Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


 Route::get('/dashboard', function () {
    return view('admin.layouts.app');
})->middleware(['auth', 'verified'])->name('dashboard');



 Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::middleware(['guest'])->group(function () {
//     Route::get('/', function () {
//         return view('auth.login');
//     })->name('login');
// });

require __DIR__.'/auth.php';



/* Route::get('/openai', function(){


    $result = OpenAI::completions()->create([
        'model' => 'text-davinci-003',
        'prompt' => 'PHP is',
    ]);

    echo $result['choices'][0]['text']; // an open-source, widely-used, server-side scripting language.

}); */

//  Route::get('/dashboard1', function(){
//     return view('dashboard1');
// });

Route::middleware('auth')->group(function () {

Route::get('/api/users', [UserController::class, 'index']);
Route::get('/api/getRole', [UserController::class, 'IsAdmin']);
Route::get('/api/getIdProf', [UserController::class, 'IdProf']);

Route::get('/api/salaireProf', [CentreController::class, 'getSlaireForProf']);



Route::get('/api/etudiants', [CentreController::class, 'index']);
Route::get('/api/etudiantsForPaiment', [CentreController::class, 'getUsersForPaiment']);
Route::get('/api/etudiantsForProf', [CentreController::class, 'getUsersForProf']);

Route::get('/api/professeurs', [ProfesseurController::class, 'index']);
Route::get('/api/professeursForSalaire', [CentreController::class, 'getUsersForSalaire']);
Route::get('/api/valeurs_paiments', [CentreController::class, 'getNbrMat']);
Route::get('/api/valeurs_paiment', [CentreController::class, 'getValeurPaiments']);
Route::get('/api/selectedProfesseurs/{selectedMatieres}/{selectedNiveau}/{selectedFiliere}', [CentreController::class, 'getProfesseurPourMatieres']);
Route::get('/api/valeurs_salaire', [CentreController::class, 'getValeurSalaires']);
Route::get('/api/enseignements', [ProfesseurController::class, 'getEnseignements']);




Route::get('/api/niveaux', [CentreController::class, 'getNiveaux']);
Route::get('/api/niveau', [CentreController::class, 'getNiveau']);

Route::get('/api/profs', [ProfesseurController::class, 'getProfs']);


Route::get('/api/filieres/{id}', [CentreController::class, 'getFilieres']);
Route::get('/api/filiere', [CentreController::class, 'getFiliere']);
Route::get('/api/matieres', [CentreController::class, 'getMatieres']);
Route::get('/api/matiere', [CentreController::class, 'getMatiere']);



Route::post('/api/users', [UserController::class, 'store']);
Route::post('/api/etudiants', [CentreController::class, 'store']);
Route::post('/api/professeurs', [ProfesseurController::class, 'store']);
Route::post('/api/enseignements', [ProfesseurController::class, 'createEnseignement']);

Route::post('/api/matiere', [CentreController::class, 'storeMatiere']);
Route::post('/api/niveau', [CentreController::class, 'storeNiveau']);
Route::post('/api/filiere', [CentreController::class, 'storeFiliere']);





Route::put('/api/users/{user}', [UserController::class, 'update']);
Route::put('/api/etudiants/{user}', [CentreController::class, 'update']);
// Route::put('/api/paiement/{user}', [CentreController::class, 'effectuerPaiement']);
Route::put('/api/professeurs/{user}', [ProfesseurController::class, 'update']);
Route::put('/api/valeurs_paiments', [CentreController::class, 'updateValPaiment']);
Route::put('/api/valeurs_salaires', [CentreController::class, 'updateValSalaire']);

Route::put('/api/paiements/{user}', [CentreController::class, 'effectuerPaiement']);
Route::put('/api/salaires/{user}', [CentreController::class, 'effectuerSalaire']);
Route::put('/api/enseignements/{user}', [ProfesseurController::class, 'updateEnseignement']);


Route::put('/api/matiere/{user}', [CentreController::class, 'updateMatiere']);
Route::put('/api/niveau/{user}', [CentreController::class, 'updateNiveau']);
Route::put('/api/filiere/{user}', [CentreController::class, 'updateFiliere']);






Route::delete('/api/users/{user}', [UserController::class, 'destory']);
Route::delete('/api/etudiants/{user}', [CentreController::class, 'destory']);
Route::delete('/api/professeurs/{user}', [ProfesseurController::class, 'destory']);
Route::delete('/api/enseignements/{user}', [ProfesseurController::class, 'destoryEnseignement']);
Route::delete('/api/matiere/{user}', [CentreController::class, 'destoryMatiere']);
Route::delete('/api/niveau/{user}', [CentreController::class, 'destoryNiveau']);
Route::delete('/api/filiere/{user}', [CentreController::class, 'destoryFiliere']);

Route::get('{view}', ApplicationController::class)->where('view', '(.*)');




});













