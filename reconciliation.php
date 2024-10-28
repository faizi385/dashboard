<?php

require __DIR__.'/vendor/autoload.php';

// Load the application
$app = require_once __DIR__.'/bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set up the database connection
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

// Make sure Eloquent is bootstrapped
Model::setConnectionResolver($app['db']);

// Include the GreenLine reconciliation logic
require __DIR__.'/GreenLineReconciliation.php'; // Adjust the path as necessary

// // Include the TechPos reconciliation logic
// require __DIR__.'/TechPosReconciliation.php'; // Adjust the path as necessary

require __DIR__.'/BarnetReconciliation.php'; 
require __DIR__.'/OtherPOSReconciliation.php'; 

// require __DIR__.'/ProfitTechReconciliation.php'; 

print_r('Reconciliation process completed successfully.');
