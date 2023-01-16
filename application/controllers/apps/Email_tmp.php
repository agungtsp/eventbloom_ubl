<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_tmp extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('EmailTmpModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['list_ref_email_category'] = selectlist2(array('table'=>'ref_email_category','title'=>'All Category','selected'=>$data['id_ref_email_category']));
		render('apps/email_tmp/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->EmailTmpModel->findById($id);
            if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['template_name']  = '';
			$data['subject']		= '';
			$data['page_content']	= '';
            $data['id'] 			= '';
		}
        $img_thumb					= image($data['img'],'small');//is_file_exsist(UPLOAD_DIR.'small/',$data['img']) ? ($this->baseUrl.'uploads/small/'.$data['img']) : '';
		$imagemanager				= imagemanager('img',$img_thumb,750,186);
		$data['img']				= $imagemanager['browse'];
		$data['imagemanager_config']= $imagemanager['config'];

		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
		$data['list_ref_email_category'] = selectlist2(array('table'=>'ref_email_category','title'=>'All Category','selected'=>$data['id_ref_email_category']));

		render('apps/email_tmp/add',$data,'apps');
	}
	function records(){
		$data = $this->EmailTmpModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['slideshow_title'] 		= quote_form($value['slideshow_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
		}
		render('apps/email_tmp/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$page_content 			= $this->input->post('page_content');
		$post 					= purify($this->input->post());
		$post['page_content']	= $page_content;
		$ret['error']			= 1;
		
        $this->form_validation->set_rules('id_ref_email_category', '"Email Category"', 'required'); 
		$this->form_validation->set_rules('template_name', '"Template Name"', 'required'); 
		$this->form_validation->set_rules('subject', '"Subject"', 'required'); 
		$this->form_validation->set_rules('page_content', '"Content"', 'required');
        $this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();
			$post['page_content'] = str_replace(
						array('%7B', '%7D'), 
						array('{', '}'), 
						$post['page_content']
					    );
				if($idedit){
                    auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Email Template";
					$this->EmailTmpModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$idedit = $this->EmailTmpModel->insert($post);
				}
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->EmailTmpModel->delete($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */