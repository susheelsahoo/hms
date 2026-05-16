<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">Please fix the highlighted fields.</div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="first_name">First Name</label>
                <input id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="last_name">Last Name</label>
                <input id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="organization_id">Organization</label>
                <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
                    <option value="">Platform / No Organization</option>
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}" @selected((int) old('organization_id', $user->organization_id) === $organization->id)>{{ $organization->name }}</option>
                    @endforeach
                </select>
                @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @php
                $selectedHotelValues = collect(old('hotel_ids', $selectedHotelIds ?? []))
                    ->map(fn ($hotelId) => (string) $hotelId)
                    ->all();
            @endphp
            <div class="col-md-6">
                <label class="form-label" for="hotel_ids">Hotels</label>
                <select
                    id="hotel_ids"
                    name="hotel_ids[]"
                    class="form-select @error('hotel_ids') is-invalid @enderror @error('hotel_ids.*') is-invalid @enderror"
                    multiple
                    size="5"
                ></select>
                <div class="form-text">Select one or more hotels for this user.</div>
                @error('hotel_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('hotel_ids.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="role_id">Role</label>
                <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'invited' => 'Invited', 'suspended' => 'Suspended'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $user->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" @required(! $user->exists)>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" @required(! $user->exists)>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('user-management.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save User</button>
    </div>
</div>

<script>
    (() => {
        const organizationSelect = document.getElementById('organization_id');
        const hotelSelect = document.getElementById('hotel_ids');
        const hotelsByOrganization = @json($hotelsByOrganization ?? []);
        let selectedHotelIds = @json($selectedHotelValues);

        const refreshHotels = () => {
            const organizationId = organizationSelect.value;
            const hotels = hotelsByOrganization[organizationId] || [];

            hotelSelect.innerHTML = '';
            hotelSelect.disabled = hotels.length === 0;

            if (hotels.length === 0) {
                const option = new Option(
                    organizationId ? 'No hotels found for this organization' : 'Select organization first',
                    ''
                );
                option.disabled = true;
                hotelSelect.append(option);
                return;
            }

            hotels.forEach((hotel) => {
                const option = new Option(hotel.name, hotel.id);
                option.selected = selectedHotelIds.includes(String(hotel.id));
                hotelSelect.append(option);
            });
        };

        organizationSelect.addEventListener('change', () => {
            selectedHotelIds = [];
            refreshHotels();
        });

        refreshHotels();
    })();
</script>
