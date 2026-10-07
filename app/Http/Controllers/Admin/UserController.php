<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system users with filtering and search.
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');
        $search = trim((string) $request->query('q'));

        $query = User::query();

        // Filter role
        if (! empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        // Filter status
        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Search name, email, username, phone
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Statistics for summary badges/cards
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'operator' => User::where('role', User::ROLE_OPERATOR)->count(),
            'bendahara' => User::where('role', User::ROLE_BENDAHARA)->count(),
            'kepala_sekolah' => User::where('role', User::ROLE_KEPALA_SEKOLAH)->count(),
            'pewawancara' => User::where('role', User::ROLE_PEWAWANCARA)->count(),
            'guru' => User::where('role', User::ROLE_GURU)->count(),
            'calon_siswa' => User::where('role', User::ROLE_CALON_SISWA)->count(),
        ];

        $availableRoles = [
            User::ROLE_OPERATOR => 'Operator',
            User::ROLE_BENDAHARA => 'Bendahara',
            User::ROLE_KEPALA_SEKOLAH => 'Kepala Sekolah',
            User::ROLE_PEWAWANCARA => 'Pewawancara',
            User::ROLE_GURU => 'Guru',
            User::ROLE_ADMIN => 'Administrator',
        ];

        return view('admin.users.index', compact('users', 'stats', 'availableRoles', 'roleFilter', 'statusFilter', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $availableRoles = [
            User::ROLE_OPERATOR => 'Operator',
            User::ROLE_BENDAHARA => 'Bendahara',
            User::ROLE_KEPALA_SEKOLAH => 'Kepala Sekolah',
            User::ROLE_PEWAWANCARA => 'Pewawancara',
            User::ROLE_GURU => 'Guru',
            User::ROLE_ADMIN => 'Administrator',
        ];

        return view('admin.users.create', compact('availableRoles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $allowedRoles = [
            User::ROLE_OPERATOR,
            User::ROLE_BENDAHARA,
            User::ROLE_KEPALA_SEKOLAH,
            User::ROLE_PEWAWANCARA,
            User::ROLE_GURU,
            User::ROLE_ADMIN,
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
            'role.required' => 'Hak akses / role wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'username' => ! empty($validated['username']) ? trim($validated['username']) : null,
            'phone' => ! empty($validated['phone']) ? trim($validated['phone']) : null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (function_exists('activity')) {
            activity('user_management')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log("Menambahkan pengguna baru: {$user->name} dengan peran {$user->role_label} ({$user->email})");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$user->name} ({$user->role_label}) berhasil dibuat.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $availableRoles = [
            User::ROLE_OPERATOR => 'Operator',
            User::ROLE_BENDAHARA => 'Bendahara',
            User::ROLE_KEPALA_SEKOLAH => 'Kepala Sekolah',
            User::ROLE_PEWAWANCARA => 'Pewawancara',
            User::ROLE_GURU => 'Guru',
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_CALON_SISWA => 'Calon Siswa',
        ];

        return view('admin.users.edit', compact('user', 'availableRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $allowedRoles = [
            User::ROLE_OPERATOR,
            User::ROLE_BENDAHARA,
            User::ROLE_KEPALA_SEKOLAH,
            User::ROLE_PEWAWANCARA,
            User::ROLE_GURU,
            User::ROLE_ADMIN,
            User::ROLE_CALON_SISWA,
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'password' => ['nullable', 'string', Password::min(6), 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar untuk pengguna lain.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'role.required' => 'Hak akses / role wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
            'password.min' => 'Password minimal terdiri dari :min karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Safety guard: Admin editing self cannot demote self or deactivate self
        $isSelf = (int) $user->id === (int) auth()->id();

        if ($isSelf && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah peran akun Administrator Anda sendiri.');
        }

        $isActive = $isSelf ? true : $request->boolean('is_active');

        $updateData = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'username' => ! empty($validated['username']) ? trim($validated['username']) : null,
            'phone' => ! empty($validated['phone']) ? trim($validated['phone']) : null,
            'role' => $validated['role'],
            'is_active' => $isActive,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (function_exists('activity')) {
            activity('user_management')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log("Memperbarui data akun pengguna: {$user->name} ({$user->email})");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusLabel = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if (function_exists('activity')) {
            activity('user_management')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log("Akun pengguna {$user->name} ({$user->email}) telah {$statusLabel}");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Status akun {$user->name} berhasil {$statusLabel}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deletion if user has attached interview records
        if ($user->wawancara()->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', "Pengguna {$user->name} memiliki riwayat penilaian wawancara dan tidak dapat dihapus. Silakan nonaktifkan akun ini jika tidak digunakan.");
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role_label;

        $user->delete();

        if (function_exists('activity')) {
            activity('user_management')
                ->causedBy(auth()->user())
                ->log("Menghapus akun pengguna: {$userName} ({$userRole} - {$userEmail})");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$userName} berhasil dihapus dari sistem.");
    }
}
