<?php

namespace SmartlyJobs\Entities\User\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SmartlyJobs\Entities\Organisation\Models\Organisation;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    const TABLE = 'users';

    const ID = 'id';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const REMEMBER_TOKEN = 'remember_token';

    const CONFIRM_PASSWORD = 'password_confirmation';

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PASSWORD
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN
    ];


    public function organisations()
    {
        return $this->belongsToMany(
            Organisation::class,
            Organisation::ORGANISATION_USER_RELATED_TABLE,
            Organisation::ORGANISATION_USER_RELATED_USER_ID,
            Organisation::ORGANISATION_USER_RELATED_ORGANISATION_ID
        );
    }

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


    /**
     * Set the user's first name.
     *
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
