<div class="d-flex align-items-center gap-2">

    {{-- View --}}
    @if($viewUrl)
    <a href="{{ $viewUrl }}"
        class="btn btn-sm btn-outline-primary action-btn"
        title="View">
        <i class="ri-eye-fill"></i>
    </a>
    @endif

    {{-- Edit --}}
    @if($editUrl)
    <a href="{{ $editUrl }}"
        class="btn btn-sm btn-outline-secondary action-btn"
        title="Edit">
        <i class="ri-pencil-fill"></i>
    </a>
    @endif

    {{-- Delete --}}
    @if($deleteUrl)
    <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="btn btn-sm btn-outline-danger action-btn"
            title="Delete"
            onclick="return confirm('Are you sure?')">
            <i class="ri-delete-bin-fill"></i>
        </button>
    </form>
    @endif

</div>