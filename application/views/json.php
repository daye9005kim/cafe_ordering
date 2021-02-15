<?php

if (!isset($status)) {
    $status = 404;
}

$msg = 'Not found.';
if ($status === 200) {
    $msg = 'ok';
} else if ($status === 400) {
    $msg = 'Bad Request';
} else if ($status === 401) {
    $msg = 'Unauthorized';
} else if ($status === 404) {
    $msg = 'Not found';
} else if ($status === 500) {
    $msg = 'Internal Server Error';
} else if ($status === 308) {
    $msg = 'Permanent Redirect';
}

if (!isset($data)) {
    $data = new stdclass;
}

$this->output
    ->set_content_type('application/json')
    ->set_status_header($status, $msg)
    ->set_output(json_encode($data));
