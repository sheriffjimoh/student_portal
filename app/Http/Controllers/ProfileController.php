<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;



class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', ['user' =>   $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'date_of_birth' => 'required|date',
                'address' => 'required|string',
                'photo' => 'nullable|image|max:1024'
            ]);
    
            if ($request->hasFile('photo')) {
                if ($user->photo_path) {
                    Storage::delete($user->photo_path);
                }
                $path = $request->file('photo')->store('photos', 'public');
                $validated['photo_path'] = $path;
            }
    
            $user->update($validated);
    
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the profile.'
            ], 500);
        }
    }
}
