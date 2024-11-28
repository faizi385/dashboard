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
                        <h3 class="text-center">${{ number_format(round(   $totalFeeSum ), 2) }}</h3> <!-- Show total revenue -->
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
                        <h3 class="text-center">{{ $totalPurchasedProducts }}</h3>
                        <p class="text-center">Total Purchase Products</p>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- First Chart -->
        {{-- <div class="col-lg-6 mt-4">
            <div class="chart-container">
                <div id="chart"></div>
            </div>
        </div> --}}

        <!-- Second Chart -->
        <div class="col-lg-6 mt-4">
            <div class="chart-container">
                <div id="chart1"></div>
            </div>
        </div>
        <div class="col-lg-6 mt-4">
            <div class="chart-container">
                <div id="chart2"></div>
            </div>
        </div>
    </div>

    <div class="row">
  
    <div class="col-lg-6 mt-4">
        <div class="chart-container">
            <div id="chart4"></div>
        </div>
    </div>
    <div class="col-lg-6 mt-4">
        <div class="chart-container">
            <div id="chart5"></div>
        </div>
    </div>
</div>
<div class="row">
    
    {{-- <div class="col-lg-6 mt-4">
        <div class="chart-container">
            <div id="chart4"></div>
        </div>
    </div> --}}
</div>
</div>




<script>
 
  document.addEventListener('DOMContentLoaded', function () {
    // First chart configuration (Total IRCC Revenue)
    // var totalIrccDollarAllRetailers = Math.round(@json($totalIrccDollarAllRetailers) / 1000);

    // var optionsChart1 = {
    //     series: [{
    //         name: "Total Revenue",
    //         data: [totalIrccDollarAllRetailers] // Rounded to nearest integer
    //     }],
    //     chart: {
    //         height: 350,
    //         type: 'line',
    //         zoom: { enabled: false }
    //     },
    //     dataLabels: { enabled: false },
    //     stroke: { curve: 'straight' },
    //     title: { text: 'Total Revenue - October', align: 'left' },
    //     grid: {
    //         row: {
    //             colors: ['#f3f3f3', 'transparent'],
    //             opacity: 0.5
    //         },
    //     },
    //     xaxis: { categories: ['Total'] },
    //     yaxis: {
    //         labels: {
    //             formatter: function(value) {
    //                 return Math.round(value) + "k"; // Rounded Y-axis in 'k' format
    //             }
    //         }
    //     }
    // };

    // var chart1 = new ApexCharts(document.querySelector("#chart"), optionsChart1);
    // chart1.render();


    // Total purchase sum for the logged-in retailer
    var totalPurchaseSum = Math.round(@json($totalPurchaseSum));

var options = {
    series: [{
        name: 'Distributor Purchases',
        data: [totalPurchaseSum]
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    title: {
        text: 'Total Purchases of Distributor',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#333'
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: true,
        style: {
            colors: ['#fff'], // White color for data labels
            fontSize: '14px',
            fontWeight: 'bold',
        },
        offsetY: -10, // Adjust to center within bars
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: ['Total Purchases']
    },
    yaxis: {
        title: {
            text: '$ (thousands)'
        }
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$ " + val;
            }
        }
    }
};

// Render the chart
var chart = new ApexCharts(document.querySelector("#chart1"), options);
chart.render();



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
                position: 'center',  // Position values in the center of the bars
            },
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return '' + Math.round(val); // Rounded values as currency
        },
        style: {
            fontSize: '12px',
            colors: ["#ffffff"] // White color for text inside the bar
        },
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
        text: 'Total Purchase by Province - October',
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

var totalDealsPurchase = @json($totalDealsPurchaseCount);
var totalNonDealsPurchase = @json($totalNonDealsPurchaseCount);

var options = {
    series: [{
        name: 'Deals Purchase',
        data: [totalDealsPurchase]
    }, {
        name: 'Non-Deals Purchase',
        data: [totalNonDealsPurchase]
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    title: {
        text: 'Total Purchases: Deals vs Non-Deals',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#333'
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: true,
        style: {
            colors: ['#fff'], // White color for data labels
            fontSize: '14px',
            fontWeight: 'bold',
        },
        offsetY: -10, // Adjust to center within bars
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: ['Purchase Type'],
    },
    yaxis: {
        title: {
            text: 'Total Purchases'
        }
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " Purchases";
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#chart4"), options);
chart.render();


        
var topLocations = @json($topLocations);

var locationCategories = topLocations.map(location => 
    location.location.length > 15 ? location.location.slice(0, 12) + '...' : location.location
);
var totalPurchases = topLocations.map(location => location.total_purchase);

var options = {
    series: [{
        name: 'Total Purchases',
        data: totalPurchases
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    title: {
        text: 'Top 5 Locations by Total Purchases',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#333'
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return "" + val.toFixed(2);
        },
        style: {
            fontSize: '14px',
            colors: ["#fff"], // White color for data labels
        }
    },
    xaxis: {
        categories: locationCategories,
        title: {
            text: 'Locations',
            style: {
                fontSize: '12px',
                fontWeight: 'bold',
                color: '#666'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Total Purchases',
            style: {
                fontSize: '12px',
                fontWeight: 'bold',
                color: '#666'
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$" + val.toFixed(2);
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#chart5"), options);
chart.render();


      
</script>

@endsection
