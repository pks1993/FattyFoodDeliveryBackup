<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'category_type_id';

    public function assign()
    {
        return $this->hasMany('App\Models\Category\CategoryAssign','category_type_id');
    }
}
