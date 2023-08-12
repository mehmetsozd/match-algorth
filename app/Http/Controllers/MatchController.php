<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserMatchLike;
use App\Models\UserPicture;
use App\Models\User;


class MatchController extends Controller
{
    public function getUserList(Request $request)
{
    $validator = Validator::make($request->all(),[
        'gender' => 'required'
    ]);

    $gender = $request->gender;

    $user = Auth::user();
    $my_user_id = $user->id;

    $query = UserPicture::where('user_id', '!=', $my_user_id)
        ->select(['user_id', 'picture', 'id']);

    if (!is_null($gender)) {
        $query = $query->whereHas('user', function ($query) use ($gender) {
            $query->where('gender', $gender);
        });
    }

    $relativeUsers = $query->whereNotIn('user_id', function ($subquery) use ($my_user_id) {
        $subquery->select('liked_user_id')
            ->from('user_match_likes')
            ->where('user_id', $my_user_id);
    })->paginate(10);

    foreach ($relativeUsers as $item) {
        $user = User::find($item->user_id);
        $item->name = $user->name;
        $item->picture = $item->picture;
        $item->user_id = $user->id;
        $item->picture_id = $item->id;
        unset($item->id);
    }

    return response()->json($relativeUsers, 200);
}

    
    
    
    

    public function matchUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'liked_user_id'=>'required'
        ]);

        if ($validator->fails()) {
            print response()->json([$request],400);
        }

        $likedUser = User::where('id',$request->liked_user_id)->first();
        if ($likedUser) {
            $user = Auth::user();
            $matchCheck = UserMatchLike::where('liked_user_id',$user->id)->where('user_id',$request->liked_user_id)->first();
            UserMatchLike::create([
                'user_id'=>$user->id,
                'liked_user_id'=>$request->liked_user_id,
            ]);
            if($matchCheck){
                return response()->json(['message' => 'Matched'], 200);
            }else{
                return response()->json(['message' => 'waiting for match'], 204);
            }
        }else{
            return response()->json(['message' => 'User not found'], 404);
        }
       

    }
}
