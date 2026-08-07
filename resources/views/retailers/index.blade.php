@extends('layouts.app')

@section('content')

<div class="card">

    <div class="card-header">

        <div class="row align-items-center">

            <div class="col-md-6">
                <h4 class="mb-0">
                    Retailer List
                </h4>
            </div>

            <div class="col-md-6 text-md-end mt-3 mt-md-0">

                <a href="{{ route('retailers.create') }}"
                    class="btn btn-success">

                    <i class="bx bx-plus"></i>
                    Add Retailer

                </a>

            </div>

        </div>

    </div>

    <div class="card-body">

        <x-datatable-search />

        <div class="table-responsive mt-4">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="5%">#</th>

                        <th>Shop</th>

                        <th>Name</th>

                        <th>Mobile</th>

                        <th>Email</th>

                        <th>Status</th>

                        <th width="220">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($retailers as $retailer)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $retailer->shop_name }}
                        </td>

                        <td>
                            {{ $retailer->name }}
                        </td>

                        <td>
                            {{ $retailer->mobile }}
                        </td>

                        <td>
                            {{ $retailer->email ?? '-' }}
                        </td>

                        <td>

                            @if($retailer->is_active)

                            <span class="badge bg-label-success">
                                Active
                            </span>

                            @else

                            <span class="badge bg-label-danger">
                                Inactive
                            </span>

                            @endif

                        </td>

                        <td>

                            <div class="d-flex flex-wrap gap-2">

                                <a href="{{ route('retailers.edit',$retailer->id) }}"
                                    class="btn btn-sm btn-warning">

                                    <i class="bx bx-edit"></i>

                                </a>

                                <form action="{{ route('retailers.destroy',$retailer->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this retailer?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="btn btn-sm btn-danger">

                                        <i class="bx bx-trash"></i>

                                    </button>

                                </form>

                                <form action="{{ route('retailers.toggle.status',$retailer->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        class="btn btn-sm {{ $retailer->is_active ? 'btn-secondary' : 'btn-success' }}">

                                        @if($retailer->is_active)

                                        <i class="bx bx-block"></i>

                                        @else

                                        <i class="bx bx-check-circle"></i>

                                        @endif

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center">

                            No Retailer Found

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">

            {{ $retailers->links() }}

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

});

</script>

@endpush