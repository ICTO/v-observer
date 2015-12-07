<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'cas_username'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the users attached to this group (user).
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User','user_group', 'group_id', 'user_id')->withPivot('role')->withTimestamps();
    }

    /**
     * The groups that belongs to this user.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Models\User','user_group', 'user_id', 'group_id')->withPivot('role')->withTimestamps();
    }

    /**
     * Get all of the questions.
     */
    public function questionaires()
    {
        return $this->hasMany('App\Models\Questionaire', 'owner_id');
    }

}
