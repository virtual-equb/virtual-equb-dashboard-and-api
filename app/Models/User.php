<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use HasFactory;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'gender',
        // 'role',
        'enabled',
        'token',
        'fcm_id',
        'address'
    ];
    public function getAddressAttribute($value){
        return json_decode($value);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];
     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // public function roles()
    // {
    //     return $this->belongsTo(Role::class, 'role');
    // }

    // public function hasRole($role)
    // {
    //     return $this->roles()->where('name', $role)->exists();
    // }

    // public function permissions()
    // {
    //     // return $this->roles->map->permissions->flatten()->pluck('name')->unique();
    //     return $this->roles()->with('permissions')->get()
    //         ->pluck('permissions')->flatten();
    // }

    // public function hasPermission($permission)
    // {
    //     return $this->permissions()->contains($permission);
    // }

    // public function assignRole($role)
    // {
    //     // $this->roles()->attach($role);
    //     $roles = Role::where('name', $role)->first();
    //     if ($roles) {
    //         $this->roles()->attach($roles);
    //     }
    // }

    // public function removeRole($role)
    // {
    //     $this->roles()->detach($role);
    // }

}