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

            <div class="col-md-3">
                <label class="form-label" for="subscription_plan_id">Subscription Plan <span class="text-danger">*</span></label>
                <select id="subscription_plan_id" name="subscription_plan_id" class="form-select @error('subscription_plan_id') is-invalid @enderror" required>
                    <option value="">-- Select a Plan --</option>
                    @foreach ($subscriptionPlans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('subscription_plan_id', $organization->subscription_plan_id) == $plan->id)>
                            {{ $plan->name }} (${{ number_format($plan->price_monthly, 2) }}/mo)
                        </option>
                    @endforeach
                </select>
                @error('subscription_plan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Select the default subscription plan for this organization.</div>
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

        <!-- Subscription Plan Details Section -->
        <div class="row g-3 mt-4">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Subscription Plan Details</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="editPlanBtn" data-bs-toggle="modal" data-bs-target="#editPlanModal">
                            Edit Plans
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="planDetailsContainer">
                            @if (old('subscription_plan_id', $organization->subscription_plan_id))
                                @php
                                    $selectedPlanId = old('subscription_plan_id', $organization->subscription_plan_id);
                                    $selectedPlan = $subscriptionPlans->find($selectedPlanId);
                                @endphp
                                @if ($selectedPlan)
                                    <div class="plan-card">
                                        <h5>{{ $selectedPlan->name }}</h5>
                                        <p class="text-secondary mb-2">{{ $selectedPlan->description }}</p>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <small class="text-muted">Monthly Price:</small>
                                                <p class="h6 mb-0">${{ number_format($selectedPlan->price_monthly, 2) }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Yearly Price:</small>
                                                <p class="h6 mb-0">${{ number_format($selectedPlan->price_yearly, 2) }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Hotel Limit:</small>
                                                <p class="h6 mb-0">{{ $selectedPlan->hotel_limit === -1 ? 'Unlimited' : $selectedPlan->hotel_limit }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Staff Limit:</small>
                                                <p class="h6 mb-0">{{ $selectedPlan->staff_limit === -1 ? 'Unlimited' : $selectedPlan->staff_limit }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Room Limit:</small>
                                                <p class="h6 mb-0">{{ $selectedPlan->room_limit === -1 ? 'Unlimited' : $selectedPlan->room_limit }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Booking Limit:</small>
                                                <p class="h6 mb-0">{{ $selectedPlan->booking_limit === -1 ? 'Unlimited' : $selectedPlan->booking_limit }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-secondary">Select a subscription plan above to view details.</p>
                                @endif
                            @else
                                <p class="text-secondary">Select a subscription plan above to view details.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('super-admin.organizations.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Organization</button>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscription Plans</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    @forelse ($subscriptionPlans as $plan)
                        <div class="col-md-6">
                            <div class="card h-100 border-1">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $plan->name }}</h6>
                                    <p class="card-text small text-secondary">{{ $plan->description }}</p>
                                    <div class="mb-2">
                                        <span class="badge bg-info">${{ number_format($plan->price_monthly, 2) }}/mo</span>
                                        <span class="badge bg-secondary">${{ number_format($plan->price_yearly, 2) }}/yr</span>
                                    </div>
                                    <div class="small mb-2">
                                        <p class="mb-1"><strong>Hotels:</strong> {{ $plan->hotel_limit === -1 ? 'Unlimited' : $plan->hotel_limit }}</p>
                                        <p class="mb-1"><strong>Staff:</strong> {{ $plan->staff_limit === -1 ? 'Unlimited' : $plan->staff_limit }}</p>
                                        <p class="mb-0"><strong>Rooms:</strong> {{ $plan->room_limit === -1 ? 'Unlimited' : $plan->room_limit }}</p>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ route('super-admin.subscription-plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                        Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-secondary">No active subscription plans available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('super-admin.subscription-plans.create') }}" class="btn btn-primary">Create New Plan</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const country = document.getElementById('country');
        const currency = document.getElementById('currency_display');
        const planSelect = document.getElementById('subscription_plan_id');
        const planDetailsContainer = document.getElementById('planDetailsContainer');

        if (!country || !currency) {
            return;
        }

        const syncCurrency = () => {
            currency.value = country.options[country.selectedIndex]?.dataset.currency || 'USD';
        };

        // Update plan details when plan selection changes
        const updatePlanDetails = () => {
            const selectedId = planSelect.value;
            if (!selectedId) {
                planDetailsContainer.innerHTML = '<p class="text-secondary">Select a subscription plan above to view details.</p>';
                return;
            }

            // Find the selected plan and show its details
            const plansData = @json($subscriptionPlans);
            const selectedPlan = plansData.find(p => p.id === parseInt(selectedId));
            
            if (selectedPlan) {
                planDetailsContainer.innerHTML = `
                    <div class="plan-card">
                        <h5>${selectedPlan.name}</h5>
                        <p class="text-secondary mb-2">${selectedPlan.description}</p>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <small class="text-muted">Monthly Price:</small>
                                <p class="h6 mb-0">$${parseFloat(selectedPlan.price_monthly).toFixed(2)}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Yearly Price:</small>
                                <p class="h6 mb-0">$${parseFloat(selectedPlan.price_yearly).toFixed(2)}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Hotel Limit:</small>
                                <p class="h6 mb-0">${selectedPlan.hotel_limit === -1 ? 'Unlimited' : selectedPlan.hotel_limit}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Staff Limit:</small>
                                <p class="h6 mb-0">${selectedPlan.staff_limit === -1 ? 'Unlimited' : selectedPlan.staff_limit}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Room Limit:</small>
                                <p class="h6 mb-0">${selectedPlan.room_limit === -1 ? 'Unlimited' : selectedPlan.room_limit}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Booking Limit:</small>
                                <p class="h6 mb-0">${selectedPlan.booking_limit === -1 ? 'Unlimited' : selectedPlan.booking_limit}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        };

        country.addEventListener('change', syncCurrency);
        planSelect.addEventListener('change', updatePlanDetails);
        
        syncCurrency();
        updatePlanDetails();
    });
</script>
