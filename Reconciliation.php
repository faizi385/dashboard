<?php

require __DIR__ . '/vendor/autoload.php';


$app = require_once __DIR__ . '/bootstrap/app.php';


$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


Model::setConnectionResolver($app['db']);

 require __DIR__ . '/ReconciliationPos/GreenLineReconciliation.php';
 require __DIR__ . '/ReconciliationPos/TechPosReconciliation.php';
 require __DIR__ . '/ReconciliationPos/CovaReconciliation.php';
 require __DIR__ . '/ReconciliationPos/BarnetReconciliation.php';
 require __DIR__ . '/ReconciliationPos/OtherPOSReconciliation.php';
 require __DIR__ . '/ReconciliationPos/IdealReconciliation.php';
 require __DIR__ . '/ReconciliationPos/ProfitTechReconciliation.php';
 require __DIR__ . '/ReconciliationPos/TendyReconciliation.php';
 require __DIR__ . '/ReconciliationPos/GlobalTillReconciliation.php';
print_r('ReconciliationPos process completed successfully.');
