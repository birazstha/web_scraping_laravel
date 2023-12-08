<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <title>Staff Photo Gallery</title>
</head>

<body>

    <nav>
        <h1>Staff Lists</h1>
    </nav>


    <div class="gallery">
        @foreach ($staffs as $staff)
            <div class="staff">
                <div class="staff__image">
                    <img src="{{ $staff->image }}" alt="Staff 1">
                </div>
                <div class="details">
                    <h3>{{ $staff->name }}</h3>
                    <p>{{ $staff->designation->title }}</p>
                    <p>{{ $staff->department->title }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- <div class="gallery">
        <div class="staff">
            <div class="staff__image">
                <img src="{{ asset('test.jpg') }}" alt="Staff 1">
            </div>
            <div class="details">
                <h3>John Doe</h3>
                <p>Manager</p>
                <p>Human Resources</p>
            </div>
        </div>

        <div class="staff">
            <div class="staff__image">
                <img src="{{ asset('test.jpg') }}" alt="Staff 1">
            </div>
            <div class="details">
                <h3>John Doe</h3>
                <p>Manager</p>
                <p>Private Secretariat of the Honorable Minister</p>
            </div>
        </div>
    </div> --}}



</body>

</html>
