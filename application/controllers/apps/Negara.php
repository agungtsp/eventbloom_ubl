<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Negara extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Negara_model');
	}
	function index(){
		render('apps/negara/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->Negara_model->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
			$data['is_edit']	= '';
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['code']		= '';
			$data['name']			= '';
			$data['is_edit']	= 'hidden';
			$data['id']	= '';
		}
		
		render('apps/negara/add',$data,'apps');
	}
	function records(){
		$data = $this->Negara_model->records();
		foreach ($data['data'] as $key => $value) {
		}
		render('apps/negara/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;

		$this->form_validation->set_rules('code', '"Code"', 'required'); 
		$this->form_validation->set_rules('name', '"Name"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
			
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Country";
					$this->Negara_model->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Country";
					$this->Negara_model->insert($post);
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
		$this->Negara_model->delete($id);
		detail_log();
		insert_log("Delete Country");
	}
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */