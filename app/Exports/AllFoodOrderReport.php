<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AllFoodOrderReport implements FromCollection ,WithHeadings
{
    public function headings(): array {
        return [
            "No.","restaurant_id","Date","RiderName", "customer_order_id", "customer_booking_id", "TranstationAmount","rider_delivery_fee","Income(%)","profit",
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getAllFoodOrders());
    }

}
