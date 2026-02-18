<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'CATEGORY';

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'category_name',
        'creator_id'
    ];

    /**
     * Karena SQL Server menggunakan GETDATE() secara default, 
     * Laravel akan tetap mencoba mengupdate kolom ini secara otomatis.
     */
    public $timestamps = true;
    
    // Jika format tanggal di SQL Server berbeda, Anda bisa menyesuaikan ini
    protected $dateFormat = 'Y-m-d H:i:s';
}
