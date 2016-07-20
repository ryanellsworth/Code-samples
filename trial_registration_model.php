<?php

class Trial_registration_model extends CI_Model {
	
	public function save_trial_user($data) {
		$this->db->trans_start();
		
		$trial_user_id = $this->db->query("SELECT NEXTVAL('trial_user_trial_user_id_seq'::regclass) as key")->row()->key;

		$this->load->library('cypher');
		$hashed_user_id = $this->cypher->encrypt($trial_user_id.time(), 'B');

		$trial_query = "INSERT INTO trial_user (trial_user_id, hashed_user_id) VALUES (?, ?)";
		$trial_query_bindings = array($trial_user_id, $hashed_user_id);
		$trial_user_result = $this->db->query($trial_query, $trial_query_bindings);
		
		$user_query = "UPDATE users SET trial_user_id = ? WHERE id = ?";
		$user_query_bindings = array($trial_user_id, $data->user_id);
		$user_query_result = $this->db->query($user_query, $user_query_bindings);
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}

	public function send_confirmation_email($user) {
		$expiration_date = date("F j, Y", strtotime("+30 days"));
		$this->load->helper('email');
		$email = new stdClass();
		$email->from = ['noreply@studiesweekly.com' => 'Studies Weekly'];
		$email->reply_to = 'web.bot@studiesweekly.com';
		$email->to = $user->email;
		$email->subject = '***';
		$message_template = "
		***";
		$email->message = $message_template;
		send_email_message($email);
	}

	public function send_newsletter_email($form_data) {
		$this->load->helper('email');
		$email = new stdClass();
		$email->from = ['noreply@studiesweekly.com' => 'Studies Weekly'];
		$email->reply_to = 'web.bot@studiesweekly.com';
		$email->to = $form_data->email;
		$email->subject = '***';
		$message_template = "
		***";

		$email_address = urlencode($form_data->email);
		$hash = sha1(microtime(true).mt_rand(10000,90000));
		$hash = urlencode($hash);
		$confirm_link = site_url()."newsletter_signup/confirm/{$email_address}/{$hash}";
		$message = sprintf($message_template, $form_data->first_name, $confirm_link, $confirm_link);
		$email->message = $message;
		send_email_message($email);
	}

	public function save_newsletter_user($form_data)
	{
		$this->db->insert('newsletter_users', $form_data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
}