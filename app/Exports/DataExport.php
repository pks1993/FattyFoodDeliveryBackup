<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromCollection, WithHeadings
{
    public function headings(): array {
        return [
            "customer_id","name","phone","image",'created_at','updated_at'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getCustomers());
    }
}
