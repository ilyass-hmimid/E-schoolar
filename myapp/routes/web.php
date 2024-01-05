<?php

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Profile\AvatarController;


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

Route::get('/', function () {
    return view('welcome');
});

/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

//  Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

/* Route::get('/openai', function(){


    $result = OpenAI::completions()->create([
        'model' => 'text-davinci-003',
        'prompt' => 'PHP is',
    ]);

    echo $result['choices'][0]['text']; // an open-source, widely-used, server-side scripting language.

}); */

/* Route::get('/dashboard1', function(){
    return view('dashboard1');
}); */

Route::get('/api/users', [UserController::class, 'index']);
Route::get('/api/etudiants', [CentreController::class, 'index']);
Route::get('/api/etudiantsForPaiment', [CentreController::class, 'getUsersForPaiment']);
Route::get('/api/professeurs', [ProfesseurController::class, 'index']);
Route::get('/api/professeursForSalaire', [CentreController::class, 'getUsersForSalaire']);
Route::get('/api/valeurs_paiments', [CentreController::class, 'getNbrMat']);
Route::get('/api/valeurs_paiment', [CentreController::class, 'getValeurPaiments']);
Route::get('/api/selectedProfesseurs/{selectedMatieres}/{selectedNiveau}/{selectedFiliere}', [CentreController::class, 'getProfesseurPourMatieres']);
Route::get('/api/valeurs_salaire', [CentreController::class, 'getValeurSalaires']);




Route::get('/api/niveaux', [CentreController::class, 'getNiveaux']);
Route::get('/api/filieres/{id}', [CentreController::class, 'getFilieres']);
Route::get('/api/matieres', [CentreController::class, 'getMatieres']);


Route::post('/api/users', [UserController::class, 'store']);
Route::post('/api/etudiants', [CentreController::class, 'store']);
Route::post('/api/professeurs', [ProfesseurController::class, 'store']);


Route::put('/api/users/{user}', [UserController::class, 'update']);
Route::put('/api/etudiants/{user}', [CentreController::class, 'update']);
// Route::put('/api/paiement/{user}', [CentreController::class, 'effectuerPaiement']);
Route::put('/api/professeurs/{user}', [ProfesseurController::class, 'update']);
Route::put('/api/valeurs_paiments', [CentreController::class, 'updateValPaiment']);
Route::put('/api/paiements/{user}', [CentreController::class, 'effectuerPaiement']);




Route::delete('/api/users/{user}', [UserController::class, 'destory']);
Route::delete('/api/etudiants/{user}', [CentreController::class, 'destory']);
Route::delete('/api/professeurs/{user}', [ProfesseurController::class, 'destory']);



Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
