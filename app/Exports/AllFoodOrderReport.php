<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;



class AllFoodOrderReport implements FromCollection ,WithHeadings
{
    use Exportable;

    protected $from_date;
    protected $to_date;

    function __construct($from_date,$to_date) {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function headings(): array {
        return [
            "order_id","restaurant_id","Date","RiderName", "customer_order_id", "customer_booking_id", "TranstationAmount","rider_delivery_fee","Income(%)","profit",
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getAllFoodOrders($this->from_date,$this->to_date));
    }

}
