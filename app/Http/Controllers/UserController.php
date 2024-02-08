<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    public function IsAdmin() {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function IdProf() {
        return Auth::user()->IdProf;
    }



    public function index()
    {
        $users = User::latest()->get()->map(function ($user){

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'email' => $user->email,
                'created_at' => ($user->created_at) ? $user->created_at->format(config('app.date_format')) : null,

            ];
        });
        return $users;
    }
    public function store() {
        $data = request()->validate([
            'name' => 'required', // Supposons que 'name' contient les données JSON comme {"id":5,"Prenom":"Yassine","Nom":null}
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // Vérifier si le champ 'name' est une chaîne JSON valide
        if ($this->isJson($data['name'])) {
            $jsonData = json_decode($data['name'], true); // Convertir la chaîne JSON en tableau associatif
            if (isset($jsonData['Prenom'])) {
                $prenom = $jsonData['Prenom']; // Récupérer le prénom du tableau JSON
                $id = $jsonData['id'];
            }
        }
        else{$prenom=request('name');$id=null;}

                if(User::where('name',$prenom)->where('role',request('role'))->where('IdProf',$id)->first())
                {
                    return 0;
                }
                return User::create([
                    'name' => $prenom, // Stocker seulement le prénom dans la base de données
                    'role' => request('role'),
                    'email' => request('email'),
                    'IdProf' => $id,
                    'password' => bcrypt(request('password')),
                ]);




    }

    // Fonction pour vérifier si une chaîne est au format JSON valide
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public function update(User $user)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' .$user->id,
            'password' => 'sometimes|min:8',
        ]);
        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);
        return $user;
    }

    public function destory(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
