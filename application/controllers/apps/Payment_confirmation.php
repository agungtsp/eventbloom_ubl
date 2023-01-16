<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_confirmation extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('EventParticipantPaymentModel');
	}

	function index() {
		$data['list_event']   = selectlist2(array(
			'table' => 'event',
			'where' => array('is_delete' => 0),
			'title' => 'Semua', 
		));
		
		$data['list_status_payment'] = selectlist2(array(
			'table' => 'ref_status_payment',
			'title' => 'Semua', 
		));

		$data['list_bank'] = selectlist2(array(
			'table' => 'ref_bank',
			'title' => 'Semua', 
		));

		render('apps/payment_confirmation/index',$data,'apps');
	}

	public function verify($id){
		if($id){
			$data = $this->EventParticipantPaymentModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['proses']        = 'Update';
			$data                  = quote_form($data);
			$data['payment_date']  = iso_date_custom_format($data['payment_date'],'d-m-Y');
			$data['verify_date']   = iso_date_custom_format($data['verify_date'],'d-m-Y');
			$data['total_price']   = number_format($data['total_price'],0,',','.');
			$data['total_payment'] = number_format($data['total_payment'],0,',','.');
			
			$data['list_status_payment'] = selectlist2(array(
				'table'    => 'ref_status_payment',
				'title'    => 'Semua', 
				'selected' => $data['id_ref_status_payment'], 
			));

			$data['list_bank'] = selectlist2(array(
				'table'    => 'ref_bank',
				'selected' => $data['id_ref_bank'],				
			));

			load_js('jquery.number.js','assets/js');
			load_js('payment.js','assets/js/modules/event');

			render('apps/payment_confirmation/verify',$data,'apps');
		} else {
			redirect('payment_confirmation');
		}
	}

	function records() { 
		$data = $this->EventParticipantPaymentModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['total_price']   = number_format($value['total_price'],0,',','.');
			$data['data'][$key]['total_payment'] = number_format($value['total_payment'],0,',','.');
		}
		render('apps/payment_confirmation/records',$data,'blank');
	}	
	
	function proses($idedit){
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;

		$this->form_validation->set_rules('id_ref_bank', '"Nama Bank"', 'required'); 
		$this->form_validation->set_rules('bank_account_name', '"Nama Pemilik Rekening"', 'required'); 
		$this->form_validation->set_rules('bank_account_number', '"Nomor Rekening"', 'required');
		$this->form_validation->set_rules('total_price', '"Total Harga"', 'trim|required');
		$this->form_validation->set_rules('total_payment', '"Total Bayar"', 'trim|required');
		$this->form_validation->set_rules('payment_date', '"Tanggal Bayar"', 'trim|required');
		$this->form_validation->set_rules('verify_date', '"Tanggal Verifikasi"', 'trim|required');
		$this->form_validation->set_rules('id_ref_status_payment', '"Status Pembayaran"', 'trim|required');

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{
			auth_update();

			$this->db->trans_start();
			$post['payment_date']   = iso_date_custom_format($post['payment_date'],'Y-m-d');
			$post['verify_date']    = iso_date_custom_format($post['verify_date'],'Y-m-d');
			$post['total_price']    = (float)str_replace('.', '', $post['total_price']);
			$post['total_payment']  = (float)str_replace('.', '', $post['total_payment']);
			$post['user_id_verify'] = id_user();
			
			$ret['message'] = 'Update Success';
			$act			= "Update Participant Payment";
			$this->EventParticipantPaymentModel->update($post,$idedit);

			// Update status payment di table participant
			$this->load->model('EventParticipantModel');
			$this->EventParticipantModel->update(
				array(
					'id_ref_status_payment' => $post['id_ref_status_payment']
			), $post['id_event_participant']);

			detail_log();
			insert_log($act);
			$this->db->trans_complete();

			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}

	function get_bank_info() {
		$id_bank     = purify($this->input->get('id_bank'));
		$description = db_get_one('ref_bank','description',"id=$id_bank");
		
		echo json_encode($description);
	}
}

/* End of file payment_confirmation.php */
/* Location: ./application/controllers/apps/payment_confirmation.php */