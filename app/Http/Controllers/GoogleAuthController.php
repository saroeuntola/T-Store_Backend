<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Handle Google callback
//   public function handleGoogleCallback()
// {
//     try {
//         $googleUser = Socialite::driver('google')->stateless()->user();

//         // Extract user details
//         $email = $googleUser->getEmail();
//         $name = $googleUser->getName();
//         $googleId = $googleUser->getId();
//         $avatar = $googleUser->getAvatar(); // Get profile image

//         // Check if user exists, otherwise create a new one
//         $user = User::updateOrCreate([
//             'email' => $email,
//         ], [
//             'username' => $name,
//             'google_id' => $googleId,
//             'profile' => $avatar,
//             'password' => bcrypt(Str::random(16)),
//         ]);


//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'user' => $user,
//             'token' => $token,
//         ]);
//     } catch (\Exception $e) {
//         return response()->json(['error' => 'Authentication failed'], 401);
//     }
// }
public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'username' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'google_user_' . Str::random(5),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'profile' => $googleUser->getAvatar(),
                    'sex' => 'other',
                    'password' => Hash::make(Str::random(16)),
                ]);
                $user->assignRole('user');
            }
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
            $token = JWTAuth::fromUser($user);

            $roles = $user->getRoleNames();
            $user->Roles = $roles;

            $permissions = $user->getAllPermissions()->pluck('name');
            $user->Permissions = $permissions;

            $user->token = $token;
            $data = $user->makeHidden('permissions', 'roles')->toArray();

            return
                response()->json([
                   'status' => 'success',
                    'user' => $data,
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate with Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

