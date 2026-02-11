<!DOCTYPE html>
<html>
<head>
    <title>Weather Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <h1 class="mb-4">Weather Dashboard</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('cities.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="City Name" value="{{ old('name') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="country" class="form-control" placeholder="Country" value="{{ old('country') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Add City</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>City</th>
                <th>Country</th>
                <th>Latest Temperature</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cities as $city)
                <tr>
                    <td>{{ $city->name }}</td>
                    <td>{{ $city->country }}</td>
                    <td>{{ optional($city->weatherMeasurements->first())->temperature ?? 'No data' }}°C</td>
                    <td>
                        <form action="{{ route('cities.destroy', $city) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
