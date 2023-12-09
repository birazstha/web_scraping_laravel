<!-- resources/views/export.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th> S.No. </th>
                <th width="200px"> Name </th>
                <th width="300px"> Department </th>
                <th width="250px"> Designation </th>
            </tr>
        </thead>
        <tbody style="background-color: #696969; vertical-align: top;">
            @foreach ($items as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td width="200px">{{ $item->name ?? 'N/A' }}</td>
                    <td width="300px">{{ $item->department->title ?? 'N/A' }}</td>
                    <td width="250px">{{ $item->designation->title ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
