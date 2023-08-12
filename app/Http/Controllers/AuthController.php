<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use App\Models\UserPicture;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Storage; // Storage sınıfının namespace'ini ekleyin



class AuthController extends Controller
{
    /**
     * Registers a user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
    
        // Kullanıcı adını isteğe bağlı olarak kullanıcıdan alabilirsiniz
        if (isset($data['name'])) {
            $data['name'] = $data['name'];
        } else {
            // Kullanıcı adını otomatik olarak oluşturabilirsiniz
            $data['name'] = 'user'.uniqid();
        }

        //profile photo upload
        $profilePhoto = $request->file('profile_photo');
        if ($profilePhoto) {
            $file_name = md5(time() . rand(0, 99999) . time()) . '.' . $profilePhoto->getClientOriginalExtension();
            $filePath = $profilePhoto->storeAs('uploads', $file_name, 'public');
            $url = Storage::disk('public')->url($filePath); // Dosyanın URL'si
            $data['profile_photo'] = $url; // Dosya URL'sini veritabanına kaydet
        }
    
        $user = User::create($data);
        $token = $user->createToken('UserToken')->accessToken;
    
        // return $this->success([
        //     'token' => $token,
        // ], 'User has been register successfully.');
        return response()->json(['token'=>$token]);
    }

    /**
     * Logins a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $isValid = $this->isValidCredential($request);

        if (!$isValid['success']) {
            return $this->error($isValid['message'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $isValid['user'];
        $token = $user->createToken('UserToken')->accessToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Login successfully!');
    }

    /**
     * Validates user credential
     *
     * @param LoginRequest $request
     * @return array
     */
   private function isValidCredential(LoginRequest $request) : array
{
    $data = $request->validated();

    if (!auth()->attempt(['phone_number' => $data['phone_number'], 'password' => $data['password']])) {
        return [
            'success' => false,
            'message' => 'Invalid Credential'
        ];
    }

    $user = auth()->user();

    return [
        'success' => true,
        'user' => $user
    ];
}


    /**
     * Logins a user with token
     *
     * @return JsonResponse
     */
    public function loginWithToken() : JsonResponse
    {
        return $this->success(auth()->user(),'Login successfully!');
    }

    /**
     * Logouts a user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return $this->success(null, 'Logout successfully!');
    }

    /**
     * Deletes user account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->delete();

        // revoke user's access token
        $user->token()->revoke();

        return $this->success(null, 'Account deleted successfully!');
    }

}