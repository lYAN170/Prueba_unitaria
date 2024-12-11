<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use App\Models\Personaa;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // Para eliminar archivos antiguos
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Admin::class, 'admin');
    }

    public function index(): Renderable
    {
        $this->authorize('viewAny', Admin::class);

        return view('backend.pages.admins.index', [
            'admins' => Admin::all(),
        ]);
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $this->authorize('create', Admin::class);

        // Validación adicional
        $request->validate([
            'is_superuser' => 'nullable|boolean', // Asegura que sea un valor booleano
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);

        // Verifica si el administrador seleccionado es superusuario y si tiene roles asignados
        if ($request->boolean('is_superuser') && !empty($request->roles)) {
            return redirect()->back()->withErrors(['roles' => 'Superusers cannot have roles assigned.']);
        }

        // Verificación de persona
        $personaa = Personaa::find($request->input('personaa_id'));
        if (!$personaa) {
            return redirect()->back()->withErrors(['personaa_id' => 'Selected persona does not exist.']);
        }

        // Manejo del avatar
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $avatarPath = 'avatars/' . $filename;            
            $file->move(public_path('avatars'), $filename);
        }

        // Crear el nuevo administrador
        $admin = Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_superuser' => $request->boolean('is_superuser'),
            'personaa_id' => $request->input('personaa_id'),  // Asegúrate de usar personaa_id
            'fecha_creacion' => now(),  // Fecha actual para la creación
            'avatar' => $avatarPath,
        ]);

        // Asignar roles si no es un superusuario
        if (!$request->boolean('is_superuser') && $request->has('roles')) {
            $roles = $request->input('roles');
            $roleNames = is_array($roles) && is_numeric($roles[0])
                ? Role::whereIn('id', $roles)->pluck('name')->toArray()
                : $roles;
            $admin->assignRole($roleNames);
        }

        session()->flash('success', __('Admin has been created.'));
        return redirect()->route('admin.admins.index');
    }

    public function create(): Renderable
    {
        $this->authorize('create', Admin::class);
    
        // Obtener todas las personas y roles
        $personaas = Personaa::all();  // Puedes personalizar esto si necesitas una consulta específica
        $roles = Role::all();
        $isSuperadminSelected = old('is_superuser', false);
    
        // Pasar las variables a la vista
        return view('backend.pages.admins.create', compact('personaas', 'roles', 'isSuperadminSelected'));
    }
    public function edit(Admin $admin): Renderable
    {
        $this->authorize('update', $admin);

        return view('backend.pages.admins.edit', [
            'admin' => $admin,
            'roles' => Role::all(),
        ]);
    }

    public function update(AdminRequest $request, Admin $admin): RedirectResponse
    {
        $this->authorize('update', $admin);

        // Validación adicional
        $request->validate([
            'is_superuser' => 'nullable|boolean', // Asegura que sea un valor booleano
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);

        // Verifica si el administrador seleccionado es superusuario y si tiene roles asignados
        if ($request->boolean('is_superuser') && $request->has('roles')) {
            return redirect()->back()->withErrors(['roles' => 'Superusers cannot have roles assigned.']);
        }

        // Verificación de persona
        $personaa = Personaa::find($request->input('personaa_id'));
        if (!$personaa) {
            return redirect()->back()->withErrors(['personaa_id' => 'Selected persona does not exist.']);
        }

        // Manejo del avatar con eliminación del anterior
        $avatarPath = $admin->avatar;
        if ($request->hasFile('avatar')) {
            // Eliminar el avatar anterior si existe
            if ($avatarPath && File::exists(public_path($avatarPath))) {
                File::delete(public_path($avatarPath));
            }

            $file = $request->file('avatar');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $avatarPath = 'avatars/' . $filename;
            $file->move(public_path('avatars'), $filename);
        }

        // Datos a actualizar
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'is_superuser' => $request->boolean('is_superuser'),
            'personaa_id' => $request->input('personaa_id'),  // Asegúrate de usar personaa_id
            'avatar' => $avatarPath,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Actualizar admin
        $admin->update($data);

        // Asignar roles si no es un superusuario
        if (!$request->boolean('is_superuser')) {
            $roles = $request->input('roles', []);
            $roleNames = is_array($roles) && is_numeric($roles[0])
                ? Role::whereIn('id', $roles)->pluck('name')->toArray()
                : $roles;

            $admin->syncRoles($roleNames);
        } else {
            $admin->syncRoles([]);  // Eliminar roles si es un superusuario
        }

        session()->flash('success', 'Admin has been updated.');
        return redirect()->route('admin.admins.index');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        $this->authorize('delete', $admin);

        // No permitir eliminar un superusuario
        if ($admin->is_superuser) {
            session()->flash('error', 'Cannot remove superuser.');
            return redirect()->route('admin.admins.index');
        }

        // Eliminar el avatar del usuario si existe
        if ($admin->avatar && File::exists(public_path($admin->avatar))) {
            File::delete(public_path($admin->avatar));
        }

        // Eliminar el admin
        $admin->delete();

        session()->flash('success', 'Admin has been deleted.');
        return redirect()->route('admin.admins.index');
    }
}
