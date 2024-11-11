@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-white">Retailer Dashboard</h1>
    
    <div class="col-lg-6">
        <div class="chart-container">
            <div id="chart"></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="chart-container">
            <div id="chart1"></div>
        </div>
    </div>
</div>

<style>
    .chart-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    #chart, #chart1 {
        min-height: 350px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data for the first chart (IRCC Revenue)
        var totalIrccDollarAllRetailers = @json($totalIrccDollarAllRetailers);

        // First chart configuration: Total IRCC Revenue
        var optionsChart1 = {
            series: [{
                name: "IRCC Revenue",
                data: [totalIrccDollarAllRetailers]  // Use the total IRCC dollar data from the controller
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Total IRCC Revenue',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Total']  // Example static category; adjust as needed
            }
        };

        // Render the first chart
        var chart1 = new ApexCharts(document.querySelector("#chart"), optionsChart1);
        chart1.render();

        // Second chart configuration: Sample Stock Data
        var optionsChart2 = {
          series: [{
            name: "Stock Price",
            data: [31, 40, 28, 51, 42, 109, 100]  // Sample data for demonstration
          }],
          chart: {
            type: 'area',
            height: 350,
            zoom: {
              enabled: false
            }
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'straight'
          },
          title: {
            text: 'Fundamental Analysis of Stocks',
            align: 'left'
          },
          subtitle: {
            text: 'Price Movements',
            align: 'left'
          },
          labels: ["2023-01-01", "2023-02-01", "2023-03-01", "2023-04-01", "2023-05-01", "2023-06-01", "2023-07-01"],
          xaxis: {
            type: 'datetime'
          },
          yaxis: {
            opposite: true
          },
          legend: {
            horizontalAlign: 'left'
          }
        };

        // Render the second chart
        var chart2 = new ApexCharts(document.querySelector("#chart1"), optionsChart2);
        chart2.render();
    });
</script>

@endsection
