@if(!session('dc_warehouse_id'))
<button type="button"
    class="btn btn-availability" disabled>
    Check Availability
</button>

@elseif(($product->available_stock ?? 0) > 0)
<button type="submit"
    class="btn btn-add-active">
    ADD
</button>

@else
<button type="button"
    class="btn btn-out-stock" disabled>
    Out of Stock
</button>

@endif




<style>
    /* Base button */


    /* 🟢 ADD (in stock) */
    .btn-add-active {
        background-color: #28a745;
        color: #ffffff;
        border-color: #28a745;

    }

    .btn-add-active:hover {
        background-color: #ffffff;
        border-color: #067420;
    }

    /* ⚪ CHECK AVAILABILITY */
    .btn-availability {
        background-color: transparent;
        color: #6c757d;
        border: 1px dashed #ced4da;
        cursor: not-allowed;
        border-radius: 10px;

    }

    /* 🔴 OUT OF STOCK (transparent) */
    .btn-out-stock {
        background-color: #ff001994;
        color: #ffffff;
        border: 1px solid #f1aeb5;
        cursor: not-allowed;
        border-radius: 10px;
        
        /* 
                 background-color: #11ff00c2;
        color: #ffffff;
        border: 1px solid #03fd5a;
        cursor: not-allowed;
                border-radius: 10px; */



    }
</style>