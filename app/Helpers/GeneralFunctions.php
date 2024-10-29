<?php

namespace App\Helpers;
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Storage;

class GeneralFunctions{
    public static function formatAmountValue($value)
    {
        $currencyString = trim($value);
        $currencyString = preg_replace("/[^0-9.-]/", "", $currencyString);
        $currencyDouble = (double) $currencyString;
        return $currencyDouble;
    }

    public function CleanGTIN($GTIN){
        // if($GTIN){
            $Filter_GTIN = preg_replace("/[^0-9.-]|^00/", "", $GTIN);
            $Filter_GTIN = ltrim($Filter_GTIN, '0');
            return $Filter_GTIN;
        // }
    }

    public static function checkAvgCostCleanSheet($sku,$province)
    {
        if ($province == 'ON' || $province == 'Ontario') {
            $province_name = 'Ontario';
            $province_id = 'ON';
        } elseif ($province == 'MB' || $province == 'Manitoba') {
            $province_name = 'Manitoba';
            $province_id = 'MB';
        } elseif ($province == 'BC' || $province == 'British Columbia') {
            $province_name = 'British Columbia';
            $province_id = 'BC';
        } elseif ($province == 'AB' || $province == 'Alberta') {
            $province_name = 'Alberta';
            $province_id = 'AB';
        } elseif ($province == 'SK' || $province == 'Saskatchewan') {
            $province_name = 'Saskatchewan';
            $province_id = 'SK';
        }

        $checkCleanSheet = CleanSheet::where('sku',$sku)
             ->where('province',$province_name)
             ->whereNotNull('average_cost')
             ->where('average_cost', '!=', '0')
             ->where('average_cost', '!=', '-0')
             ->orderBy('id', 'desc')
             ->first();
        if(empty($checkCleanSheet) || !$checkCleanSheet){
            $checkCleanSheet = CleanSheet::where('sku',$sku)
                 ->where('province_slug',$province_id)
                 ->whereNotNull('average_cost')
                 ->where('average_cost', '!=', '0')
                 ->where('average_cost', '!=', '-0')
                 ->orderBy('id', 'desc')
                 ->first();
            if(empty($checkCleanSheet) || !$checkCleanSheet){
                $average_cost = '0.00';
                return $average_cost ;
            }
            else{
                $average_cost = $checkCleanSheet->average_cost;
                return $average_cost;
            }
        }
        else{
            $average_cost = $checkCleanSheet->average_cost;
            return $average_cost;
        }

    }
    public static function provinceName($name)
    {
        if ($name == 'ON' || $name == 'Ontario') {
            return 'Ontario';
        } elseif ($name == 'SK' || $name == 'Saskatchewan') {
            return 'Saskatchewan';
        }
        elseif ($name == 'AB' || $name == 'Alberta'){
            return 'Alberta';
        }
        elseif ($name == 'BC' || $name == 'British Columbia'){
            return 'British Columbia';
        }
        elseif ($name == 'MB' || $name == 'Manitoba'){
            return 'Manitoba';
        }
        else{
            return 'Null';
        }
    }

    public static function getProvince($province,&$province_id, &$province_name){

        if($province){
            if ($province == 'ON' || $province == 'Ontario') {
                $province_name = 'Ontario';
                $province_id = 'ON';
            } elseif ($province == 'MB' || $province == 'Manitoba') {
                $province_name = 'Manitoba';
                $province_id = 'MB';
            } elseif ($province == 'BC' || $province == 'British Columbia') {
                $province_name = 'British Columbia';
                $province_id = 'BC';
            } elseif ($province == 'AB' || $province == 'Alberta') {
                $province_name = 'Alberta';
                $province_id = 'AB';
            } elseif ($province == 'SK' || $province == 'Saskatchewan') {
                $province_name = 'Saskatchewan';
                $province_id = 'SK';
            }
            return;
        }


    }
}


