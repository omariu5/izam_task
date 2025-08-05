<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | This value determines the quantity at which a low stock alert will be
    | triggered. When stock falls below this threshold, the LowStockDetected
    | event will be fired.
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),
];
