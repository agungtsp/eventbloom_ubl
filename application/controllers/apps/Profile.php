<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Profile extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		
	}
	function index(){
		$this->load->model('Model_user');
		$this->data['disabled'] = 'disabled';

		$user    = $this->data['id_auth_user'];
		$dt_user = $this->db->get_where('auth_user', "id_auth_user = '$user'")->row_array();
		
		$this->data['userid']      = $dt_user['userid'];
		$this->data['username']    = $dt_user['username'];
		$this->data['grup_select'] = selectlist2(array('table'=>'auth_user_grup','id'=>'id_auth_user_grup','name'=>'grup','selected'=>$dt_user['id_auth_user_grup']));
		$this->data['email']       = $dt_user['email'];
		$this->data['phone']       = $dt_user['phone'];
		
		$data['checked_male']      = $dt_user['gender'] == 'M' ? 'checked' : '';
		$data['checked_female']    = $dt_user['gender'] == 'F' ? 'checked' : '';

        $data['list_negara']      = selectlist2(array(
            'table'    => 'ref_negara',
            'where'    => 'is_delete = 0',
            'selected' => $dt_user['kode_ref_negara'],
            'id'       => 'code' 
        ));

        $data['list_provinsi']    = selectlist2(array(
			'table'    => 'ref_provinsi',
			'where'    => 'is_delete = 0',
			'selected' => $dt_user['kode_ref_provinsi'],
			'name'     => 'provinsi',
			'id'       => 'kode_provinsi',
        ));

		$data['kode_ref_provinsi']  = $dt_user['kode_ref_provinsi'];
		$data['kode_ref_kabupaten'] = $dt_user['kode_ref_kabupaten'];
		$data['kode_ref_kecamatan'] = $dt_user['kode_ref_kecamatan'];
		$data['kode_ref_kelurahan'] = $dt_user['kode_ref_kelurahan'];
		$data['postal_code']        = $dt_user['postal_code'];
		$data['address']            = $dt_user['address'];
		$data['facebook']           = $dt_user['facebook'];
		$data['instagram']          = $dt_user['instagram'];
		$data['twitter']            = $dt_user['twitter'];
		$data['website']            = $dt_user['website'];
		$data['birthdate']          = iso_date_custom_format($dt_user['birthdate'],'d-m-Y');
		
		load_js('profile.js','assets/js/modules/profile');

		render('apps/system/profile',$data,'apps');
	}
	
	function proses(){
		$post  = purify($this->input->post());
		$id    = $post['id_auth_user'];
		$email = $post['email'];
        
        $this->form_validation->set_rules('username', '"Nama Lengkap"', 'trim|required'); 
        $this->form_validation->set_rules('email', '"Email"', 'trim|required'); 
        $this->form_validation->set_rules('gender', '"Jenis Kelamin"', 'trim|required');
        $this->form_validation->set_rules('phone', '"No. Telepon"', 'trim|required'); 

		unset ($post['id_auth_user']);
		
		$cek_email = $this->db->get_where('auth_user', "id_auth_user != '$id' and email = '$email'")->num_rows();
		
		if (trim($email)!='' && $cek_email){
			echo 'err_email';
		} else if ($this->form_validation->run() == FALSE){
			echo ' '.validation_errors(' ','<br>');
		} else {
            $post['birthdate'] = iso_date_custom_format($post['birthdate'],'Y-m-d');

			$this->db->update('auth_user',$post, "id_auth_user = '$id'");
			detail_log();
			insert_log('Update Profil Pengguna');
			echo 'Update Success';
		}
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

