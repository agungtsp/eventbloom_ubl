<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller {
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
		render('apps/event/index',$data,'apps');
	}

	public function add($id=''){
		if($id){
			$data = $this->EventModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']                 = 'Edit';
			$data['proses']                = 'Update';
			$data                          = quote_form($data);
			$data['is_edit']               = '';
			$data['start_date']            = iso_date_custom_format($data['start_date'],'d-m-Y');
			$data['end_date']              = iso_date_custom_format($data['end_date'],'d-m-Y');
			$data['early_bird_start_date'] = iso_date_custom_format($data['early_bird_start_date'],'d-m-Y');
			$data['early_bird_end_date']   = iso_date_custom_format($data['early_bird_end_date'],'d-m-Y');
		}
		else{
			$data['judul']                 = 'Add';
			$data['proses']                = 'Save';
			$data['name']                  = '';
			$data['image']                 = '';
			$data['description']           = '';
			$data['start_date']            = '';
			$data['end_date']              = '';
			$data['uri_path']              = '';
			$data['address']               = '';
			$data['longitude']             = '';
			$data['latitude']              = '';
			$data['location_name']         = '';
			$data['facebook']              = '';
			$data['instagram']             = '';
			$data['website']               = '';
			$data['twitter']               = '';
			$data['max_participant']       = '';
			$data['early_bird_start_date'] = '';
			$data['early_bird_end_date']   = '';
			$data['is_private']            = '';
			$data['id']                    = '';
		}
		
		$data['show_is_featured'] = (group_id() == 1) ? '' : 'invis';

		$i = 0;
		if( strpos($data['image'],',') !== false ) {
			$list_images = explode(',', $data['image']);
			foreach ($list_images as $key => $value) {
				$images = image($value,'small');
				$data['list_images'][$key]['images'] = imagemanager('image',$images,277,150,"_$i","[$i]",$value)['browse'];
				$data['list_images'][$key]['invis_del_event_image'] = '';

				$i++;
			}
		} else {
			$images = $data['image'] ? image($data['image'],'small') : '';
			$data['list_images'][$i]['images'] = imagemanager('image',$images,277,150,"_$i","[$i]",$data['image'])['browse'];
			$data['list_images'][$i]['invis_del_event_image'] = $images ? '' : 'invis';
		}
		
		$imagemanager                = imagemanager("image",'',277,150,"___NO_IMG_IMAGE","[__NO_IMG_IMAGE]");
		$data['template_image']      = $imagemanager['browse']; 
		$data['imagemanager_config'] = $imagemanager['config'];
		$data['gmaps_location']      = '';
		$data['checked_is_private']  = ($data['is_private']=='Y') ? 'checked' : '';
		$data['hidden_is_private']   = ($data['is_private']=='Y') ? '' : 'hidden';
		$data['checked_is_featured'] = ($data['is_featured']=='Y') ? 'checked' : '';

		// start speaker
		$this->load->model('RefEventSpeakerModel');
		$list_event_speaker = $this->RefEventSpeakerModel->findBy(
			array(
				'is_delete' => 0,
				'id_event'  => $data['id']
			)
		);

		$wn = 0;
		$data['template_speaker_image'] = imagemanager("image_speaker",'',277,150,"___NO_IMG_IMAGE","[__NO_IMG_IMAGE]")['browse'];

		if ($list_event_speaker && $data['id']) {
			foreach ($list_event_speaker as $key => $value) {
				$data['event_speaker'][$key]['speaker_num']             = $wn;
				$data['event_speaker'][$key]['id_ref_event_speaker']    = $value['id'];
				$data['event_speaker'][$key]['speaker_name']            = $value['name'];
				$data['event_speaker'][$key]['speaker_topic']           = $value['topic'];
				$data['event_speaker'][$key]['speaker_desc']            = $value['description'];
				$data['event_speaker'][$key]['invis_del_speaker']       = '';
			
				$img_thumb                                      = $value['image'] ? image($value['image'],'small') : '';
				$imagemanager                                   = imagemanager("image_speaker",$img_thumb,277,150,"_$wn","[$wn]");
				$data['event_speaker'][$key]['speaker_image'] = $imagemanager['browse'];

				$wn++;
			}
		} else {
			$data['event_speaker'] = array(array(
				'speaker_num'             => $wn,
				'speaker_name'            => '',
				'speaker_desc'            => '',
				'speaker_topic'           => '',
				'speaker_image'           => imagemanager("image_speaker",'',277,150,"_$wn","[$wn]")['browse'],
				'id_ref_event_speaker'    => '',
				'invis_del_speaker'       => 'invis'
			));
		}
		// end speaker

		// start price
		$this->load->model('EventPriceModel');
		$list_event_pricing = $this->EventPriceModel->findBy(
			array(
				'is_delete' => 0,
				'id_event'  => $data['id']
			)
		);

		$wn = 0;
		$data['template_pricing_image'] = imagemanager("image_pricing",'',277,150,"___NO_IMG_IMAGE","[__NO_IMG_IMAGE]")['browse'];
		$data['event_price_early_bird'] = array();
		$data['event_price_normal']     = array();

		if ($list_event_pricing && $data['id']) {
			foreach ($list_event_pricing as $key => $value) {
				$event_price[$key]['pricing_num']        = $wn;
				$event_price[$key]['id_event_price']     = $value['id'];
				$event_price[$key]['pricing_name']       = $value['name'];
				$event_price[$key]['pricing_desc']       = $value['description'];
				$event_price[$key]['pricing_min_age']    = $value['min_age'];
				$event_price[$key]['pricing_max_age']    = $value['max_age'];
				$event_price[$key]['pricing_price']      = number_format($value['price'],0,',','.');
				$event_price[$key]['pricing_start_date'] = iso_date_custom_format($value['start_date'],'d-m-Y');
				$event_price[$key]['pricing_end_date']   = iso_date_custom_format($value['end_date'],'d-m-Y');
				$event_price[$key]['invis_del_pricing']  = '';
				
				$img_thumb                               = $value['image'] ? image($value['image'],'small') : '';
				$imagemanager                            = imagemanager("image_pricing",$img_thumb,277,150,"_$wn","[$wn]");
				$event_price[$key]['pricing_image']      = $imagemanager['browse'];

				if ($value['is_early_bird'] == 'Y') {
					$data['event_price_early_bird'][$key] = $event_price[$key];
				} else {
					$data['event_price_normal'][$key]     = $event_price[$key];
				}

				$wn++;
			}
		}
		$event_price = array(
			'pricing_num'        => $wn,
			'pricing_name'       => '',
			'pricing_desc'       => '',
			'pricing_min_age'    => '',
			'pricing_max_age'    => '',
			'pricing_price'      => '',
			'pricing_start_date' => '',
			'pricing_end_date'   => '',
			'pricing_image'      => imagemanager("image_pricing",'',277,150,"_$wn","[$wn]")['browse'],
			'id_event_price'     => '',
			'invis_del_pricing'  => 'invis'
		);
		if (empty($data['event_price_early_bird'])) {
			$data['event_price_early_bird'][] = $event_price;
		}
		
		if (empty($data['event_price_normal'])) {
			$data['event_price_normal'][]     = $event_price;
		}
		// end price

		$data['list_event_status']   = selectlist2(array(
			'table'    => 'ref_event_status', 
			'title'    => 'Pilih Status', 
			'selected' => $data['id_ref_event_status']
		));
		
		$data['list_event_category'] = selectlist2(array(
			'table'    => 'ref_event_category', 
			'title'    => 'Pilih Kategori', 
			'where'    => 'is_delete=0', 
			'selected' => $data['id_ref_event_category']
		));

		load_js('jquery.geocomplete.js,jquery.number.js','assets/js');
		load_js('event.js','assets/js/modules/event');

		render('apps/event/add',$data,'apps');
	}

	function records() {
		$data = $this->EventModel->records();
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
		render('apps/event/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify(null_empty($this->input->post()));
		$ret['error']			= 1;
		
		$where['uri_path']		= $post['uri_path'];
		if($idedit){
			$where['a.id !=']		= $idedit;
		}
		$unik 					= $this->EventModel->findBy($where);

		$this->form_validation->set_rules('id_ref_event_category', '"Kategori Event"', 'required'); 
		$this->form_validation->set_rules('name_event', '"Nama"', 'required'); 
		$this->form_validation->set_rules('description', '"Deskripsi"', 'required'); 
		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
		$this->form_validation->set_rules('start_date', '"Tanggal Mulai Event"', 'required'); 
		$this->form_validation->set_rules('end_date', '"Tanggal Akhir Event"', 'required'); 
		$this->form_validation->set_rules('address', '"Alamat"', 'required'); 
		$this->form_validation->set_rules('max_participant', '"Maks. Peserta"', 'required'); 
		$this->form_validation->set_rules('id_ref_event_status', '"Status Event"', 'required');
		if ($post['is_private']) {
			$this->form_validation->set_rules('password', '"Password"', 'required');
		}

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik){
			$ret['message']	= "Page URL $post[uri_path] sudah ada!";
		}
		else{
			$data_event_speaker         = $post['data_speaker'];
			$images_data_event_speaker  = $post['image_speaker'];
			$ids_ref_event_speaker      = $post['id_ref_event_speaker'];
			$data_event_pricing         = $post['data_pricing'];
			$images_data_event_pricing  = $post['image_pricing'];
			$ids_event_price            = $post['id_event_price'];
			$post['name']               = $post['name_event'];
			unset(
				$post['gmaps_location'],
				$post['data_speaker'],
				$post['image_speaker'],
				$post['id_ref_event_speaker'],
				$post['data_pricing'],
				$post['image_pricing'],
				$post['id_event_price'],
				$post['name_event']
			);

			$post['start_date']            = iso_date_custom_format($post['start_date'],'Y-m-d');
			$post['end_date']              = iso_date_custom_format($post['end_date'],'Y-m-d');
			$post['early_bird_start_date'] = iso_date_custom_format($post['early_bird_start_date'],'Y-m-d');
			$post['early_bird_end_date']   = iso_date_custom_format($post['early_bird_end_date'],'Y-m-d');
			$post['is_private']            = ($post['is_private'] == 1) ? 'Y' : 'N';
			$post['is_featured']           = ($post['is_featured'] == 1) ? 'Y' : 'N';
			$post['image']                 = $post['image'] ? implode(',', $post['image']) : '';
			
			$this->db->trans_start();   
			
			if($idedit){
				auth_update();
				if(!$post['image']){
					unset($post['image']);
				}
				$ret['message'] = 'Update Success';
				$act			= "Update Event";
				$this->EventModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act            = "Insert Event";
				$idedit         = $this->EventModel->insert($post);
			}
				
			/*================= start of event speaker =================*/
			$this->load->model('RefEventSpeakerModel');

			if (isset($ids_ref_event_speaker)) {
				
				/* delete speaker */
				$this->RefEventSpeakerModel->multi_delete($idedit,$ids_ref_event_speaker);
				foreach ($data_event_speaker as $key => $speaker) {
					$speaker['id_event'] = $idedit;

					if (!empty($images_data_event_speaker)) {
						$speaker['image'] = $images_data_event_speaker[$key];
					}
					
					if ($ids_ref_event_speaker[$key]) {

						if(!$speaker['image']){
							unset($speaker['image']);
						}

						$this->RefEventSpeakerModel->update($speaker,$ids_ref_event_speaker[$key]);
					} else {
						if ($speaker['name']) {
							$this->RefEventSpeakerModel->insert($speaker);
						}
					}
				}
			}
			/*================= end of event speaker =================*/

			/*================= start of event price =================*/
			$this->load->model('EventPriceModel');

			if (isset($ids_event_price)) {
				
				/* delete event price */
				$this->EventPriceModel->multi_delete($idedit,$ids_event_price);

				foreach ($data_event_pricing as $key => $pricing) {
					$pricing['id_event'] = $idedit;

					if (!empty($images_data_event_pricing)) {
						$pricing['image'] = $images_data_event_pricing[$key];
					}
					
					$pricing['start_date']    = iso_date_custom_format($pricing['start_date'],'Y-m-d');
					$pricing['end_date']      = iso_date_custom_format($pricing['end_date'],'Y-m-d');
					$pricing['price']         = (float)str_replace('.', '', $pricing['price']);
					$pricing['is_early_bird'] = ($pricing['is_early_bird'] == 1) ? 'Y' : 'N';

					if ($ids_event_price[$key]) {

						if(!$pricing['image']){
							unset($pricing['image']);
						}

						$this->EventPriceModel->update($pricing,$ids_event_price[$key]);
					} else {
						if ($pricing['name']) {
							$this->EventPriceModel->insert($pricing);
						}
					}
				}
			}
			/*================= end of event price =================*/
			
			detail_log();
			insert_log($act);
			$this->db->trans_complete();

			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}

	function del() {
		auth_delete();
		$id = $this->input->post('iddel');
		$this->EventModel->delete($id);
		detail_log();
		insert_log("Delete Event");
	}

	function participant($id_event) {
		if ($id_event && group_id() != 4) { // not member (peserta)
			$check_event   = db_get_one('event','name',md5field('id')." = '$id_event'");

			if ($check_event) {
				$data['idx_event']   = $id_event;
				$data['event_name']  = $check_event;
				
				$data['list_negara'] = selectlist2(array(
					'table'    => 'ref_negara',
					'where'    => 'is_delete = 0',
					'id'       => 'code' 
				));

				$data['list_status_payment'] = selectlist2(array(
					'table'    => 'ref_status_payment',
					'title'    => 'Semua',
				));

				$data['list_bank'] = selectlist2(array(
					'table'    => 'ref_bank',
					'title'    => 'Semua',
				));

				render('apps/event/participant_index',$data,'apps');
			} else {
				redirect('apps/event');
			}
		} else {
			redirect('apps/event');
		}
	}

	function participant_records($id_event) {
		$data = $this->EventParticipantModel->records(md5field('b.id')." = '$id_event'");
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['id_event'] = md5plus($value['id_event']);
			$data['data'][$key]['idx']      = md5plus($value['id']);
		}
		render('apps/event/participant_records',$data,'blank');
	}

	function participant_add($id_event, $id = '') {
		if ($id_event) {
			$data_event = $this->EventModel->findBy(md5field('a.id')." = '$id_event'",1);

			if (!$id && group_id() == 4) { // cek jika dari halaman depan dan member sudah mendaftar
				$data     = $this->EventParticipantModel->findBy(
					md5field('a.id_event')." = '$id_event'"
				,1);

				if ($data) {
					redirect("apps/event/$id_event/participant/add/".md5plus($data['id']));
				}
			}

			if ($data_event) {
				if($id){
					$data     = $this->EventParticipantModel->findBy(
						md5field('a.id_event')." = '$id_event' AND ".
						md5field('a.id')." = '$id'"
					,1);
					if(!$data){
						die('404');
					}
					$data['judul']         = 'Edit';
					$data['proses']        = 'Update';
					$data                  = quote_form($data);
					$data['disabled_user'] = 'disabled';
					$data['birthdate']     = iso_date_custom_format($data['birthdate'],'d-m-Y');
					$data['sub_price']     = number_format($data['sub_price'],0,',','.');
					$data['total_price']   = number_format($data['total_price'],0,',','.');
				}
				else{
					$data['judul']              = 'Add';
					$data['proses']             = 'Save';
					$data['fullname']           = '';
					$data['email']              = '';
					$data['phone']              = '';
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
					$data['disabled_user']      = '';
					$data['sub_price']          = '';
					$data['total_price']        = '';
					$data['id']                 = '';
					$data['birthdate']          = '';

					if (group_id() == 4) {
						$this->load->model('UserModel');

						$data_user                  = $this->UserModel->findById(id_user());
						$price                      = $this->get_event_price_auto(md5plus($data_event['id']),$data_user['birthdate']);
						$data_user['is_early_bird'] = $price['is_early_bird'];
						$data_user['sub_price']     = ($price['price']) ? number_format($price['price'],0,',','.') : '';
						$data_user['total_price']   = $data_user['sub_price'];
						$data_user['birthdate']     = iso_date_custom_format($data_user['birthdate'],'d-m-Y');
						$data_user['disabled_user'] = 'disabled';
						$data_user['fullname']      = $data_user['username'];

						unset($data_user['id']);

						$data = array_merge($data, $data_user);
					}
				}
				
				$data['id_event']    = $id_event;
				$data['id_group']    = group_id();
				$data['is_editable'] = (strtotime($data_event['start_date']) > time() && $data_event['id_ref_status_payment'] != 2) ? 1 : 0;
				
				$data['list_user'] = selectlist2(array(
					'table'      => 'auth_user',
					'where'      => "is_delete = 0 AND id_auth_user_grup = 4",
					'selected'   => md5plus($data['id_auth_user']),
					'name'       => 'email',
					'id'         => 'id_auth_user',
					'is_encrypt' => 1
				));

				$data['checked_male']          = $data['gender'] == 'M' ? 'checked' : '';
				$data['checked_female']        = $data['gender'] == 'F' ? 'checked' : '';
				$data['checked_is_early_bird'] = $data['is_early_bird'] == 'Y' ? 'checked' : '';
				
				$data['list_negara']   = selectlist2(array(
					'table'    => 'ref_negara',
					'where'    => 'is_delete = 0',
					'selected' => $data['kode_ref_negara'],
					'id'       => 'code' 
				));

				$data['list_provinsi'] = selectlist2(array(
					'table'    => 'ref_provinsi',
					'where'    => 'is_delete = 0',
					'selected' => $data['kode_ref_provinsi'],
					'name'     => 'provinsi',
					'id'       => 'kode_provinsi',
				));

				load_js('jquery.number.js','assets/js');
				load_js('participant.js','assets/js/modules/event');

				render('apps/event/participant_add',$data,'apps');
			} else {
				redirect('apps/event');
			}
		} else {
			redirect('apps/event');
		}
	}

	function participant_proses($idedit=''){
		$post         = purify(null_empty($this->input->post()));
		$ret['error'] = 1;

		$post['id_event']   = db_get_one('event','id',md5field('id')." = '$post[id_event]'");
		if ($post['id_event']) {
			$this->form_validation->set_rules('fullname', '"Nama Lengkap"', 'required'); 
			$this->form_validation->set_rules('email', '"Email"', 'required'); 
			$this->form_validation->set_rules('phone', '"Phone"', 'required');
			$this->form_validation->set_rules('gender', '"Jenis Kelamin"', 'trim|required');
			$this->form_validation->set_rules('birthdate', '"Tanggal Lahir"', 'trim|required');
			$this->form_validation->set_rules('total_price', '"Total Bayar"', 'trim|required');

			if (!$idedit) {
				$this->form_validation->set_rules('id_auth_user', '"ID Pengguna"', 'required'); 
			} 

			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}
			else{
				$this->db->trans_start();

				$post['birthdate']   = iso_date_custom_format($post['birthdate'],'Y-m-d');
				$post['sub_price']   = (float)str_replace('.', '', $post['sub_price']);
				$post['total_price'] = (float)str_replace('.', '', $post['total_price']);

				if ($post['id_auth_user']) {
					$post['id_auth_user']   = db_get_one('auth_user','id_auth_user',md5field('id_auth_user')." = '$post[id_auth_user]'");
				} 
				
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Participant";
					$this->EventParticipantModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act            = "Insert Participant";
					$idedit         = $this->EventParticipantModel->insert($post);
				}

				detail_log();
				insert_log($act);
				$this->db->trans_complete();
				set_flash_session('message',$ret['message']);
				$ret['error'] = 0;
			}
		}
		
		echo json_encode($ret);
	}

	function get_detail_participant() {
		$get = purify($this->input->get());
		$id  = $get['id'];

		$data['msg']   = 'Data tidak ditemukan!';
		$data['error'] = 1;

		$this->load->model('UserModel');

		$data = $this->UserModel->findBy(md5field('a.id_auth_user')." = '$id'",1);
		if ($data) {
			$data['sub_price']   = $this->get_event_price($get['id_event'],$data['birthdate'],'N');
			$data['birthdate']   = iso_date_custom_format($data['birthdate'],'d-m-Y');
			$data['error']       = 0;
		}

		echo json_encode($data);
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

	function event_price() {
		$get = purify($this->input->get());
		$is_early_bird    = ($get['is_early_bird']) ? 'Y' : 'N';
		$get['birthdate'] = iso_date_custom_format('Y-m-d',$get['birthdate']);
		
		$price = $this->get_event_price($get['id_event'],$get['birthdate'],$is_early_bird);
		
		if($price==0){
			$price = $this->get_event_price($get['id_event'],$get['birthdate'],'N');
		}
		echo json_encode($price); 
	}

	private function get_event_price($id_event,$birthdate,$is_early_bird) {
		$age = calculate_age($birthdate);
		$this->load->model('EventPriceModel');
		
		$this->db->where('min_age <=',$age);
		$this->db->where('max_age >=',$age);
		$this->db->where('is_early_bird',$is_early_bird);
		$data = $this->EventPriceModel->findBy(md5field('id_event')." = '$id_event'",1);

		return ($data) ? $data['price'] : 0;
	}	

	private function get_event_price_auto($id_event,$birthdate) {
		$age = calculate_age($birthdate);
		$this->load->model('EventPriceModel');
		
		$data_price = $this->EventPriceModel->findBy(md5field('id_event')." = '$id_event'");

		$data['is_early_bird'] = '';
		$data['price']         = '';

		foreach ($data_price as $key => $value) {
			if (strtotime($value['start_date']) <= time()
				&& strtotime($value['end_date']) >= time()
			) {
				$data['is_early_bird'] = $value['is_early_bird'];
				$data['price']         = ($value['price']) ? $value['price'] : 0;
			}
		}
		return $data;
	}

	function participant_del() {
		auth_delete();
		$id = $this->input->post('iddel');
		$this->EventParticipantModel->delete($id);
		detail_log();
		insert_log("Delete Event Participant");
	}

	function participant_cancel() {
		auth_delete();
		$id_event_participant = $this->input->post('iddel');
		$data_participant     = $this->EventParticipantModel->findBy(
			md5field('a.id')." = '$id_event_participant'"
		,1);

		if (group_id() == 4 
			&& !empty($data_participant) 
			&& $data_participant['id_auth_user'] == id_user()
		) {
			$this->EventParticipantModel->update(array('is_delete'=>2),$data_participant['id']);
			detail_log();
			insert_log("Cancel Event Participant");
		}
	}

	function payment($id_event,$id_event_participant) {
		if ($id_event) {
			$event_name   = db_get_one('event','name',md5field('id')." = '$id_event'");

			if ($event_name) {
				$data_participant     = $this->EventParticipantModel->findBy(
					md5field('a.id_event')." = '$id_event' AND ".
					md5field('a.id')." = '$id_event_participant'"
				,1);

				if ($data_participant) {
					$this->load->model('EventParticipantPaymentModel');
					$data = $this->EventParticipantPaymentModel->findBy(array(
								'a.id_event_participant' => $data_participant['id'] 
							),1);

					if ($data) {
						$data['proses']              = 'Update';
						$data['payment_date']        = iso_date_custom_format($data['payment_date'],'d-m-Y');
					} else {
						$data['proses']                = 'Save';
						$data['total_price']           = '';
						$data['total_payment']         = $data_participant['total_price'];
						$data['id_ref_bank']           = '';
						$data['bank_account_name']     = '';
						$data['bank_account_number']   = '';
						$data['payment_date']          = '';
						$data['note']                  = '';
						$data['id']                    = '';
					}

					$data['list_bank'] = selectlist2(array(
						'table'    => 'ref_bank',
						'selected' => $data['id_ref_bank'],				
					));

					$data['name_event']           = $event_name;
					$data['id_event']             = $id_event;
					$data['id_event_participant'] = $id_event_participant;
					$data['total_price']          = number_format($data_participant['total_price'],0,',','.');
					$data['total_payment']        = number_format($data['total_payment'],0,',','.');
					$data['participant_email']    = $data_participant['email'];

					load_js('jquery.number.js','assets/js');
					load_js('payment.js','assets/js/modules/event');
					render('apps/event/payment',$data,'apps');
				} else {
					if (group_id() == 4) {
						redirect("apps/event");
					}
					else {
						redirect("apps/event/$id_event/participant/$id_event_participant");
					}
				}
			} else {
				redirect('apps/event');
			}
		} else {
			redirect('apps/event');
		}
	}

	function payment_process(){
		$post         = purify(null_empty($this->input->post()));
		$ret['error'] = 1;

		$post['id_event_participant'] = db_get_one('event_participant','id',md5field('id')." = '$post[id_event_participant]'");
		if ($post['id_event_participant']) {
			$this->form_validation->set_rules('id_ref_bank', '"Nama Bank"', 'required'); 
			$this->form_validation->set_rules('bank_account_name', '"Nama Pemilik Rekening"', 'required'); 
			$this->form_validation->set_rules('bank_account_number', '"Nomor Rekening"', 'required');
			$this->form_validation->set_rules('total_price', '"Total Harga"', 'trim|required');
			$this->form_validation->set_rules('total_payment', '"Total Bayar"', 'trim|required');
			$this->form_validation->set_rules('payment_date', '"Tanggal Bayar"', 'trim|required');

			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}
			else{
				$idedit = $post['id_event_participant_payment'];
				unset($post['id_event_participant_payment']);

				$this->load->model('EventParticipantPaymentModel');

				$this->db->trans_start();
				$post['payment_date']  = iso_date_custom_format($post['payment_date'],'Y-m-d');
				$post['total_price']   = (float)str_replace('.', '', $post['total_price']);
				$post['total_payment'] = (float)str_replace('.', '', $post['total_payment']);
		
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Participant Payment";
					$this->EventParticipantPaymentModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act            = "Insert Participant Payment";
					$idedit         = $this->EventParticipantPaymentModel->insert($post);
				}

				detail_log();
				insert_log($act);
				$this->db->trans_complete();
				set_flash_session('message',$ret['message']);
				$ret['error'] = 0;
			}
		}
		
		echo json_encode($ret);
	}

	function get_bank_info() {
		$id_bank     = purify($this->input->get('id_bank'));
		$description = db_get_one('ref_bank','description',"id=$id_bank");
		
		echo json_encode($description);
	}

	function questionnaire($id_event) {
		if ($id_event && group_id() != 4) { // not member (peserta)
			$check_event   = db_get_one('event','name',md5field('id')." = '$id_event'");
			$id_event_current   = db_get_one('event','id',md5field('id')." = '$id_event'");

			if ($check_event) {
				$data['idx_event']   = $id_event;
				$data['event_name']  = $check_event;
				$this->load->model("QuestionnaireModel");
				$this->load->model("QuestionnaireAnswerModel");
				$this->load->model("EventQuestionnaireAnswerModel");
				$this->db->order_by("id", "asc");
				$data['list_questionnaire']   = $this->QuestionnaireModel->findBy(array());
				$counter = 1;
				foreach ($data['list_questionnaire'] as $key => $value) {
					$this->db->order_by("id", "asc");
					$list_answer   = $this->QuestionnaireAnswerModel->findBy(array("id_questionnaire" => $value['id']));
					$data['list_questionnaire'][$key]["id_questionnaire"] = $value['id'];
					$data['list_questionnaire'][$key]["no"] = $counter++;
					foreach ($list_answer as $key_answer => $value_answer){
						$data['list_questionnaire'][$key]["total_question"][] = array(
							"label" => $value_answer['name'],
							"data" => $this->EventQuestionnaireAnswerModel->records(array(
								"id_event" => $id_event_current, 
								"id_questionnaire" => $value['id'], 
								"id_questionnaire_answer" => $value_answer['id']
							), 1)
						);
					}
					// print_r($this->db->last_query());exit();
					$data['list_questionnaire'][$key]["total_question"] = json_encode($data['list_questionnaire'][$key]["total_question"]);
				}
				$data['list_questionnaire_chart'] = $data['list_questionnaire'];
				$this->db->group_by("id_input");
				$data['total_participant'] = $this->EventQuestionnaireAnswerModel->records(array(
					"id_event" => $id_event_current, 
				), 1);
				$this->db->group_by("id_input");
				$this->db->where("a.user_id_create is null");
				$data['total_participant_non_member'] = $this->EventQuestionnaireAnswerModel->records(array(
					"id_event" => $id_event_current, 
				), 1);
				$this->db->group_by("a.user_id_create");
				$this->db->where("a.user_id_create is not null");
				$data['total_participant_member'] = $this->EventQuestionnaireAnswerModel->records(array(
					"id_event" => $id_event_current, 
				), 1);
				render('apps/event/questionnaire_index',$data,'apps');
			} else {
				redirect('apps/event');
			}
		} else {
			redirect('apps/event');
		}
	}
}

/* End of file event.php */
/* Location: ./application/controllers/apps/event.php */