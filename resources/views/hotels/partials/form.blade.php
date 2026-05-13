<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">Please fix the highlighted fields.</div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $hotel->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="slug">Slug</label>
                <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $hotel->slug) }}" placeholder="Auto-generated from name">
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $hotel->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $hotel->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $hotel->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="address">Address</label>
                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $hotel->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="city">City</label>
                <input id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $hotel->city) }}">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="state">State</label>
                <input id="state" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $hotel->state) }}">
                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="country">Country</label>
                <select id="country" name="country" class="form-select @error('country') is-invalid @enderror" required>
                    @foreach (config('countries') as $code => $country)
                        <option
                            value="{{ $code }}"
                            data-currency="{{ $country['currency'] }}"
                            data-timezone="{{ $country['timezone'] }}"
                            @selected(old('country', $hotel->country ?? $organization->country ?? 'US') === $code)
                        >
                            {{ $country['name'] }} ({{ $code }})
                        </option>
                    @endforeach
                </select>
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="zip_code">Zip Code</label>
                <input id="zip_code" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code', $hotel->zip_code) }}">
                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="timezone">Timezone</label>
                <input id="timezone" name="timezone" class="form-control @error('timezone') is-invalid @enderror" value="{{ old('timezone', $hotel->timezone ?? $organization->timezone ?? 'UTC') }}" required>
                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label" for="currency">Currency</label>
                <input id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $hotel->currency ?? $organization->currency ?? 'USD') }}" maxlength="3" required>
                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="checkin_time">Check-in Time</label>
                <input id="checkin_time" type="time" name="checkin_time" class="form-control @error('checkin_time') is-invalid @enderror" value="{{ old('checkin_time', $hotel->checkin_time) }}">
                @error('checkin_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="checkout_time">Check-out Time</label>
                <input id="checkout_time" type="time" name="checkout_time" class="form-control @error('checkout_time') is-invalid @enderror" value="{{ old('checkout_time', $hotel->checkout_time) }}">
                @error('checkout_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="star_rating">Star Rating</label>
                <select id="star_rating" name="star_rating" class="form-select @error('star_rating') is-invalid @enderror">
                    <option value="">Not rated</option>
                    @for ($rating = 1; $rating <= 5; $rating++)
                        <option value="{{ $rating }}" @selected((string) old('star_rating', $hotel->star_rating) === (string) $rating)>{{ $rating }}</option>
                    @endfor
                </select>
                @error('star_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance', 'suspended' => 'Suspended'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $hotel->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('super-admin.organizations.hotels.index', $organization) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Hotel</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const country = document.getElementById('country');
        const currency = document.getElementById('currency');
        const timezone = document.getElementById('timezone');

        if (!country || !currency || !timezone) {
            return;
        }

        country.addEventListener('change', () => {
            currency.value = country.options[country.selectedIndex]?.dataset.currency || currency.value;
            timezone.value = country.options[country.selectedIndex]?.dataset.timezone || timezone.value;
        });
    });
</script>
