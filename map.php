<?php

function multiple_number( $number ){

    return $number*2;

}
$numbers = [1,2,3,4,5];

$multiple_numbers = array_map('multiple_number', $numbers);

var_dump($multiple_numbers);