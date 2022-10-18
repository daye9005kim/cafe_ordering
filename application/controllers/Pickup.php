<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pickup extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('url');
		redirect('/member/login');
	}

	public function set()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER) || empty($SES_USER['name'])) {
			return $this->load->view('json', array('status' => 308, 'url' => '/member/login', 'data' => '로그인 해주세요.'));
		}

		$ordnum = $this->input->post('ordnum');
		$pickup_yn = strtoupper($this->input->post('pickup_yn'));

		if (empty($ordnum)) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문번호가 없습니다.'));
		}

		if (empty($pickup_yn) || !in_array($pickup_yn, array('Y', 'N'))) {
			return $this->load->view('json', array('status' => 400, 'data' => '픽업 여부가 없습니다.'));
		}

		$param = array('ordnum' => $ordnum, 'member_name' => $SES_USER['name'], 'volunteer' => $pickup_yn);
		$msg = '지원 완료';

		$pickup = $this->Pickup_model->select($param);

		if ($pickup_yn === 'Y') {
			//중복확인
			if (!empty($pickup)) {
				return $this->load->view('json', array('status' => 200, 'data' => $msg));
			}
			if ($this->Pickup_model->set($param) === false) {
				return $this->load->view('json', array('status' => 400, 'data' => '입력 실패'));
			}
		} else if ($pickup_yn === 'N') {
			$msg = '지원 취소 했습니다.';
			if (!empty($pickup)) {
				$this->Pickup_model->delete($param);
			}
		}

		return $this->load->view('json', array('status' => 200, 'data' => $msg));
	}


	/**
	 * 랜덤 뽑기
	 * @return object|string
	 */
	public function pick()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER) || empty($SES_USER['name'])) {
			return $this->load->view('json', array('status' => 308, 'url' => '/member/login', 'data' => '로그인 해주세요.'));
		}

		$ordnum = $this->input->get_post('ordnum');
		if (empty($ordnum)) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문번호가 없습니다.'));
		}

		$param = array('ordnum' => $ordnum);
		//몇 명 뽑을지
		$total = $this->Order_model->get_total($param);
		$pick_cnt = intval(ceil($total / 8));
		$limit = $pick_cnt;

		//인원 확인
		$volunteer = $this->Pickup_model->get_volunteer($param);
		$volunteer_cnt = empty($volunteer) ? 0 : count($volunteer);
		if ($limit === $volunteer_cnt) {
			return $this->load->view('json', array('status' => 200, 'data' => $volunteer));
		}
		if ($limit > $volunteer_cnt) {
			$limit = $limit - $volunteer_cnt;
		}

		//랜덤 뽑기
		$param['limit'] = $limit;
		$this->Pickup_model->set_random($param);

		//뽑힌 인원 조회
		$volunteer = $this->Pickup_model->get_volunteer($param);
		$volunteer_cnt = empty($volunteer) ? 0 : count($volunteer);

		$member_names = join(', ', array_column($volunteer, 'member_name'));
		if ($pick_cnt > $volunteer_cnt) {
			$short = $pick_cnt - $volunteer_cnt;
			return $this->load->view('json', array('status' => 400, 'data' => $member_names . ' 외 ' . $short . '명 모자랍니다.'));
		}

		return $this->load->view('json', array('status' => 200, 'data' => $volunteer));
	}
}
