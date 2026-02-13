  <div class="modal-body">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

                        <label class="fw-semibold d-block mb-2">Saved Addresses *</label>

                        <div class="d-flex gap-2 mb-3" id="addressTabs">
                            <button type="button" class="btn btn-outline-success address-tab" data-type="1">🏠 Home</button>
                            <button type="button" class="btn btn-outline-primary address-tab" data-type="2">🏢 Work</button>
                            <button type="button" class="btn btn-outline-warning address-tab" data-type="3">📍 Other</button>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="floating-group">
                                    <input type="text" name="first_name" class="floating-input"
                                        placeholder=" "
                                        value="{{ old('first_name', $address->first_name ?? '') }}" required>
                                    <span class="floating-placeholder">First Name *</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="floating-group">
                                    <input type="text" name="last_name" class="floating-input"
                                        placeholder=" "
                                        value="{{ old('last_name', $address->last_name ?? '') }}" required>
                                    <span class="floating-placeholder">Last Name *</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-group">
                                <input type="text" name="flat_house" class="floating-input"
                                    placeholder=" "
                                    value="{{ old('flat_house', $address->flat_house ?? '') }}" required>
                                <span class="floating-placeholder">Flat / House no / Building *</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-group">
                                <input type="text" name="floor" class="floating-input"
                                    placeholder=" "
                                    value="{{ old('floor', $address->floor ?? '') }}">
                                <span class="floating-placeholder">Floor (optional)</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-group">
                                <input type="text" name="area" class="floating-input"
                                    placeholder=" "
                                    value="{{ old('area', $address->area ?? '') }}" required>
                                <span class="floating-placeholder">Area / Sector / Locality *</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-group">
                                <input type="text" name="landmark" class="floating-input"
                                    placeholder=" "
                                    value="{{ old('landmark', $address->landmark ?? '') }}">
                                <span class="floating-placeholder">Nearby Landmark</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="floating-group">
                                    <input type="text" name="city" class="floating-input"
                                        placeholder=" "
                                        value="{{ old('city', $address->city ?? '') }}" required>
                                    <span class="floating-placeholder">City *</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="floating-group">
                                    <input type="text"
                                        name="postcode"
                                        id="pincode"
                                        class="floating-input"
                                        maxlength="6"
                                        value=" "
                                        oninput="pincodeManuallyChanged = true">
                                    <span class="floating-placeholder">Pincode *</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-group">
                                <input type="text" name="phone" class="floating-input"
                                    placeholder=" "
                                    maxlength="10"
                                    value="{{ old('phone', $address->phone ?? '') }}" required>
                                <span class="floating-placeholder">Mobile *</span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation(this)">
                            📍 Use Current Location
                        </button>

                        <div class="modal-footer">
                            <button type="button"
                                class="btn btn-success w-100"
                                onclick="saveAddress()">
                                Save Address
                            </button>
                        </div>


                    </div>
                </div>
            </div>