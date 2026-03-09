<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entity extends Model
{
    protected $table = 'ENTITY';
    protected $fillable = [
        'id', 'code', 'npk', 'employee_name', 'dept_id', 'dept_name', 
        'no_loker', 'line_id', 'line_name', 'status', 'entity_link_qr',
        'created_at', 'updated_at', 'creator_id', 'category', 'package', 'code_esd', 'total_set_esd'
    ];

    public $incrementing = true;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'ENTITY_DETAIL_ITEM', 'entity_id', 'item_id')
                    ->withPivot('size', 'notes', 'creator_id', 'status', 'receive_date', 'return_date', 'return_notes');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'entity_id', 'id');
    }
    
    public function codeEsd()
    {
        // Hubungkan ke model CodeEsd menggunakan kolom 'code_esd'
        return $this->belongsTo(CodeEsd::class, 'code_esd');
    }

    protected static function booted()
{
    static::creating(function ($entity) {
        // HANYA buat kode otomatis jika belum diisi manual
        if (empty($entity->code)) {
            $year = date('Y');
            // Gunakan id sebagai patokan urutan agar lebih presisi daripada created_at
            $latest = self::whereYear('created_at', $year)->latest('id')->first();
            $number = $latest ? (intval(substr($latest->code, -4)) + 1) : 1;
            $entity->code = 'ENT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
    });
}
}
