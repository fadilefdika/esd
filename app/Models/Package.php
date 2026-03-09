<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'PACKAGE';
    protected $fillable = [
        'package_name','created_at', 'updated_at'
    ];

    public $incrementing = true;
    
    protected $primaryKey = 'id';

    public function items()
    {
        return $this->belongsToMany(Item::class, 'PACKAGE_DETAIL_ITEM', 'package_id', 'item_id')
                    ->withPivot('size');
    }

}
