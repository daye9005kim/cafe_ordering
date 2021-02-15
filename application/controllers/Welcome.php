<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{
    public function index()
    {


        var_dump($this->Starbucks_model->fetch());
        return $this->load->view('view', array('status' => 200, 'data' => array()));
    }
}