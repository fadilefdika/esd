<?php
namespace App\Models;

use Dom\Entity;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'TRANSACTION'; 
    protected $fillable = [
        'id', 'entity_id', 'transaction_code', 'transaction_start_date', 
        'transaction_end_date', 'transaction_type', 'transaction_status', 
        'transaction_image_start', 'transaction_image_finish',
        'created_at', 'updated_at', 'creator_id'
    ];

    protected $casts = [
        'transaction_start_date' => 'datetime',
        'transaction_end_date' => 'datetime',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'TRANSACTION_DETAIL_ITEM', 'transaction_id', 'item_id');
    }
}