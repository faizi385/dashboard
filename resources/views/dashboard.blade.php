@extends('layouts.app')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid p-2">
        <h1 class="text-white text-center mb-4">Super Admin Dashboard</h1>

        <!-- Scrollable Cards -->
        <div class="scrollable-container mb-4">
            <div class="scrollable-cards">
                {{-- <!-- Total Payout (With Tax) -->
                <div class="col-lg-3 col-6 pickable-card">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 class="text-center">${{ number_format(round($totalPayoutWithTaxAllRetailers), 2) }}</h3>
                            <p class="text-center">Total Payout (With Tax)</p>
                        </div>
                    </div>
                </div> --}}

                <!-- Overall Revenue -->
                <div class="col-lg-3 col-6 pickable-card">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3 class="text-white text-center">${{ number_format(round($totalIrccDollarAllRetailers), 2) }}</h3>
                            <p class="text-white text-center">Overall Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Availed Deals -->
                <div class="col-lg-3 col-6 pickable-card">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 class="text-center">{{ $totalMappedOffers }}</h3>
                            <p class="text-center">Availed Deals</p>
                        </div>
                    </div>
                </div>

                <!-- Unavailed Deals -->
                <div class="col-lg-3 col-6 pickable-card">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3 class="text-center">{{ $totalUnmappedOffers }}</h3>
                            <p class="text-center">Unavailed Deals</p>
                        </div>
                    </div>
                </div>
     
                    <div class="col-lg-3 col-6 pickable-card">
                        <div style="background-color: #1F509A" class="small-box">
                            <div class="inner">
                                <h3 class="text-center text-white">{{ $totalDeals }}</h3> <!-- Display total deals -->
                                <p class="text-center text-white">Overall Deals</p>
                            </div>
                        </div>
                    </div>
            
            <!-- Overall Revenue -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3 class="text-center">${{ number_format(round($totalIrccDollarAllRetailers), 2) }}</h3>
                        <p class="text-center">Overall Revenue</p>
                    </div>
                    <div class="icon">
                        <i class=""></i>
                    </div>
                </div>
            </div>

            </div>
        </div>
        
        <!-- Area Charts -->
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
                    <div id="province-purchase-cost-chart"></div>
                </div>
            </div>
        </div>

        <!-- Additional Charts -->
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
    .small-box {
        background-color: white !important;
        color: rgb(38, 160, 252) !important;
        display: flex;
        align-items: center; 
        justify-content: center; 
        height: 100%;
        text-align: center; 
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .small-box .inner {
        display: flex;
        flex-direction: column;
        align-items: center; 
        justify-content: center; 
        width: 100%;
        height: 100%;
    }

     .chart-container {
        background-color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        min-height: 350px;       
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

    const provinceData = {
    Alberta: {{ $totalPurchaseCostByProvince['Alberta'] ?? 0 }},
    Ontario: {{ $totalPurchaseCostByProvince['Ontario'] ?? 0 }},
    "British Columbia": {{ $totalPurchaseCostByProvince['British Columbia'] ?? 0 }},
    Manitoba: {{ $totalPurchaseCostByProvince['Manitoba'] ?? 0 }},
    Saskatchewan: {{ $totalPurchaseCostByProvince['Saskatchewan'] ?? 0 }}
};

// Prepare data for the chart
const provincePurchaseCosts = Object.entries(provinceData).map(([province, cost]) => ({
    x: province,
    y: Math.round(cost) // Round values
}));

// Chart options
var options = {
    series: [{
        name: "Purchase Cost",
        data: provincePurchaseCosts
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    xaxis: {
        type: 'category',
        labels: {
            style: {
                fontSize: '12px',
                fontWeight: 700,
                colors: ['#304758']
            },
            formatter: function(val) {
                return val; // Use province names directly
            }
        },
        group: {
            style: {
                fontSize: '10px',
                fontWeight: 700
            },
            groups: [
                { title: 'Provinces', cols: 5 } // Group label spanning all provinces
            ]
        }
    },
    title: {
        text: 'Province-Wise Purchase Costs',
        align: 'center',
        style: {
            fontSize: '18px',
            color: '#444'
        }
    },
    tooltip: {
        x: {
            formatter: function(val) {
                return val; // Display province name
            }
        }
    }
};

// Render the chart
var chart = new ApexCharts(document.querySelector("#province-purchase-cost-chart"), options);
chart.render();


    const topProducts = @json($topProducts); 


const productNames = topProducts.map(product => product.product_name); 
const productPurchases = topProducts.map(product => Math.round(product.total_purchases));  

console.log(productNames); 

const thirdChartOptions = {
    series: [{
        name: 'Total Purchases',
        data: productPurchases
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
        categories: productNames, 
        labels: {
            style: {
                fontWeight: 'bold'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Products' 
        }
    },
    tooltip: {
        y: {
            formatter: val => val + " units"
        }
    }
};

new ApexCharts(document.querySelector("#third-chart"), thirdChartOptions).render();



});




</script>
@endsection
