<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->only(['search', 'role', 'status']);

        $users = User::query()
            ->withCount(['reports', 'letters'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, function ($query, $role) {
                if ($role === 'operator') {
                    $query->whereIn('role', ['operator', 'staff']);
                } else {
                    $query->where('role', $role);
                }
            })
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', true))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('users.index', compact('users', 'stats', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,operator,verifikator,pimpinan,staff'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama lengkap',
            'email' => 'email',
            'position' => 'jabatan',
            'phone' => 'nomor telepon',
            'role' => 'peran',
            'password' => 'kata sandi',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'] ?? 'Staf Sekretariat',
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log('created', 'Menambahkan pengguna baru "'.$user->name.'"', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna "'.$user->name.'" berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,operator,verifikator,pimpinan,staff'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama lengkap',
            'email' => 'email',
            'position' => 'jabatan',
            'phone' => 'nomor telepon',
            'role' => 'peran',
            'password' => 'kata sandi',
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'] ?? 'Staf Sekretariat',
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($user->id === $request->user()->id) {
            $payload['role'] = $user->role;
            $payload['is_active'] = true;
        }

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        ActivityLogger::log('updated', 'Memperbarui data pengguna "'.$user->name.'"', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna "'.$user->name.'" berhasil diperbarui.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLogger::log(
            'updated',
            ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' akun "'.$user->name.'"',
            $user
        );

        return back()->with('success', 'Status akun "'.$user->name.'" berhasil diubah menjadi '.($user->is_active ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->reports()->exists() || $user->letters()->exists()) {
            return back()->with('error', 'Pengguna memiliki data laporan/surat. Nonaktifkan akun sebagai gantinya agar riwayat tetap utuh.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('deleted', 'Menghapus pengguna "'.$name.'"', null);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna "'.$name.'" berhasil dihapus.');
    }
}
