@extends('layouts.app')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid p-2">
        <!-- Summary Boxes -->
        <div class="row">
            <!-- Total Payout (With Tax) -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 class="text-center"> ${{ number_format(round($totalPayoutWithTaxAllRetailers), 2) }}</h3>
                        <p class="text-center">Total Payout (With Tax)</p>
                    </div>
                    <div class="icon">
                        <i class=""></i>
                    </div>
                </div>
            </div>
            
            <!-- Overall Revenue -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3 class="text-white text-center">${{ number_format(round($totalIrccDollarAllRetailers), 2) }}</h3>
                        <p class="text-white text-center">Overall Revenue</p>
                    </div>
                    <div class="icon">
                        <i class=""></i>
                    </div>
                </div>
            </div>

            <!-- Availed Deals -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 class="text-center">{{ $totalMappedOffers }}</h3>
                        <p class="text-center">Availed Deals</p>
                    </div>
                    <div class="icon">
                        <i class=""></i>
                    </div>
                </div>
            </div>

            <!-- Unavailed Deals -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 class="text-center">{{ $totalUnmappedOffers }}</h3>
                        <p class="text-center">Unavailed Deals</p>
                    </div>
                    <div class="icon">
                        <i class=""></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Area Charts -->
        <div class="row">
      
            <div class="col-lg-6 mt-4">
                <div class="chart-container">
                    <div id="chart"></div>
                </div>
            </div>
          

            <section class="col-lg-6 connectedSortable mt-4 ">
                <div class="card">
                
                    <div class="card-header">
                        <h3 style="font-weight: 600;" class="card-title ">
                            <i  class=" "></i> Province-Wise Purchase Cost
                        </h3>
                    </div>
                
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 300px;">
                            <canvas id="province-purchase-cost-chart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </section>
           
        </div>
        <div class="row">
            <!-- Third Graph -->
            <div class="col-lg-6 mt-4">
                <div class="chart-container">
                    <div id="third-chart"></div>
                </div>
            </div>
        </div>
        
       
    </div>
</section>
<style>
     .chart-container {
        background-color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        min-height: 350px;       
    }
    .small-box>.inner {
        height: 20vh;
        padding: 10px;
    }
</style>
<!-- Include Chart.js and ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 

<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentMonth = new Date().toLocaleString('default', { month: 'short' });

    // Static data for September and October
    const staticData = {
        payoutWithoutTax: {
            September:  1065,  // Static value for September
            October: 1186,    // Static value for October
        },
        payoutWithTax: {
            September: 1190,  // Static value for September (with tax)
            October: 1287,    // Static value for October (with tax)
        }
    };

    // Dynamic data (from backend)
    const dynamicPayoutWithoutTax = Math.round({{ $totalPayoutAllRetailers }});  // Dynamic value for current month
    const dynamicPayoutWithTax = Math.round({{ $totalPayoutWithTaxAllRetailers }});  // Dynamic value for current month

    // ApexCharts Config for First Chart (with static and dynamic data)
    const apexOptions = {
        series: [
            {
                name: 'Payout (Without Tax)',
                data: [
                    staticData.payoutWithoutTax.September,  // Static value for September
                    staticData.payoutWithoutTax.October,    // Static value for October
                    dynamicPayoutWithoutTax                 // Current month dynamic value
                ]
            },
            {
                name: 'Payout (With Tax)',
                data: [
                    staticData.payoutWithTax.September,  // Static value for September
                    staticData.payoutWithTax.October,    // Static value for October
                    dynamicPayoutWithTax                 // Current month dynamic value
                ]
            }
        ],
        chart: {
            type: 'bar',
            height: 350
        },
        xaxis: {
            categories: ['Aug', 'Sep', 'Oct']  // Include September, October, and current month labels
        },
        title: {
        text: 'Yearly Payouts 2024',
       // Center the title
        margin: 10,      // Optional: Adjust margin
        style: {
            fontSize: '18px',   // Customize font size
            fontWeight: 'bold', // Customize font weight
            color: '#333'       // Optional: Set color
        }
    },
        yaxis: {
            title: {
                text: '$(Thousands)'
            }
        },
        tooltip: {
            y: {
                formatter: val => "$" + val.toFixed(2)  // Round to 2 decimal places in tooltip
            }
        }
    };

    // Render Apex Chart
    new ApexCharts(document.querySelector("#chart"), apexOptions).render();




    // Province-Wise Purchase Cost Chart
    const provinceData = {
        Alberta: {{ $totalPurchaseCostByProvince['Alberta'] ?? 0 }},
        Ontario: {{ $totalPurchaseCostByProvince['Ontario'] ?? 0 }},
        "British Columbia": {{ $totalPurchaseCostByProvince['British Columbia'] ?? 0 }},
        Manitoba: {{ $totalPurchaseCostByProvince['Manitoba'] ?? 0 }},
        Saskatchewan: {{ $totalPurchaseCostByProvince['Saskatchewan'] ?? 0 }}
    };

    // Round the province-wise purchase costs
    const provincePurchaseCosts = Object.values(provinceData).map(value => Math.round(value));

    const provinceLabels = Object.keys(provinceData);

    new Chart(document.getElementById('province-purchase-cost-chart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: provinceLabels,
            datasets: [{
                label: '',
                data: provincePurchaseCosts,  // Rounded values
                backgroundColor: ['#FF6347', '#3b8bba', '#FFD700', '#32CD32', '#8A2BE2'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins:{
            legend: {
                display:false
            }},
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const topProducts = @json($topProducts); // Pass the top products array from PHP to JavaScript

// Extract product names and total purchases from the data
const productNames = topProducts.map(product => product.product_name); // Extract product names
const productPurchases = topProducts.map(product => Math.round(product.total_purchases));  // Round purchase totals

console.log(productNames); // For debugging, ensure product names are correct

const thirdChartOptions = {
    series: [{
        name: 'Total Purchases',
        data: productPurchases // Rounded values
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            borderRadiusApplication: 'end',
            horizontal: true,
        }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '12px',
            fontWeight: 'bold',
            colors: ['#fff']
        },
        formatter: function(val) {
            return val + " units";
        },
        dropShadow: {
            enabled: true,
            top: 2,
            left: 2,
            blur: 3,
            opacity: 0.5
        }
    },
    title: {
        text: 'Top 5 Purchased Products October',
        margin: 10,
        style: {
            fontSize: '18px',
            fontWeight: 'bold',
            color: '#333'
        }
    },
    xaxis: {
        categories: productNames, // Use product names instead of SKUs
        labels: {
            style: {
                fontWeight: 'bold'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Products' // Rename Y-axis title if necessary
        }
    },
    tooltip: {
        y: {
            formatter: val => val + " units"
        }
    }
};

// Render the chart
new ApexCharts(document.querySelector("#third-chart"), thirdChartOptions).render();



});




</script>
@endsection
