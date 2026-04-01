<?php
namespace App\Models;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'ITEM';
    protected $fillable = ['id', 'item_name', 'created_at', 'updated_at', 'creator_id'];

    // Relasi ke Entity melalui tabel Pivot
    public function entities()
    {
        return $this->belongsToMany(Entity::class, 'ENTITY_DETAIL_ITEM', 'item_id', 'entity_id')
                    ->withPivot('size', 'notes');
    }
}
