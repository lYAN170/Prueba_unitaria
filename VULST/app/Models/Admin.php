<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo Admin.
 * 
 * @method static where(string $column, string $operator, mixed $value = null)
 * @method static findOrFail(int $id)
 * @method static create(array $attributes)
 * 
 * @property string $name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property bool $is_superuser
 * @property string|null $avatar
 * @property string|null $avatar_url
 * @property int|null $personaa_id
 */
class Admin extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * El guard predeterminado para este modelo.
     * 
     * @var string
     */
    protected string $guard_name = 'admin';

    /**
     * Los atributos asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_superuser',
        'personaa_id', // Relación con personaas
        'estado',
        'avatar',
    ];

    /**
     * Los atributos que se deben ocultar para los arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_superuser' => 'boolean',
    ];

    /**
     * Accessor para obtener la URL completa del avatar.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Devuelve una imagen predeterminada si no hay avatar.
        return asset('default-avatar.png');
    }

    /**
     * Obtiene los grupos de permisos.
     *
     * @return Collection
     */
    public static function getPermissionGroups(): Collection
    {
        return DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();
    }

    /**
     * Obtiene los permisos por nombre de grupo.
     *
     * @param string $group_name
     * @return Collection
     */
    public static function getPermissionsByGroupName(string $group_name): Collection
    {
        return DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
    }

    /**
     * Verifica si un rol tiene los permisos especificados.
     *
     * @param \Spatie\Permission\Models\Role $role
     * @param Collection $permissions
     * @return bool
     */
    public static function roleHasPermissions($role, Collection $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Relación con la tabla de personaas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personaa()
    {
        return $this->belongsTo(Personaa::class, 'personaa_id');
    }

    public function persona()
{
    return $this->belongsTo(Persona::class);
}
}


