@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            @include('layouts.sidebar')

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl container-p-y">
                        <div class="card">

                            <div class="card-header">
                                @if ($mode == 'add')
                                    <h4>Add Role</h4>
                                @endif

                                @if ($mode == 'edit')
                                    <h4>Edit Role</h4>
                                @endif

                                @if ($mode == 'show')
                                    <h4>Role</h4>
                                @endif

                            </div>

                            <div class="card-body">
                                <form
                                    action="{{ $mode == 'edit' ? route('roles.update', $role->id) : route('roles.store') }}"
                                    method="POST">
                                    @csrf
                                    @if ($mode == 'edit')
                                        @method('PUT') <!-- Use PUT method for editing -->
                                    @endif

                                    <div class="d-flex gap-3">
                                        <div class="mb-3 flex-fill">
                                            <label class="form-label">
                                                Role Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Enter role name"
                                                value="{{ old('name', $role->name ?? '') }}"
                                                @if ($mode == 'show') disabled @endif>

                                            @error('name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 flex-fill">
                                            <label class="form-label">
                                                Description <span class="text-danger"></span>
                                            </label>

                                            <input type="text" name="description" class="form-control"
                                                placeholder="Enter description"
                                                value="{{ old('description', $role->description ?? '') }}"
                                                @if ($mode == 'show') disabled @endif>

                                            @error('description')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <a href="{{ route('roles.index') }}" class="btn btn-success">Cancel</a>
                                        @if ($mode != 'show')
                                            <button
                                                class="btn btn-success">{{ $mode == 'edit' ? 'Update Role' : 'Save Role' }}</button>
                                        @endif
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
</body>
