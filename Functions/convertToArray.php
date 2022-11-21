<?php

function convertToArray ($stringToConvert) {
    $convertedArray = str_replace(array('[',']'),'',$stringToConvert);
    $convertedArray = str_replace("'",'',$convertedArray);
    $convertedArray = explode(",",$convertedArray);

    $convertedArray = array_filter($convertedArray);

    return $convertedArray;
}

?>