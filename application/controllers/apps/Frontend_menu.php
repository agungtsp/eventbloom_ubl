<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend_menu extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('frontendMenuModel');
	}
	function index(){
			$data['list_menu_position'] 		= selectlist2(array('table'=>'menu_position','title'=>'All Position'));
			$data['list_parent'] 				= selectlist2(array('table'=>'frontend_menu','title'=>'Root'));
		render('apps/frontend_menu/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->frontendMenuModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data['publish_date'] = iso_date($data['publish_date']);
			$data = quote_form($data);
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['uri_path']		= '';
			$data['teaser']			= '';
			$data['page_content']	= '';
			$data['extra_param']	= '';
			$data['id'] 			= '';
			$data['name'] 			= '';
			$data['seo_title']			= '';
			$data['meta_description']	= '';
			$data['meta_keywords']		= '';
			$data['description']		= '';
			$data['publish_date'] 	= date('d-m-Y');
		}
		
		$data['list_menu_position'] 		= selectlist2(array('table'=>'menu_position','selected'=>$data['id_menu_position']));
		$data['list_parent'] 				= selectlist2(array('table'=>'frontend_menu','selected'=>$data['id_parent']));
		$data['list_frontend_menu_type'] 	= selectlist2(array('table'=>'frontend_menu_type','selected'=>$data['id_frontend_menu_type']));
		$data['list_module']			 	= selectlist2(array('table'=>'module','selected'=>$data['id_module']));
		$data['list_status_publish']		= selectlist2(array('table'=>'status_publish','selected'=>$data['id_status_publish']));

		render('apps/frontend_menu/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->frontendMenuModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] =image($data['img'],'ori'); 
			if(!$data){
				die('404');
			}
			$data['page_name'] = quote_form($data['page_name']);
			$data['teaser'] = quote_form($data['teaser']);
		}
		render('apps/frontend_menu/view',$data,'apps');
	}
	function records(){
		$data = $this->frontendMenuModel->records();
		foreach ($data['data'] as $key => $value) {
			// $data['data'][$key]['name'] = quote_form($value['name']);
			$data['data'][$key]['publish_date'] = iso_date($value['publish_date']);
		}
		render('apps/frontend_menu/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;
		$where['uri_path']		= $post['uri_path'];
		if($idedit){
			$where['id !=']		= $idedit;
		}
		// $unik 					= $this->frontendMenuModel->findBy($where);
		$this->form_validation->set_rules('id_menu_position', '"Position"', 'required'); 
		$this->form_validation->set_rules('name', '"Menu Name"', 'required'); 
		$this->form_validation->set_rules('id_frontend_menu_type', '"Menu Type"', 'required'); 
		if($post['id_frontend_menu_type'] == 2){
			$this->form_validation->set_rules('extra_param', '"URL"', 'required'); 
		}
		else{
			// $this->form_validation->set_rules('id_module', '"Module"', 'required'); 
		}
		$this->form_validation->set_rules('id_status_publish', '"Status"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		// else if($unik){
		// 	$ret['message']	= "Page URL $post[uri_path] already taken";
		// }
		else{   
			$this->db->trans_start();   
				$post['publish_date'] = iso_date($post['publish_date']);
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Frontend menu";
					$this->frontendMenuModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Frontend menu";
					$this->frontendMenuModel->insert($post);
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
		$this->frontendMenuModel->delete($id);
		detail_log();
		insert_log("Delete Frontend menu");
	}

	function get_callback($id=0){
		echo db_get_one('module','callback',array('id'=>$id));
	}
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */