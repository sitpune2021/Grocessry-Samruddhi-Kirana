<div class="row mx-3 my-0 justify-content-between datatable-toolbar">
    <div class="dt-layout-start col-12 col-md-auto mt-0">
        <!-- Optional: Show entries -->
    </div>

    <div class="dt-layout-end col-12 col-md-auto ms-md-auto mt-2 mt-md-0">
        <div class="dt-search">
            <label for="dt-search-1" class="datatable-search-label">
                Search:
            </label>

            <input
                type="search"
                class="form-control datatable-search-input"
                id="dt-search-1"
                placeholder="Search..."
                aria-controls="DataTables_Table_1"
            >
        </div>
    </div>
</div>

<style>
/* =========================================================
   DATATABLE SEARCH
========================================================= */

.datatable-toolbar {
    padding: 12px 0;
}

.dt-search {
    display: flex;
    align-items: center;
    gap: 10px;
}

.datatable-search-label {
    margin: 0;
    color: #66716b;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.datatable-search-input {
    width: 250px;
    min-height: 38px;

    margin: 0 !important;

    border: 1px solid #dfe7e2;
    border-radius: 8px;

    color: #39443e;
    font-size: 13px;

    box-shadow: none;
}

.datatable-search-input:focus {
    border-color: #198754;

    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.08);
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 767px) {

    .datatable-toolbar {
        margin-left: 0 !important;
        margin-right: 0 !important;

        padding: 10px 12px;
    }

    .dt-layout-end {
        width: 100%;
    }

    .dt-search {
        width: 100%;
        display: flex;
        align-items: center;
    }

    .datatable-search-label {
        flex: 0 0 auto;
    }

    .datatable-search-input {
        width: 100%;
        flex: 1 1 auto;
    }
}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .datatable-toolbar {
        padding: 8px 10px;
    }

    .dt-search {
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
    }

    .datatable-search-label {
        font-size: 12px;
    }

    .datatable-search-input {
        width: 100%;
        min-height: 40px;
    }
}
</style>
