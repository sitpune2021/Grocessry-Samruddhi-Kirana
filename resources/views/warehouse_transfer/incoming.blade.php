@include('layouts.header')
<style>
    .flexs{
        display: flex;
        /* margin-top:20px; */
    }
</style>
<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <!-- Form card -->
                        <div class="col-12 col-md-10 col-lg-12">
                            <div class="card mb-4">
                                <h4 class="card-header text-center">
                                    Incoming Request
                                </h4>

                                <div class="card-body">

                                    @foreach($requests as $req)
                                        <div class="mb-4 p-3 border rounded">

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="mb-0">
                                                    Request No: <strong>{{ $req->request_no }}</strong>
                                                </h5>
                                                <span class="badge bg-warning text-dark">
                                                    {{ ucfirst($req->status ?? 'pending') }}
                                                </span>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Product Name</th>
                                                            <th>Requested Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($req->items as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $item->product->name }}</td>
                                                                <td>{{ $item->requested_qty }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="flexs gap-2 mt-3 ">
                                            <div class="">
                                                <form method="POST" action="{{ url('warehouse-transfer-request/approve/'.$req->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        Approve
                                                    </button>
                                                </form>
                                            </div>

                                                <div class="">
                                                    <form method="POST" class="" action="{{ url('warehouse-transfer-request/reject/'.$req->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        Reject
                                                    </button>
                                                </form>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
</body>