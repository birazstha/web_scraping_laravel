<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>



<title>Staff Photo Gallery</title>
</head>

<body>
    <nav>
        <h1>Staff Lists</h1>
    </nav>

    @include('filter')


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
                    {{-- <p>{{ $staff->department->title }}</p> --}}
                </div>
            </div>
        @empty
            <p>No Data Found</p>
        @endforelse
    </div>


</body>

</html>
