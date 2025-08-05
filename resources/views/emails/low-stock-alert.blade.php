@component('mail::message')
# Low Stock Alert

The following item has fallen below the minimum threshold:

**Item:** {{ $data['item'] }}  
**SKU:** {{ $data['sku'] }}  
**Warehouse:** {{ $data['warehouse'] }}  
**Current Quantity:** {{ $data['current_quantity'] }}  
**Threshold:** {{ $data['threshold'] }}

Please restock this item soon to maintain inventory levels.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
