<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .weather-card {
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border: none;
        }

        .weather-card:hover {
            transform: translateY(-5px);
        }

        .card-temp {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        }

        .card-humidity {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        }

        .card-wind {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
        }

        .card-pressure {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
        }

        .weather-icon {
            font-size: 2.5rem;
        }

        .location-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        #useLocationBtn {
            margin-left: 10px;
        }

        .location-info-card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
            transition: transform 0.3s ease;
        }

        .location-info-card:hover {
            transform: translateY(-5px);
        }

        .icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .bg-sunrise {
            background: rgba(255, 193, 7, 0.2);
            color: #FFC107;
        }

        .bg-sunset {
            background: rgba(255, 152, 0, 0.2);
            color: #FF9800;
        }

        .bg-cloud {
            background: rgba(224, 224, 224, 0.2);
            color: #E0E0E0;
        }

        .bg-visibility {
            background: rgba(66, 165, 245, 0.2);
            color: #42A5F5;
        }

        .weather-icon-lg {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="location-header">
            <h1>Weather Dashboard</h1>
            <form class="d-flex" id="locationForm">
                <div class="input-group">
                    <input type="text" class="form-control" id="locationInput" placeholder="Enter city name"
                        value="{{ $location ?? '' }}">
                    <button class="btn btn-secondary" style="border-radius: 5px;font-size:14px;" type="submit"><i
                            class="bi bi-search"></i>
                    </button>
                    <button class="btn btn-outline-primary" type="button" id="useLocationBtn">
                        <i class="bi bi-geo-alt"></i> Use My Location
                    </button>
                </div>
            </form>
        </div>

        @if (isset($error))
            <div class="alert alert-warning">{{ $error }}</div>
        @endif

        <!-- General Section -->
        <div class="row mb-2">
            <div class="col-md-3 mb-4">
                <div class="card weather-card card-temp text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-thermometer-half weather-icon mb-3"></i>
                        <h5 class="card-title">Temperature</h5>
                        <h2 class="display-4">{{ round($current['main']['temp']) }}°C</h2>
                        <p class="card-text">Feels like {{ round($current['main']['feels_like']) }}°C</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card weather-card card-humidity text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-droplet weather-icon mb-3"></i>
                        <h5 class="card-title">Humidity</h5>
                        <h2 class="display-4">{{ $current['main']['humidity'] }}%</h2>
                        <p class="card-text">{{ $current['weather'][0]['description'] }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card weather-card card-wind text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-wind weather-icon mb-3"></i>
                        <h5 class="card-title">Wind Speed</h5>
                        <h2 class="display-4">{{ $current['wind']['speed'] }} m/s</h2>
                        <p class="card-text">Direction: {{ $current['wind']['deg'] ?? 'N/A' }}°</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card weather-card card-pressure text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-speedometer2 weather-icon mb-3"></i>
                        <h5 class="card-title">Pressure</h5>
                        <h2 class="display-4">{{ $current['main']['pressure'] }} hPa</h2>
                        <p class="card-text">Sea level: {{ $current['main']['sea_level'] ?? 'N/A' }} hPa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Info -->
        <div class="card mb-4 location-info-card"
            style="border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        {{ $current['name'] }}, {{ $current['sys']['country'] ?? '' }}
                    </h3>
                    <div class="weather-icon-lg">
                        @php
                            $iconCode = $current['weather'][0]['icon'] ?? '01d';
                            $iconUrl = "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
                        @endphp
                        <img src="{{ $iconUrl }}" alt="Weather Icon" style="height: 60px; width: 60px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-sunrise me-3">
                                <i class="bi bi-sunrise fs-4"></i>
                            </div>
                            <div>
                                <p class="mb-0 small text-white-50">Sunrise</p>
                                <h5 class="mb-0">{{ date('H:i', $current['sys']['sunrise']) }}</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-cloud me-3">
                                <i class="bi bi-clouds fs-4"></i>
                            </div>
                            <div>
                                <p class="mb-0 small text-white-50">Cloudiness</p>
                                <h5 class="mb-0">{{ $current['clouds']['all'] ?? 0 }}%</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-sunset me-3">
                                <i class="bi bi-sunset fs-4"></i>
                            </div>
                            <div>
                                <p class="mb-0 small text-white-50">Sunset</p>
                                <h5 class="mb-0">{{ date('H:i', $current['sys']['sunset']) }}</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-visibility me-3">
                                <i class="bi bi-eye fs-4"></i>
                            </div>
                            <div>
                                <p class="mb-0 small text-white-50">Visibility</p>
                                <h5 class="mb-0">{{ ($current['visibility'] ?? 0) / 1000 }} km</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="table-container p-4">
            <h3 class="mb-4">Last 5-Day Weather History</h3>
            <div class="table-responsive">
                <table class="table table-success table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Temperature (°C)</th>
                            <th>Humidity (%)</th>
                            <th>Wind Speed (m/s)</th>
                            <th>Pressure (hPa)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y (D)') }}</td>
                                <td>{{ $day['temp'] }}</td>
                                <td>{{ $day['humidity'] }}</td>
                                <td>{{ $day['wind_speed'] }}</td>
                                <td>{{ $day['pressure'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('locationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const location = document.getElementById('locationInput').value.trim();
                if (location) {
                    window.location.href = `/?location=${encodeURIComponent(location)}`;
                }
            });

            document.getElementById('useLocationBtn').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            fetch(
                                    `/weather-by-coords?lat=${position.coords.latitude}&lon=${position.coords.longitude}`
                                )
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        alert(data.error);
                                    } else {
                                        window.location.href =
                                            `/?location=${encodeURIComponent(data.location)}`;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Failed to fetch weather data for your location.');
                                });
                        },
                        function(error) {
                            alert('Error getting location: ' + error.message);
                        }
                    );
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            });

            if (window.location.search.indexOf('location=') === -1 && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        fetch(
                                `/weather-by-coords?lat=${position.coords.latitude}&lon=${position.coords.longitude}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                if (!data.error) {
                                    window.location.href =
                                        `/?location=${encodeURIComponent(data.location)}`;
                                }
                            });
                    },
                    function(error) {
                        console.log('Location access denied or error:', error);
                    }
                );
            }
        });
    </script>
</body>

</html>
