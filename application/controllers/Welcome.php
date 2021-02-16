<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{
    public function index()
    {

//        print_r($this->Order_model->select(array('num'=>'2','ordnum' => '602b2335be448', 'status' => '1', 'member_name' => '김다예', 'product_cd' => '9200000002502', 'product_cnt' => '1', 'comment' => '잘먹겠습니다.')));

//                print_r($this->Order_model->select(array('ordnum' => '602b2335be448')));
//        print_r($this->Order_model->insert(array('ordnum' => '602b2335be448', 'status' => '1', 'member_name' => '김다예', 'product_cd' => '9200000002502', 'product_cnt' => '1', 'comment' => '잘먹겠습니다.')));


//        print_r($this->Buyer_model->insert(array('ordnum' => uniqid(), 'member_name' => '김민철', 'start' => date('Y-m-d H:i:s'), 'end' => date('Y-m-d H:i:s', strtotime('6 hours')), 'comment'=>'쏩니다.')));
//        print_r($this->Buyer_model->select(array( 'member_name' => '김민철', 'now' => true)));

        //print_r($this->Starbucks_model->select(array('product_cd' => '110563', 'cate_cd' => 'W0000003', 'content' => '물')));
        //print_r($this->Member_model->select(array('dept' => '플랫폼혁신본부', 'team' => 'R&D센터_인프라팀', 'part' => '데이터운영파트', 'pos' => '대리', 'name' => '강전구')));
        return $this->load->view('view', array('status' => 200, 'data' => array()));
    }
}