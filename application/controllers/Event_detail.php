<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Event_detail extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
    function index($uri_path){
    	$this->load->model("EventPublicModel");
    	$where = array(
			'a.start_date <=' => date('Y-m-d'),
			'a.end_date >=' => date('Y-m-d'),
			'a.id_ref_event_status' => 1,
			'a.uri_path' => $uri_path
		);
    	$data = $this->EventPublicModel->find_event($where, 1);
    	if(!$data){
    		redirect(base_url("notfound"));
    		exit();
    	}
    	if( strpos($data['event_image'],',') !== false ) {
			$list_images = explode(',', $data['event_image']);
			foreach ($list_images as $key_image => $data_image) {
				$data['list_image'][$key_image]['img']    = image($data_image,'large');
				$data['list_image'][$key_image]['key']    = image($data_image,'large');
				$data['list_image'][$key_image]['status'] = ($key_image==0) ? "active" : "";
			}
		} else {
			$data['list_image'][0]['img']    = $data['event_image'] ? image($data['event_image'],'large') : '';
			$data['list_image'][0]['key']    = 0;
			$data['list_image'][0]['status'] = "active";
		}
		$data['list_image_icon'] = $data['list_image'];
		$data['event_date']      = ($data['start_date']==$data['end_date']) ? $data['start_date'] : $data['start_date'] . " - " . $data['end_date'];
		
		$data['list_speakers']   = $this->EventPublicModel->find_speaker(array("id_event" => $data['id_event']));
    	foreach ($data['list_speakers'] as $key => $value) {
    		$data['list_speakers'][$key]['speaker_img'] = image($value['speaker_img'],'large');
    	}
    	
		$data['show_speakers_tab']   = (count($data['list_speakers']) > 0) ? "" : "hidden";
		$data['active_event']        = "active";
		$data['header_search_event'] = 1;
		$data['page_name']           = $data['event_name'];
		$data['idx_event']           = md5plus($data['id_event']);
		$data['current_url'] 		 = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$this->load->model('EventPriceModel');
		$event_pricing = $this->EventPriceModel->findBy(
			array(
				'id_event'  => $data['id_event']
			), 1
		);
		$data['price'] = number_format($event_pricing['price'],0,',','.');
		
		$this->load->model("EventParticipantPaymentModel");
		$this->db->where("b.id_event", $data['id_event']);
		$this->db->where("a.id_ref_status_payment !=", 3);
		$total_participant = $this->EventParticipantPaymentModel->records(array(), 1);
		$data['show_register'] = (in_array(group_id(), array(1, 2, 3, 5)) || ($total_participant >= $data['max_participant'])) ? 'hidden' : '';
		$data['show_soldout'] = ($total_participant >= $data['max_participant']) ? "" : "hidden";
		$this->load->model("QuestionnaireModel");
		$this->load->model("QuestionnaireAnswerModel");
		$this->db->order_by("id", "asc");
		$data['list_questionnaire']   = $this->QuestionnaireModel->findBy(array());
		$counter = 1;
    	foreach ($data['list_questionnaire'] as $key => $value) {
			$this->db->order_by("id", "asc");
			$list_answer   = $this->QuestionnaireAnswerModel->findBy(array("id_questionnaire" => $value['id']));
			$data['list_questionnaire'][$key]["id_questionnaire"] = $value['id'];
			$data['list_questionnaire'][$key]["no"] = $counter++;
			foreach ($list_answer as $key_answer => $value_answer){
				$data['list_questionnaire'][$key]["question"][] = array(
					"id_answer" => $value_answer['id'],
					"answer" => $value_answer['name']
				);
			}
    	}
		$data['total_questionnaire'] = count($data['list_questionnaire']);
		$data['id_user'] = id_user();
		load_js('questionnaire.js');
		render("event_detail", $data);
    }

	function process_questionnaire() {
		$this->load->model("EventQuestionnaireAnswerModel");

		$post         = purify($this->input->post());
		$ret['error'] = 1;
		$this->form_validation->set_rules('id_event', "id_event",'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$ret['msg'] = validation_errors(' ',' ');
		} else {
			$data_insert['id_input'] = generateRandomString();
			$data_insert['id_event'] = $post['id_event'];
			$data_insert['user_id_create'] = $post['user_id_create'];
			for ($i=1; $i <= (int)$post['total_questionnaire']; $i++) { 
				$data_answer = explode(";", $post['answer'.$i]);
				$data_insert['id_questionnaire'] = $data_answer[0];
				$data_insert['id_questionnaire_answer'] = $data_answer[1];
				$this->EventQuestionnaireAnswerModel->insert($data_insert);
			}
			$ret['error'] = 0;
			$ret['msg']   = "Terima kasih sudah mengirim kuesioner.";
		}
		
		echo json_encode($ret);
	}
}