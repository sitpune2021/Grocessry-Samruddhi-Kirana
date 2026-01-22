@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">

            {{-- 🔐 Permissions --}}
            @php
                $canView   = hasPermission('supplier_challan.view');
                $canCreate = hasPermission('supplier_challan.create');
                $canEdit   = hasPermission('supplier_challan.edit');
                $canDelete = hasPermission('supplier_challan.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Supplier Challans</h5>
                </div>

                <div class="col-md-auto ms-auto">
                    @if($canCreate)
                        <a href="{{ route('supplier_challan.create') }}"
                           class="btn btn-success btn-sm d-flex align-items-center gap-1">
                            <i class="bx bx-plus"></i> Create Challan
                        </a>
                    @endif
                </div>
            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-3">
                <table id="challanTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:20px;">Sr No</th>
                            <th>Challan No</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Date</th>
                            {{-- <th>Status</th> --}}

                            @if($canView || $canEdit || $canDelete)
                                <th class="text-center" style="width:180px;">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($challans as $index => $challan)
                            <tr>
                                <td class="text-center fw-semibold">
                                    {{ $challans->firstItem() + $index }}
                                </td>

                                <td>{{ $challan->challan_no }}</td>

                                <td>
                                    {{ $challan->supplier->supplier_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $challan->warehouse->name ?? '-' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($challan->challan_date)->format('d-m-Y') }}
                                </td>

                                {{-- <td>
                                    @if($challan->status === 'received')
                                        <span class="badge bg-success">Received</span>
                                    @elseif($challan->status === 'partial')
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td> --}}

                                {{-- 🔘 ACTION BUTTONS --}}
                                @if($canView || $canEdit || $canDelete)
                                <td class="text-center" style="white-space:nowrap;">

                                    @if($canView)
                                        <a href="{{ route('supplier_challan.show', $challan->id) }}"
                                           class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    @endif

                                    @if($canEdit)
                                        <a href="{{ route('supplier_challan.edit', $challan->id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                    @endif

                                    @if($canDelete)
                                        <form action="{{ route('supplier_challan.destroy', $challan->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this challan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No supplier challans found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $challans->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection
