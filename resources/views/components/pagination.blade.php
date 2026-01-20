<div class="row mx-3 justify-content-between">

    <!-- Info -->
    <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
        <div class="dt-info" aria-live="polite" role="status">
             {{ $from }}  {{ $to }} {{ $total }}
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
        <div class="dt-paging">
            <nav aria-label="pagination">
                <ul class="pagination">

                    <li class="page-item disabled">
                        <button class="page-link previous" type="button">
                            <i class="icon-base bx bx-chevron-left icon-sm"></i>
                        </button>
                    </li>

                    @for ($i = 1; $i <= ceil($total / 10); $i++)
                        <li class="page-item {{ $i == 1 ? 'active' : '' }}">
                        <button class="page-link" type="button">
                            {{ $i }}
                        </button>
                        </li>
                        @endfor

                        <li class="page-item">
                            <button class="page-link next" type="button">
                                <i class="icon-base bx bx-chevron-right icon-sm"></i>
                            </button>
                        </li>
                </ul>
            </nav>
        </div>
    </div>
</div>