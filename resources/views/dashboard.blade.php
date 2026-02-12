<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <h1 class="mb-4">Weather Dashboard</h1>

    <div class="mb-4 d-flex gap-2">
        <a href="{{ route('cities.index') }}" class="btn btn-outline-primary">Cities</a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Weather Dashboard</a>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Cities Table --}}
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

    <div class="mt-5">
        @foreach($cities as $city)
            <div class="mb-5">
                <h4 class="mb-3">{{ $city->name }}, {{ $city->country }}</h4>

                @if($city->weatherMeasurements->isEmpty())
                    <div class="text-muted">No data</div>
                @else
                    <div style="height: 210px;">
                        <canvas id="chart-city-{{ $city->id }}"></canvas>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const citySeries = @json($citySeries);

            citySeries.forEach((series) => {
                const canvas = document.getElementById(`chart-city-${series.id}`);
                if (!canvas) return;

                const labels = [...series.labels].reverse();
                const temps = [...series.temps].reverse();

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: `${series.name} temperature (°C)`,
                            data: temps,
                            borderWidth: 2,
                            tension: 0.25,
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true },
                        },
                        scales: {
                            y: { title: { display: true, text: '°C' } },
                            x: { title: { display: true, text: 'Time' } },
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
