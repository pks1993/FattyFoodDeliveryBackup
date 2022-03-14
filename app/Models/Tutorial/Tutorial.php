<?php

namespace App\Models\Tutorial;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $table = 'tutorials';
    protected $primaryKey = 'tutorial_id';
    protected $guarded=[];
}
