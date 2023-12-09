<form action="{{ route('staff.filter') }}" method="get">
    <div class="filters">

        <div class="row">
            {{-- Department --}}
            <div class="col-md-3">
                <label for="department">Department:</label>
                <select id="department" class="form-select form-select mb-3" name="department_id">
                    <option value="">--Select Department--</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            {{ $department->id == request()->department_id ? 'selected' : '' }}>
                            {{ $department->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Designation --}}
            <div class="col-md-3">
                <label for="department">Designation:</label>
                <select id="department" class="form-select form-select mb-3" name="designation_id">
                    <option value="">--Select Designation</option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}"
                            {{ $designation->id == request()->designation_id ? 'selected' : '' }}>
                            {{ $designation->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Name --}}
            <div class="col-md-3">
                <label for="department">Name:</label>
                <input type="text" class="form-control" name="name" placeholder="Enter Name"
                    value="{{ request()->name ?? '' }}">
            </div>

            {{-- Action Buttons --}}
            <div class="col-md-3 buttons">
                <button type="submit" class="btn btn-success m-1">Filter</button>
                <a href="{{ route('scrap') }}" class="btn btn-primary m-1">Reset</a>
                <a href="{{ route('export', ['department_id' => request()->department_id, 'designation_id' => request()->designation_id, 'name' => request()->name]) }}"
                    class="btn btn-warning m-1">Export</a>

            </div>


        </div>

    </div>




</form>
