<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>VWT Algorithm</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    {{--Bootstrap style--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    {{--Chart.js--}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="antialiased">
<div class="container">
    <div class=" text-center mt-5 ">
        <h1>VWT Algorithm</h1>
    </div>
    <div class="row" id="errors">
        @foreach($errors as $error)
            <div class="alert alert-danger" role="alert">
                {{$error}}
            </div>
        @endforeach
    </div>
    <div class="row" id="VWTForm">
        <div class="col-lg-7 mx-auto">
            <div class="card mt-2 mx-auto p-4 bg-light">
                <div class="card-body bg-light">
                    <div class="container">
                        <form id="contact-form" action="/" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="controls">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="formFile" class="form-label">Json file with data</label>
                                        <input class="form-control" type="file" id="formFile" name="formFile">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked="{{old('flexCheckDefault')}}"
                                                   id="flexCheckDefault" name="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                I would like to give my own keys for the algorithm
                                            </label></div>
                                    </div>
                                    <div class="row">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Process data</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- /.8 -->
        </div> <!-- /.row-->
    </div>
</div>
<div class="container col-6" id="charts">
    <canvas id="myChart"></canvas>
</div>
</body>

<script>
    const labels = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
    ];

    const data = {
        labels: labels,
        datasets: [{
            label: 'My First dataset',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5, 2, 20, 30, 45],
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {},
    };
</script>

<script>
    const showCharts = {{ isset($result) }}
    // showCharts = true;

    if (showCharts) {
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        document.getElementById('charts').style.display = 'block';
    } else {
        document.getElementById('charts').style.display = 'none';
    }
</script>
