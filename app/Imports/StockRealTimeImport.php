<?php

namespace App\Imports;

use App\StockRealTime;
use Maatwebsite\Excel\Concerns\ToModel;

class StockRealTimeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new StockRealTime([
            // 'id'     => $row[0],
            'product_running_id'    => $row[1],
            'amount' => $row[2],
            'transfer_in_out_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
    }
}
