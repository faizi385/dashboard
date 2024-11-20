@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h1 class="text-white text-center mb-4">Supplier Dashboard</h1>
    
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
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    var displayDates = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var rawDates = @json($purchases->pluck('date'));
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
        dataLabels: { enabled: false },
        xaxis: { categories: provinceLabels },
        title: { text: 'Purchases by Province', align: 'left' }
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
        dataLabels: { enabled: false },
        legend: { show: false },
        xaxis: { categories: offerProvinceLabels, labels: { style: { colors: colors, fontSize: '12px' } } },
        title: { text: 'Total Deals by Province', align: 'left' }
    };
    var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
    chart3.render();

    
    var retailerNames = @json(  $retailerNames); 
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
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val + " Deals";
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: retailerNames,
            position: 'top'
        },
        yaxis: {
            labels: { formatter: function (val) { return val + " deals"; } }
        },
        title: {
            text: 'Top 5 Distributor with Deals',
            align: 'center'
        }
    };

    var chart4 = new ApexCharts(document.querySelector("#chart4"), options4);
    chart4.render();


</script>
@endsection
