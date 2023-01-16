<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('UserModel');
	}
	function index(){
		$data['list_user_group'] 		= selectlist2(array('table'=>'auth_user_grup','id'=>'id_auth_user_grup','name'=>'grup','title'=>'All User Group'));
		render('apps/user/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->UserModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']        = 'Edit';
			$data['proses']       = 'Update';
			$data                 = quote_form($data);
			$data['is_edit']      = '';
			$data['is_show_pass'] = 'hidden';
			$data['birthdate']    = iso_date_custom_format($data['birthdate'],'d-m-Y');
		}
		else{
			$data['judul']              = 'Add';
			$data['proses']             = 'Save';
			$data['userid']             = '';
			$data['username']           = '';
			$data['email']              = '';
			$data['password']           = '';
			$data['phone']              = '';
			$data['is_edit']            = 'hidden';
			$data['is_show_pass']       = '';
			$data['id_auth_user']       = '';
			$data['gender']             = '';
			$data['kode_ref_negara']    = '';
			$data['kode_ref_provinsi']  = '';
			$data['kode_ref_kabupaten'] = '';
			$data['kode_ref_kecamatan'] = '';
			$data['kode_ref_kelurahan'] = '';
			$data['postal_code']        = '';
			$data['address']            = '';
			$data['facebook']           = '';
			$data['instagram']          = '';
			$data['twitter']            = '';
			$data['website']            = '';
			$data['birthdate']          = '';
		}

		$data['checked_male']      = $data['gender'] == 'M' ? 'checked' : '';
		$data['checked_female']    = $data['gender'] == 'F' ? 'checked' : '';

		$data['list_negara']      = selectlist2(array(
			'table'    => 'ref_negara',
			'where'    => 'is_delete = 0',
			'selected' => $data['kode_ref_negara'],
			'id'       => 'code' 
		));

		$data['list_provinsi']    = selectlist2(array(
			'table'    => 'ref_provinsi',
			'where'    => 'is_delete = 0',
			'selected' => $data['kode_ref_provinsi'],
			'name'     => 'provinsi',
			'id'       => 'kode_provinsi',
		));
		
		$data['list_user_group'] 		= selectlist2(array('table'=>'auth_user_grup','id'=>'id_auth_user_grup','name'=>'grup','title'=>'All User Group','selected'=>$data['id_auth_user_grup']));

		load_js('user.js','assets/js/modules/user');

		render('apps/user/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->UserModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] =image($data['img'],'ori'); 
			if(!$data){
				die('404');
			}
			$data['page_name'] = quote_form($data['page_name']);
			$data['teaser'] = quote_form($data['teaser']);
		}
		render('apps/user/view',$data,'apps');
	}
	function records(){
		$data = $this->UserModel->records();
		foreach ($data['data'] as $key => $value) {
			// $data['data'][$key]['name'] = quote_form($value['name']);
			$data['data'][$key]['publish_date'] = iso_date($value['publish_date']);
			$data['data'][$key]['banned_title'] = ($value['is_banned']==1) ? 'Aktifkan User' : 'Nonaktifkan User';
		}
		render('apps/user/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;

		$grp = $post['id_auth_user_grup'];
		if (empty($post['userpass'])) {
			unset($post['userpass']) ;
		}
		else {
			$post['userpass'] = md5($post['userpass']);
		}
		
		$this->form_validation->set_rules('userid', '"User ID"', 'required'); 
		$this->form_validation->set_rules('username', '"User Name"', 'required'); 
		$this->form_validation->set_rules('email', '"Email"', 'required'); 
		$this->form_validation->set_rules('phone', '"Phone"', 'required'); 
		$this->form_validation->set_rules('id_auth_user_grup', '"User Group"', 'required'); 
		$this->form_validation->set_rules('gender', '"Jenis Kelamin"', 'trim|required');
		$this->form_validation->set_rules('birthdate', '"Tanggal Lahir"', 'trim|required');
		$this->form_validation->set_rules('kode_ref_negara', '"Negara"', 'trim|required');
		$this->form_validation->set_rules('postal_code', '"Kode Pos"', 'trim|required');

		if (strtoupper($post['kode_ref_negara']) == 'ID') {
			$this->form_validation->set_rules('kode_ref_provinsi', '"Provinsi"', 'trim|required');
			$this->form_validation->set_rules('kode_ref_kabupaten', '"Kabupaten"', 'trim|required');
			$this->form_validation->set_rules('kode_ref_kecamatan', '"Kecamatan"', 'trim|required');
			$this->form_validation->set_rules('kode_ref_kelurahan', '"Kelurahan"', 'trim|required');
		} else {
			$post['kode_ref_provinsi']  = NULL;
			$post['kode_ref_kabupaten'] = NULL;
			$post['kode_ref_kecamatan'] = NULL;
			$post['kode_ref_kelurahan'] = NULL;
		}

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
			$where 				= ($idedit) ? "and id_auth_user not in ($idedit)" : '';
			$cek_code           = db_get_one('auth_user',"userid","(userid = '$post[userid]' or email = '$post[email]') and is_delete = 0 $where");
			if($cek_code){
				$ret['error'] = 1;
				$ret['message'] =  " Account Name $post[userid] already exsist";
			} else {
				$post['birthdate'] = iso_date_custom_format($post['birthdate'],'Y-m-d');
				
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update User Management";
					$this->UserModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert User Management";
					$this->UserModel->insert($post);
				}
				detail_log();
				insert_log($act);
				$this->db->trans_complete();
			}
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->UserModel->delete($id);
		detail_log();
		insert_log("Delete User Management");
	}

	function get_callback($id=0){
		echo db_get_one('module','callback',array('id'=>$id));
	}
	function banned(){
		$post = $this->input->post();
		auth_update();
		$this->UserModel->update(array('is_banned'=>$post['is_banned']),$post['id']);
		detail_log();
		insert_log("Banned User");
	}

	function get_kabupaten() {
		$get      = $this->input->get();
		$code     = $get['code'];
		$selected = $get['selected'];

		$data['list_data'] = 'Tidak ditemukan';
		if ($code) {
			$data['list_data'] = selectlist2(array(
				'table'      => 'ref_kabupaten',
				'where'      => "is_delete = 0 AND kode_provinsi = '$code'",
				'selected'   => $selected,
				'name'       => 'kabupatenkota',
				'id'         => 'kode_kabupaten',
			));
		}
		echo json_encode($data);
	}

	function get_kecamatan() {
		$get      = $this->input->get();
		$code     = $get['code'];
		$selected = $get['selected'];

		$data['list_data'] = 'Tidak ditemukan';
		if ($code) {
			$data['list_data'] = selectlist2(array(
				'table'      => 'ref_kecamatan',
				'where'      => "is_delete = 0 AND kode_kabupaten = '$code'",
				'selected'   => $selected,
				'name'       => 'kecamatan',
				'id'         => 'kode_kecamatan',
			));
		}
		echo json_encode($data);
	}

	function get_kelurahan() {
		$get      = $this->input->get();
		$code     = $get['code'];
		$selected = $get['selected'];

		$data['list_data'] = 'Tidak ditemukan';
		if ($code) {
			$data['list_data'] = selectlist2(array(
				'table'      => 'ref_kelurahan',
				'where'      => "is_delete = 0 AND kode_kecamatan = '$code'",
				'selected'   => $selected,
				'name'       => 'kelurahan',
				'id'         => 'kode_kelurahan',
			));
		}
		echo json_encode($data);
	}
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */