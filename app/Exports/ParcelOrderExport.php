<?php

namespace App\Exports;

use App\Models\Backup\Backup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ParcelOrderExport implements FromCollection ,WithHeadings
{
    public function headings(): array {
        return [
            "order_id", "merch_order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "rider_delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude", "current_address", "building_system", "address_type", "customer_address_phone", "rider_parcel_block_note", "rider_parcel_address", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "from_pickup_note", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "to_drop_note", "parcel_type_id", "from_parcel_city_id", "to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note", "parcel_extra_cover_id", "payment_method_id", "trade_status", "payment_total_amount", "notify_time", "trans_end_time", "order_status_id", "restaurant_remark", "order_time", "rider_restaurant_distance", "state_id", "city_id", "is_review_status", "is_force_assign", "is_admin_force_order", "created_at", "updated_at"
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect(Backup::getAllParcelOrders());
    }
}
