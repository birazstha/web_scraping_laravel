@extends('master')

@section('content')

    @forelse ($departments as $department)
        <h2 class="department">{{ $department->title }}</h2>
        <hr>
        <div class="gallery">
            @forelse ($department->staffs as $staff)
                <div class="staff" data-department="{{ $staff->department->title }}"
                    data-designation="{{ $staff->designation->title }}">
                    <div class="staff__image">
                        <img src="{{ $staff->image }}" alt="{{ $staff->name }}">
                    </div>
                    <div class="details">
                        <h5>{{ $staff->name }}</h5>
                        <p class="designation">{{ $staff->designation->title }}</p>
                        <p>
                            {!! $staff->email ? '<a href="mailto:' . $staff->email . '" class="email">' . $staff->email . '</a>' : 'NA' !!}

                        </p>
                        <p class="">{{ $staff->phone ?? 'NA' }}</p>
                        </p>
                    </div>
                </div>
            @empty
                <div class="alert alert-danger text-center m-3" role="alert">
                    No data found.
                </div>
            @endforelse
        </div>
        <hr>
    @empty
        <div class="alert alert-danger text-center m-3" role="alert">
            No data found.
        </div>
    @endforelse
@endsection
