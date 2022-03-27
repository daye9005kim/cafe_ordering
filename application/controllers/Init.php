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

//        $this->Order_model->create();
//        $this->Buyer_model->create();
//
//		$drink = $this->Starbucks_model->fetch();
//        $member = $this->Member_model->fetch();
//		return $this->load->view('json', array('status' => 200, 'data' => array('member' => $member, 'drink' => $drink)));
	}

	/**
	 * 음료 메뉴 생성
	 * @return object|string
	 */
	public function drink()
	{
		$cafe = $this->input->get('cafe');

		$config = $this->config->item('cafe');
		if (!array_key_exists($cafe, $config)) {
			return $this->load->view('view', array('status' => 400, 'data' => '카페 셋팅이 안 되어있습니다.'));
		}

		$file_name = $config[$cafe]['file_name'];
		$period = strtotime('-1 hour');

		if (!is_file($file_name)) {
			return $this->load->view('json', array('status' => 400, 'data' => '음료 데이터를 생성하십시오.'));
		}

		if (filemtime($file_name) > $period) {
			return $this->load->view('json', array('status' => 400, 'data' => '업데이트한지 1시간 미만임.'));
		}

		$drink = $this->Starbucks_model->fetch($cafe);
		return $this->load->view('json', array('status' => 200, 'data' => array('drink' => $drink)));
	}


	/**
	 * 사원 목록 생성
	 * @return object|string
	 */
	public function member()
	{
		$file_name = '/tmp/member.log';
		$period = strtotime('-1 hour');

		if (!is_file($file_name)) {
			return $this->load->view('json', array('status' => 400, 'data' => '사원 데이터를 생성하십시오.'));
		}

		if (filemtime($file_name) > $period) {
			return $this->load->view('json', array('status' => 400, 'data' => '업데이트한지 1시간 미만임.'));
		}

		$member = $this->Member_model->fetch();
		return $this->load->view('json', array('status' => 200, 'data' => array('member' => $member)));
	}
}
