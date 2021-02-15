<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{
    public function index()
    {


        //print_r($this->Starbucks_model->select(array('product_cd' => '110563', 'cate_cd' => 'W0000003', 'content' => '물')));
        //print_r($this->Member_model->select(array('dept' => '플랫폼혁신본부', 'team' => 'R&D센터_인프라팀', 'part' => '데이터운영파트', 'pos' => '대리', 'name' => '강전구')));
        return $this->load->view('view', array('status' => 200, 'data' => array()));
    }
}