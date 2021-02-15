<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Init extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $drink = $this->Starbucks_model->fetch();
        $member = $this->Member_model->fetch();
        return $this->load->view('json', array('status' => 200, 'data' => array('member' => $member, 'drink' => $drink)));
    }
}
