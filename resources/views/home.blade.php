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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
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
                                        <input class="form-control" type="file" id="formFile" name="formFile" value="{{old('lambda')}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <h4>Calculate for the following lambda value:</h4>
                                    <div class="col-md-12">
                                        <label for="slider" class="form-label">Lambda value (divided by 10.000)</label>
                                        <input type="range" class="form-range" id="slider" min="0" max="20"/>
                                        <input type="number" class="form-input col-12" id="value" name="lambda"/>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>Bursts to consider</h4>
                                            <label class="form-input-label" for="numberOfBurstsToConsider">
                                                How many previous burst should be considered to determine the positioning time
                                            </label>
                                            <input class="form-number-input" type="number"
                                                id="numberOfBurstsToConsider" name="numberOfBurstsToConsider" value="{{old('numberOfBurstsToConsider')}}">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>CWT config</h4>
                                            <label class="form-input-label" for="constantWaitingTime">
                                                What should be the constant waiting time
                                            </label>
                                            <input class="form-number-input" type="number"
                                                id="constantWaitingTime" name="constantWaitingTime" value="{{old('constantWaitingTime')}}">
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
    <div class="col-12">
        <canvas id="chartBurstPeakTimes"></canvas>
    </div>
    <div class="col-12">
        <canvas id="chartTotalCost"></canvas>
    </div>
    <div class="col-12 input-group">
        <button class="col-12 btn btn-success" onclick="copyText()">Copy result json</button>
    </div>
    <div class="input-group col-12">
        <span class="input-group-text">Results</span>
        <textarea id="results" class="form-control" aria-label="With textarea" readonly>
            @json($peakPositioningTimes ?? '')
        </textarea>
    </div>
</div>
</body>

<script>
    @if(isset($chartBurstPeakTimes))
        {{--const labels = {{json_encode($chartBurstPeakTimes->xAxisValues)}};--}}

        const chartBurstPeakTimesData = {
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

        const chartBurstPeakTimesConfig = {
            type: 'line',
            data: chartBurstPeakTimesData,
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

    @if(isset($chartTotalCosts))
        {{--const labels = {{json_encode($chartBurstPeakTimes->xAxisValues)}};--}}

        const chartTotalCostData = {
            // labels: labels,
            datasets: [
                @foreach($chartTotalCosts as $dataset)
                {
                    label: '{{$dataset->getLabel()}}',
                    backgroundColor: '#{{$dataset->getBackgroundColor()}}',
                    borderColor: '#{{$dataset->getBorderColor()}}',
                    data: {{json_encode(\Illuminate\Support\Arr::flatten($dataset->getData()))}},
                },
                @endforeach
            ]
        };

        const chartTotalCostConfig = {
            type: 'line',
            data: chartTotalCostData,
            options: {
                scales: {
                    x: {
                        type: 'category',
                        labels: {{json_encode($chartTotalCosts->getXAxisValues())}},
                        title: {
                            display: true,
                            text: 'Lambda'
                        },
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total Cost'
                        },
                    }
                }
            },
        };
    @endif
</script>

<script>
    const showCharts = {{ isset($peakPositioningTimes) ? 'true' : 'false' }} ;
    // showCharts = true;

    if (showCharts) {
        const chartBurstPeakTimes = new Chart(
            document.getElementById('chartBurstPeakTimes'),
            chartBurstPeakTimesConfig
        );
        const chartTotalCost = new Chart(
            document.getElementById('chartTotalCost'),
            chartTotalCostConfig
        );
        document.getElementById('charts').style.display = 'block';
    } else {
        document.getElementById('charts').style.display = 'none';
    }
</script>

<script>
    function copyText() {
        var Text = document.getElementById("results");
        Text.select();
        navigator.clipboard.writeText(Text.value);
    }
</script>

<script>
    function LogSlider(options) {
        options = options || {};
        this.minpos = options.minpos || 0;
        this.maxpos = options.maxpos || 100;
        this.minlval = Math.log(options.minval || 1);
        this.maxlval = Math.log(options.maxval || 100000);

        this.scale = (this.maxlval - this.minlval) / (this.maxpos - this.minpos);
    }

    LogSlider.prototype = {
        // Calculate value from a slider position
        value: function(position) {
            return Math.exp((position - this.minpos) * this.scale + this.minlval);
        },
        // Calculate slider position from a value
        position: function(value) {
            return this.minpos + (Math.log(value) - this.minlval) / this.scale;
        }
    };


    // Usage:

    var logsl = new LogSlider({maxpos: 20, minval: 1, maxval: 10000});

    $('#slider').on('change', function() {
        var val = logsl.value(+$(this).val());
        $('#value').val(val.toFixed(0));
    });

    $('#value').on('keyup', function() {
        var pos = logsl.position(+$(this).val());
        $('#slider').val(pos);
    });

    $('#value').val({{old('lambda') ?? 0}}).trigger("keyup");
</script>
