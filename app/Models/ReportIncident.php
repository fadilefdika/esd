<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportIncident extends Model
{
    // Beritahu Laravel nama tabel aslinya
    protected $table = 'REPORT_INCIDENTS';

    // Karena SQL Server menggunakan format tanggal yang sedikit beda
    protected $dateFormat = 'Y-m-d H:i:s.v';

    protected $fillable = [
        'entity_id', 'item_id', 'set_no', 'report_type', 
        'details', 'evidence_path', 
        'status_report', 'creator_id'
    ];

    // Relasi ke tabel ENTITY
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'id');
    }

    // Relasi ke tabel ITEM
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}