<?php

/**
 * controller for every kid votes area
 */
class Every_kid_votes extends CI_Controller
{

	public function __construct() {
		parent::__construct();
		$this->load->model('every_kid_votes_model', 'ekv');
	}

	public function index() {
		if(!$this->allowed_user()) {
			show_404();
		}

		$data['title'] = "Every Kid Votes Admin";
		$data['nav'] = $this->auth->buildNavigation($this->session->userdata('ID'));
		$this->load->view('templates/header', $data);
		$this->load->view('every-kid-votes/index');
		$this->load->view('templates/footer', $data);
	}

	public function registration($id = "") {
		if(!$this->allowed_user()) {
			show_404();
		}

		$result = $this->ekv->get_registration($id);
		$new_result = array();
		foreach ($result as $r) {
			if(!empty($r->first_name) && !empty($r->last_name))
				$r->full_name = $r->first_name." ".$r->last_name;
			else $r->full_name = "";
			$new_result[] = $r;
		}

		header('Content-Type: application/json');
		echo json_encode($new_result);
	}

	public function update_registration() {
		if(!$this->allowed_user()) {
			show_404();
		}

		$data = $this->input->post();
		$result = $this->ekv->update_registration($data);
		$this->logging->log_action("registration id = {$data['id']}");
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function kit_request($id = "") {
		if(!$this->allowed_user()) {
			show_404();
		}

		$result = $this->ekv->get_kit_request($id);
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function update_kit() {
		if(!$this->allowed_user()) {
			show_404();
		}

		$data = $this->input->post();
		$result = $this->ekv->update_kit_request($data);
		$this->logging->log_action("kit request id = {$data['id']}");
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function districts($state) {
		$districts = $this->ekv->get_districts($state);
		header('Content-Type: application/json');
		echo $districts;
	}

	public function schools($district) {
		$schools = $this->ekv->get_schools($district);
		header('Content-Type: application/json');
		echo $schools;
	}

	public function email() {
		if(!$this->allowed_user()) {
			show_404();
		}

		$data = $this->input->post();
		$result = $this->ekv->resend_email($data);
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function user_list() {
		if(!$this->allowed_user()) {
			show_404();
		}
		$this->load->view('every-kid-votes/user_list');
	}

	public function user_details() {
		if(!$this->allowed_user()) {
			show_404();
		}
		$this->load->view('every-kid-votes/user_details');
	}

//......and so on