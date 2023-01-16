<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kelurahan_model extends CI_Model {

    var $table      = 'ref_kelurahan';
    var $tableAs    = 'ref_kelurahan a';

	function __construct()
	{
		parent::__construct();
	}

	function records($where = array(), $isTotal = 0)
    {
        $alias['search_kode_kelurahan'] = 'a.kode_kelurahan';
        $alias['search_kelurahan']      = 'a.kelurahan';
        $alias['search_kecamatan']      = 'b.kecamatan';

        query_grid($alias,$isTotal);
        $this->db->select('a.id_kelurahan, a.kode_kelurahan, a.kelurahan, b.kecamatan');
        $this->db->where("a.is_delete", 0);
        $this->db->join('ref_kecamatan b', 'a.kode_kecamatan = b.kode_kecamatan','left');

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

    function update($data, $id)
    {
        $where['id_kelurahan'] = $id;
        $this->db->update($this->table, $data, $where);
    }

    function insert($data)
    {
        $this->db->insert($this->table, array_filter($data));
        $id = $this->db->insert_id();

        return $id;
    }

    function findById($id)
    {
        $where['is_delete'] = 0;
        $where['a.id_kelurahan'] = $id;

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
            $this->db->query("UPDATE ref_kelurahan SET is_delete=N'$data[is_delete]' WHERE id_kelurahan = $id");
        }
        else
        {
            $this->db->query("UPDATE ref_kelurahan SET page_name=N'$data[page_name]', uri_path=N'$data[uri_path]', teaser=N'$data[teaser]',
            page_content=N'$data[page_content]' WHERE id_kelurahan = $id");
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
