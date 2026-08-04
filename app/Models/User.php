<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        'username',
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
            'preferences' => 'array',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $ip = request()?->ip();
        Log::info('Send password reset link to: ' . $this->email . ' from IP:' . $ip);
        $this->notify(new ResetPasswordNotification($token));
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
            'userid' => $this->id,
            'Roles to check' => $roles,
            'RequireAll' => $requireAll,
            //'Current user roles' => implode(',', $userRoles) ,
        ]);
        
        // check against one role
        if (is_string($roles)) {
            return in_array($roles, $userRoles);
        }

        // check all roles present for user
        if ($requireAll) {
            return empty(array_diff($roles, $userRoles));
        }

        // check if at least one role present or return false
        return !empty(array_intersect($roles, $userRoles));
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        Log::debug(__METHOD__ . ' called.');
        $roles = $this->roles->pluck('name')->toArray();
        
        Log::debug('User roles:', $roles);
        return $roles;
    }

    /**
     * Eloquent relation to user_bookmarks
     */
    public function bookmarks()
    {
        return $this->hasMany(UserBookmark::class);
    }

    /**
     * Check if cave is in user bookmarks
     * 
     *  @param string $caveUuid single uuid to check
     *  @return bool
     */
    public function isBookmark($caveUuid): bool
    {
        return $this->bookmarks()->where('cave_uuid', $caveUuid)->exists();
    }

    public function addRole(int|string|Role $role)
    {
        if ($role instanceof Role) {
            $roleId = $role->id;
        } elseif (is_int($role)) {
            $roleId = $role;
        } else {
            $roleId = Role::where('name', $role)->value('id');

            if (!$roleId) {
                throw new \InvalidArgumentException("Role '{$role}' not found");
            }
        }

        $this->roles()->syncWithoutDetaching([$roleId]);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }


    /**
     * Check whether the user is allowed to authenticate.
     *
     * This method performs various account status checks:
     * - account expiration date
     * - account disabled status
     * - other authentication constraints can be added here
     *
     * Return values:
     * - true  : authentication is allowed
     * - string: authentication is denied; the string contains the user-facing error message
     *
     * @return bool|string
     */
    public function canAuthenticate()
    {
        Log::debug(__METHOD__ . ' called.');
        
        //check if account disabled
        if ($this->is_disabled == false) {
            Log::debug('Account NOT disabled');
        }
        else{
            Log::warning('Account IS disabled');
            return __('varcave.login.account_disabled');
        }

        //check account expiration date
        if (!$this->expires_at || Carbon::parse($this->expires_at)->isFuture()) {
            Log::debug('Account DOES NOT expired');
        }
        else{
            Log::warning('Account IS expired');
            return __('varcave.login.account_expired');
        }

        return true; //user is allowed to authenticate
    }

}
