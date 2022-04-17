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
                                    <h4>Calculate for the following lambda values:</h4>
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(0, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[0]" name="flexCheckDefault[0]" value="0.1">
                                            <label class="form-check-label" for="flexCheckDefault[0]">
                                                lambda = 0.1
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(1, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[1]" name="flexCheckDefault[1]" value="0.2">
                                            <label class="form-check-label" for="flexCheckDefault[1]">
                                                lambda = 0.2
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(2, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[2]" name="flexCheckDefault[2]" value="0.3">
                                            <label class="form-check-label" for="flexCheckDefault[2]">
                                                lambda = 0.3
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(3, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[3]" name="flexCheckDefault[3]" value="0.4">
                                            <label class="form-check-label" for="flexCheckDefault[3]">
                                                lambda = 0.4
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(4, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[4]" name="flexCheckDefault[4]" value="0.5">
                                            <label class="form-check-label" for="flexCheckDefault[4]">
                                                lambda = 0.5
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(5, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[5]" name="flexCheckDefault[5]" value="0.6">
                                            <label class="form-check-label" for="flexCheckDefault[5]">
                                                lambda = 0.6
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(6, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[6]" name="flexCheckDefault[6]" value="0.7">
                                            <label class="form-check-label" for="flexCheckDefault[6]">
                                                lambda = 0.7
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(7, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[7]" name="flexCheckDefault[7]" value="0.8">
                                            <label class="form-check-label" for="flexCheckDefault[7]">
                                                lambda = 0.8
                                            </label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" @if(array_key_exists(8, old('flexCheckDefault'))) checked @endif
                                                   id="flexCheckDefault[8]" name="flexCheckDefault[8]" value="0.9">
                                            <label class="form-check-label" for="flexCheckDefault[8]">
                                                lambda = 0.9
                                            </label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>Bursts to consider</h4>
                                                <input class="form-number-input" type="number"
                                                id="numberOfBurstsToConsider" name="numberOfBurstsToConsider" value="{{old('numberOfBurstsToConsider')}}">
                                                <label class="form-input-label" for="numberOfBurstsToConsider">
                                                    How many previous burst should be considered to determine the positioning time
                                                </label>
                                        </div>

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
    @if(isset($chartBurstPeakTimes))
        {{--const labels = {{json_encode($chartBurstPeakTimes->xAxisValues)}};--}}

        const data = {
            // labels: labels,
            datasets: [
                @foreach($chartBurstPeakTimes as $dataset)
                {
                    label: '{{$dataset->getLabel()}}',
                    backgroundColor: '#{{$dataset->getBackgroundColor()}}',
                    borderColor: '#{{$dataset->getBorderColor()}}',
                    data: {{json_encode(\Illuminate\Support\Arr::flatten($dataset->getData()))}},
                },
                @endforeach
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                scales: {
                    x: {
                        type: 'category',
                        labels: {{json_encode($chartBurstPeakTimes->getXAxisValues())}},
                        title: {
                            display: true,
                            text: 'Burst'
                        },
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Positioning Time'
                        },
                    }
                }
            },
        };
    @endif
</script>

<script>
    const showCharts = {{ !empty($peakPositioningTimes) }}
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
