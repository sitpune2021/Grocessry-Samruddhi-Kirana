    {{-- View --}}
    @if($viewUrl)
    <a href="{{ $viewUrl }}"
        class="btn btn-icon btn-lg text-primary"
        title="View">
        <i class="bx bx-show bx-md"></i>
    </a>
    @endif

    {{-- Edit --}}
    @if($editUrl)
    <a href="{{ $editUrl }}"
        class="btn btn-icon btn-lg text-secondary"
        title="Edit">
        <i class="bx bx-edit bx-md"></i>
    </a>
    @endif

    {{-- Delete --}}
    @if($deleteUrl)
    <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="btn btn-icon btn-lg text-danger"
            title="Delete"
            onclick="return confirm('Are you sure?')">
            <i class="bx bx-trash bx-md"></i>
        </button>
    </form>
    @endif