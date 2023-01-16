<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Contact_us extends CI_Controller {
	function __construct(){
		parent::__construct();

		$this->load->model('ContactUsModel');
	}
    function index(){
		$data['active_contact_us'] = "active";
		$data['page_name']         = "Hubungi Kami";
    	load_js('contact_us.js');
		render('contact_us',$data);
	}

	function process() {
		$post         = purify($this->input->post());
		$ret['error'] = 1;
		$this->form_validation->set_rules('fullname', $this->data['contact input name'],'trim|required');
		$this->form_validation->set_rules('email', $this->data['contact input email'],'trim|required|valid_email');
		$this->form_validation->set_rules('subject', $this->data['contact input title'],'trim|required');
		$this->form_validation->set_rules('handphone', $this->data['contact input phone'],'trim|required');
		$this->form_validation->set_rules('message', $this->data['contact input message'],'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$ret['msg'] = validation_errors(' ',' ');
		} else {
			$id = $this->ContactUsModel->insert($post);
			
			$ret['error'] = 0;
			$ret['msg']   = "Terima kasih sudah menghubungi kami, $post[fullname]! Pesan anda telah terkirim.";
		}
		
		echo json_encode($ret);
	}
}