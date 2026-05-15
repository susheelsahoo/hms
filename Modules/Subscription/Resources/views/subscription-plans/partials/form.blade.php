<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the highlighted fields:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <!-- Basic Information -->
            <div class="col-12">
                <h6 class="mb-3 text-secondary">Basic Information</h6>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="name">Plan Name <span class="text-danger">*</span></label>
                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $plan->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                <input id="slug" type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $plan->slug ?? '') }}" placeholder="lowercase-with-dashes" required>
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="form-text text-muted">Used in URLs and API endpoints</small>
            </div>

            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $plan->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Pricing -->
            <div class="col-12 mt-4">
                <h6 class="mb-3 text-secondary">Pricing</h6>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="price_monthly">Monthly Price <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input id="price_monthly" type="number" name="price_monthly" step="0.01" min="0" class="form-control @error('price_monthly') is-invalid @enderror" value="{{ old('price_monthly', $plan->price_monthly ?? '0.00') }}" required>
                    @error('price_monthly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="price_yearly">Yearly Price <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input id="price_yearly" type="number" name="price_yearly" step="0.01" min="0" class="form-control @error('price_yearly') is-invalid @enderror" value="{{ old('price_yearly', $plan->price_yearly ?? '0.00') }}" required>
                    @error('price_yearly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <small class="form-text text-muted">Yearly savings: <strong id="savings">$0.00</strong></small>
            </div>

            <!-- Resource Limits -->
            <div class="col-12 mt-4">
                <h6 class="mb-3 text-secondary">Resource Limits <span class="text-muted">(use -1 for unlimited)</span></h6>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="hotel_limit">Hotel Limit <span class="text-danger">*</span></label>
                <input id="hotel_limit" type="number" name="hotel_limit" min="-1" class="form-control @error('hotel_limit') is-invalid @enderror" value="{{ old('hotel_limit', $plan->hotel_limit ?? '1') }}" required>
                @error('hotel_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="staff_limit">Staff Limit <span class="text-danger">*</span></label>
                <input id="staff_limit" type="number" name="staff_limit" min="-1" class="form-control @error('staff_limit') is-invalid @enderror" value="{{ old('staff_limit', $plan->staff_limit ?? '5') }}" required>
                @error('staff_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="room_limit">Room Limit <span class="text-danger">*</span></label>
                <input id="room_limit" type="number" name="room_limit" min="-1" class="form-control @error('room_limit') is-invalid @enderror" value="{{ old('room_limit', $plan->room_limit ?? '50') }}" required>
                @error('room_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="booking_limit">Booking Limit per Month <span class="text-danger">*</span></label>
                <input id="booking_limit" type="number" name="booking_limit" min="-1" class="form-control @error('booking_limit') is-invalid @enderror" value="{{ old('booking_limit', $plan->booking_limit ?? '100') }}" required>
                @error('booking_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="storage_limit">Storage Limit (MB) <span class="text-danger">*</span></label>
                <input id="storage_limit" type="number" name="storage_limit" min="-1" class="form-control @error('storage_limit') is-invalid @enderror" value="{{ old('storage_limit', $plan->storage_limit ?? '1000') }}" required>
                @error('storage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Trial Settings -->
            <div class="col-12 mt-4">
                <h6 class="mb-3 text-secondary">Trial Settings</h6>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input id="is_trial" type="checkbox" name="is_trial" class="form-check-input" value="1" @checked(old('is_trial', $plan->is_trial ?? false))>
                    <label class="form-check-label" for="is_trial">
                        Is Trial Plan
                    </label>
                </div>
                <small class="form-text text-muted">Enable if this is a trial/free plan</small>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="trial_days">Trial Days</label>
                <input id="trial_days" type="number" name="trial_days" min="1" class="form-control @error('trial_days') is-invalid @enderror" value="{{ old('trial_days', $plan->trial_days ?? '14') }}" disabled id="trial_days_input">
                @error('trial_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Status -->
            <div class="col-12 mt-4">
                <h6 class="mb-3 text-secondary">Status</h6>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input id="is_active" type="checkbox" name="is_active" class="form-check-input" value="1" @checked(old('is_active', $plan->is_active ?? true))>
                    <label class="form-check-label" for="is_active">
                        Active Plan
                    </label>
                </div>
                <small class="form-text text-muted">Inactive plans cannot be selected by organizations</small>
            </div>
        </div>

        <!-- Help Text -->
        <div class="alert alert-info mt-4 mb-0">
            <small>
                <strong>Resource Limits:</strong> Set to <code>-1</code> for unlimited resources. Regular numbers represent maximum allowed.
            </small>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('super-admin.subscription-plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            {{ isset($plan->id) ? 'Update Plan' : 'Create Plan' }}
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const priceMonthly = document.getElementById('price_monthly');
        const priceYearly = document.getElementById('price_yearly');
        const savingsDisplay = document.getElementById('savings');
        const isTrialCheckbox = document.getElementById('is_trial');
        const trialDaysInput = document.getElementById('trial_days_input');

        const updateSavings = () => {
            const monthly = parseFloat(priceMonthly.value) || 0;
            const yearly = parseFloat(priceYearly.value) || 0;
            const monthlyTotal = monthly * 12;
            const savings = Math.max(0, monthlyTotal - yearly);
            savingsDisplay.textContent = '$' + savings.toFixed(2);
        };

        const toggleTrialDays = () => {
            trialDaysInput.disabled = !isTrialCheckbox.checked;
        };

        priceMonthly.addEventListener('change', updateSavings);
        priceMonthly.addEventListener('input', updateSavings);
        priceYearly.addEventListener('change', updateSavings);
        priceYearly.addEventListener('input', updateSavings);
        isTrialCheckbox.addEventListener('change', toggleTrialDays);

        updateSavings();
        toggleTrialDays();
    });
</script>
