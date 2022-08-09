<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromCollection, WithHeadings
{
    public function headings(): array {
        return [
            "customer_id", "customer_name", "customer_phone", "customer_type_id", "is_restricted", "latitude", "longitude", "state_id", "city_id", "image", "device_id", "fcm_token", "os_type", "is_delete", "otp", "order_count", "order_amount", "created_at", "updated_at"
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getCustomers());
    }
}
