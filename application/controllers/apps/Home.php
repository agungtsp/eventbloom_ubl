<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
	}
	function index(){
		$this->load->model('EventModel');
		$this->load->model('UserModel');
		if(in_array(group_id(), array(1,2,3))){
			$total_active_events = $this->EventModel->records(
				array(
					'early_bird_start_date <=' => date('Y-m-d'),
					'early_bird_end_date >=' => date('Y-m-d'),
					'id_ref_event_status' => 1
				)
			,1);
			$data['total_active_events'] = number_format($total_active_events,0,',','.');

			$total_inactive_events = $this->EventModel->records(
				'(early_bird_end_date < "'. date('Y-m-d').'" or id_ref_event_status = 2)',1);
			$data['total_inactive_events'] = number_format($total_inactive_events,0,',','.');

			$total_active_member_participant = $this->UserModel->records(
				array(
					'a.id_auth_user_grup' => 4
				)
			,1);
			$data['total_active_member_participant'] = number_format($total_active_member_participant,0,',','.');

			$total_active_member_eo = $this->UserModel->records(
				array(
					'a.id_auth_user_grup' => 5
				)
			,1);
			$data['total_active_member_eo'] = number_format($total_active_member_eo,0,',','.');
			
			$list_event_category = $this->db->select("id, name")->get_where("ref_event_category", array(
				"is_delete" => 0
			))->result_array();
			foreach ($list_event_category as $key => $value) {
				$total_event_category[] = array(
					$value['name'],
					$this->EventModel->records(
						array(
							'a.id_ref_event_category' => $value['id'],
							// 'a.id_ref_event_status' => 1
						)
					,1)
				);
			}
			$data['total_event_category'] = json_encode($total_event_category);
			render('apps/home/home_admin',$data,'apps');
		} else if(group_id()==5){
			render('apps/home/home_event_organizer',$data,'apps');
		} else {
			render('apps/home/home_participant',$data,'apps');
		}
	}

	function imagemanager(){
		$post = purify($this->input->post());
		$file = $_FILES;
		if($file){
			$file 	= $_FILES['img'];
	        $fname 	= $file['name'];
	        // $ext	= explode('.',$fname);
	        // $ext	= '.'.$ext[count($ext)-1];
			$maxFileSize = MAX_UPLOAD_SIZE * 1024 * 1024;
	        if(!is_writable(UPLOAD_DIR)){//kalo ga bisa upload
	            $ret['error']   = 1;
	            $ret['message'] = "Directory is readonly";
	        } else if($file['size']>=$maxFileSize){
				$ret['error']   = 1;
	            $ret['message'] = "Max File size is ".MAX_UPLOAD_SIZE."MB";
			}
	        else if($fname){
	            // $folder=UPLOAD_DIR.'temp/';
	            if(!file_exists($folder)){//kalo blm ada foldernya, bikin dulu
	                @mkdir($folder);
	            }
	            // $new_file = rand(1000,9999).$ext;
	            // move_uploaded_file($file['tmp_name'],$folder.$new_file);
	            $upload = upload_file('img','temp');
	            $ret['filename'] = base_url()."images/article/temp/".$upload['file_name'];
	            $ret['file'] = $upload['file_name'];
				$ret['size'] = $upload['file_size'];
	            $ret['width']= $upload['image_width'];
	            $ret['height']= $upload['image_height'];
	            $ret['message'] = 'success';
	        }
	        echo json_encode($ret);
	        exit;
		}
		
		$this->load->model('fileManagerModel');
		$total_records = $this->fileManagerModel->getTotal("(user_id_create = ".id_user() ." or is_public = 1) and name LIKE '%".$post['searchPicture']."%'");
		$per_page = 12;
		$data['pages'] = ceil($total_records/$per_page);
		$data['load'] = base_url().'apps/home/imagemanager';
		// $data['search'] = base_url().'apps/home/search';

		//sanitize post value
		if(isset($post['page'])){
			$page_number = filter_var($post["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
			if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
		}else{
			$page_number = 1;
		}

		//get current starting point of records
		$offset = (($page_number-1) * $per_page);


		$data['list_data'] = $this->fileManagerModel->getAll("(user_id_create = ".id_user() ." or is_public = 1) and name LIKE '%".$post['searchPicture']."%'", $per_page, $offset);
		foreach ($data['list_data'] as $key => $value) {
			$data['list_data'][$key]['title'] = str_replace((explode("_", $value['name'])[0])."_", "", $value['name']);
		}
		render('apps/filemanager',$data,'blank');

	}
	
	function imagemanager_save(){
		$post         = $this->input->post();
		$tmp          = $_SERVER['DOCUMENT_ROOT'].$this->baseUrl.'external/'.$post['tmp'];
		$ori_tmp      = UPLOAD_DIR.'temp/'.$post['name'];
		$post['name'] = md5($post['name'].date("dmYHis"))."_".$post['name'];
		$thumbs       = UPLOAD_DIR.'small/'.$post['name'];
		$ori          = UPLOAD_DIR.'large/'.$post['name'];
		
		rename($tmp,$thumbs);
		rename($ori_tmp,$ori);
		unset($post['tmp']);
		$post['user_id_create'] = id_user();
		$this->load->model('fileManagerModel');
		$this->fileManagerModel->insert($post);
	}

	function elfinder_init() {
		$opts = initialize_elfinder();
        render('elfinder',$data);
	}

	function del_image() {
		$id = $this->input->post('id');
		
		$ret['error'] = 1;
		$ret['msg']   = 'Hapus gambar gagal!';
	
		if ($id) {
			$this->load->model('fileManagerModel');
			auth_delete();

			$this->fileManagerModel->delete($id);
			
			detail_log();
			insert_log("Delete File Manager");

			$ret['error'] = 0;
			$ret['msg'] = 'Hapus gambar berhasil!';
		}

		echo json_encode($ret);
	}
}