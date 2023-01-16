<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('PagesModel');
	}
	function index(){
		render('apps/pages/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->PagesModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']             = 'Edit';
			$data['proses']            = 'Update';
			$data['page_name_data']    = $data['page_name'];
			$data['page_name_en_data'] = $data['page_name_en'];
			$data                      = quote_form($data);
		}else{
			$data['judul']             = 'Add';
			$data['proses']            = 'Simpan';
			$data['id']                = '';
			$data['uri_path']          = '';
			$data['page_name_data']    = '';
			$data['teaser']            = '';
			$data['page_content']      = '';
			$data['page_name_en_data'] = '';
			$data['teaser_en']         = '';
			$data['page_content_en']   = '';
			$data['img']               = '';
		}
		$img                         = $data['img'] ? image($data['img'],'small') : '';
		$imagemanager                = imagemanager("img",$img);
		$data['img']                 = $imagemanager['browse']; 
		$data['imagemanager_config'] = $imagemanager['config'];

		render('apps/pages/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->PagesModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] = quote_form($data['page_name']);
			$data['teaser'] = quote_form($data['teaser']);
		}
		render('apps/pages/view',$data,'apps');
	}
	function records(){
		$data = $this->PagesModel->records();
		foreach ($data['data'] as $key => $value) {
		}
		render('apps/pages/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$post 					= purify($this->input->post());

		$ret['error']			= 1;
		$where['uri_path']		= $post['uri_path'];
		if($idedit){
			$where['id !=']		= $idedit;
		}
		$unik 					= $this->PagesModel->fetchRow($where);
		$this->form_validation->set_rules('page_name', '"page Name"', 'required'); 
		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
		$this->form_validation->set_rules('teaser', '"Teaser"', 'required'); 
		$this->form_validation->set_rules('page_content', '"Content"', 'required');

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik){
			$ret['message']	= "Page URL $post[uri_path] already taken";
		}
		else{   
			$this->db->trans_start();
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Pages";
				$this->PagesModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Pages";
				$this->PagesModel->insert($post);
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
		$this->PagesModel->delete($id);
		detail_log();
		insert_log("Delete Pages");
	}
}

/* End of file pages.php */
/* Location: ./application/controllers/apps/pages.php */