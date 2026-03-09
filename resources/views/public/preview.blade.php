<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Loker - {{ $entity->employee_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-box { background-color: white; border: 2px solid #0d6efd; display: inline-block; padding: 5px 15px; font-weight: bold; }
        .info-table { width: 100%; background: white; border-collapse: collapse; }
        .info-table td { padding: 12px 15px; border: 1px solid #dee2e6; }
        .label-col { width: 30%; font-weight: bold; color: #333; }
        .value-col { width: 70%; }
        .highlight-orange { background-color: #ff9800 !important; color: white; font-weight: bold; }
        .btn-submit { background-color: #ff9800; color: white; border: none; padding: 15px; width: 100%; font-weight: bold; text-transform: uppercase; border-radius: 5px; transition: 0.3s; }
        .btn-submit:hover { background-color: #e68a00; color: white; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="d-flex align-items-center mb-3">
                <div class="header-box text-primary me-2">
                    <i class="bi bi-lock-fill"></i> INFORMASI
                </div>
                <h4 class="mb-0 fw-bold">ESD</h4>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <table class="info-table">
                    <tr>
                        <td class="label-col">Nomor</td>
                        <td class="value-col highlight-orange">{{ $entity->code }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">NPK:</td>
                        <td class="value-col">{{ $entity->npk }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Nama:</td>
                        <td class="value-col">{{ $entity->employee_name }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Posisi:</td>
                        <td class="value-col">{{ $entity->line_name ?? 'Staff' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Dept:</td>
                        <td class="value-col">{{ $entity->dept_name }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Keterangan:</td>
                        <td class="value-col">{{ $entity->information }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">SIZE</td>
                        <td class="value-col">
                            @php 
                                // Mengambil size dari salah satu item (misal Seragam)
                                $itemSize = $entity->items->first()->pivot->size ?? '-';
                            @endphp
                            {{ $itemSize }}
                        </td>
                    </tr>
                </table>
            </div>

            <a href="#" class="btn btn-submit shadow">
                Submit Laundry
            </a>

            <div class="text-center mt-4">
                <small class="text-muted">Astra Visteon Indonesia &copy; 2026</small>
            </div>

        </div>
    </div>
</div>

</body>
</html>