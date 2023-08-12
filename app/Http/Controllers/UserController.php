<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
       /**
     * Gets users except yourself
     *
     */
    public function userData()
    {
        $user = User::where('id', '=', auth()->user()->id)->first();
        return response()->json($user);
    }
}
