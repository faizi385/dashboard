@extends('layouts.app')

@section('content')
<div class="container p-2">
 
    <div id="toggleBox" 
    style="position: fixed; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 1000; transition: transform 0.3s ease;">
   <i class="fas fa-lightbulb text-dark" style="font-size: 42px; animation: bounce 1.5s infinite;"></i>
</div>


<div id="smallBox" class="p-4 rounded shadow-lg" 
    style="display: none; background-color: #343a40; color: white; position: fixed; right: 50px; top: 50%; transform: translateY(-50%); width: 320px; z-index: 1000; border-radius: 10px;">
   <h5 class="text-center mb-4">Interactive Menu</h5>
   <ul class="list-unstyled mb-3">
       <li class="mb-3">
           <button id="showGraph1" class="btn btn-link text-decoration-none text-light w-100 d-flex align-items-center justify-content-start px-3 py-2 rounded">
               <i class="fas fa-chart-bar me-3 text-primary" style="font-size: 18px;"></i>
               <span>Distributors Who Availed vs Did Not Avail Deals</span>
           </button>
       </li>
       <li class="mb-3">
           <button id="showGraph2" class="btn btn-link text-decoration-none text-light w-100 d-flex align-items-center justify-content-start px-3 py-2 rounded">
               <i class="fas fa-map-marked-alt me-3 text-success" style="font-size: 18px;"></i>
               <span>Total Deals by Province</span>
           </button>
       </li>
       <li class="mb-3">
           <button id="showGraph3" class="btn btn-link text-decoration-none text-light w-100 d-flex align-items-center justify-content-start px-3 py-2 rounded">
               <i class="fas fa-credit-card me-3 text-info" style="font-size: 18px;"></i>
               <span>Deal Purchases vs Non-Deal Purchases</span>
           </button>
       </li>
   </ul>
   <div class="text-center">
       <button id="closeBox" class="btn btn-sm btn-light">Close</button>
   </div>
</div>

 
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <h5>Filter Options</h5>
        <form id="filtersForm">
            <div class="row">
                <div class="col-md-4">
                    <label for="dateFilter">Date</label>
                    <input type="month" id="dateFilter" class="form-control">
                </div>
                
                
                
             <div class="col-md-4">
    <label for="distributorFilter">Distributors</label>
    <select id="distributorFilter" name="distributor" class="form-control">
        <option value="">Select Distributor</option>
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
            <option value="{{ $province->id }}">{{ $province->name }}</option>
        @endforeach
    </select>
</div>

                
            </div>
        </form>
    </div>
    

   
    <div class="mb-4">
    </div>

 
    <div id="chart1" class="bg-white p-4 rounded shadow-sm mb-4" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-4">
                <div class="chart-container">
                    <div id="chart1-container"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart2" class="bg-white p-4 rounded shadow-sm mb-4" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-4">
                <div class="chart-container">
                    <div id="chart2-container"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="chart3" class="bg-white p-4 rounded shadow-sm mb-4" style="display: none;" >
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-4">
                <div class="chart-container">
                    <div id="chart3-container"></div>
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
    
    document.getElementById('distributorFilter').addEventListener('change', function () {
    const distributorId = this.value; 
    const provinceFilter = document.getElementById('provinceFilter');

  
    provinceFilter.innerHTML = '<option value="all" disabled>Loading...</option>';

  
    fetch(`/analytics/get-provinces?distributor_id=${distributorId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            provinceFilter.innerHTML = '<option value="all">All Provinces</option>'; 

            if (Object.keys(data).length === 0) {

                const noProvincesOption = document.createElement('option');
                noProvincesOption.value = '';
                noProvincesOption.textContent = 'No Provinces Available';
                provinceFilter.appendChild(noProvincesOption);
            } else {
        
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.id;  
                    option.textContent = province.name;  
                    provinceFilter.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching provinces:', error);
            provinceFilter.innerHTML = '<option value="all" disabled>Error loading provinces</option>';
        });
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBox = document.getElementById('toggleBox');
    const smallBox = document.getElementById('smallBox');
    const closeBox = document.getElementById('closeBox');

    toggleBox.addEventListener('click', function () {
        if (smallBox.style.display === 'block') {
            smallBox.style.display = 'none';
        } else {
            smallBox.style.display = 'block';
        }
    });

    closeBox.addEventListener('click', function () {
        smallBox.style.display = 'none';
    });

    const graph1Btn = document.getElementById('showGraph1');
    const graph2Btn = document.getElementById('showGraph2');
    const graph3Btn = document.getElementById('showGraph3');
    const chart1Div = document.getElementById('chart1');
    const chart2Div = document.getElementById('chart2');
    const chart3Div = document.getElementById('chart3');

const distributorFilter = document.getElementById('distributorFilter');
const showGraph2 = document.getElementById('showGraph2');
const showGraph3 = document.getElementById('showGraph3');
const showGraph1 = document.getElementById('showGraph1');  

distributorFilter.closest('.col-md-4').style.display = 'none';

showGraph2.addEventListener('click', function() {
    distributorFilter.closest('.col-md-4').style.display = 'block'; 
  
    document.getElementById('chart1').style.display = 'none'; 
    document.getElementById('chart3').style.display = 'none'; 
    document.getElementById('chart2').style.display = 'block'; 
});

showGraph3.addEventListener('click', function() {
    distributorFilter.closest('.col-md-4').style.display = 'block'; 

    document.getElementById('chart1').style.display = 'none'; 
    document.getElementById('chart2').style.display = 'none'; 
    document.getElementById('chart3').style.display = 'block'; 
});

showGraph1.addEventListener('click', function() {
    distributorFilter.closest('.col-md-4').style.display = 'none';
    document.getElementById('chart2').style.display = 'none'; 
    document.getElementById('chart3').style.display = 'none'; 
    document.getElementById('chart1').style.display = 'block'; 
});

    graph1Btn.addEventListener('click', function () {
        chart1Div.style.display = 'block';
        chart2Div.style.display = 'none';
        chart3Div.style.display = 'none';
    });

    graph2Btn.addEventListener('click', function () {
        chart1Div.style.display = 'none';
        chart2Div.style.display = 'block';
        chart3Div.style.display = 'none';
    });

    graph3Btn.addEventListener('click', function () {
        chart1Div.style.display = 'none';
        chart2Div.style.display = 'none';
        chart3Div.style.display = 'block';
    });

    chart1Div.style.display = 'block';  
    chart2Div.style.display = 'none'; 
    chart3Div.style.display = 'none'; 

var retailersAvailed = @json($availedRetailers); 
var retailersNotAvailed = @json($nonAvailedRetailers); 
var monthName = @json($monthName); 

var month = new Date(monthName + " 1, 2024");
var shortMonth = month.toLocaleString('en-US', { month: 'short' });

var options6 = {
    series: [
        {
            name: 'Availed Distributors',
            data: [retailersAvailed]
        },
        {
            name: 'Unavailed Distributors',
            data: [retailersNotAvailed]
        }
    ],
    chart: { type: 'bar', height: 350 },
    plotOptions: { bar: { columnWidth: '50%', endingShape: 'rounded' } },
    colors: ['rgba(0, 227, 150, 0.85)', 'rgba(0, 123, 255, 0.85)'],
    dataLabels: {
        enabled: true,
        formatter: function (val, opt) {
            return opt.seriesIndex === 0 ? "Availed" : "Unavailed";
        },
        style: { fontSize: '12px', colors: ['#FFFFFF'] }
    },
    xaxis: { categories: [shortMonth], labels: { style: { fontSize: '12px' } } },
    yaxis: { title: { text: 'Number of Retailers' } },
    title: { text: 'Distributors Who Availed vs Did Not Avail Deals', align: 'center' },
    legend: { position: 'top', horizontalAlign: 'center' }
};

var chart6 = new ApexCharts(document.querySelector("#chart1-container"), options6);
chart6.render();

document.getElementById('provinceFilter').addEventListener('change', function () {
    const province = this.value; 
    if (!province) {
        alert('Please select a province.');
        return;
    }

    console.log('Province Changed:', province);

    const selectedDate = document.getElementById('dateFilter').value || monthName;

    fetch(`/analytics/get-availed-vs-nonavailed?province=${province}&date=${selectedDate}`)
        .then(response => response.json())
        .then(data => {
            const retailersAvailed = data.availed || 0;
            const retailersNotAvailed = data.notAvailed || 0;

            console.log('Updated Data:', { retailersAvailed, retailersNotAvailed });

            chart6.updateOptions({
                series: [
                    {
                        name: 'Availed Distributors',
                        data: [retailersAvailed]
                    },
                    {
                        name: 'Unavailed Distributors',
                        data: [retailersNotAvailed]
                    }
                ]
            });
        })
        .catch(error => {
            console.error('Error fetching availed vs non-availed data:', error);
        });
});

document.getElementById('dateFilter').addEventListener('change', function () {
    const selectedDate = this.value; 
    if (!selectedDate) return; 

    console.log('Date Filter Changed:', selectedDate);

    const selectedProvince = document.getElementById('provinceFilter').value || '';

    fetch(`/analytics/get-availed-vs-nonavailed?province=${selectedProvince}&date=${selectedDate}`)
        .then(response => response.json())
        .then(data => {
            const retailersAvailed = data.availed || 0;
            const retailersNotAvailed = data.notAvailed || 0;

            console.log('Updated Data for Date:', { retailersAvailed, retailersNotAvailed });
  
            chart6.updateOptions({
                series: [
                    {
                        name: 'Availed Distributors',
                        data: [retailersAvailed]
                    },
                    {
                        name: 'Unavailed Distributors',
                        data: [retailersNotAvailed]
                    }
                ]
            });
        })
        .catch(error => {
            console.error('Error fetching data for selected date:', error);
        });
});

const provinceFilter = document.getElementById('provinceFilter');
const dateFilter = document.getElementById('dateFilter');
const chartContainer = document.querySelector("#chart2-container");
let chart3; 

const fetchFilteredChartData = (date, province) => {
    return new Promise((resolve, reject) => {
        axios
            .post('/api/get-chart-data', { date, province })
            .then(response => resolve(response.data))
            .catch(error => {
                console.error('Error fetching data:', error);
                reject(error);
            });
    });
};

const renderChart2 = (filteredLabels, filteredData) => {
    const options = {
        series: [{ data: filteredData }],
        chart: { height: 350, type: 'bar' },
        colors: ['#104E8B', '#008B8B', '#8B0000', '#B8860B', '#006400', '#C71585', '#FF4500', '#4B0082'],
        plotOptions: { bar: { columnWidth: '45%', distributed: true } },
        dataLabels: {
            enabled: true,
            style: { fontSize: '12px' },
            formatter: val => val.toFixed(2),
        },
        legend: { show: false },
        xaxis: { categories: filteredLabels, labels: { style: { fontSize: '12px' } } },
        title: { text: 'Total Deals by Province', align: 'left' },
    };

    if (chart3) chart3.destroy();  
    chart3 = new ApexCharts(chartContainer, options);
    chart3.render();
};

const applyFilters = () => {
    const selectedProvince = provinceFilter.value;
    const selectedDate = dateFilter.value;

    console.log('Selected Province:', selectedProvince); 
    console.log('Selected Date:', selectedDate); 

    fetchFilteredChartData(selectedDate, selectedProvince)
        .then(data => {
       
            if (data.labels.length === 0) {
                console.log('No data available for the selected filters');
                renderChart2([], []);  
            } else {
                renderChart2(data.labels, data.data);  
            }
        })
        .catch(error => {
      
            console.error('Error fetching filtered chart data:', error);
            renderChart2([], []);
        });
};

provinceFilter.addEventListener('change', applyFilters);
dateFilter.addEventListener('change', applyFilters);

renderChart2(@json($offerProvinceLabels), @json($offerData));  

    const dealPurchases = @json($dealPurchases); 
    const nonDealPurchases = @json($nonDealPurchases); 

    var options = {
        series: [{
            name: 'Deal Purchases',
            data: [dealPurchases]
        }, {
            name: 'Non-Deal Purchases',
            data: [nonDealPurchases]
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Purchases'],
        },
        yaxis: {
            title: {
                text: 'Purchase Amount ($)'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$ " + val + ""
                }
            }
        },
        dataLabels: { 
                enabled: true, 
                style: { fontSize: '12px' },
                formatter: function (val) { return val.toFixed(2); }
            },
        title: { text: 'Deals Purchases vs Non-Deals Purchases', align: 'center' },
    };

    var chart = new ApexCharts(document.querySelector("#chart3-container"), options);
    chart.render();
});

</script>
@endsection
