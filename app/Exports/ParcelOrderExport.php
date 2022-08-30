<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ParcelOrderExport implements FromCollection ,WithHeadings
{
    public function headings(): array {
        return [
            "No.","Date","RiderName", "customer_order_id", "customer_booking_id", "Income","rider_delivery_fee","profit",
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getAllParcelOrders());
    }
}
