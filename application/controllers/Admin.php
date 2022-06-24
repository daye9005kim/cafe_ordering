<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index($page = 0)
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$admin = $this->config->item('admin');
		if (!in_array($SES_USER['name'], $admin['member'])) {
			return $this->load->view('view', array('status' => 400, 'data' => '당신은 관리자가 아닙니다.'));
		}

		if (empty($page)) {
			$page = 0;
		}
		$this->load->library('pagination');
		$param['start'] = $page;
		$param['limit'] = 20;
		
		$buyer = $this->Buyer_model->select($param);
		$config['total_rows'] = $this->Buyer_model->total_rows($param);
		$this->pagination->initialize($config);

		$team = $this->Member_model->team();

		$return = array(
			'buyer' => empty($buyer) ? array() : $buyer,
			'team' => empty($team) ? array() : $team
		);
		return $this->load->view('view', array('status' => 200, 'data' => $return, 'pagination' => array('total_rows' => $config['total_rows'])));
	}
}
