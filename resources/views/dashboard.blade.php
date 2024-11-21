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
                        <h3>{{ number_format(round($totalPayoutWithTaxAllRetailers), 2) }}</h3>
                        <p>Total Payout (With Tax)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </div>
            
            <!-- Overall Revenue -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="text-white">{{ number_format(round($totalIrccDollarAllRetailers), 2) }}</h3>
                        <p class="text-white">Overall Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                </div>
            </div>

            <!-- Availed Deals -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $totalMappedOffers }}</h3>
                        <p>Availed Deals</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-map"></i>
                    </div>
                </div>
            </div>

            <!-- Unavailed Deals -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $totalUnmappedOffers }}</h3>
                        <p>Unavailed Deals</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-close"></i>
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
            <!-- Province-Wise Purchase Cost Chart -->
            <section class="col-lg-6 connectedSortable mt-4 ">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i> Province-Wise Purchase Cost
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
            September: 65,  // Static value for September
            October: 86,    // Static value for October
        },
        payoutWithTax: {
            September: 78,  // Static value for September (with tax)
            October: 98,    // Static value for October (with tax)
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
            categories: ['Sep', 'Oct', currentMonth]  // Include September, October, and current month labels
        },
        yaxis: {
            title: {
                text: '$ (thousands)'
            }
        },
        tooltip: {
            y: {
                formatter: val => "$ " + val.toFixed(2)  // Round to 2 decimal places in tooltip
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
                label: 'Purchase Cost by Province',
                data: provincePurchaseCosts,  // Rounded values
                backgroundColor: ['#FF6347', '#3b8bba', '#FFD700', '#32CD32', '#8A2BE2'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const topProducts = @json($topProducts); // Pass the top products array from PHP to JavaScript
const productSKUs = topProducts.map(product => product.sku);
const productPurchases = topProducts.map(product => Math.round(product.total_purchases));  // Round purchase totals

const thirdChartOptions = {
    series: [{
        name: 'Total Purchases',
        data: productPurchases  // Rounded values
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
        enabled: false
    },
    title: {
        text: 'Top 5 Products',
        align: 'center', // Center the title
        margin: 10,      // Optional: Adjust margin
        style: {
            fontSize: '18px',   // Customize font size
            fontWeight: 'bold', // Customize font weight
            color: '#333'       // Optional: Set color
        }
    },
    xaxis: {
        categories: productSKUs, // Use SKUs as categories
        // categories: ['Vape', 'Gum', '11144', '55544', '88888'],
        labels: {
            style: {
                fontWeight: 'bold' // Bold the SKUs in the x-axis
            }
        }
    },
    yaxis: {
        title: {
            text: 'Units Sold' // Rename the y-axis title if necessary
        }
    },
    tooltip: {
        y: {
            formatter: val => val + " units"  // Show units in tooltip
        }
    }
};

new ApexCharts(document.querySelector("#third-chart"), thirdChartOptions).render();

});




</script>
@endsection
