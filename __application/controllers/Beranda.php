<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}



class Beranda extends JINGGA_Controller
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

        $this->nsmarty->assign("nama_kab_kota", $this->auth['nama_kab_kota']);

        $this->nsmarty->assign("nama_kecamatan", $this->auth['nama_kecamatan']);

        $this->nsmarty->assign("nama_kelurahan_desa", $this->auth['nama_desa']);


        $this->nsmarty->assign("startDate", datepicker_range($this->auth['tahun'], 'startDate'));

        $this->nsmarty->assign("endDate", datepicker_range($this->auth['tahun'], 'endDate'));
        //Tambahan yunia untuk usulan penilaian
        $this->nsmarty->assign("host", base_url());
    }


    function index()
    {

        $kec = $this->db->query("SELECT * FROM cl_kecamatan where 1=1 limit 1")->row();
        $this->nsmarty->assign("profile_kec", $kec->profil);
        $this->nsmarty->assign("nm_kec", $kec->nama);
        $this->nsmarty->assign("geolocation", $kec->geolocation);
        $qu = $this->db->query("SELECT 'TOTAL SURAT' AS ket,
                IFNULL(SUM(CASE WHEN DATE(b.tgl_surat) = CURDATE() THEN 1 ELSE 0 END), 0) AS total_hari_ini,
                IFNULL(SUM(CASE WHEN DATE(b.tgl_surat) = CURDATE() - INTERVAL 1 DAY THEN 1 ELSE 0 END), 0) AS total_kemarin,
                IFNULL(SUM(CASE WHEN YEARWEEK(b.tgl_surat, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END), 0) AS total_minggu_ini,
                IFNULL(SUM(CASE WHEN MONTH(b.tgl_surat) = MONTH(CURDATE()) AND YEAR(b.tgl_surat) = YEAR(CURDATE()) THEN 1 ELSE 0 END), 0) AS total_bulan_ini,
                IFNULL(SUM(CASE WHEN YEAR(b.tgl_surat) = YEAR(CURDATE()) THEN 1 ELSE 0 END), 0) AS total_tahun_ini,
                IFNULL(COUNT(b.tgl_surat), 0) AS total_seluruhnya
            FROM
                tbl_data_surat b
            WHERE 1 = 1;");
        $this->nsmarty->assign("statistik_surat", $qu->result_array());


        $this->nsmarty->assign("main_css", $this->lib->assetsmanager('css', 'login'));

        $this->nsmarty->assign("peta", $this->lib->assetsmanager('js', 'login'));

        $this->nsmarty->display('beranda.html');
    }

    function login_app()
    {
        $this->nsmarty->display('backend/main-login.html');
    }
}
