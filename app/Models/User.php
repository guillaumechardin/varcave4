<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firstname',
        'lastname ',
        'email',
        'theme',
        'map_layer',
        'datatables_max_items',
        'pref_coord_system',
        'caving_group',
        'language',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user is member of one or many roles
     *
     * @param string|array $roles Single role or array of roles
     * @param bool $requireAll If true, check membership on all roles
     * @return bool
     */
    public function hasRole(string|array $roles, bool $requireAll = false): bool
    {
        $userRoles = $this->getRoles();

        Log::debug(__METHOD__ . ' called.', [
            'Roles to check' => $roles,
            'RequireAll' => $requireAll,
            'Current user roles' => implode(',', $userRoles) ,
        ]);
        
        if (is_string($roles)) {
            return in_array($roles, $userRoles);
        }

        if ($requireAll) {
            return empty(array_diff($roles, $userRoles));
        }

        // au moins un rôle présent
        return !empty(array_intersect($roles, $userRoles));
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles->pluck('name')->toArray();
    }
}
