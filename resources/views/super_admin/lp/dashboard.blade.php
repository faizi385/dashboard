@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-white">LP Dashboard</h1>
    
    <!-- Chart Container -->
    <div class="chart-container col-lg-6">
        <div id="chart"></div>
    </div>
</div>



<style>
    /* Custom container style to center the chart and make it half width */
    .chart-container {
        background-color: white; /* Set the background color to white */
        padding: 20px; /* Add some padding around the chart */
        border-radius: 8px; /* Optional: rounded corners for a sleek look */
    }
</style>



<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Ensure dates are in a proper format (e.g., "YYYY-MM-DD") for consistency
    var rawDates = @json($purchases->pluck('date')); // Raw dates from the server
    var rawPurchases = @json($purchases->pluck('purchase')); // Raw purchase data from the server

    // Format dates to "YYYY-MM" (to group by month)
    var formattedDates = rawDates.map(function(date) {
        var formattedDate = new Date(date);
        return formattedDate.getFullYear() + '-' + (formattedDate.getMonth() + 1).toString().padStart(2, '0'); // "2023-01", "2023-02", etc.
    });

    var monthlyPurchases = {};

    // Aggregate purchase data by month (formatted as "YYYY-MM")
    rawDates.forEach(function(date, index) {
        var month = formattedDates[index];
        if (!monthlyPurchases[month]) {
            monthlyPurchases[month] = 0;
        }
        monthlyPurchases[month] += rawPurchases[index];
    });

    // Get the distinct months and purchases
    var distinctMonths = Object.keys(monthlyPurchases);
    var purchases = distinctMonths.map(function(month) {
        return monthlyPurchases[month];
    });

    // Total sum of purchases
    var totalPurchases = purchases.reduce(function(a, b) { return a + b; }, 0);

    var displayDates = distinctMonths.map(function(month) {
        var [year, monthNum] = month.split('-');
        return new Date(year, monthNum - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
    });

    // ApexCharts options
    var options = {
        series: [{
            name: "Purchases",
            data: purchases // Sum of purchases for each month
        }],
        chart: {
            type: 'area',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        title: {
            text: 'Total Purchases Over Time',
            align: 'left'
        },
        subtitle: {
            text: 'Total Purchases: ' + totalPurchases, // Display the total sum of purchases
            align: 'left'
        },
        labels: displayDates, // Use the human-readable month format
        xaxis: {
            type: 'category', // Use 'category' type for categorical x-axis values
        },
        yaxis: {
            opposite: true
        },
        legend: {
            horizontalAlign: 'left'
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
@endsection
