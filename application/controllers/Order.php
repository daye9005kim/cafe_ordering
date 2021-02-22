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

		$buyer = $this->Buyer_model->select(array('now' => true));

		if (empty($buyer)) {
			return $this->load->view('view', array('status' => 400, 'data' => '구매자가 생성되지 않았습니다.'));
		}

    	$menu = $this->Starbucks_model->select(array());
		$buyer = $this->Buyer_model->select(array('now' => true));
		$order = $this->Order_model->select(array('ordnum' => $buyer[0]['ordnum'], 'member_name' => $SES_USER['name']));

		return $this->load->view('view', array('status' => 200, 'data' => array('user' => $SES_USER, 'menu' => $menu, 'buyer' => $buyer, 'order' => isset($order[0]) ? $order[0] : array())));

    }

	/**
	 * 메뉴 가져오기
	 * @return object|string
	 */
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

	/**
	 * 내 주문 보기
	 * @return object|string
	 */
	public function get() {
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$ordnum = $this->input->get('ordnum');

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}

		$order = $this->Order_model->select(array('ordnum' => $ordnum, 'member_name' => $SES_USER['name']));

		return $this->load->view('view', array('status' => 200, 'data' => array('order' => $order)));
	}


	/**
	 * 최종 주문서
	 * @return object|string
	 */
	public function prnt() {
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$ordnum = $this->input->get('ordnum');

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}

		$order = $this->Order_model->select(array('ordnum' => $ordnum));
		$arr = array();
		$total = 0;
		foreach ($order as $item) {
			$total += $item['product_cnt'];
			$cnt = $item['product_cnt'];
			if (!isset($arr[$item['product_nm']][$item['product_size']])) {
				$arr[$item['product_nm']] = array(
					'tall'=> 0,
					'grande'=> 0,
					'venti'=> 0
				);
			}
			$arr[$item['product_nm']][$item['product_size']] = $arr[$item['product_nm']][$item['product_size']] + $cnt;
		}
		return $this->load->view('view', array('status' => 200, 'data' => array('order' => $arr, 'total' => $total)));
	}

	/**
	 * 주문하기
	 * @return object|string
	 */
	public function set()
	{
		$code = $this->input->post('menu_code');
		$size = $this->input->post('size');
		$cnt = $this->input->post('cnt');
		$comment = $this->input->post('comment');
		$ordnum = $this->input->post('ordnum');

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

		$buyer = $this->Buyer_model->select(array('ordnum' => $ordnum, 'now' => true));

		if (empty($buyer)) {
			return $this->load->view('json', array('status' => 400, 'data' => '구매자가 생성되지 않았습니다.'));
		}

		$param = array(
			'ordnum' => $buyer[0]['ordnum'],
			'status' => '1',
			'member_name' => $SES_USER['name'],
			'product_cd' => $code,
			'product_size' => empty($size) ? 'tall' : $size,
			'product_cnt' => empty($cnt) ? 1 : intval($cnt),
			'comment' => $comment
		);

		$order = $this->Order_model->select(array('ordnum' => $buyer[0]['ordnum'], 'member_name' => $SES_USER['name']));
		if (count($order) > 0) {
			if ($this->Order_model->update($param)) {
				return $this->load->view('json', array('status' => 200, 'data' => array('info' => $param, 'msg' => '주문 성공')));
			}
		} else {
			if ($this->Order_model->insert($param)) {
				return $this->load->view('json', array('status' => 200, 'data' => array('info' => $param, 'msg' => '주문 성공')));
			}
		}

		return $this->load->view('json', array('status' => 400, 'data' => '주문 실패'));
	}


	/**
	 * 주문 시작
	 * @return object|string
	 */
	public function start() {
		$buyer = $this->Buyer_model->select(array('now' => true));

		if (count($buyer) > 0) {
			return $this->load->view('json', array('status' => 400, 'data' => '생성된 주문자가 존재합니다. 아직 주문이 완료되지 않았습니다.'));
		}

    	$this->Buyer_model->insert(array(
    		'ordnum' => uniqid(),
    		'member_name' => '김민철',
    		'start' => date('Y-m-d H:i:s'),
    		'end' =>  date('Y-m-d H:i:s', strtotime('1 hour')),
    		'comment' => "음료 고르세요.",
		));
	}
}
