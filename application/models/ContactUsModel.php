<?php

class ContactUsModel extends  CI_Model{
	var $table = 'contact_us';
	var $tableAs = 'contact_us a';
    function __construct(){
       parent::__construct();
    }
	function records($where=array(),$isTotal=0,$isdelete=0){
		$alias['search_fullname'] 		= 'a.fullname';
		$alias['search_email'] 			= 'a.email';

	 	query_grid($alias,$isTotal);
		$this->db->select("*");
		if($isdelete==0){
			$this->db->where('a.is_delete !=',1);
		}
		$query = $this->db->get($this->tableAs, $where);

		if($isTotal==0){
			$data = $query->result_array();
		}else{
			return $query->num_rows();
		}
		$ttl_row = $this->records($where,1);
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = null;#visitor
		sent_email_by_category(2,$data, $data['email']);
		sent_email_by_category(3,$data, EMAIL_CUSTOMER_SERVICE);
		$this->db->insert($this->table,array_filter($data));
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}
	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function reject($id){
		$data['is_delete'] = 2;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete !='] = 1;
		$this->db->select('a.*');
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete !='] = 1;
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function export_to_excel($where,$is_single_row=0){
		$this->db->select("a.*,b.name as topic,a.message as komentar");
		$this->db->where('a.is_delete !=',1);

		if($is_single_row==1){
			return $this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}	
	}
}

/* End of file ContactUsModel.php */
/* Location: ./application/models/ContactUsModel.php */