<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class List_event extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model(array('EventModel','EventParticipantModel'));
	}

	function index() {
		$data['list_event_status']   = selectlist2(array(
			'table'    => 'ref_event_status', 
			'title'    => 'Semua', 
		));
		
		$data['list_event_category'] = selectlist2(array(
			'table'    => 'ref_event_category', 
			'title'    => 'Semua', 
			'where'    => 'is_delete=0', 
		));
		render('apps/list_event/index',$data,'apps');
	}	

	function records() {
		$data = $this->EventModel->records_list();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['event_desc']         	= limit_text($value['event_desc'], 20);
			$data['data'][$key]['start_date']            = iso_date_custom_format($value['start_date'],'d-m-Y');
			$data['data'][$key]['end_date']              = iso_date_custom_format($value['end_date'],'d-m-Y');
			$data['data'][$key]['idx']                   = md5plus($value['id']);

			$participant = $this->EventParticipantModel->findBy("
					id_event=$value[id] 
					AND id_auth_user = ".id_user()." 
					AND is_delete=0
				",1);
			$id_event_participant = (group_id() == 4) ? md5plus($participant['id']) : '';
			
			$data['data'][$key]['idx_event_participant'] = ($id_event_participant) ? $id_event_participant : '';
			$data['data'][$key]['show_nonmember']        = (group_id() == 4) ? 'invis' : ''; // 4 = Member (Peserta)
			$data['data'][$key]['show_member']           = (group_id() == 4) ? '' : 'invis'; // 4 = Member (Peserta)
			$data['data'][$key]['show_payment_member']   = (group_id() == 4 && $id_event_participant) ? '' : 'invis'; // 4 = Member (Peserta)
			$data['data'][$key]['show_cancel']           = (group_id() == 4 
															&& $id_event_participant 
															&& $participant['id_ref_status_payment'] != 2
															) ? '' : 'invis'; // 4 = Member (Peserta)

		}
		render('apps/list_event/records',$data,'blank');
	}	
	
}