@extends('layouts.app')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card shadow-sm border-0 rounded-6 mx-4 my-4">


        <div class="card-header bg-white">

            <div class="row align-items-center">

                <div class="col-lg-6 col-md-6 col-12">
                    <h4 class="mb-2 mb-md-0">
                        <i class="bx bx-store"></i>
                        Retailer List
                    </h4>
                </div>

                <div class="col-lg-6 col-md-6 col-12 text-md-end">

                    <a href="{{ route('retailers.create') }}"
                        class="btn btn-success">

                        Add Retailer

                    </a>

                </div>

            </div>

        </div>

        <div class="card-body">

            {{-- SUCCESS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-1"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            {{-- ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-1"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            <div class="row mb-3">

                <div class="col-12">

                    <x-datatable-search />

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle text-nowrap">

                    <thead class="table-light">

                        <tr>

                            <th width="60">#</th>

                            <th>Shop Name</th>

                            <th>Retailer Name</th>

                            <th>Mobile</th>

                            <th>Email</th>

                            <th>Status</th>

                            <th width="220" class="text-center">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($retailers as $retailer)

                        <tr>

                            <td>
                                {{ $retailers->firstItem() + $loop->index }}
                            </td>

                            <td>

                                <strong>
                                    {{ $retailer->shop_name }}
                                </strong>

                            </td>

                            <td>

                                {{ $retailer->name }}

                            </td>

                            <td>

                                {{ $retailer->mobile }}

                            </td>

                            <td>

                                {{ $retailer->email ?: '-' }}

                            </td>

                            <td>

                                @if($retailer->is_active)

                                <span class="badge bg-success">

                                    Active

                                </span>

                                @else

                                <span class="badge bg-danger">

                                    Inactive

                                </span>

                                @endif

                            </td>
                          
                            <td class="text-center" style="white-space: nowrap;">

                                {{-- Edit --}}
                                <a href="{{ route('retailers.edit', $retailer->id) }}"
                                class="btn btn-sm btn-primary">
                                    <i class="bx bx-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('retailers.destroy', $retailer->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this retailer?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                </form>

                                {{-- Status --}}
                                <form action="{{ route('retailers.toggle.status', $retailer->id) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            class="btn btn-sm {{ $retailer->is_active ? 'btn-secondary' : 'btn-success' }}"
                                            title="{{ $retailer->is_active ? 'Deactivate' : 'Activate' }}">

                                        @if($retailer->is_active)
                                            <i class="bx bx-block"></i>
                                        @else
                                            <i class="bx bx-check-circle"></i>
                                        @endif

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="7" class="text-center py-5">

                                <img src="{{ asset('admin/assets/img/no-data.svg') }}"
                                    width="140"
                                    class="mb-3"
                                    onerror="this.style.display='none'">

                                <br>

                                <span class="text-muted">

                                    No Retailers Found

                                </span>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($retailers->hasPages())

            <div class="d-flex justify-content-end mt-4">

                {{ $retailers->links() }}

            </div>

            @endif

        </div>

    </div>

</div>

@endsection

@push('styles')

<style>
.table td,
.table th{
    vertical-align: middle;
}

.card{
    border-radius:12px;
}

.btn-sm{
    min-width:38px;
}

.badge{
    font-size:13px;
    padding:7px 12px;
}

@media(max-width:768px){

    .card-header .btn{
        width:100%;
        margin-top:10px;
    }

    .table{
        font-size:13px;
    }

    .btn-sm{
        min-width:35px;
        padding:5px 8px;
    }

    .pagination{
        justify-content:center;
    }
}
</style>

@endpush

@push('scripts')

@endpush