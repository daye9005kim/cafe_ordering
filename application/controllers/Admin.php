<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$buyer = $this->Buyer_model->select(array('interval' => 5));
		$team = $this->Member_model->team();

		$admin = $this->config->item('admin');
		if (!in_array($SES_USER['name'], $admin['member'])) {
			return $this->load->view('view', array('status' => 400, 'data' => '당신은 관리자가 아닙니다.'));
		}

		$return = array(
			'buyer' => empty($buyer) ? array() : $buyer,
			'team' => empty($team) ? array() : $team
		);
		return $this->load->view('view', array('status' => 200, 'data' => $return));
	}
}
