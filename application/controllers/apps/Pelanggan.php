<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pelanggan extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('PelangganModel');
	}
	function index(){
		render('apps/pelanggan/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->PelangganModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
			$data['is_edit']	= '';
			$data['is_show_pass']	= 'hidden';
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['address']		= '';
			$data['fullname']			= '';
			$data['email']	= '';
			$data['password']	= '';
			$data['phone']	= '';
			$data['is_edit']	= 'hidden';
			$data['is_show_pass']	= '';
			$data['id']	= '';
		}
		
		render('apps/pelanggan/add',$data,'apps');
	}
	function records(){
		$data = $this->PelangganModel->records();
		foreach ($data['data'] as $key => $value) {
		}
		render('apps/pelanggan/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;

		$this->form_validation->set_rules('fullname', '"Fullname"', 'required'); 
		// $this->form_validation->set_rules('email', '"Email"', 'required'); 
		$this->form_validation->set_rules('phone', '"Phone"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
			
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Pelanggan";
					$this->PelangganModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Pelanggan";
					$this->PelangganModel->insert($post);
				}
				detail_log();
				insert_log($act);
				$this->db->trans_complete();
				
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->PelangganModel->delete($id);
		detail_log();
		insert_log("Delete Pelanggan");
	}
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */