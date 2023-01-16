<?php
class EventParticipantModel extends  CI_Model{
	var $table = 'event_participant';
	var $tableAs = 'event_participant a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_code'] = 'a.code';
	 	
	 	query_grid($alias,$isTotal);

		$this->db->select("a.*, c.name as negara, d.name as status_payment, f.name as bank_name");
		$this->db->join('event b','b.id=a.id_event','left');
		$this->db->join('ref_negara c','c.code=a.kode_ref_negara','left');
		$this->db->join('ref_status_payment d','d.id=a.id_ref_status_payment','left');
		$this->db->join('event_participant_payment e','e.id_event_participant=a.id','left');
		$this->db->join('ref_bank f','f.id=e.id_ref_bank','left');
		$this->db->where('a.is_delete',0);

		if (group_id() == 5) { // if login as EO 
			$this->db->where('b.user_id_create', id_user());
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
		$data['create_date']  = date('Y-m-d H:i:s');
		$data['code'] 	  = rand_code(12);
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] 			= $id;
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("a.*");
		$this->db->where('a.is_delete',0);
		
		if (group_id() == 4) {
			$this->db->where('a.id_auth_user', id_user());
		}

		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 
 }
