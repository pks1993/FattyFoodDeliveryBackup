<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';
    protected $primaryKey = 'notification_template_id';
    protected $guarded=[];

}
