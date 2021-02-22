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
		$buyer = $this->Buyer_model->select(array('now' => true));

		return $this->load->view('view', array('status' => 200, 'data' => array('user' => $SES_USER, 'menu' => $menu, 'buyer' => $buyer)));

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

	public function set()
	{
		$code = $this->input->post('menu_code');
		$size = $this->input->post('size');
		$cnt = $this->input->post('cnt');
		$comment = $this->input->post('comment');

		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}
		if (empty($code)) {
			return $this->load->view('json', array('status' => 400, 'data' => '메뉴를 입력해주세요.'));
		}

		$menu = $this->Starbucks_model->select(array('product_cd' => $code));

		if (empty($menu)) {
			return $this->load->view('json', array('status' => 400, 'data' => '일치하는 메뉴가 없습니다.'));
		}

		$buyer = $this->Buyer_model->select(array('member_name' => '김민철', 'now' => true));

		if (empty($buyer)) {
			return $this->load->view('json', array('status' => 400, 'data' => '구매자가 생성되지 않았습니다.'));
		}

		$param = array(
			'ordnum' => $buyer[0]['ordnum'],
			'status' => '1',
			'member_name' => $SES_USER['name'],
			'product_cd' => $code,
			'product_size' => empty($size) ? 'tall' : $size,
			'product_cnt' => empty($cnt) ? 1 : $cnt,
			'comment' => $comment,
		);

		if ($this->Order_model->insert($param)) {
			return $this->load->view('json', array('status' => 200, 'data' => array('info' => $param, 'msg' => '주문 성공')));
		}
		return $this->load->view('json', array('status' => 400, 'data' => '주문 실패'));
	}

	public function start() {
    	$this->Buyer_model->insert(array(
    		'ordnum' => uniqid(),
    		'member_name' => '김민철',
    		'start' => date('Y-m-d H:i:s'),
    		'end' =>  date('Y-m-d H:i:s', strtotime('1 hour')),
    		'comment' => "음료 고르세요.",
		));
	}
}
