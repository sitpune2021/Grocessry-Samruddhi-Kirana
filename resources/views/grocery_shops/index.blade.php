<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>#</th>
            <th>Shop Name</th>
            <th>Owner</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($shops as $shopItem)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $shopItem->shop_name }}</td>
                <td>{{ $shopItem->owner_name }}</td>
                <td>{{ $shopItem->mobile_no }}</td>
                <td>{{ $shopItem->address }}</td>
                <td>
                    <a href="{{ route('grocery-shops.edit', $shopItem->id) }}">
                        Edit
                    </a>

                    <form action="{{ route('grocery-shops.destroy', $shopItem->id) }}"
                          method="POST"
                          style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete this shop?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No shops found</td>
            </tr>
        @endforelse
    </tbody>
</table>
