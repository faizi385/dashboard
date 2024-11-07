@extends('layouts.app')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            {{-- <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($totalPayoutAllRetailers, 2) }}</h3>
                        <p>Total Payout (Without Tax)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div> --}}
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($totalPayoutWithTaxAllRetailers, 2) }}</sup></h3>
                        <p>Total Payout </p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="text-white">{{ number_format($totalIrccDollarAllRetailers, 2) }}</h3>
                        <p class="text-white">Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
           
             <!-- Mapped Offers -->
             <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $totalMappedOffers }}</h3>
                        <p>Mapped Offers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-map"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Unmapped Offers -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $totalUnmappedOffers}}</h3>
                        <p>Unmapped Offers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-close"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->
        
        <!-- Area Chart -->
        <div class="row">
            <section class="col-lg-6 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Total Revenue
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                {{-- <li class="nav-item">
                                    <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                                </li> --}}
                                {{-- <li class="nav-item">
                                    <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                                <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                            </div>
                            <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                                <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-lg-6 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Total Purchase Cost 
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 300px;">
                            <canvas id="stacked-bar-chart-canvas" height="300" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.row -->

        <!-- Province-wise Purchase Cost Chart -->
        <div class="row">
            <section class="col-lg-6 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Province-wise Purchase Cost
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 300px;">
                            <canvas id="province-purchase-cost-chart" height="300" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.row -->
    </div>
</section>


<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Define all months
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        

        const currentMonthIndex = new Date().getMonth();

        // Initialize payouts data arrays with null for all months, only set current month
        const payoutsData = Array(12).fill(null);
        const payoutsDataTax = Array(12).fill(null);
        const irccDollarData = Array(12).fill(null);

        payoutsData[currentMonthIndex] = {{ $totalPayoutAllRetailers }}; 
        payoutsDataTax[currentMonthIndex] = {{ $totalPayoutWithTaxAllRetailers }}; 
        irccDollarData[currentMonthIndex] = {{ $totalIrccDollarAllRetailers }}; 
        // Area chart setup for IRCC Dollar data
        const ctx = document.getElementById('revenue-chart-canvas').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: "IRCC Dollar",
                    data: irccDollarData,
                    backgroundColor: "rgba(60,141,188,0.2)",
                    borderColor: "rgba(60,141,188,1)",
                    pointBackgroundColor: "rgba(60,141,188,1)",
                    pointBorderColor: "#3b8bba",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(60,141,188,1)",
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: true
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return `Total Revenue: ${context.raw || 0}`;
                            }
                        }
                    }
                }
            }
        });

        // Donut chart setup (Static for Product Distribution Example)
        const donutCtx = document.getElementById('sales-chart-canvas').getContext('2d');
        
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ["Product A", "Product B", "Product C"],
                datasets: [{
                    data: [300, 50, 100],
                    backgroundColor: ['#FF6347', '#3b8bba', '#FFD700'],
                    hoverBackgroundColor: ['#FF4500', '#005f87', '#FFA500']
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Stacked Bar Chart setup for Payouts and IRCC Dollar with/without tax
        const stackedCtx = document.getElementById('stacked-bar-chart-canvas').getContext('2d');

        new Chart(stackedCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Payout (Without Tax)',
                        data: payoutsData,
                        backgroundColor: "rgba(60,141,188,0.9)"
                    },
                    {
                        label: 'Payout (With Tax)',
                        data: payoutsDataTax,
                        backgroundColor: "rgba(0,255,0,0.9)" // Green color for payout with tax
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw || 0}`;
                            }
                        }
                    }
                }
            }
        });
        const purchaseCostData = {
        Alberta: {{ $totalPurchaseCostByProvince['Alberta'] }},
        Ontario: {{ $totalPurchaseCostByProvince['Ontario'] }},
        "British Columbia": {{ $totalPurchaseCostByProvince['British Columbia'] }},
        Manitoba: {{ $totalPurchaseCostByProvince['Manitoba'] }},
        Saskatchewan: {{ $totalPurchaseCostByProvince['Saskatchewan'] }}
    };

    const provinceLabels = ["Alberta", "Ontario", "British Columbia", "Manitoba", "Saskatchewan"];
    const provincePurchaseCosts = provinceLabels.map(province => purchaseCostData[province]);

    const provinceCtx = document.getElementById('province-purchase-cost-chart').getContext('2d');

    new Chart(provinceCtx, {
        type: 'bar',
        data: {
            labels: provinceLabels, 
            datasets: [{
                label: 'Purchase Cost by Province',
                data: provincePurchaseCosts,
                backgroundColor: ['#FF6347', '#3b8bba', '#FFD700', '#32CD32', '#8A2BE2'],
                borderColor: '#000000',
                borderWidth: 1,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { offset: true },
                    barPercentage: 0.8,
                    categoryPercentage: 1.0
                },
                y: { beginAtZero: true }
            },
            plugins: {
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: $${context.raw || 0}`;
                        }
                    }
                }
            }
        }
        });
    });
</script>



@endsection
