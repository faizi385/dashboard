@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h1 class="text-white text-center mb-4">Supplier Dashboard</h1>
    <div class="row">
        <!-- Total Payout (With Tax) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 class="text-center"> </h3>
                    <p class="text-center">Total Revenue</p>
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
                    {{-- <h3 class="text-white text-center">${{ number_format(round($totalIrccDollarAllRetailers), 2) }}</h3> --}}
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
                    {{-- <h3 class="text-center">{{ $totalMappedOffers }}</h3> --}}
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
                    {{-- <h3 class="text-center">{{ $totalUnmappedOffers }}</h3> --}}
                    <p class="text-center">Unavailed Deals</p>
                </div>
                <div class="icon">
                    <i class=""></i>
                </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 gx-5"> <!-- gy-4 adds vertical spacing, gx-5 adds horizontal spacing -->
        <!-- First Chart Container -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div id="chart1"></div>
            </div>
        </div>

        <!-- Second Chart Container -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div id="chart2"></div>
            </div>
        </div>
    </div>

    <div class="row gy-4 gx-5 mt-3">
        <!-- Third Chart Container -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div id="chart3"></div>
            </div>
        </div>

        <!-- Fourth Chart Container -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div id="chart4"></div>
            </div>
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
    #chart1, #chart2, #chart3, #chart4 {
        min-height: 350px;
    }
     .small-box>.inner {
        height: 20vh;
        padding: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    var displayDates = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var rawDates = @json($purchases->pluck('reconciliation_date'));
    var rawDates = @json(['2024-10-01']);
    var rawPurchases = @json($purchases->pluck('purchase'));
    var monthlyPurchases = {};

    rawDates.forEach(function(date, index) {
        var parsedDate = new Date(date);
        if (!isNaN(parsedDate)) {
            var monthIndex = parsedDate.getMonth();
            monthlyPurchases[monthIndex] = (monthlyPurchases[monthIndex] || 0) + rawPurchases[index];
        }
    });

    var purchases = Array.from({ length: 12 }, (_, i) => monthlyPurchases[i] || 0);
    var totalPurchases = purchases.reduce((a, b) => a + b, 0);

    var options1 = {
        series: [{ name: "Purchases", data: purchases }],
        chart: { type: 'area', height: 350 },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        title: { text: 'Total Purchases Over Time', align: 'left' },
        subtitle: { text: 'Total Purchases: ' + totalPurchases.toFixed(2), align: 'left' },
        labels: displayDates,
        xaxis: { type: 'category' },
        yaxis: { opposite: true },
        legend: { horizontalAlign: 'left' }
    };
    var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
    chart1.render();

    var provinceLabels = @json($provinces);
var provincePurchases = @json($purchaseData);

var options2 = {
    series: [{ data: provincePurchases }],
    chart: { type: 'bar', height: 350 },
    plotOptions: { bar: { borderRadius: 4, horizontal: true } },
    dataLabels: { 
        enabled: true,              // Enable data labels
        style: { fontSize: '12px' }, // Adjust font size
        formatter: function(val) {
            return val.toFixed(2); // Display the value with 2 decimal points
        }
    },
    xaxis: { categories: provinceLabels },
    title: { text: 'Purchases by Province - October', align: 'left' }
};
var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
chart2.render();

var offerProvinceLabels = @json($offerProvinceLabels);
var offerData = @json($offerData);
var colors = ['#1E90FF', '#00CED1', '#DC143C', '#FFD700', '#32CD32', '#FF69B4', '#FF6347', '#8A2BE2'];

var options3 = {
    series: [{ data: offerData }],
    chart: { height: 350, type: 'bar' },
    colors: colors,
    plotOptions: { bar: { columnWidth: '45%', distributed: true } },
    dataLabels: { 
        enabled: true,              // Enable data labels
        style: { fontSize: '12px' }, // Adjust font size
        formatter: function(val) {
            return val.toFixed(2); // Display the value with 2 decimal points
        }
    },
    legend: { show: false },
    xaxis: { categories: offerProvinceLabels, labels: { style: { colors: colors, fontSize: '12px' } } },
    title: { text: 'Total Deals by Province - October', align: 'left' }
};
var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
chart3.render();

var retailerNames = @json($retailerNames);
var retailerOfferCounts = @json($retailerOfferCounts);

var options4 = {
    series: [{
        name: 'Deals',
        data: retailerOfferCounts
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    dataLabels: {
        enabled: true,              // Enable data labels
        formatter: function (val) {
            return val + " Deals";  // Display the value with text
        },
        offsetY: -20,               // Adjust position if necessary
        style: {
            fontSize: '12px',        // Adjust font size
            colors: ["#FFFFFF"]      // Set text color to white
        }
    },
    xaxis: {
        categories: retailerNames,
        position: 'bottom'
    },
    yaxis: {
        labels: { 
            formatter: function (val) { return val + " Deals"; } 
        }
    },
    title: {
        text: 'Top 5 Distributors with Deals - October',
    }
};

var chart4 = new ApexCharts(document.querySelector("#chart4"), options4);
chart4.render();

</script>
@endsection
