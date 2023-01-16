<?php
class EventModel extends  CI_Model{
	var $table = 'event';
	var $tableAs = 'event a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_description'] = 'a.description';
	 	
	 	query_grid($alias,$isTotal);

		$this->db->select("a.id,
			a.name as event_name, 
			a.description as event_desc,
			a.start_date,
			a.end_date,
			b.name as event_category, 
			c.name as event_status
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->where('a.is_delete', 0);

		if (group_id() == 5) { // EO 
			$this->db->where('a.user_id_create', id_user());
		}
		else if (group_id() == 4) { // Member (Peserta)
			$this->db->select("d.code");
			$this->db->join('event_participant d',"a.id=d.id_event 
								AND d.is_delete=0 
								AND d.id_auth_user =".id_user()
							);
			$this->db->where('a.id_ref_event_status', 1);
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
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['update_date'] 	= date('Y-m-d H:i:s');
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

		if (group_id() == 5) { // if login as EO 
			$this->db->where('a.user_id_create', id_user());
		}

		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("a.*");
		$this->db->where('a.is_delete',0);
		
		if (group_id() == 5) { // EO
			$this->db->where('a.user_id_create', id_user());
		}

		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function records_list($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_description'] = 'a.description';
	 	
	 	if (group_id() == 4) {
		 	$this->db->select('GROUP_CONCAT(id_event) as ids_event');
		 	$this->db->where('is_delete', 0);
		 	$this->db->where('id_auth_user', id_user());
		 	$ids_event = $this->db->get('event_participant')->row()->ids_event;
	 	}

	 	query_grid($alias,$isTotal);

		$this->db->select("a.id,
			a.name as event_name, 
			a.description as event_desc,
			a.start_date,
			a.end_date,
			b.name as event_category, 
			c.name as event_status
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->where('a.is_delete', 0);

		if (group_id() == 4) { // Member (Peserta)
			$this->db->join('event_participant d',"a.id=d.id_event
								AND d.is_delete=0"
							,'left');
			$this->db->where_not_in('a.id',explode(',', $ids_event));
			$this->db->where('a.id_ref_event_status', 1);;
			$this->db->where('a.early_bird_start_date <= ', date('Y-m-d'));
			$this->db->where('a.early_bird_end_date >= ', date('Y-m-d'));
		}

		$query = $this->db->get_where($this->tableAs,$where);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records_list($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
 }
