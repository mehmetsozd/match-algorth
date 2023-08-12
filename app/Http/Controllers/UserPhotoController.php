<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPicture;

class UserPhotoController extends Controller
{
    /**
     * Gets users yourself photo
     *
     */
    public function getPhoto()
    {
        $user = User::where('id', '=', auth()->user()->id)->first();
        $userPicture = UserPicture::where('user_id','=',$user->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($userPicture);
    }


    
}
