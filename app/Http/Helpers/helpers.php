<?php

use App\Models\GeneralSetting;



function gs($key = null) {
    $general = GeneralSetting::first();
        
    if ($key) {
        return @$general->$key;
    }

    return $general;
}


