<table border="1" width="100%" cellpadding="8">
    <thead>
        <tr>
            <th>#</th>
            <th>Retailer</th>
            <th>Category</th>
            <th>Product</th>
            <th>Base Price (₹)</th>
            <th>Discount %</th>
            <th>Discount Amt (₹)</th>
            <th>Effective Price (₹)</th>
            <th>Effective From</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($pricings as $index => $p)
            <tr>
                <td>{{ $pricings->firstItem() + $index }}</td>
                <td>{{ $p->retailer->name ?? '-' }}</td>
                <td>{{ $p->category->name ?? '-' }}</td>
                <td>{{ $p->product->name ?? '-' }}</td>
                <td>{{ number_format($p->base_price, 2) }}</td>
                <td>{{ $p->discount_percent ?? 0 }}%</td>
                <td>{{ number_format($p->discount_amount, 2) }}</td>
                <td><strong>{{ number_format($p->effective_price, 2) }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($p->effective_from)->format('d-m-Y') }}</td>

                <td>
                    <span style="color: {{ $p->is_active ? 'green' : 'red' }}">
                        {{ $p->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('retailer-pricing.edit', $p->id) }}">Edit</a>

                    <form action="{{ route('retailer-pricing.delete', $p->id) }}"
                          method="POST"
                          style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete pricing?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" align="center">No pricing found</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $pricings->links() }}
