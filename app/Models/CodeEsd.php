<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CodeEsd extends Model
{
    use HasFactory;

    // Menghubungkan secara spesifik ke tabel CODE_ESD di SSMS
    protected $table = 'CODE_ESD';

    // Mendaftarkan kolom agar bisa diisi melalui proses Import
    protected $fillable = [
        'name',
        'jumlah_karyawan', // Kolom baru yang Anda tambahkan
        'creator_id',
    ];

    /**
     * Jika Anda tidak menggunakan kolom created_at/updated_at bawaan Laravel,
     * set properti ini menjadi false. Namun, di SQL tadi kita sudah menyiapkannya.
     */
    public $timestamps = true;//

    public function entities()
    {
        return $this->hasMany(Entity::class, 'code_esd');
    }
}
