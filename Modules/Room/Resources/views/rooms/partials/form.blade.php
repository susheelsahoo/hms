<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">Please fix the highlighted fields.</div>
        @endif

        @if ($roomTypes->isEmpty())
            <div class="alert alert-warning">
                Create a room type before adding rooms.
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="room_type_id">Room Type</label>
                <select id="room_type_id" name="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror" required>
                    <option value="">Select room type</option>
                    @foreach ($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" @selected((string) old('room_type_id', $room->room_type_id) === (string) $roomType->id)>
                            {{ $roomType->name }}
                        </option>
                    @endforeach
                </select>
                @error('room_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="room_number">Room Number</label>
                <input id="room_number" name="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number', $room->room_number) }}" required>
                @error('room_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="floor_number">Floor</label>
                <input id="floor_number" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror" value="{{ old('floor_number', $room->floor_number) }}">
                @error('floor_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="capacity">Capacity</label>
                <input id="capacity" type="number" min="1" max="50" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $room->capacity) }}" required>
                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="price">Price Override</label>
                <input id="price" type="number" min="0" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $room->price) }}" placeholder="Optional">
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $room->status ?? 'available') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $room->notes) }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('room-management.rooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary" @disabled($roomTypes->isEmpty())>Save Room</button>
    </div>
</div>
