<form method="POST"
      action="{{ isset($shop)
            ? route('grocery-shops.update', $shop->id)
            : route('grocery-shops.store') }}">

    @csrf

    @if(isset($shop))
        @method('PUT')
    @endif

    <input type="text"
           name="shop_name"
           placeholder="Shop Name"
           value="{{ old('shop_name', $shop->shop_name ?? '') }}"
           required>

    <input type="text"
           name="owner_name"
           placeholder="Owner Name"
           value="{{ old('owner_name', $shop->owner_name ?? '') }}">

    <input type="text"
           name="mobile_no"
           placeholder="Mobile No"
           value="{{ old('mobile_no', $shop->mobile_no ?? '') }}">

    <textarea name="address"
              placeholder="Address">{{ old('address', $shop->address ?? '') }}</textarea>

    <button type="submit">
        {{ isset($shop) ? 'Update Shop' : 'Save Shop' }}
    </button>
</form>
