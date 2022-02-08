<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public static $data;
    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return ["Order #", "Customer Name", "Customer Email", "Customer Contact", "Charity Name", "Total Price", "Payment Status", "Order Status", "Date"];
    }
}
