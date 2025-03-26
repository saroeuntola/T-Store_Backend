<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
  public function UserCount(){

        $count = User::count();
        return response()->json([
        'count' => $count
        ]);
    }

   public function index()
{
    try {
        $users = User::all();

        // Loop through users and attach roles
        foreach ($users as $user) {
            $user->roles = $user->getRoleNames();
        }

        return response()->json(['user' => $users]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

     public function create(){
        try{
            $roles = Role::pluck('name','name')->all();
            return response()->json(['data' => $roles]);
        }catch(\Exception $e){
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage(),
            ]);
        }

    }
public function store(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'sex' => 'required|string',
        'roles' => 'required'
    ]);

    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'sex' => $validated['sex'],


    ]);
    $user->assignRole($request->input('roles'));
    return response()->json([
        'access' => true,
        'message' => 'User created.',
        'user' => $user
    ]);
}

   public function show($id)
{
    $user = User::with('roles')->findOrFail($id);

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found.',
        ], 404);
    }


    return response ()->json([
        'status' => 'success',
        'user' => $user,
    ]);
}

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'email' => 'required|email',
                'roles' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            $user->update($request->all());
            $user->syncRoles($request->input('roles'));
            return response()->json(['message' => 'Update successful', 'user' => $user], 200);
        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
public function updatePassword(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'password' => '|required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'Update successful', 'data' => $user], 200);
    } catch (\Exception $ex) {
        return response()->json(['message' => $ex->getMessage()], 500);
    }

}

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted.',
        ]);
    }




   public function updateStatus($id)
{
    try {
        $user = User::find($id);
        if ($user) {
            $newStatus = $user->status === 'Active' ? 'Inactive' : 'Active';
            $user->status = $newStatus;
            $user->save();
            return response()->json(['message' => 'User status updated.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 400);
    }
}

 public function addProfilePicture(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB file size limit
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($request->hasFile('profile')) {
            $image = $request->file('profile');

            // Delete old image if it exists
            if ($user->profile) {
                $oldImagePath = storage_path('app/public/user_image/') . $user->profile;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Store the new image in storage/app/public/user_image/
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/user_image', $imageName);

            // Update user profile field
            $user->profile = $imageName;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile picture added successfully',
            'user' => $user
        ], 201);
    } catch (\Exception $ex) {
        return response()->json(['message' => $ex->getMessage()], 500);
    }
}





}
