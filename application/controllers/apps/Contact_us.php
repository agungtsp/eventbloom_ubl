<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact_us extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('ContactUsModel');
	}
	function index(){
		render('apps/contact_us/index',$data,'apps');
	}
	function records(){
		$data = $this->ContactUsModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date_time($value['create_date']);
		}
		render('apps/contact_us/records',$data,'blank');
	}
	function detail($id){
		$data                = $this->ContactUsModel->findById($id);
		$data['create_date'] = iso_date_time($data['create_date']);
		
		render('apps/contact_us/detail',$data,'blank');
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->ContactUsModel->reject($id);
		detail_log();
		insert_log("Reject Contact Us");
	}
}

/* End of file contact_us.php */
/* Location: ./application/controllers/apps/contact_us.php */