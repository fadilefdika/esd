<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EsdImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Manufacturing'      => new DepartmentImport('MANUFACTURING'),
            'Maintenance & IT'   => new DepartmentImport('MAINTENANCE & IT'),
            'Marketing & PPIC'   => new DepartmentImport('MARKETING & PPIC'),
            'Quality'            => new DepartmentImport('QUALITY'),
            'MPL & Purchasing'   => new DepartmentImport('MPL & PURCHASING'),
            'Product Engineering' => new DepartmentImport('PRODUCT ENGINEERING'),
            'Process Engineering' => new DepartmentImport('PROCESS ENGINEERING'),
            // Khusus tab ini sesuai permintaan Anda
            'Finance & Accounting' => new DepartmentImport('FINANCE ACCOUNTING'),
            'Advanced Manufacturing Engineer' => new DepartmentImport('ADVANCED MANUFACTURING ENGINEERING'),
            'HRGA & EHS'         => new DepartmentImport('HRGA & EHS'),
        ];
    }
}