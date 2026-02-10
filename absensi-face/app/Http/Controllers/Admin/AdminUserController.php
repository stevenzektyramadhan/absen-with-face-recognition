<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Display list of employees.
     */
    public function index()
    {
        $users = User::where('role', 'karyawan')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create a new employee.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'jabatan'  => ['required', 'string', 'max:255'],
            'foto'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('photos', 'public');
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username,
            'jabatan'  => $request->jabatan,
            'foto'     => $fotoPath,
            'password' => Hash::make('12345678'),
            'role'     => 'karyawan',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Karyawan berhasil ditambahkan! Password default: 12345678');
    }

    /**
     * Delete an employee.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus user admin!');
        }

        // Delete photo if exists
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
