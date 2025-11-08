<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::select('id', 'name', 'email', 'role', 'title', 'title_description')
            ->orderBy('id', 'desc')
            ->paginate(15);
        return Inertia::render('admin/Accounts', [
            'accounts' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'title' => 'nullable|string|max:255',
            'title_description' => 'nullable|string|max:255',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'title' => $validated['title'] ?? null,
            'title_description' => $validated['title_description'] ?? null,
        ]);
        return redirect()->back()->with('success', 'Account created!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'title' => 'nullable|string|max:255',
            'title_description' => 'nullable|string|max:255',
        ]);
        $user->update($validated);
        return redirect()->back()->with('success', 'Account updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Account deleted!');
    }
}
