<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************
  * Created : Sept 27 2011
  * Creator : ivan lubis
  * Email : ihate.haters@yahoo.com
  * Content : Login
  * Project : 
  * CMS ver : CI ver.2
*************************************/	
class Auth_model extends CI_Model
{
	function __construct(){
		parent::__construct();
	}
	function check_login($userid,$password){
		$data['ip'] 		= $_SERVER['REMOTE_ADDR'];
		$redir 				= 'apps/login';
		if ($userid!='' && $password!=''){
		 $query = $this->db->get_where('auth_user',"(userid = '$userid' or email = '$userid') and is_delete=0");
			//$this->db->where("userid","$userid");
			//$query=$this->db->get("auth_user");
			if ($query->num_rows() > 0){
				$row = $query->row(); 
				$data['id_auth_user'] 	= $row->id_auth_user;
				$userpass = $row->userpass;
				$password = md5($password);
				if ($password == $userpass && $password != "") {
					$this->load->library("session");
					if($row->is_banned==1){
						set_flash_session('error_login','Anda tidak memiliki izin untuk mengakses web ini.');
						$data['activity'] = "Banned User : $userid";
					} else {
						$user_sess = array();
						$user_sess = array(
													'admin_name'=>$row->username,
													'admin_id_auth_user_group'=>$row->id_auth_user_grup,
													'id'=>$row->id_auth_user,
													'admin_id_auth_user'=>$row->id_auth_user,
													'admin_id_ref'=>$row->id_ref,
													'admin_type'=>$row->tipe,
													'profil_mitra_id'=>$row->profil_mitra_id,
													'admin_id_ref_user_category'=>$row->id_ref_user_category
													);
						$this->load->model('LoginTransactionModel');
						$this->LoginTransactionModel->check_user($user_sess);
						$this->session->set_userdata('ADM_SESS',$user_sess);
						$this->session->unset_userdata('MEM_SESS');
						$data['activity'] 		= "Login";
						if($row->id_auth_user_grup==1){
							$redir ='apps/home';
						} else {
							$redir ='apps/home';
						}
					}
				}
				else {
          set_flash_session('error_login','Incorrect password');
					$data['activity'] = "Incorrect password";
				}
			} 
			else {
			   set_flash_session('error_login','Username atau password yang anda masukkan salah');
			   $data['activity'] = "User not found : $userid";
			}
		}
		else{
			//kalo userid or password or dua2nya kosong
			set_flash_session('error_login','Username and Password is Required');
			redirect('apps/login');
			exit;
		}
		$data['log_date'] =  date('Y-m-d H:i:s');
		$this->db->insert('access_log',$data);
		redirect($redir);
	}
	function auth_pages($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
	  $mulai = (int)$mulai -1;
	  if ($total==1){
		  $sql	= "SELECT count(*) ttl from auth_user_grup ";//where 1 $where";
		  $data	= $this->db->query($sql)->row()->ttl;
	  }
	  else{
		  $sql	= "SELECT id_auth_user_grup id, grup from auth_user_grup ";//where 1
						 // $where order by $sidx $sord limit $mulai,$end ";
		  $dt	= $this->db->query($sql)->result_array();
		  $n 	= 0;
		  foreach($dt as $dtx){
			  $data[$n]['id'] 				= $dtx['id'];
			  $data[$n]['edit'] 			=  edit_grid($dtx['id']);
			  $data[$n]['del'] 				= ($dtx['id'] <= 10) ? '' : delete_grid($dtx['id']);
			  $data[$n]['grup'] 			= $dtx['grup'];
			  $data[$n]['total'] 			= $this->db->get_where('auth_user',array('id_auth_user_grup'=>$dtx['id']))->num_rows();
			  ++$n;
		  }
	  }
	  return $data;
	}

	function send_password($email){
        // $data['ip']         = $_SERVER['REMOTE_ADDR'];
        $query = $this->db->get_where('auth_user',"email = '$email'  and is_delete=0");

        if ($query->num_rows() > 0){
            $row = $query->row(); 

            $this->load->helper('string');
            $data_now = random_string('alnum',8);
            $newpass = md5($data_now);

            $where['email'] = $row->email;
            $data['userpass'] = $newpass;
            $this->db->update('auth_user',$data,$where);//

            $this->load->library('parser');
            $this->load->helper('mail');
            $this->load->model('model_user','model');
            $emailTmp = $this->getEmailTemplate(1);
            
            $dataEmailContent['userid'] = $row->userid;
            $dataEmailContent['fullname'] = $row->username;
            $dataEmailContent['email'] = $row->email;
            $dataEmailContent['password'] = $data_now;
            $emailContent = $this->parser->parse_string($emailTmp['page_content'], $dataEmailContent, TRUE);

            $mail['to'] = $row->email;
            $mail['subject'] = $emailTmp['subject'];
            $mail['content'] = $emailContent;

            sent_mail($mail);
            $this->session->set_flashdata('warning','Password baru telah dikirim ke alamat email Anda');
            redirect('apps/login/forget_password');
        }else{
            $this->session->set_flashdata('error_login','Email yang Anda masukkan salah');
            $data['activity'] = "change password not found email : $email";
            $data['log_date'] =  date('Y-m-d H:i:s');
            $this->db->insert('access_log',$data);
            redirect('apps/login/forget_password');
        }
	}
	function getEmailTemplate($idEmail='')
	{
		if ($idEmail == '') {
			exit('ID Email Required!');
		}
		$where['id'] = $idEmail;
		$emailTmp = $this->db->get_where('email_tmp', $where)->row_array();
		return $emailTmp;
	}

}

