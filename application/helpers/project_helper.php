<?php

function calculate_age($birthdate,$format=array()){
    $from = new DateTime($birthdate);
    $to   = new DateTime('today');
    if(!empty($format)){
        foreach ($format as $key => $value) {
            if(in_array($value, array('y','m','d'))){
                $age[$value] = $from->diff($to)->$value;
            }
        }
    } else {
        $age = $from->diff($to)->y;
    }
    return $age;
}