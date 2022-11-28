<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Model;

class NotiMenu extends Model
{
    protected $table = 'noti_menus';
    protected $primaryKey = 'noti_menu_id';
    protected $guarded=[];
}
