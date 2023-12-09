@extends('master')

@section('content')
    <h2 class="department">{{ $departmentTitle }}</h2>
    <hr>
    @error('msg')
        <div class="alert alert-danger text-center m-3" role="alert">
            {{ $message }}
        </div>
    @enderror

    <div class="gallery">
        @forelse ($staffs as $staff)
            <div class="staff" data-department="{{ $staff->department->title }}"
                data-designation="{{ $staff->designation->title }}">
                <div class="staff__image">
                    <img src="{{ $staff->image }}" alt="{{ $staff->name }}">
                </div>
                <div class="details">
                    <h5>{{ $staff->name }}</h5>
                    <p class="designation">{{ $staff->designation->title }}</p>
                    <p>
                        {!! $staff->email ? '<a href="mailto:' . $staff->email . '" class="email">' . $staff->email . '</a>' : 'N/A' !!}
                    </p>
                    <p class="">{{ $staff->phone ?? 'N/A' }}</p>
                    </p>
                </div>
            </div>
        @empty
            <p>No Data Found</p>
        @endforelse
    </div>
@endsection
