@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h1 class="text-white text-center mb-4">Distributor Dashboard</h1>
    
    <div class="row">
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
    <div class="col-lg-6 mt-4">
        <div class="chart-container">
            <div id="chart2"></div>
        </div>
    </div>
</div>

<style>
    .chart-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        min-height: 350px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // First chart configuration (Total IRCC Revenue)
// Convert IRCC Revenue data to thousands (k format)
var totalIrccDollarAllRetailers = @json($totalIrccDollarAllRetailers) / 1000;

// Chart configuration (Total IRCC Revenue)
var optionsChart1 = {
    series: [{
        name: "IRCC Revenue",
        data: [totalIrccDollarAllRetailers] // Displayed in 'k' format
    }],
    chart: {
        height: 350,
        type: 'line',
        zoom: { enabled: false }
    },
    dataLabels: { enabled: false },
    stroke: { curve: 'straight' },
    title: { text: 'Total IRCC Revenue', align: 'left' },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    xaxis: { categories: ['Total'] },
    yaxis: {
        labels: {
            formatter: function(value) {
                return value + "k"; // Y-axis in 'k' format
            }
        }
    }
};

var chart1 = new ApexCharts(document.querySelector("#chart"), optionsChart1);
chart1.render();


        // Second chart configuration (Total Purchases)
        var totalPurchaseSum = @json($totalPurchaseSum);
        var optionsChart2 = {
            series: [{
                name: "Overall Purchases",
                data: [totalPurchaseSum]
            }],
            chart: {
                type: 'area',
                height: 350,
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            title: { text: 'Overall Purchases', align: 'left' },
            xaxis: { categories: ['Total'] },
            yaxis: { opposite: true },
            legend: { horizontalAlign: 'left' }
        };
        var chart2 = new ApexCharts(document.querySelector("#chart1"), optionsChart2);
        chart2.render();

        // Third chart configuration: Total Purchase Cost by Province
        var provinceData = @json($provinceData);  // Array of objects with province and total purchase
        var provinces = provinceData.map(function(item) { return item.province; });
        var purchases = provinceData.map(function(item) { return item.total_purchase; });

        var options = {
            series: [{
                name: 'Total Purchases',
                data: purchases
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // Position at the top of the bars
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return '$' + val.toFixed(2);  // Format the value as currency
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            xaxis: {
                categories: provinces,  // Provinces dynamically on the X-axis
                position: 'top',
                axisBorder: { show: false },
                axisTicks: { show: false },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: { enabled: true },
            },
            yaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    show: true,
                    formatter: function (val) {
                        return '$' + val.toFixed(2);  // Format the value as currency
                    }
                }
            },
            title: {
                text: 'Total Purchase Cost by Location',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#444'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();
    });
</script>

@endsection
