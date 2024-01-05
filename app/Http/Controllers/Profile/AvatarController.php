<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $req){

        $path = Storage::disk('public')->put('avatars',$req->file('avatar'));
        //$path = $req->file('avatar')->store('avatars','public');
        if($oldAvatar = $req->user()->avatar)
        Storage::disk('public')->delete($oldAvatar);

        auth()->user()->update(['avatar' => "$path"]);
        //return back()->with('message','avatar is chenged ');
        return redirect(route('profile.edit'))->with('message','avatar is updated');
    }
}
