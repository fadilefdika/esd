<?php
namespace App\Imports;

use App\Models\Entity;
use App\Models\CodeEsd;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class DepartmentImport implements ToModel, WithHeadingRow
{
    /**
     * Baris 1 adalah header gabungan, jadi baris 2 adalah Heading Row asli.
     *
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // 1. Validasi NPK: Bersihkan spasi & pastikan tidak kosong (Mencegah error baris hantu)
        //
        $npk = isset($row['npk']) ? trim((string)$row['npk']) : null;
        if (empty($npk)) {
            return null;
        }

        $currentUserId = auth()->id() ?? 1;

        // 2. Lookup Master Data & API
        $apiResult = $this->fetchEmployeeData($npk);
        $codeEsdId = $this->getAndIncrementCodeEsd($row['kode'] ?? null);

        // 3. Simpan/Update ke tabel ENTITY
        // Menggunakan nama kolom baru: 'package', 'total_set_esd', 'code_esd'
        $entity = Entity::updateOrCreate(
            ['npk' => $npk],
            [
                'employee_name' => $row['nama'] ?? 'UNKNOWN', // Nama apa adanya (tanpa strtoupper)
                'dept_id'       => $apiResult['dept_id'] ?? null,
                'dept_name'     => strtoupper($row['departemen'] ?? '-'), // Tetap UPPERCASE
                'no_loker'      => $apiResult['no_loker'] ?? '-',
                'line_id'       => $apiResult['line_id'] ?? null,
                'line_name'     => $apiResult['line_name'] ?? '-',
                'status'        => 'AKTIF',
                'creator_id'    => $currentUserId,
                'package'       => $row['paket'] ?? '-', 
                'total_set_esd' => intval($row['set_seragam_esd'] ?? 0),
                'code_esd'      => $codeEsdId, 
                'category'      => $row['category'] ?? '-',
            ]
        );

        // 4. Proses Alokasi Item (Baju, Celana, Sepatu, Topi, Jilbab)
        $this->processEntityItems($entity, $row, $currentUserId);

        return null;
    }

    /**
     * Mencari ID di tabel CODE_ESD dan menambah counter jumlah_karyawan.
     *
     */
    private function getAndIncrementCodeEsd($kodeName)
    {
        if (empty($kodeName)) return null;

        $kodeClean = trim(strtoupper($kodeName));
        
        // Cari data kode, jika tidak ada maka buat baru
        $code = CodeEsd::firstOrCreate(['name' => $kodeClean]);
        
        // Increment jumlah karyawan sesuai permintaan
        $code->increment('jumlah_karyawan');

        return $code->id;
    }

    /**
     * Mengambil data tambahan dari API internal.
     */
    private function fetchEmployeeData($npk)
    {
        try {
            $response = Http::withToken(env('API_KEY'))
                ->get(env('API_BASE_URL') . '/api/v1/users', ['search' => $npk]);
            
            return collect($response->json()['data'] ?? [])->firstWhere('npk', $npk);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Menyinkronkan item ke pivot table ENTITY_DETAIL_ITEM.
     *
     */
    private function processEntityItems($entity, $row, $currentUserId)
    {
        $itemsToSync = [];

        // Kategori 1: Seragam (Baju ID 1 & Celana ID 2)
        if (($row['jumlah'] ?? 0) == 1) {
            $size = $row['size'] ?? '-';
            $itemsToSync[1] = ['size' => $size, 'status' => 'Diterima', 'creator_id' => $currentUserId];
            $itemsToSync[2] = ['size' => $size, 'status' => 'Diterima', 'creator_id' => $currentUserId];
        }

        // Kategori 2: Sepatu (ID 5) - Kolom 'Jumlah' kedua otomatis jadi 'jumlah_2'
        if (($row['jumlah_2'] ?? 0) == 1) {
            $itemsToSync[5] = ['size' => $row['size_2'] ?? '-', 'status' => 'Diterima', 'creator_id' => $currentUserId];
        }

        // Kategori 3: Topi (ID 3)
        if (($row['topi_esd'] ?? 0) == 1) {
            $itemsToSync[3] = ['size' => '-', 'status' => 'Diterima', 'creator_id' => $currentUserId];
        }

        // Kategori 4: Jilbab (ID 6)
        if (($row['jilbab_esd'] ?? 0) == 1) {
            $itemsToSync[6] = ['size' => '-', 'status' => 'Diterima', 'creator_id' => $currentUserId];
        }

        // Gunakan syncWithoutDetaching agar data tidak duplikat di SQL Server
        if (!empty($itemsToSync)) {
            $entity->items()->syncWithoutDetaching($itemsToSync);
        }
    }
}