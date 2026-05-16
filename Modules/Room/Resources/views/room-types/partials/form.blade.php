<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">Please fix the highlighted fields.</div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Name</label>
                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $roomType->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="slug">Slug</label>
                <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $roomType->slug) }}" placeholder="Auto-generated from name">
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="rate_type_id">Rate Type</label>
                <select id="rate_type_id" name="rate_type_id" class="form-select @error('rate_type_id') is-invalid @enderror">
                    <option value="">None</option>
                    @foreach ($rateTypes as $rateType)
                        <option value="{{ $rateType->id }}" @selected((string) old('rate_type_id', $roomType->rate_type_id) === (string) $rateType->id)>
                            {{ $rateType->name }} ({{ number_format((float) $rateType->base_rate, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('rate_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="max_adults">Max Adults</label>
                <input id="max_adults" type="number" min="1" max="20" name="max_adults" class="form-control @error('max_adults') is-invalid @enderror" value="{{ old('max_adults', $roomType->max_adults) }}" required>
                @error('max_adults') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="max_children">Max Children</label>
                <input id="max_children" type="number" min="0" max="20" name="max_children" class="form-control @error('max_children') is-invalid @enderror" value="{{ old('max_children', $roomType->max_children) }}" required>
                @error('max_children') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="base_price">Base Price</label>
                <input id="base_price" type="number" min="0" step="0.01" name="base_price" class="form-control @error('base_price') is-invalid @enderror" value="{{ old('base_price', $roomType->base_price) }}" placeholder="Defaults to selected rate type base rate">
                @error('base_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="size">Size</label>
                <input id="size" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size', $roomType->size) }}" placeholder="320 sq ft">
                @error('size') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="bed_type">Bed Type</label>
                <input id="bed_type" name="bed_type" class="form-control @error('bed_type') is-invalid @enderror" value="{{ old('bed_type', $roomType->bed_type) }}" placeholder="King bed">
                @error('bed_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $roomType->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('room-management.room-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Room Type</button>
    </div>
</div>
