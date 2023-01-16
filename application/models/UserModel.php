<?php
class UserModel extends  CI_Model{
	var $table = 'auth_user';
	var $tableAs = 'auth_user a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name']      = 'a.name';
		$alias['search_usergroup'] = 'a.id_auth_user_grup';
		$_GET['sort_field']        = ($_GET['sort_field']=="a.id") ? "id_auth_user" : $_GET['sort_field'];

	 	query_grid($alias,$isTotal);

		$this->db->select("a.id_auth_user as id, a.id_auth_user_grup, a.is_delete,grup, a.userid, a.username,a.email,a.phone, b.grup as user_group");
		$this->db->where('a.is_delete',0);
		$this->db->join('auth_user_grup b','b.id_auth_user_grup = a.id_auth_user_grup');
		$query = $this->db->get_where($this->tableAs,$where);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id_auth_user'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id_auth_user'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("a.*, a.id_auth_user as id, grup");
		$this->db->where('a.is_delete',0);
		$this->db->join('auth_user_grup b','b.id_auth_user_grup = a.id_auth_user_grup');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 
 }
