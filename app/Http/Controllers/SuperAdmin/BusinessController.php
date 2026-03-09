<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Services\TenantDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::all();
        return view('super-admin.businesses.index', compact('businesses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required',
            'owner_name' => 'required',
            'email' => 'required|email|unique:businesses,email',
            'phone' => 'required',
            'gst_number' => 'nullable|string|max:20',
            'state' => 'required|string',
            'password' => 'required|min:8',
        ]);

        $dbName = 'billing_' . Str::slug($request->business_name) . '_' . time();

        // 1. Create Business Record
        $business = Business::create([
            'business_name' => $request->business_name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gst_number' => $request->gst_number,
            'state' => $request->state,
            'database_name' => $dbName,
            'status' => 'active',
        ]);

        // 2. Create Database
        TenantDatabaseService::createDatabase($dbName);

        // 3. Switch and Run Migrations
        TenantDatabaseService::switchToTenant($dbName);
        TenantDatabaseService::runTenantMigrations();

        // 4. Create Business Admin User (in central database)
        // Note: switchToTenant changed default connection, so we specify 'mysql' for User creation if we want it central
        User::create([
            'business_id' => $business->id,
            'name' => $request->owner_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'business_admin',
        ]);

        return redirect()->back()->with('success', 'Business registered successfully!');
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'business_name' => 'required',
            'owner_name' => 'required',
            'email' => 'required|email|unique:businesses,email,' . $business->id,
            'phone' => 'required',
            'gst_number' => 'nullable|string|max:20',
            'state' => 'required|string',
        ]);

        $business->update($request->only(['business_name', 'owner_name', 'email', 'phone', 'gst_number', 'state']));

        return redirect()->back()->with('success', 'Business updated successfully!');
    }

    public function destroy(Business $business)
    {
        // Delete the business's separate database
        TenantDatabaseService::deleteDatabase($business->database_name);
        
        $business->delete();
        return redirect()->back()->with('success', 'Business and its database deleted successfully!');
    }

    public function toggleStatus(Business $business)
    {
        $business->status = $business->status === 'active' ? 'suspended' : 'active';
        $business->save();

        return redirect()->back()->with('success', 'Business status updated!');
    }
}
