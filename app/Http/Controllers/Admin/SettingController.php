<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        $admin = $this->currentAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        return view('admin.settings.index', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = $this->currentAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin->id),
            ],
        ]);

        $admin->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $admin = $this->currentAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    private function currentAdmin(): ?Admin
    {
        $adminId = session('admin_id');

        if (! $adminId) {
            return null;
        }

        return Admin::find($adminId);
    }
}
