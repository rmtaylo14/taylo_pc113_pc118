<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialMail;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::where('role', '!=', 'admin')->get();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'address' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'role' => 'required|in:admin,manager,user',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $path = $file->store('profile_pictures', 'public');
                $validated['profile_picture_url'] = asset('storage/' . $path);
            }

            // $user = User::create($validated);
            // if($user) {
            //     Mail::to($user->email)->send(new CredentialMail($id, $firstname));
            // }else{
            //     return response()->json([
            //         'message' => 'Error sending email',
            //     ], 500);
            // }


            return response()->json([
                'message' => 'User added successfully',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error adding user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::find($request->id);

            $validated = $request->validate([
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
                'address' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'role' => 'nullable|in:admin,manager,user',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $path = $file->store('profile_pictures', 'public');
                $user->profile_picture_url = asset('storage/' . $path);
            }

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->update($validated);
            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function findUser(Request $request) {
    $user = User::find($request->id);

    return response()->json([
        'user' => $user
    ]);
    }

  



public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'address' => 'nullable|string|max:255',
        'phone_number' => 'nullable|string|max:20',
        'password' => 'required|string|min:6',
        'role' => 'in:user'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::create([
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'email' => $request->email,
        'address' => $request->address,
        'phone_number' => $request->phone_number,
        'password' => Hash::make($request->password),
        'role' => 'user'
    ]);

    // Corrected to pass user's firstname
    Mail::to($user->email)->send(new CredentialMail($user->firstname));

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user
    ], 201);
}




    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function user()
    {
        return response()->json([
            'user' => Auth::user(),
            'message' => 'Authenticated User',
        ], 200);
    }


}
