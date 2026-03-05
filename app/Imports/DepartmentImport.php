<?php
namespace App\Imports;

use App\Models\Entity;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class DepartmentImport implements ToModel, WithHeadingRow
{
    protected $deptName;

    public function __construct($deptName)
    {
        // Paksa nama departemen dari 'EsdImport' menjadi UPPERCASE
        $this->deptName = strtoupper($deptName);
    }

    public function model(array $row)
    {
        if (!isset($row['npk']) || empty(trim((string)$row['npk']))) {
            return null; // Laravel akan melewati baris ini dan lanjut ke baris berikutnya
        }
        
        $currentUserId = auth()->id() ?? 1;

        // 1. Ambil data dari API
        $apiUrl = env('API_BASE_URL', 'http://localhost:1411');
        $apiToken = env('API_KEY');

        $response = Http::withToken($apiToken)
            ->get($apiUrl . '/api/v1/users', ['search' => $row['npk']]);

        // FIX: Cari data yang NPK-nya BENAR-BENAR sama dengan Excel (bukan sekadar mirip)
        $apiResult = collect($response->json()['data'] ?? [])
            ->firstWhere('npk', (string)$row['npk']);

        // dd([
        //     'INFO' => 'Sedang mengetes tab: ' . $this->deptName,
        //     'DATA_DARI_EXCEL' => $row, // Lihat semua key asli di sini
        //     'SIMULASI_ITEM_SPLIT' => [
        //         // Gunakan seragam_esd sesuai header Excel
        //         'has_uniform' => (($row['seragam_esd'] ?? 0) == 1 || Str::contains(strtolower($row['seragam_esd'] ?? ''), ['baju', 'celana'])),
        //         'size_terdeteksi' => $row['size'] ?? '-',
        //         'has_sepatu' => (($row['sepatu_esd'] ?? 0) == 1),
        //         'size_sepatu' => $row['size_2'] ?? '-', // Kolom 'Size' kedua otomatis jadi size_2
        //         'has_jilbab' => (($row['jilbab_esd'] ?? 0) == 1),
        //         'has_topi' => (($row['topi_esd'] ?? 0) == 1),
        //     ]
        // ]);

        // 2. Simpan ke tabel ENTITY
        $entity = Entity::updateOrCreate(
            ['npk' => $row['npk']], // Cocokkan berdasarkan NPK dari Excel
            [
                'employee_name' => strtoupper($row['nama']), // Paksa Nama UPPERCASE
                'dept_id'       => $apiResult['dept_id'] ?? null,
                'dept_name'     => $this->deptName, // Menggunakan parameter constructor (UPPERCASE)
                'no_loker'      => $apiResult['no_loker'] ?? '-',
                'line_id'       => $apiResult['line_id'] ?? null,
                'line_name'     => $apiResult['line_name'] ?? '-',
                'status'        => 'AKTIF',
                'creator_id'    => $currentUserId,
                'information'   => $row['paket'] ?? '-', // Diambil dari kolom 'Paket' Excel
                'category'      => $row['category'] ?? '-',
            ]
        );

        // 3. Simpan Detail Item (Pemisahan Baju, Celana, dll)
        $this->processEntityItems($entity, $row, $currentUserId);

        return null;
    }

    private function processEntityItems($entity, $row, $currentUserId)
    {
        // 1. Baju (ID 1) & Celana (ID 2)
        if (($row['seragam_esd'] ?? 0) == 1) {
            $size = $row['size'] ?? '-';
            
            // Ganti attach() menjadi syncWithoutDetaching()
            $entity->items()->syncWithoutDetaching([
                1 => ['size' => $size, 'status' => 'Diterima', 'creator_id' => $currentUserId],
                2 => ['size' => $size, 'status' => 'Diterima', 'creator_id' => $currentUserId]
            ]);
        }

        // 2. Topi (ID 3) - Ini yang menyebabkan error (1, 3) tadi
        if (($row['topi_esd'] ?? 0) == 1) {
            $entity->items()->syncWithoutDetaching([
                3 => ['size' => '-', 'status' => 'Diterima', 'creator_id' => $currentUserId]
            ]);
        }

        // 3. Sepatu (ID 5)
        if (($row['sepatu_esd'] ?? 0) == 1) {
            $entity->items()->syncWithoutDetaching([
                5 => [
                    'size' => $row['size_2'] ?? '-', 
                    'status' => 'Diterima', 
                    'creator_id' => $currentUserId
                ]
            ]);
        }

        // 4. Jilbab (ID 6 - Hasil Insert SQL di atas)
        if (($row['jilbab_esd'] ?? 0) == 1) {
            $entity->items()->syncWithoutDetaching(6, ['size' => '-', 'status' => 'Diterima', 'creator_id' => $currentUserId]);
        }
    }
}
