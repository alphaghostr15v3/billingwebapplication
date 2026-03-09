<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $business = $user->business;
        
        $layout = $user->role === 'super_admin' ? 'layouts.super-admin' : 'layouts.business';
        $routePrefix = $user->role === 'super_admin' ? 'super-admin' : 'business';
        
        return view('profile.index', compact('user', 'business', 'layout', 'routePrefix'));
    }

    /**
     * Update personal information.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:mysql.users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(public_path($user->profile_photo))) {
                unlink(public_path($user->profile_photo));
            }

            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile_photos'), $filename);
            $user->profile_photo = 'uploads/profile_photos/' . $filename;
        }

        if ($request->new_password) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update business information (Business Admin only).
     */
    public function updateBusiness(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'business_admin' || !$user->business) {
            abort(403);
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->business->update([
            'business_name' => $request->business_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Business details updated successfully!');
    }
}
