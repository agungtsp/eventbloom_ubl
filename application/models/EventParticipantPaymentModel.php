<?php
class EventParticipantPaymentModel extends  CI_Model{
	var $table = 'event_participant_payment';
	var $tableAs = 'event_participant_payment a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name']                  = 'a.name';
		$alias['search_id_event']              = 'b.id_event';
		$alias['search_email']                 = 'b.email';
		$alias['search_fullname']              = 'b.fullname';
		$alias['search_id_ref_status_payment'] = 'a.id_ref_status_payment';
		$alias['search_id_ref_bank']           = 'a.id_ref_bank';
		$alias['search_code']                  = 'b.code';
	 	
	 	query_grid($alias,$isTotal);

		$this->db->select("
			a.*, 
			b.code,
			b.email, 
			b.fullname, 
			c.name as event_name, 
			d.name as status_payment, 
			e.name as bank_name
		");
		$this->db->join('event_participant b','b.id=a.id_event_participant','left');
		$this->db->join('event c','c.id=b.id_event','left');
		$this->db->join('ref_status_payment d','d.id=a.id_ref_status_payment','left');
		$this->db->join('ref_bank e','e.id=a.id_ref_bank','left');
		$this->db->where('b.is_delete',0);
		$this->db->where('c.is_delete',0);

		if (group_id() == 5) { // if login as EO 
			$this->db->where('c.user_id_create', id_user());
		}

		$query = $this->db->get_where($this->tableAs,$where);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date']    = date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] 			= $id;
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function findById($id){
		$where['a.id'] = $id;
		$this->db->select("a.*, b.email as participant_email, c.name as event_name");
		$this->db->join('event_participant b','b.id=a.id_event_participant','left');
		$this->db->join('event c','c.id=b.id_event','left');
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("a.*");
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 
 }
