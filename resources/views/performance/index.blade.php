@extends('layouts.app')

@section('content')
<div class="container p-2">
    <!-- Unique Icon -->
    <div id="toggleBox" 
    style="position: fixed; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 1000; transition: transform 0.3s ease;">
   <i class="fas fa-lightbulb text-dark" style="font-size: 42px; animation: bounce 1.5s infinite;"></i>
</div>

<!-- Hidden box -->
<div id="smallBox" class="p-4 rounded shadow-lg" 
    style="display: none; background-color: #343a40; color: white; position: fixed; right: 50px; top: 50%; transform: translateY(-50%); width: 320px; z-index: 1000; border-radius: 10px;">
   <h5 class="text-center mb-4">Interactive Menu</h5>
   <ul class="list-unstyled mb-3">
       <li class="mb-3">
           <button id="showGraph1" class="btn btn-link text-decoration-none text-light w-100 d-flex align-items-center justify-content-start px-3 py-2 rounded">
               <i class="fas fa-chart-bar me-3 text-primary" style="font-size: 18px;"></i>
               <span>Province Wise Purchase Cost</span>
           </button>
       </li>
       <li class="mb-3">
           <button id="showGraph2" class="btn btn-link text-decoration-none text-light w-100 d-flex align-items-center justify-content-start px-3 py-2 rounded">
               <i class="fas fa-map-marked-alt me-3 text-success" style="font-size: 18px;"></i>
               <span>Total Deals by Province</span>
           </button>
       </li>
    
   </ul>
   <div class="text-center">
       <button id="closeBox" class="btn btn-sm btn-light">Close</button>
   </div>
</div>

    <!-- Filter Section -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <h5>Filter Options</h5>
        <form id="filtersForm">
            <div class="row">
                <div class="col-md-4">
                    <label for="dateFilter">Date</label>
                    <select id="dateFilter" class="form-control">
                        <!-- Example Date Options -->
                        <option value="october">October</option>
                        <option value="november">November</option>
                        <option value="december">December</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="distributorFilter">Distributors</label>
                    <select id="distributorFilter" name="distributor" class="form-control">
                        <option value="">All Distributors</option>
                        <!-- Dynamically populate distributors from the passed data -->
                        @foreach ($distributors as $id => $name)
                            <option value="{{ $id }}" {{ isset($selectedDistributor) && $selectedDistributor == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="provinceFilter">Province</label>
                    <select id="provinceFilter" class="form-control">
                        <option value="all">All Provinces</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->name }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
    


    <div class="mb-4">
    </div>

    <!-- Chart Section -->
    <div id="chart1" class="bg-white p-4 rounded shadow-sm mb-4" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-8 mt-4">
                <div class="chart-container">
                    <div id="province-purchase-cost-chart"></div>
                </div>
            </div>
        </div>
    </div>

<div id="chart2" class="bg-white p-4 rounded shadow-sm mb-4" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-8 mt-4">
                <div class="chart-container">
                    <div id="chart2-container"></div>
                </div>
            </div>
        </div>
    </div>

</div>
    <style>
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBox = document.getElementById('toggleBox');
    const smallBox = document.getElementById('smallBox');
    const closeBox = document.getElementById('closeBox');

    // Toggle the visibility of the box when the icon is clicked
    toggleBox.addEventListener('click', function () {
        if (smallBox.style.display === 'block') {
            smallBox.style.display = 'none';
        } else {
            smallBox.style.display = 'block';
        }
    });

    // Hide the box when the close button is clicked
    closeBox.addEventListener('click', function () {
        smallBox.style.display = 'none';
    });

    // Graphs visibility logic
    const graph1Btn = document.getElementById('showGraph1');
    const graph2Btn = document.getElementById('showGraph2');
    const graph3Btn = document.getElementById('showGraph3');
    const chart1Div = document.getElementById('chart1');
    // const chart2Div = document.getElementById('chart2');
    // const chart3Div = document.getElementById('chart3');

    graph1Btn.addEventListener('click', function () {
        chart1Div.style.display = 'block';
        chart2Div.style.display = 'none';
        chart3Div.style.display = 'none';
    });

    // graph2Btn.addEventListener('click', function () {
    //     chart1Div.style.display = 'none';
    //     chart2Div.style.display = 'block';
    //     chart3Div.style.display = 'none';
    // });

    // graph3Btn.addEventListener('click', function () {
    //     chart1Div.style.display = 'none';
    //     chart2Div.style.display = 'none';
    //     chart3Div.style.display = 'block';
    // });
    // Initial rendering for both graphs (no need to set one as the default)
    chart1Div.style.display = 'block';  // Graph 1 visible
    // chart2Div.style.display = 'block';  // Graph 2 visible
    // chart3Div.style.display = 'block'; 


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

});

</script>
@endsection
