@if(!session('dc_warehouse_id'))
    <button type="button" class="btn-add-sm disabled">
        Check Availability
    </button>

@elseif(($product->available_stock ?? 0) > 0)
    <button type="submit" class="btn-add-sm">
        ADD
    </button>

@else
    <button type="button" class="btn-add-sm disabled">
        Out of Stock
    </button>
@endif
