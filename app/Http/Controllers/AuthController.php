<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
        ['except' => 
            [
                'login', 
                'register', 
                'redirectToGoogle',
                'handleGoogleCallback'
            ]
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

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
                    'sex' => 'other',
                    'password' => Hash::make(Str::random(16)),
                ]);
                $user->assignRole('admin');
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
    
            return redirect("http://localhost:3000/google-auth-success?token={$token}");

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate with Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function login(Request $request)
    {
        try{
             $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
            JWTAuth::setToken($token);
            $user = JWTAuth::user();
            $auth = User::where('id', $user->id)->first();

            //get user's role
            $roles = $auth->getRoleNames();
            $auth->Roles = $roles;

            //get all user's permissions
            $permissions = $auth->getAllPermissions()->pluck('name');
            $auth->Permissions = $permissions;

            $auth->token = $token;
            $data = $auth->makeHidden('permissions', 'roles')->toArray();

            return response()->json([
                'message' => 'You are login successfully',
                'status' => 'success',
                'user' => $data
            ], 200, [], JSON_NUMERIC_CHECK);
    }
    catch (\Exception $e){
        return response()->json([
           'message' => $e->getMessage()
        ], 500);
    }

    }


   public function register(Request $request)
{
   try{
     $validatedData = $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'sex' => 'required|string',
    ]);

    $user = User::create([
        'username' => $validatedData['username'],
        'email' => $validatedData['email'],
        'sex' => $validatedData['sex'],
        'password' => Hash::make($validatedData['password']),
    ]);
    // $user->syncPermissions(['book-list']);
    $user->assignRole('user');
    //get user's role
    $roles = $user->getRoleNames();
    $user->Roles = $roles;

    //get all user's permissions
    $permissions = $user->getAllPermissions()->pluck('name');
    $user->Permissions = $permissions;

    // $user->token = JWTAuth::fromUser($user);
    $data = $user->makeHidden('permissions', 'roles')->toArray();

    return response()->json([
       'message' => 'User created successfully',
        'user' => $data
    ], 201, [], JSON_NUMERIC_CHECK);
   }
   catch (\Exception $e){
       return response()->json([
          'message' => $e->getMessage()
       ], 500);
   }
}

     public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function refresh()
    {
        return response()->json([
            'user' => JWTAuth::user(),
            'token' => JWTAuth::refresh(),
        ]);
    }

    public function me(){
        //get_current_user

        try{
        $user = JWTAuth::user();
        $auth = User::where('id', $user->id)->first();
            //get user's role
            $roles = $auth->getRoleNames();
            $auth->Roles = $roles;
            //get all user's permissions
            $permissions = $auth->getAllPermissions()->pluck('name');
            $auth->Permissions = $permissions;
            $data = $auth->makeHidden('permissions', 'roles')->toArray();

            return response()->json($data);

        }
        catch (\Exception $e){
            return response()->json([
               'message' => $e->getMessage()
            ], 500);
        }
    }
}
