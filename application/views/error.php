<?php

if (!isset($status)) {
    $status = 404;
}

if (!isset($data)) {
    $data = array();
}


echo $status;
print_r($data);