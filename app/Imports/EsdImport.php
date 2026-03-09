<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EsdImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        /**
         * Karena semua data sekarang berada di sheet utama (sheet pertama),
         * kita hanya perlu mendaftarkan satu instance DepartmentImport.
         * * Kita menggunakan index '0' agar Laravel Excel otomatis mengambil sheet pertama,
         * atau Anda bisa menggunakan nama sheet utamanya jika ada (misal: 'Sheet1').
         * * Kita tidak lagi mengirimkan parameter departemen (seperti 'MANUFACTURING') 
         * ke constructor karena data departemen akan diambil langsung dari kolom Excel.
         */
        return [
            0 => new DepartmentImport(), 
        ];
    }
}