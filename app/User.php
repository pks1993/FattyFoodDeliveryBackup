<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    protected $primaryKey = 'user_id';
    protected $guarded=[];

    public function zone()
    {
        return $this->belongsTo('App\Models\Zone\Zone','zone_id','zone_id');
    }
    public function order()
    {
        return $this->hasMany('App\Models\Order','order_id');
    }
    public function wishlist()
    {
        return $this->belongsTo('App\Models\Wishlist','user_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $fillable = [
        'name', 'email', 'password','phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
