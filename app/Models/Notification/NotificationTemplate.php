<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';
    protected $primaryKey = 'notification_template_id';
    protected $guarded=[];

    public function customer_order()
    {
        return $this->hasOne('App\Models\Order\CustomerOrder','order_id','order_id');
    }
    public function noti_menu()
    {
        return $this->hasOne('App\Models\Notification\NotiMenu','noti_menu_id','notification_type');
    }

}
