<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}



class Cek_dokumen extends JINGGA_Controller
{

    function __construct()
    {

        parent::__construct();

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

        header("If-Modified-Since: Mon, 22 Jan 2008 00:00:00 GMT");

        header("Cache-Control: no-store, no-cache, must-revalidate");

        header("Cache-Control: post-check=0, pre-check=0", false);

        header("Cache-Control: private");

        header("Pragma: no-cache");



        $this->nsmarty->assign('acak', md5(date('H:i:s')));

        $this->temp = "backend/";

        $this->load->model('mbackend');

        $this->load->library(array('encrypt', 'lib'));



        $array_setting = array(
            'a.cl_provinsi_id' => $this->auth['cl_provinsi_id'],
            'a.cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
            'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
            'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
        );
        $this->setting = $this->db->where($array_setting)->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')->get("tbl_setting_apps a")->row_array();



        if ($this->setting) {

            $this->nsmarty->assign("isi_setting", 'ada');
        }



        $this->nsmarty->assign("main_css", $this->lib->assetsmanager('css', 'main'));

        $this->nsmarty->assign("main_js", $this->lib->assetsmanager('js', 'main'));

        $this->nsmarty->assign("setting", $this->setting);

        $this->nsmarty->assign("tahun", $this->auth['tahun']);

        $this->nsmarty->assign("cl_user_group_id", $this->auth['cl_user_group_id']);

        $this->nsmarty->assign("startDate", datepicker_range($this->auth['tahun'], 'startDate'));

        $this->nsmarty->assign("endDate", datepicker_range($this->auth['tahun'], 'endDate'));
        //Tambahan yunia untuk usulan penilaian
        $this->nsmarty->assign("host", base_url());
    }

    function get_qrcode_dokumen()
    {
        $this->load->library('Qr');

        $setting = "" . $this->auth['cl_kelurahan_desa_id'] . "";

        $randomized_string = randomize_letters($setting);
        // URL untuk QR code
        $urlfile = base_url("cek-dokumen/") . $randomized_string;

        $dir = date('Ymd');
        $tempdir = FCPATH . "__repository/qrcode/" . $this->auth['cl_kelurahan_desa_id'] . "/";
        if (!is_dir($tempdir)) {
            mkdir($tempdir, 0755, true); // Buat folder jika belum ada
        }

        // Hapus file lama
        $files = glob($tempdir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Hapus file
            }
        }

        $filename = 'qr_' . md5($urlfile) . '.png';
        $filepath = $tempdir . $filename;

        $fpath = "__repository/qrcode/" . $this->auth['cl_kelurahan_desa_id'] . "/" . $filename;

        // Menghasilkan QR Code
        $quality = 'H'; // Kualitas QRCode
        $ukuran = 8; // Ukuran besar QRCode
        $padding = 0;

        // Generate QR Code
        QRCode::png($urlfile, $filepath, $quality, $ukuran, $padding);

        // Kirimkan respon ke view
        $this->db->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);
        $query = $this->db->update('tbl_setting_apps', [
            'qr_kelurahan' => $fpath,
        ]);
        if (!$query) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan QR Code ke database.'
            ]);
            return;
        }
        $data = [
            'status' => 'success',
            'message' => 'QR Code berhasil dibuat',
            'qr_code_url' => base_url("__repository/qrcode/" . $this->auth['cl_kelurahan_desa_id'] . "/" . $filename)
        ];

        echo json_encode($data);
    }

    function view_dokumen()
    {
        $encrypted_setting = $this->uri->segment(2);
        $decrypted_setting = preg_replace('/[^0-9]/', '', $encrypted_setting);
        if (empty($decrypted_setting)) {
            show_404();
            return;
        }

        $sql_kel = $this->db->query("SELECT * FROM tbl_setting_apps WHERE cl_kelurahan_desa_id = ?", [$decrypted_setting])->row();
        $data['nama_kelurahan'] = $sql_kel->nama_desa;
        $data['nama_kecamatan'] = $sql_kel->nama_kecamatan;
        $data['cl_kelurahan_desa_id'] = $decrypted_setting;
        $this->load->view('cek_dokumen', $data);
    }

    function cek_validasi()
    {

        $kode = $this->input->post('kode');
        $p1 = '';
        $p2 = '';
        $p3 = '';
        $cl_kelurahan_desa_id = '';
        $cek = $this->db->get_where('tbl_data_surat', array('kode_unik' => $kode));
        if ($cek->num_rows() == 0) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
            ));
            return;
        }
        if (!empty($kode)) {
            $this->db->select('id,cl_jenis_surat_id,tbl_data_penduduk_id,cl_kelurahan_desa_id');
            $this->db->where('kode_unik', $kode);
            $query = $this->db->get('tbl_data_surat');
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $p3 = $row->id;
                $p1 = $row->cl_jenis_surat_id;
                $p2 = $row->tbl_data_penduduk_id;
                $cl_kelurahan_desa_id = $row->cl_kelurahan_desa_id;
            } else {
                $this->session->set_flashdata('error', 'Kode tidak valid');
                redirect('obackendpage');
            }
        }


        $data = $this->mbackend->getdata('tbl_setting_apps', 'row_array', $cl_kelurahan_desa_id);

        $array_setting = array(
            'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
            'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
            'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
            'c.id'              => $p3
        );

        $this->setting = $this->db->select('a.*')->where($array_setting)
            ->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
            ->join('tbl_data_surat c', 'a.cl_kelurahan_desa_id=c.cl_kelurahan_desa_id', 'inner')
            ->get("tbl_setting_apps a")->row_array();

        $this->nsmarty->assign("setting", $this->setting);

        $data = $this->mbackend->getdata('cetak_surat', 'variable', $p1, $p2, $p3);

        $jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $p1))->row_array();

        $filename = str_replace(" ", "_", $jenis_surat['jenis_surat']) . "_" . $p2;

        $temp = "backend/cetak/surat_" . $p1 . ".html";

        $this->nsmarty->assign('data', $data);

        if ($data['data_surat'] <> '') {
            $htmlcontent = $data['data_surat'];
        } else {
            if ($p1==143) {
                $htmlcontent = '';
                $i = 1;
                foreach ($data['surat']['info_tambahan']['pemohon'] as $row) {
                    $data_temp = $data;
                    $row['nama_status_sktm'] = $this->db->where('id',$row['status_sktm'])->get('cl_status_kawin')->row('nama_status_kawin');
                    $row['nama_agama_sktm'] = $this->db->where('id',$row['agama_sktm'])->get('cl_agama')->row('nama_agama');
                    $row['nama_pekerjaan_sktm'] = $this->db->where('id',$row['pekerjaan_sktm'])->get('cl_jenis_pekerjaan')->row('nama_pekerjaan');
                    $data_temp['surat']['info_tambahan']['pemohon'] = $row;
                    // $this->nsmarty->assign('mod', $mod);
                    $this->nsmarty->assign('data', $data_temp);
                    $htmlcontent .= $this->nsmarty->fetch($temp);
                    if (count($data['surat']['info_tambahan']['pemohon']) != $i) {
                        $htmlcontent .= '<pagebreak />';
                    }
                    $i++;
                }
            }else{
                $htmlcontent = $this->nsmarty->fetch($temp);
            }

        }


        if ($data) {
            $this->nsmarty->assign("setting", $data);
            $this->nsmarty->assign("isi_setting", 'ada');
        } else {
            $this->nsmarty->assign("isi_setting", 'tidak ada');
        }

        if ($htmlcontent <> '') {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Data berhasil ditemukan',
                'data' => $data,
                'jenis_surat' => $jenis_surat,
                'content' => $htmlcontent
            ));
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
            ));
        }
    }
}
