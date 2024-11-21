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
                        <h3>{{ number_format($totalPayoutWithTaxAllRetailers, 2) }}</sup></h3>
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
                        <h3 class="text-white">{{ number_format($totalIrccDollarAllRetailers, 2) }}</h3>
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
</style>
<!-- Include Chart.js and ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentMonth = new Date().toLocaleString('default', { month: 'short' });

        // ApexCharts Config
        const apexOptions = {
            series: [
                {
                    name: 'Payout (Without Tax)',
                    data: [{{ $totalPayoutAllRetailers }}]
                },
                {
                    name: 'Payout (With Tax)',
                    data: [{{ $totalPayoutWithTaxAllRetailers }}]
                }
            ],
            chart: {
                type: 'bar',
                height: 350
            },
            xaxis: {
                categories: [currentMonth]
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                }
            },
            tooltip: {
                y: {
                    formatter: val => "$ " + val.toFixed(2)
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

        const provinceLabels = Object.keys(provinceData);
        const provincePurchaseCosts = Object.values(provinceData);

        new Chart(document.getElementById('province-purchase-cost-chart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: provinceLabels,
                datasets: [{
                    label: 'Purchase Cost by Province',
                    data: provincePurchaseCosts,
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
    });
</script>


<style>
    .small-box>.inner {
        height: 20vh;
        padding: 10px;
    }
</style>

@endsection
