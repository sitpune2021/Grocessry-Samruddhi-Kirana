// Data table search box
$(document).ready(function () {
    var table = $('#DataTables_Table_1').DataTable();

    // Search
    $('.datatable-search').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Length change
    $('.datatable-length').on('change', function () {
        table.page.len(this.value).draw();
    });
});