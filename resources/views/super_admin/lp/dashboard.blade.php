@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h1 class="text-white text-center mb-4">Supplier Dashboard</h1>
    <div class="row">
        <!-- Total Payout (With Tax) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 class="text-center">${{ number_format($totalRevenue, 2) }}</h3>
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
                    <h3 class="text-center">{{ $totalDistributors }}</h3>
                    <p class="text-center">Total Distributors</p>
                </div>
                <div class="icon">
                    <i class=""></i> <!-- Use an appropriate FontAwesome icon -->
                </div>
            </div>
        </div>
        

    <div class="scrollable-container ">
        <div class="scrollable-cards">
            <!-- Total Revenue -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 class="text-center">${{ number_format($totalRevenue, 2) }}</h3>
                        <p class="text-center">Total Revenue</p>
                    </div>
                </div>
            </div>
            <!-- Total Distributors -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3 class="text-white text-center">{{ $totalDistributors }}</h3>
                        <p class="text-white text-center">Total Distributors</p>
                    </div>
                </div>
            </div>
            <!-- Total Carevouts -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 class="text-center">{{ $totalCarevouts }}</h3>
                        <p class="text-center">Total Carevouts</p>
                    </div>
                </div>
            </div>
            <!-- Total Reports Submitted -->
            <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 class="text-center">{{ $totalReportsSubmitted }}</h3>
                        <p class="text-center">Total Reports Submitted</p>
                    </div>
                </div>
            </div>
            <!-- Total Deals -->
            <div class="col-lg-3 col-6 pickable-card">
                <div style="background-color: #1F509A"  class="small-box ">
                    <div class="inner">
                        <h3 class="text-center text-white">{{ $totalDeals }}</h3>
                        <p class="text-center text-white">Total Deals</p>
                    </div>
                </div>
            </div>
            <!-- Additional Cards -->
            {{-- <div class="col-lg-3 col-6 pickable-card">
                <div class="small-box bg-info">
                    <div class="inner">
                        <p class="text-center">Additional Data</p>
                    </div>
                </div>
            </div> --}}
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
        <div class="row gy-4 gx-5 mt-3">
     
            <div class="col-lg-6">
                <div class="chart-container">
                    <div id="chart5"></div>
                </div>
            </div>
    
            <div class="col-lg-6">
                <div class="chart-container">
                    <div id="chart6"></div>
                </div>
            </div>
        </div>
        <div class="row gy-4 gx-5 mt-3">
     
            <div class="col-lg-6">
                <div class="chart-container">
                    <div id="chart7"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <div id="chart8"></div>
                </div>
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
        margin-top: 20px;
    }
    #chart1, #chart2, #chart3, #chart4 {
        min-height: 350px;
    }
    .small-box {
        background-color: white !important;
        color: rgb(38, 160, 252) !important;
        display: flex;
        align-items: center; 
        justify-content: center; 
        height: 100%;
        text-align: center; 
    
    }

    .small-box .inner {
        display: flex;
        flex-direction: column;
        align-items: center; 
        justify-content: center; 
        width: 100%;
        height: 100%;
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

// Updated with darker colors
var colors = ['#104E8B', '#008B8B', '#8B0000', '#B8860B', '#006400', '#C71585', '#FF4500', '#4B0082'];

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

var availedOffers = @json($availedOffers); // Replace with actual data for availed deals 
var unavailedOffers = @json($totalUnmappedOffers); // Replace with actual data for unavailed deals
var monthName = @json($monthName); // Fetch the dynamic month name (e.g., "October" or "Oct")
var month = new Date(monthName + " 1, 2024"); // Assuming monthName is like "October 2024"
var shortMonth = month.toLocaleString('en-US', { month: 'short' });
var options = {
    series: [{
        name: 'Availed Deals',
        data: [availedOffers] // Single value for availed deals
    }, {
        name: 'Unavailed Deals',
        data: [unavailedOffers] // Single value for unavailed deals
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            endingShape: 'rounded'
        }
    },
    colors: ['rgba(0, 227, 150, 0.85)', 'rgba(0, 123, 255, 0.85)'], // Custom colors for availed and unavailed deals
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return val + " Deals"; // Format data label to include 'Deals' text
        },
        style: {
            fontSize: '12px',
            colors: ['#FFFFFF'] // Adjust text color if necessary
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " Deals"; // Add "Deals" in tooltip as well
            }
        }
    },
    xaxis: {
        categories: [shortMonth], // Use dynamic month name from PHP
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Number of Deals'
        }
    },
    title: {
        text: 'Availed vs Unavailed Deals',
        align: 'center'
    },
    legend: {
        position: 'top',
        horizontalAlign: 'center'
    }
};

var chart = new ApexCharts(document.querySelector("#chart5"), options);
chart.render();


var retailersAvailed = @json($availedRetailers); // Fetch number of retailers who availed deals
var retailersNotAvailed = @json($nonAvailedRetailers); // Fetch number of retailers who did not avail deals
var monthName = @json($monthName); // Fetch the month name dynamically

// Create a Date object using the monthName and extract the abbreviated month (e.g., "Oct")
var month = new Date(monthName + " 1, 2024"); // Assuming monthName is like "October 2024"
var shortMonth = month.toLocaleString('en-US', { month: 'short' }); // Get the abbreviated month name

var options6 = {
    series: [{
        name: 'Availed Offers',
        data: [retailersAvailed]
    }, {
        name: 'Unavailed Offers',
        data: [retailersNotAvailed]
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            endingShape: 'rounded'
        }
    },
    colors: ['rgba(0, 227, 150, 0.85)', 'rgba(0, 123, 255, 0.85)'], // Updated with a dark blue color
    dataLabels: {
        enabled: true,
        formatter: function(val) { return val; }, // Show only the count numbers
        style: { fontSize: '12px', colors: ['#FFFFFF'] }
    },
    xaxis: {
        categories: [shortMonth], // Display the dynamically formatted month
        labels: {
            style: { fontSize: '12px' }
        }
    },
    yaxis: {
        title: { text: 'Number of Retailers' }
    },
    title: {
        text: 'Distributors Who Availed vs Did Not Avail Deals',
        align: 'center'
    },
    legend: {
        position: 'top',
        horizontalAlign: 'center'
    }
};

var chart6 = new ApexCharts(document.querySelector("#chart6"), options6);
chart6.render();

var noDealProducts = @json($noDealProducts);
var productNames = noDealProducts.map(product => product.product_name.length > 15 ? product.product_name.substring(0, 15) + '...' : product.product_name);  // Truncate long names
var totalPurchases = noDealProducts.map(product => product.total_purchase);

var options7 = {
    series: [{
        name: 'Top Purchased Products with No Deals',
        data: totalPurchases
    }],
    chart: { 
        height: 350, 
        type: 'bar', 
        stacked: false // Disable stacking (bars should be side by side)
    },
    plotOptions: { 
        bar: { 
            horizontal: true,  // Set to true to make the bars horizontal
            borderRadius: 10, 
            dataLabels: { 
                position: 'top' 
            } 
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function(val) { return val; },
        offsetX: -10, // Adjust horizontal offset for data labels if needed
        style: { fontSize: '12px', colors: ["#304758"] }
    },
    xaxis: {
        categories: productNames, // Now this will be on the Y-axis
        position: 'bottom', // Position at the bottom for horizontal bars
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: {
                fontSize: '12px',
                colors: ['#304758']
            }
        }
    },
    yaxis: {
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { 
            show: true, 
            formatter: function(val) { return val; } 
        }
    },
    title: {
        text: 'Top 5 Products with No Deals ',
        floating: false, 
        offsetY: 10,    
        align: 'center',
        style: { color: '#444' }
    }
};

var chart7 = new ApexCharts(document.querySelector("#chart7"), options7);
chart7.render();

var retailerNamesWithoutDeals = @json($retailerNamesWithoutOffers); // Retailer names
var totalPurchasesWithoutDeals = @json($retailerPurchaseTotals); // Retailer purchase totals

var options = {
    series: [{
        name: 'Total Purchases (Without Deals)', 
        data: totalPurchasesWithoutDeals, // Use total purchases without deals
    }],
    chart: {
        height: 350,
        type: 'bar',
    },
    title: {
        text: 'Top 5 Distributors with No Deals', // Title of the chart
        floating: false, 
        offsetY: 10,    // Adjust vertical position
        align: 'center', // Align title to the center
        style: { 
            color: '#444', // Title color
            fontSize: '18px', // Title font size
            fontWeight: 'bold' // Title font weight
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 10,
            columnWidth: '50%',
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 0
    },
    grid: {
        row: {
            colors: ['#fff', '#f2f2f2']
        }
    },
    xaxis: {
        labels: {
            rotate: -45
        },
        categories: retailerNamesWithoutDeals, // Use retailer names as categories
        tickPlacement: 'on'
    },
    yaxis: {
        title: {
            text: 'Total Purchases (Without Deals)', // Label for the y-axis
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
        },
    }
};

var chart = new ApexCharts(document.querySelector("#chart8"), options);
chart.render();


</script>
@endsection
