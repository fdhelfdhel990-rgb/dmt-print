<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:owner,admin,production,finance'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
            'is_active' => true,
        ]);

        return back()->with('status', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:owner,admin,production,finance'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update($validated + ['is_admin' => true, 'is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['admin' => 'Akun yang sedang digunakan tidak dapat dinonaktifkan.']);
        }

        $user->update(['is_active' => false]);

        return back()->with('status', 'Admin dinonaktifkan.');
    }
}
