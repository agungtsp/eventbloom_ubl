<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire_answer extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('QuestionnaireAnswerModel');
	}
	function index(){
		$data['list_questionnaire'] = selectlist2(array('table'=>'questionnaire','title'=>'All Questionnaire','selected'=>$data['id_questionnaire']));
		render('apps/questionnaire_answer/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->QuestionnaireAnswerModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']        = 'Edit';
			$data['proses']       = 'Update';
			$data                 = quote_form($data);
			$data['is_edit']      = '';
			$data['is_show_pass'] = 'hidden';
		}
		else{
			$data['judul']        = 'Add';
			$data['proses']       = 'Save';
			$data['name']         = '';
			$data['description']  = '';
			$data['is_edit']      = 'hidden';
			$data['is_show_pass'] = '';
			$data['id']           = '';
		}
		$img_thumb                   = image($data['image'],'small');
		$imagemanager                = imagemanager('image',$img_thumb);
		$data['image']               = $imagemanager['browse'];
		$data['imagemanager_config'] = $imagemanager['config'];
		$data['list_questionnaire'] = selectlist2(array('table'=>'questionnaire','title'=>'All Questionnaire','selected'=>$data['id_questionnaire']));
		render('apps/questionnaire_answer/add',$data,'apps');
	}
	function records(){
		$data = $this->QuestionnaireAnswerModel->records();
		foreach ($data['data'] as $key => $value) {
		}
		render('apps/questionnaire_answer/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;

		$this->form_validation->set_rules('name', '"Nama"', 'required'); 
		$this->form_validation->set_rules('id_questionnaire', '"Questionnaire"', 'required'); 

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
			
				if($idedit){
					auth_update();
					if(!$post['image']){
						unset($post['image']);
					}
					$ret['message'] = 'Update Success';
					$act			= "Update RefEventCategory";
					$this->QuestionnaireAnswerModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert RefEventCategory";
					$this->QuestionnaireAnswerModel->insert($post);
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
		$this->QuestionnaireAnswerModel->delete($id);
		detail_log();
		insert_log("Delete RefEventCategory");
	}
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */