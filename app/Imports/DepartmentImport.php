<?php
namespace App\Imports;

use App\Models\Entity;
use App\Models\CodeEsd;
use App\Models\Package;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DepartmentImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function headingRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');
        $npk = isset($row['npk']) ? trim((string)$row['npk']) : null;
        if (empty($npk)) return null;

        $currentUserId = auth()->id() ?? 1;

        // 1. Ambil Data API & ID Code ESD
        $apiResult = $this->fetchEmployeeData($npk);
        $codeEsdId = $this->getAndIncrementCodeEsd($row['kode'] ?? null);

        // 2. Persiapan Data untuk Tabel [ENTITY]
        $entityPayload = [
            'npk'           => $npk,
            'employee_name' => $row['nama'] ?? 'UNKNOWN',
            'dept_id'       => $apiResult['dept_id'] ?? null,
            'dept_name'     => strtoupper($row['departemen'] ?? '-'),
            'no_loker'      => $apiResult['no_loker'] ?? '-',
            'line_id'       => $apiResult['line_id'] ?? null,
            'line_name'     => $apiResult['line_name'] ?? '-',
            'status'        => 'AKTIF',
            'creator_id'    => $currentUserId,
            'package'       => $row['paket'] ?? '-', // Harus muncul A/B/C
            'total_set_esd' => intval($row['set_seragam_esd'] ?? 0),
            'code_esd'      => $codeEsdId,
            'category'      => $row['category'] ?? '-',
        ];

        // 3. Persiapan Data untuk Tabel [ENTITY_DETAIL_ITEM]
        $detailItemsPayload = [];
        $packageName = $row['paket'] ?? '-';
        $package = Package::where('package_name', $packageName)->with('items')->first();

        if ($package) {
            foreach ($package->items as $item) {
                /** * Logika Pemetaan Size:
                 * Item 5 (Sepatu) -> size_sepatu (Kolom H)
                 * Item 1 & 2 (Baju/Celana) -> size_seragam (Kolom F)
                 */
                $size = ($item->id == 5) ? ($row['size_sepatu'] ?? null) : ($row['size_seragam'] ?? '-');
                
                if (in_array($item->id, [3, 6])) $size = '-';

                $detailItemsPayload[] = [
                    'item_id'    => $item->id,
                    'item_name'  => $item->name, // Info tambahan untuk dd()
                    'size'       => $size,
                    'status'     => 'Diterima',
                    'creator_id' => $currentUserId,
                ];
            }
        }

        // --- GERBANG VALIDASI FINAL ---
        // Jika Anda melihat ini, berarti DATA BELUM MASUK ke SQL Server.
        // dd([
        //     'VALIDASI_ENTITY' => [
        //         'TABEL_TARGET' => 'esd.dbo.ENTITY',
        //         'DATA_SIAP_INSERT' => $entityPayload
        //     ],
        //     'VALIDASI_DETAIL_ITEM' => [
        //         'TABEL_TARGET' => 'esd.dbo.ENTITY_DETAIL_ITEM',
        //         'REFERENSI_PAKET' => $packageName,
        //         'JUMLAH_ITEM_DITEMUKAN' => count($detailItemsPayload),
        //         'DATA_SIAP_SYNC' => $detailItemsPayload
        //     ],
        //     'SUMBER_EXCEL_MENTAH' => $row // Memastikan key size_seragam & size_sepatu ada
        // ]);

        // KODE DI BAWAH HANYA JALAN JIKA dd() DI ATAS DIHAPUS/DIKOMENTARI
        $entity = Entity::updateOrCreate(['npk' => $npk], $entityPayload);
        $this->processEntityItems($entity, $row);

        return null;
    }

    /**
     * Menyinkronkan item ke pivot table ENTITY_DETAIL_ITEM berdasarkan master Paket.
     */
    private function processEntityItems($entity, $row)
    {
        // 1. Ambil jumlah set dari Excel (misal: 2)
        $totalSet = intval($row['set_seragam_esd'] ?? 1); 
        $packageName = $row['paket'] ?? '-';
        $package = Package::where('package_name', $packageName)->with('items')->first();

        if ($package) {
            $currentUserId = auth()->id() ?? 1;
            $detailItemsPayload = [];

            // 2. HAPUS data lama untuk entity ini agar tidak PK Violation saat Re-import
            \DB::table('ENTITY_DETAIL_ITEM')->where('entity_id', $entity->id)->delete();

            for ($set = 1; $set <= $totalSet; $set++) {
                foreach ($package->items as $item) {
                    // Ambil ukuran asal
                    $size = ($item->id == 5) ? ($row['size_sepatu'] ?? null) : ($row['size_seragam'] ?? '-');
                    
                    // Aturan Topi/Jilbab
                    if (in_array($item->id, [3, 6])) $size = '-';

                    $detailItemsPayload[] = [
                        'entity_id'  => $entity->id,
                        'item_id'    => (int)$item->id,
                        'set_no'     => (int)$set,
                        /**
                        * SOLUSI KRUSIAL: 
                        * Paksa nilai size menjadi STRING agar SQL Server tidak melakukan 
                        * konversi otomatis ke INT saat bertemu ukuran sepatu (misal: 265).
                        */
                        'size'       => (string)$size, 
                        'status'     => 'Diterima',
                        'notes'      => "Set ke-$set",
                        'created_at' => now(),
                        'updated_at' => now(),
                        'creator_id' => auth()->id() ?? 1
                    ];
                }
            }

            // 4. Insert massal menggunakan Query Builder (Lebih aman untuk Composite PK)
            if (!empty($detailItemsPayload)) {
                \DB::table('ENTITY_DETAIL_ITEM')->insert($detailItemsPayload);
            }
        }
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

   
}