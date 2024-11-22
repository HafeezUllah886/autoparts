<?php

namespace App\Exports;

use App\Models\products;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class productsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return products::select('name', 'partno', 'model', 'brand', 'madein', 'size', 'uom', 'pprice', 'price')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Part No', 'Model', 'Brand', 'Made In', 'Size', 'UOM', 'Purchase Price', 'Sale Price'];
    }
}
