<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kecamatan_model extends CI_Model {

	var $table      = 'ref_kecamatan';
    var $tableAs    = 'ref_kecamatan a';

    function __construct()
    {
    	parent::__construct();
    }

    function records($where = array(), $isTotal = 0)
    {
        $alias['search_kode_kecamatan'] = 'a.kode_kecamatan';
        $alias['search_kecamatan']      = 'a.kecamatan';
        $alias['search_kabupaten']      = 'b.kabupatenkota';


        query_grid($alias,$isTotal);
        $this->db->select('a.*, b.kabupatenkota');
        $this->db->where("a.is_delete", 0);
        $this->db->join('ref_kabupaten b', 'a.kode_kabupaten = b.kode_kabupaten');
        $this->db->group_by('id_kecamatan');

        $query = $this->db->get($this->tableAs);


        if ($isTotal == 0)
        {
            $data = $query->result_array();
        }
        else
        {
            return $query->num_rows();
        }

        $ttl_row = $this->records($where, 1);

        return ddi_grid($data, $ttl_row);
    }

    function insert($data)
    {
        $this->db->insert($this->table, array_filter($data));
        return $this->db->insert_id();
    }

    function update($data, $id)
    {
        $where['id_kecamatan'] = $id;
        $this->db->update($this->table, $data, $where);
    }

    function findById($id)
    {
        $where['is_delete'] = 0;
        $where['a.id_kecamatan'] = $id;

        return $this->db->get_where($this->table.' a', $where)->row_array();
    }

    function findBy($where=array(),$is_single_row=0)
    {
        $where['is_delete'] = 0;
        $this->db->select('*');
        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }

    function updateToDelete($data,$id,$del=0)
    {
        if ($del==1)
        {
            $this->db->query("UPDATE ref_kecamatan SET is_delete=N'$data[is_delete]' WHERE id_kecamatan = $id");
        }
        else
        {
            $this->db->query("UPDATE ref_kecamatan SET page_name=N'$data[page_name]', uri_path=N'$data[uri_path]', teaser=N'$data[teaser]',
            page_content=N'$data[page_content]' WHERE id_kecamatan = $id");
        }
    }

    function delete($id)
    {
        $data['is_delete'] = 1;
        $this->updateToDelete($data, $id, 1);

    }
    function fetchRow($where) {
        return $this->findBy($where,1);
    }

}
