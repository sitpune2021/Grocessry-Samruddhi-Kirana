@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Delivery Agent</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('delivery-agents.create') }}"
                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Agent
                    </a>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="driverVehicleTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 25%;">Shop Name</th>
                            <th style="width: 25%;">Agent Name</th>
                            <th style="width: 25%;">Mobile</th>
                            <th style="width: 25%;">Email</th>
                            <th style="width: 25%;">Profile Photo</th>
                            <th style="width: 25%;">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($agents as $index => $agent)
                        <tr>
                            {{-- Sr No --}}
                            <td class="text-center">
                                {{  $agents->firstItem() + $index}}
                            </td>

                            {{-- Shop Name --}}
                            <td>
                                {{ $agent->shop->shop_name ?? '-' }}
                            </td>

                            {{-- Agent Name --}}
                            <td>
                                {{ $agent->user
                        ? $agent->user->first_name . ' ' . ($agent->user->last_name ?? '')
                        : '-' }}
                            </td>

                            {{-- Mobile --}}
                            <td>
                                {{ $agent->user->mobile ?? '-' }}
                            </td>

                            {{-- Email --}}
                            <td>
                                {{ $agent->user->email ?? '-' }}
                            </td>

                            {{-- Profile Photo --}}
                            <td style="text-align: center;">
                                @if (!empty($agent->user->profile_photo))
                                <img src="{{ asset('storage/profile_photos/' . $agent->user->profile_photo) }}" width="80" height="80" class="rounded border">
                                @else
                                -
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if ($agent->status)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <x-action-buttons
                                    :view-url="route('delivery-agents.show', $agent->id)"
                                    :edit-url="route('delivery-agents.edit', $agent->id)"
                                    :delete-url="route('delivery-agents.destroy', $agent->id)" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No delivery agents found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <!-- Pagination -->
            <div class="px-3 py-2">
                <x-pagination
                    :from="$agents->firstItem()"
                    :to="$agents->lastItem()"
                    :total="$agents->total()" />
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>


@endpush