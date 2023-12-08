<form action="{{ route('staff.filter') }}" method="get">
    <div class="filters">

        <div>
            <label for="department">Department:</label>
            <select id="department" class="form-select form-select-lg mb-3" name="department_id">
                <option value="">--Select Department--</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}"
                        {{ $department->id == request()->department_id ? 'selected' : '' }}>
                        {{ $department->title }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="department">Designation:</label>
            <select id="department" class="form-select form-select-lg mb-3" name="designation_id">
                <option value="">--Select Designation</option>
                @foreach ($designations as $designation)
                    <option value="{{ $designation->id }}"
                        {{ $designation->id == request()->designation_id ? 'selected' : '' }}>
                        {{ $designation->title }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Filters</button>
    </div>

</form>
