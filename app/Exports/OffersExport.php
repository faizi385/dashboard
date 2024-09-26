<?php

namespace App\Exports;

use App\Models\Offer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OffersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Offer::all(['id', 'retailer_id', 'lp_id', 'product_name', 'provincial_sku', 'gtin', 'province', 'data_fee', 'unit_cost', 'category', 'brand', 'case_quantity', 'offer_start', 'offer_end', 'product_size', 'thc_range', 'cbd_range', 'comment', 'product_link', 'offer_date', 'created_at', 'updated_at']);
    }

    /**
     * Define the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Retailer ID',
            'LP ID',
            'Product Name',
            'Provincial SKU',
            'GTIN',
            'Province',
            'Data Fee',
            'Unit Cost',
            'Category',
            'Brand',
            'Case Quantity',
            'Offer Start',
            'Offer End',
            'Product Size',
            'THC Range',
            'CBD Range',
            'Comment',
            'Product Link',
            'Offer Date',
            'Created At',
            'Updated At',
        ];
    }
}
