<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">Please fix the highlighted fields.</div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $organization->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">

                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $organization->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $organization->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $organization->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="address">Address</label>
                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $organization->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="city">City</label>
                <input id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $organization->city) }}">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="state">State</label>
                <input id="state" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $organization->state) }}">
                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="country">Country</label>
                <select id="country" name="country" class="form-select @error('country') is-invalid @enderror" required>
                    @foreach (config('countries') as $code => $country)
                        <option
                            value="{{ $code }}"
                            data-currency="{{ $country['currency'] }}"
                            @selected(old('country', $organization->country ?? 'US') === $code)
                        >
                            {{ $country['name'] }} ({{ $code }})
                        </option>
                    @endforeach
                </select>
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="zip_code">Zip Code</label>
                <input id="zip_code" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code', $organization->zip_code) }}">
                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="currency_display">Currency</label>
                <input id="currency_display" class="form-control" value="{{ config('countries.'.old('country', $organization->country ?? 'US').'.currency', $organization->currency ?? 'USD') }}" readonly>
                <div class="form-text">Auto-selected from country.</div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('super-admin.organizations.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Organization</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const country = document.getElementById('country');
        const currency = document.getElementById('currency_display');

        if (!country || !currency) {
            return;
        }

        const syncCurrency = () => {
            currency.value = country.options[country.selectedIndex]?.dataset.currency || 'USD';
        };

        country.addEventListener('change', syncCurrency);
        syncCurrency();
    });
</script>
