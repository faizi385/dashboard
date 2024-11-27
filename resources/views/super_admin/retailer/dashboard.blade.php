@extends('layouts.admin')

@section('content')

<div class="container p-2">
    <h1 class="text-white text-center mb-4">Distributor Dashboard</h1>

    <!-- Scrollable Cards -->
    <div class="scrollable-container mb-4">
        <div class="scrollable-cards">
            <!-- Total Revenue -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-success">
                    <div class="inner">
                        {{-- <h3 class="text-center">${{ number_format(round($totalRevenue), 2) }}</h3> <!-- Show total revenue --> --}}
                        <p class="text-center">Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Total Locations -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3 class="text-white text-center">{{ $totalLocations }}</h3> <!-- Show the total locations -->
                        <p class="text-white text-center">Total Locations</p>
                    </div>
                
                </div>
            </div>

            <!-- Total Reports -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 class="text-center">{{     $totalReportsSubmitted }}</h3> <!-- Show total reports -->
                        <p class="text-center">Total Reports</p>
                    </div>
               
                </div>
            </div>

            <!-- Total Purchase -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        {{-- <h3 class="text-center">${{ number_format(round($totalPurchase), 2) }}</h3> <!-- Show total purchase --> --}}
                        <p class="text-center">Total Purchase Products</p>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- First Chart -->
        <div class="col-lg-6 mt-4">
            <div class="chart-container">
                <div id="chart"></div>
            </div>
        </div>

        <!-- Second Chart -->
        <div class="col-lg-6 mt-4">
            <div class="chart-container">
                <div id="chart1"></div>
            </div>
        </div>
    </div>

    <!-- Third Chart -->
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
    .small-box>.inner {
        height: 20vh;
        padding: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
     const scrollableContainer = document.querySelector('.scrollable-cards');

let isDown = false;
let startX;
let scrollLeft;

scrollableContainer.addEventListener('mousedown', (e) => {
    isDown = true;
    scrollableContainer.classList.add('active');
    startX = e.pageX - scrollableContainer.offsetLeft;
    scrollLeft = scrollableContainer.scrollLeft;
});

scrollableContainer.addEventListener('mouseleave', () => {
    isDown = false;
    scrollableContainer.classList.remove('active');
});

scrollableContainer.addEventListener('mouseup', () => {
    isDown = false;
    scrollableContainer.classList.remove('active');
});

scrollableContainer.addEventListener('mousemove', (e) => {
    if (!isDown) return; // Stop execution if not dragging
    e.preventDefault();
    const x = e.pageX - scrollableContainer.offsetLeft;
    const walk = (x - startX) * 2; // Adjust the scroll speed
    scrollableContainer.scrollLeft = scrollLeft - walk;
});
document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.pickable-card');

        cards.forEach(card => {
            card.addEventListener('click', () => {
                card.classList.toggle('selected');
            });
        });
    });
  document.addEventListener('DOMContentLoaded', function () {
    // First chart configuration (Total IRCC Revenue)
    var totalIrccDollarAllRetailers = Math.round(@json($totalIrccDollarAllRetailers) / 1000);

    var optionsChart1 = {
        series: [{
            name: "Total Revenue",
            data: [totalIrccDollarAllRetailers] // Rounded to nearest integer
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        title: { text: 'Total Revenue - October', align: 'left' },
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
                    return Math.round(value) + "k"; // Rounded Y-axis in 'k' format
                }
            }
        }
    };

    var chart1 = new ApexCharts(document.querySelector("#chart"), optionsChart1);
    chart1.render();

    // Second chart configuration (Total Purchases)
    var totalPurchaseSum = Math.round(@json($totalPurchaseSum));
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
        title: { text: 'Overall Purchases - October', align: 'left' },
        xaxis: { categories: ['Total'] },
        yaxis: {
            opposite: true,
            labels: {
                formatter: function(value) {
                    return Math.round(value); // Rounded Y-axis values
                }
            }
        },
        legend: { horizontalAlign: 'left' }
    };

    var chart2 = new ApexCharts(document.querySelector("#chart1"), optionsChart2);
    chart2.render();

    // Third chart configuration: Total Purchase Cost by Province
    var provinceData = @json($provinceData);  // Array of objects with province and total purchase
    var provinces = provinceData.map(function(item) { return item.province; });
    var purchases = provinceData.map(function(item) { return Math.round(item.total_purchase); });

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
            return '$' + Math.round(val); // Rounded values as currency
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#304758"]
        }
    },
    xaxis: {
        categories: provinces,  // Provinces dynamically on the X-axis
        position: 'bottom',
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
                return '$' + Math.round(val); // Rounded Y-axis values as currency
            }
        }
    },
    title: {
        text: 'Total Purchase Cost by Province - October',
        floating: false,
        offsetY: 15, // Adjust this value to add more space below the title
        style: {
            fontSize: '16px',
            color: '#444'
        }
    }
};

var chart = new ApexCharts(document.querySelector("#chart2"), options);
chart.render();

});

</script>

@endsection
