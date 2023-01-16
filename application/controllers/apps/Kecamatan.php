<?php

class Kecamatan extends CI_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->model('Kabupaten_model');
        $this->load->model('Kecamatan_model');
	}

	function index()
	{
        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));

        render('apps/kecamatan/index', $data, 'apps');
	}

    function records()
    {
        $data = $this->Kecamatan_model->records();

        render('apps/kecamatan/records', $data, 'blank');
    }

    function add($id='')
    {
        if ($id)
        {
            $data = $this->Kecamatan_model->findById($id);

            if (!$data)
            {
                die('404');
            }

            $data           = quote_form($data);
            $data['judul']  = 'Sunting';
            $data['proses'] = 'Update';
        }

        else
        {
            $data['judul'] = 'Tambah';
            $data['proses'] = 'Simpan';
            $data['kd_kec'] = '';
            $data['kode_kecamatan'] = '';
            $data['kecamatan'] = '';
            $data['id_kecamatan'] = '';
            $data['kode_kabupaten'] = '';
            $data['kd_kab'] = '';

            $data['status_publish']     = '';
            // $data['create_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['modify_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['user_id_create']     = '';
            $data['user_id_modify']     = '';
        }

        $img_thumb                      = image($data['img'],'small');
        $imagemanager                   = imagemanager('img',$img_thumb,277,150);
        $data['img']                    = $imagemanager['browse'];
        $data['imagemanager_config']    = $imagemanager['config'];


        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['status_publish']));

        $data['list_provinsi'] = selectlist2(
            array(
            'table'=>'ref_provinsi',
            'id' => 'kode_provinsi',
            'name' => 'provinsi',
            'title'=>'Pilih provinsi',
            'selected'=>$data['provinsi'],
            'provinsi'=>'provinsi',
            'where'=> 'is_delete=0')
        );

        $data['list_kabupaten'] = selectlist2(
            array(
            'table'     => 'ref_kabupaten',
            'id'        => 'kode_kabupaten',
            'name'      => 'kabupatenkota',
            'title'     => 'Pilih kabupaten',
            'selected'  => $data['kode_kabupaten'],
            'kabupaten' => 'kabupaten',
            'where'     => 'is_delete=0')
        );

        render('apps/kecamatan/add', $data, 'apps');
    }

    function proses($idedit='')
    {
        $this->layout   = 'none';
        $post           = purify($this->input->post());
        $ret['error']   = 1;

        $this->form_validation->set_rules('kd_kec', '"kd_kec"', 'required');
        $this->form_validation->set_rules('kode_kecamatan', '"Kode Kecamatan"', 'required');
        $this->form_validation->set_rules('kecamatan', '"Kecamatan"', 'required');
        $this->form_validation->set_rules('kode_kabupaten', '"Kode kabupaten"', 'required');


        if ($this->form_validation->run() == FALSE)
        {
            $ret['message']  = validation_errors(' ',' ');
        }
        else
        {
            $kabupaten   = $this->Kabupaten_model->findBy(array('kode_kabupaten'=>$post['kode_kabupaten']),1);
            $this->db->trans_start();
            if ($idedit)
            {
                auth_update();
                $ret['message'] = 'Update Success';
                $act            = "Update News";
                $post['kd_kab'] = $kabupaten['kd_kab'];
                $idedit         = $this->Kecamatan_model->update($post, $idedit);
            }
            else
            {
                auth_insert();
                $ret['message'] = 'Insert Success';
                $act            = "Insert News";
                $post['kd_kab'] = $kabupaten['kd_kab'];
                $post['kode_kabupaten'] = $kabupaten['kode_kabupaten'];
                $idedit         = $this->Kecamatan_model->insert($post);

            }

            $ret['error']   = 0;
            $this->db->trans_complete();
        }

        echo json_encode($ret);
    }

    function del()
    {
        auth_delete();
        $id     = $this->input->post('iddel');
        $data   = $this->Kecamatan_model->delete($id);
        detail_log();
        insert_log("Delete Pages");
    }

    public function import()
    {
        $this->db->trans_start();
        $ret['status']  = 0;
        $ret['message'] = 'Import Success';
        $act            = "Import Master Kecamatan";

        $fileName = $_FILES['import']['name'];

        $config = upload_file('import','','xls|xlsx|csv',10000,UPLOAD_DIR_KECAMATAN);
        $inputFileName = $config['full_path'];

        $user_sess = $this->session->userdata('ADM_SESS');
        $is_instansi = $user_sess['is_instansi'];
        $id_instansi = $user_sess['id_instansi'];

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheet_kecamatan = $objPHPExcel->getSheet(0);
        $highestRow_kecamatan = $sheet_kecamatan->getHighestRow();
        $highestColumn_kecamatan = $sheet_kecamatan->getHighestColumn();

        for ($row = 2; $row <= $highestRow_kecamatan; $row++)
        {
            $rowData = $sheet_kecamatan->rangeToArray('A' . $row . ':' . $highestColumn_kecamatan . $row, NULL,   TRUE, FALSE);
            $uri_path = generate_url($rowData[0][5]);
            $data = array(
                        "kd_kec" => $rowData[0][1],
                        "kode_kecamatan" => $rowData[0][2],
                        "kd_kab" => $rowData[0][3],
                        "kode_kabupaten" => $rowData[0][4],
                        "kecamatan" => $rowData[0][5],
                        "uri_path" => $uri_path,
                        "is_delete" => $rowData[0][7],
                        "status_publish" => $rowData[0][8],
                        "user_id_create"=>id_user(9),
                        "create_date"=>date('Y-m-d H:i:s'),
                        "user_id_modify" => $rowData[0][11],
                        "modify_date" => $rowData[0][12]
                        );
            $this->Kecamatan_model->insert($data);
        }
        //$this->db->query("DELETE FROM ref_kabupaten WHERE uri_path = 'n-a' AND is_delete = 0");
        $this->db->trans_complete();
        detail_log();
        insert_log('Import data Kecamatan');
        $ret['error'] = 0;
        echo json_encode($ret);
    }

}
