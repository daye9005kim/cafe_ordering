<?php

if (!isset($status)) {
    $status = 404;
}

if (!isset($data)) {
    $data = array();
}

if (!isset($url)) {
    $url = '';
}

$header = strtolower($this->input->get_request_header('Content-Type'));

if (strstr($header, 'application/json')) {
    $this->load->view('json', array('status' => $status, 'data' => $data));
} else {
    if ($status == 308) {
        $this->load->view('url', array('status' => $status, 'url' => $url, 'data' => $data));
    } else if ($status == 200) {
        $this->load->view($this->router->fetch_directory() . $this->router->fetch_class() . '/' . $this->router->fetch_method(), array('status' => $status, 'data' => $data));
    } else {
        $this->load->view('error', array('status' => $status, 'data' => $data));
    }
}