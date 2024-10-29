<?php

require __DIR__.'/vendor/autoload.php';


$app = require_once __DIR__.'/bootstrap/app.php';


$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


Model::setConnectionResolver($app['db']);

require __DIR__.'/GreenLineReconciliation.php'; 


// require __DIR__.'/TechPosReconciliation.php'; 

// require __DIR__.'/BarnetReconciliation.php'; 
// require __DIR__.'/OtherPOSReconciliation.php'; 
// require __DIR__.'/IdealReconciliation.php'; 
// require __DIR__.'/ProfitTechReconciliation.php'; 
// require __DIR__.'/TendyReconciliation.php'; 
// require __DIR__.'/GlobalTillReconciliation.php'; 
print_r('Reconciliation process completed successfully.');
