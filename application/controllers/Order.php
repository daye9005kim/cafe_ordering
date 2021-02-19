<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER)) {
			return $this->load->view('view', array('status' => 308, 'url' => '/member/login', 'data' => '로그인 해주세요.'));
		}

    	$menu = $this->Starbucks_model->select(array());

		return $this->load->view('view', array('status' => 200, 'data' => array('user' => $SES_USER, 'menu' => $menu)));

    }

	public function menu()
	{
		$code = $this->input->post('code');

		$menu = $this->Starbucks_model->select(array('product_cd' => $code));

		$info = array(
			"product_img" => $menu[0]['product_img'],
			"content" => $menu[0]['content']
		);

		return $this->load->view('json', array('status' => 200, 'data' => array('menu' => $info)));

	}
}
