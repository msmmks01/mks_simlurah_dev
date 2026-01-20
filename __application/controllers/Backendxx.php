<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}



class Backendxx extends JINGGA_Controller
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

	function nomor_surat()
	{

		echo format_nomor_surat('7371110', '7371110009', 1, '2023-05-06');
	}

	function index()
	{

		if ($this->auth) {

			$menu = $this->mbackend->getdata('menu', 'variable');

			$this->nsmarty->assign('menu', $menu);

			$this->nsmarty->assign('notif',  $this->mbackend->get_notif());

			$this->nsmarty->assign('get_data_bc',  $this->mbackend->get_data_bc());

			$this->nsmarty->assign('subjek',  $this->mbackend->get_subjek());

			$this->nsmarty->display('backend/main-backend.html');
		} else {

			$this->nsmarty->assign("main_css", $this->lib->assetsmanager('css', 'login'));

			$this->nsmarty->assign("main_js", $this->lib->assetsmanager('js', 'login'));

			$this->nsmarty->display('backend/main-login.html');
		}
	}



	function modul($p1 = "", $p2 = "")
	{

		$temp = 'backend/modul/' . $p1 . '/' . $p2 . '.html';

		if ($this->auth) {

			switch ($p1) {

				case "register":



					break;
			}



			$this->nsmarty->assign("main", $p1);

			$this->nsmarty->assign("mod", $p2);



			if (!file_exists($this->config->item('appl') . APPPATH . 'views/' . $temp)) {
				$this->nsmarty->display('konstruksi.html');
			} else {
				$this->nsmarty->display($temp);
			}
		}
	}

	public function do_upload()
	{
		$config['upload_path']          = './uploads/';
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 100;
		$config['max_width']            = 1024;
		$config['max_height']           = 768;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('userfile')) {
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $eridror);
		} else {
			$data = array('upload_data' => $this->upload->data());

			$this->load->view('upload_success', $data);
		}
	}

	function get_grid($mod)
	{

		$temp = 'backend/grid_config.html';

		$this->nsmarty->assign('cl_user_group_id', $this->auth["cl_user_group_id"]);
		$this->nsmarty->assign('data_user_pemeriksa_esign', $this->combo_option('data_user_pemeriksa_esign'));

		$filter = $this->combo_option($mod);

		$cekmenu = $this->db->get_where('tbl_user_menu', array('ref_tbl' => $mod))->row_array();

		if ($cekmenu) {

			$id_modul = $cekmenu['id'];

			$judul = $cekmenu['nama_menu'];
		} else {

			$id_modul = 0;

			$judul = "Data Development";
		}

		switch ($mod) {

			case "laporan_penduduk":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "data_penduduk":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "data_penduduk_asing":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "data_keluarga":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "data_sekolah":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_umkm":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_petugas_kebersihan":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_tempat_ibadah":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_pkl":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;

			case "surat_masuk":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_retribusi_sampah":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_wamis":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_rt_rw":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;

			case "data_hasil_skm":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "penilaian_rt_rw":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_4", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nik_lsm", $this->lib->fillcombo("pilih_ttd_lainnya", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nik_pembuat", $this->lib->fillcombo("pilih_ttd_lain_pembuat", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			// case "usulan_penilaian_rt_rw":

			// 	$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

			// 	$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

			// 	$this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return"));

			// 	$this->nsmarty->assign("status_penilaian", $this->lib->fillcombo("status_penilaian", "return"));
			// 	break;
			case "rekap_penilaian_kelrtrw":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_4", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nik_lsm", $this->lib->fillcombo("pilih_ttd_lainnya", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nik_pembuat", $this->lib->fillcombo("pilih_ttd_lain_pembuat", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return"));

				$this->nsmarty->assign("rw", $this->lib->fillcombo("rw", "return"));

				break;
			case "data_lorong":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;

			case "data_rekap_bulan":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_5", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$list_bulan = array(
					'' => '--- Pilih ---',
					'1' => 'Januari',
					'2' => 'Februari',
					'3' => 'Maret',
					'4' => 'April',
					'5' => 'Mei',
					'6' => 'Juni',
					'7' => 'Juli',
					'8' => 'Agustus',
					'9' => 'September',
					'10' => 'Oktober',
					'11' => 'November',
					'12' => 'Desember'
				);
				$this->nsmarty->assign('list_bulan', $list_bulan);

				break;

			case "daftar_agenda_kegiatan":

				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_5", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;

			case "laporan_hasil_kegiatan":

				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_5", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "data_dasawisma":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
			case "data_faskes":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatangananx", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;

			case "laporan_persuratan":
				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_all", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				$this->nsmarty->assign("pilih_jsurat", $this->lib->fillcombo("pilih_jsurat", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;

			case "data_pegawai_kel_kec":
				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan_3", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;

			case "laporan_kendaraan":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("nama_sopir", $this->lib->fillcombo("nama_sopir", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case "laporan_rekap_usaha":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("pilih_jsurat", $this->lib->fillcombo("pilih_jsurat", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				// $this->nsmarty->assign("pilih_jsurat", $this->lib->fillcombo("pilih_jsurat", "return", ($sts == "edit" ? $data["jenis_surat"] : "")));

				break;

			case "laporan_rekap_pengantar_kendaraan":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("pilih_jsurat", $this->lib->fillcombo("pilih_jsurat", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				// $this->nsmarty->assign("pilih_jsurat", $this->lib->fillcombo("pilih_jsurat", "return", ($sts == "edit" ? $data["jenis_surat"] : "")));

				break;
			case "laporan_persuratan_rt_rw":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;

			case "laporan_persuratan_masuk":

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				break;
			case 'data_penilaian_skm':
				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));
				break;
		}

		$prev = $this->mbackend->getdata("previliges_menu", "row_array", $id_modul, $this->auth["cl_user_group_id"]);



		$this->nsmarty->assign('data_select', $filter);

		$this->nsmarty->assign('mod', $mod);

		$this->nsmarty->assign('judul', $judul);

		$this->nsmarty->assign('prev', $prev);

		if (!file_exists($this->config->item('appl') . APPPATH . 'views/' . $temp)) {
			$this->nsmarty->display('konstruksi.html');
		} else {
			$this->nsmarty->display($temp);
		}
	}



	function get_grid_report($mod)
	{

		$temp = 'backend/__modul/grid_report_config.html';



		$cekmenu = $this->db->get_where('tbl_user_menu', array('ref_tbl' => $mod))->row_array();

		if ($cekmenu) {

			$id_modul = $cekmenu['id'];

			$judul = $cekmenu['nama_menu'];
		} else {

			$id_modul = 0;

			$judul = "Data Development";
		}



		switch ($mod) {

			case "list_work_order":

				$filter = $this->combo_option($mod);

				$prev = $this->mbackend->getdata("previliges_menu", "row_array", $id_modul, $this->auth["cl_user_group_id"]);



				$this->nsmarty->assign('upd', $this->lib->fillcombo('upd', 'return'));

				$this->nsmarty->assign('bo', $this->lib->fillcombo('staff_bo', 'return'));

				$this->nsmarty->assign('request_type', $this->lib->fillcombo('cl_request_type', 'return'));

				$this->nsmarty->assign('request_status', $this->lib->fillcombo('request_status_filter', 'return'));

				$this->nsmarty->assign('data_select', $filter);

				$this->nsmarty->assign('prev', $prev);

				break;
		}



		$this->nsmarty->assign('mod', $mod);

		$this->nsmarty->assign('judul', $judul);

		if (!file_exists($this->config->item('appl') . APPPATH . 'views/' . $temp)) {
			$this->nsmarty->display('konstruksi.html');
		} else {
			$this->nsmarty->display($temp);
		}
	}



	function get_form($mod)
	{

		$temp = 'backend/form/' . $mod . ".html";

		$sts = $this->input->post('editstatus');

		switch ($mod) {

			case "permohonan":
				$data = $this->mbackend->getdata('data_permohonan', 'row_array');

				$this->nsmarty->assign('data', $data);
				break;

			case "beranda_admin_filter":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				$array_penduduk = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $desa_id,

					'status_data' => 'AKTIF'

				);

				$array_kk = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $desa_id,

				);

				$array_surat = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $desa_id,

				);



				$jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', $array_penduduk)->num_rows();

				$jumlah_kk = $this->db->get_where('tbl_kartu_keluarga', $array_kk)->num_rows();

				$jumlah_surat = $this->db->get_where('tbl_data_surat', $array_surat)->num_rows();


				$summary_persuratan = $this->mbackend->getdata('summary_persuratan', 'result_array');

				$jenis_kelamin = $this->mbackend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');

				$agama = $this->mbackend->getdata_laporan('dashboard_agama', 'result_array');

				$sekolah = $this->mbackend->getdata_laporan('dashboard_sekolah', 'result_array');

				$umkm = $this->mbackend->getdata_laporan('dashboard_umkm', 'result_array');

				$kebersihan = $this->mbackend->getdata_laporan('dashboard_kebersihan', 'result_array');

				$ibadah = $this->mbackend->getdata_laporan('dashboard_ibadah', 'result_array');

				$pkl = $this->mbackend->getdata_laporan('dashboard_pkl', 'result_array');

				$wamis = $this->mbackend->getdata_laporan('dashboard_wamis', 'result_array');

				$longwis = $this->mbackend->getdata_laporan('dashboard_longwis', 'result_array');

				$dasawisma = $this->mbackend->getdata_laporan('dashboard_dasawisma', 'result_array');

				$rt_rw = $this->mbackend->getdata_laporan('dashboard_rt_rw', 'result_array');

				$retribusi_sampah = $this->mbackend->getdata_laporan('dashboard_retribusi_sampah', 'result_array');

				$pegawai_kel_kec = $this->mbackend->getdata_laporan('dashboard_pegawai_kel_kec', 'result_array');

				$ktp_tercetak = $this->mbackend->getdata_laporan('dashboard_ktp_tercetak', 'result_array');

				$range_umur = $this->mbackend->getdata_laporan('dashboard_range_umur', 'result_array');



				$status_data = $this->mbackend->getdata_laporan('dashboard_status_data', 'result_array');

				$status_kawin = $this->mbackend->getdata_laporan('dashboard_status_kawin', 'result_array');

				$pendidikan = $this->mbackend->getdata_laporan('dashboard_pendidikan', 'result_array');



				$data = array(

					'jumlah_penduduk' => $jumlah_penduduk,

					'jumlah_kk' => $jumlah_kk,

					'jumlah_surat' => $jumlah_surat,

					'summary_persuratan' => $summary_persuratan,

					'jenis_kelamin' => $jenis_kelamin,

					'agama' => $agama,

					'sekolah' => $sekolah,

					'umkm' => $umkm,

					'kebersihan' => $kebersihan,

					'ibadah' => $ibadah,

					'pkl' => $pkl,

					'wamis' => $wamis,

					'longwis' => $longwis,

					'dasawisma' => $dasawisma,

					'rt_rw' => $rt_rw,

					'retribusi_sampah' => $retribusi_sampah,

					'pegawai_kel_kec' => $pegawai_kel_kec,

					'ktp_tercetak' => $ktp_tercetak,

					'range_umur' => $range_umur,


					'status_data' => $status_data,

					'status_kawin' => $status_kawin,

					'pendidikan' => $pendidikan,

				);



				$this->nsmarty->assign('data', $data);

				break;

			case "beranda_admin":

				$jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', array('status_data' => 'AKTIF'))->num_rows();

				$jumlah_kk = $this->db->get('tbl_kartu_keluarga')->num_rows();

				$jumlah_surat = $this->db->get_where('tbl_data_surat', array('year(tgl_surat)' => $this->auth['tahun']))->num_rows();


				$summary_persuratan = $this->mbackend->getdata('summary_persuratan', 'result_array');


				$summary_penduduk = $this->mbackend->getdata_laporan('dashboard_summary_penduduk', 'result_array');


				$jenis_kelamin = $this->mbackend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');

				$agama = $this->mbackend->getdata_laporan('dashboard_agama', 'result_array');

				$sekolah = $this->mbackend->getdata_laporan('dashboard_sekolah', 'result_array');

				$umkm = $this->mbackend->getdata_laporan('dashboard_umkm', 'result_array');

				$kebersihan = $this->mbackend->getdata_laporan('dashboard_kebersihan', 'result_array');

				$ibadah = $this->mbackend->getdata_laporan('dashboard_ibadah', 'result_array');

				$pkl = $this->mbackend->getdata_laporan('dashboard_pkl', 'result_array');

				$wamis = $this->mbackend->getdata_laporan('dashboard_wamis', 'result_array');

				$longwis = $this->mbackend->getdata_laporan('dashboard_longwis', 'result_array');

				$dasawisma = $this->mbackend->getdata_laporan('dashboard_dasawisma', 'result_array');

				$rt_rw = $this->mbackend->getdata_laporan('dashboard_rt_rw', 'result_array');

				$retribusi_sampah = $this->mbackend->getdata_laporan('dashboard_retribusi_sampah', 'result_array');

				$pegawai_kel_kec = $this->mbackend->getdata_laporan('dashboard_pegawai_kel_kec', 'result_array');

				$ktp_tercetak = $this->mbackend->getdata_laporan('dashboard_ktp_tercetak', 'result_array');

				$range_umur = $this->mbackend->getdata_laporan('dashboard_range_umur', 'result_array');


				$status_data = $this->mbackend->getdata_laporan('dashboard_status_data', 'result_array');

				$status_kawin = $this->mbackend->getdata_laporan('dashboard_status_kawin', 'result_array');

				$pendidikan = $this->mbackend->getdata_laporan('dashboard_pendidikan', 'result_array');



				$data = array(

					'jumlah_penduduk' => $jumlah_penduduk,

					'jumlah_kk' => $jumlah_kk,

					'jumlah_surat' => $jumlah_surat,


					'summary_persuratan' => $summary_persuratan,

					'summary_penduduk' => $summary_penduduk,


					'jenis_kelamin' => $jenis_kelamin,

					'agama' => $agama,

					'sekolah' => $sekolah,

					'umkm' => $umkm,

					'kebersihan' => $kebersihan,

					'ibadah' => $ibadah,

					'pkl' => $pkl,

					'wamis' => $wamis,

					'longwis' => $longwis,

					'dasawisma' => $dasawisma,

					'rt_rw' => $rt_rw,

					'retribusi_sampah' => $retribusi_sampah,

					'pegawai_kel_kec' => $pegawai_kel_kec,

					'ktp_tercetak' => $ktp_tercetak,

					'range_umur' => $range_umur,


					'status_data' => $status_data,

					'status_kawin' => $status_kawin,

					'pendidikan' => $pendidikan,

				);



				$this->nsmarty->assign('data', $data);

				$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return"));

				break;

			// case "beranda_admin":

			// 	// SESSION & TAHUN LOGIN
			// 	$session_data = unserialize(base64_decode($this->session->userdata('s3ntr4lb0')));
			// 	$tahun_login  = isset($session_data['tahun']) ? $session_data['tahun'] : date('Y');

			// 	// FILTER DASAR (ADMIN = GLOBAL)
			// 	$array_penduduk = array(
			// 		'status_data' => 'AKTIF'
			// 	);

			// 	// JUMLAH DATA UTAMA
			// 	$this->db->where('YEAR(create_date) <=', $tahun_login);
			// 	$jumlah_penduduk = $this->db
			// 		->get_where('tbl_data_penduduk', $array_penduduk)
			// 		->num_rows();

			// 	$jumlah_kk = $this->db
			// 		->get('tbl_kartu_keluarga')
			// 		->num_rows();

			// 	$this->db->where('YEAR(tgl_surat)', $tahun_login);
			// 	$jumlah_surat = $this->db
			// 		->get('tbl_data_surat')
			// 		->num_rows();

			// 	$this->db->where('YEAR(tgl_surat)', $tahun_login);
			// 	$jumlah_surat_masuk = $this->db
			// 		->get('tbl_data_surat_masuk')
			// 		->num_rows();

			// 	// SUMMARY & DASHBOARD (RAW)
			// 	$summary_persuratan = $this->mbackend->getdata(
			// 		'summary_persuratan',
			// 		'result_array'
			// 	);

			// 	$summary_penduduk = $this->mbackend->getdata_laporan(
			// 		'dashboard_summary_penduduk',
			// 		'result_array'
			// 	);

			// 	$jenis_kelamin = $this->mbackend->getdata_laporan(
			// 		'dashboard_jenis_kelamin',
			// 		'result_array',
			// 		$tahun_login
			// 	);

			// 	$agama       = $this->mbackend->getdata_laporan('dashboard_agama', 'result_array');
			// 	$sekolah     = $this->mbackend->getdata_laporan('dashboard_sekolah', 'result_array');
			// 	$ibadah      = $this->mbackend->getdata_laporan('dashboard_ibadah', 'result_array');
			// 	$umkm        = $this->mbackend->getdata_laporan('dashboard_umkm', 'result_array');
			// 	$kebersihan  = $this->mbackend->getdata_laporan('dashboard_kebersihan', 'result_array');
			// 	$pkl         = $this->mbackend->getdata_laporan('dashboard_pkl', 'result_array');
			// 	$wamis       = $this->mbackend->getdata_laporan('dashboard_wamis', 'result_array');
			// 	$longwis     = $this->mbackend->getdata_laporan('dashboard_longwis', 'result_array');
			// 	$dasawisma   = $this->mbackend->getdata_laporan('dashboard_dasawisma', 'result_array');
			// 	$rt_rw       = $this->mbackend->getdata_laporan('dashboard_rt_rw', 'result_array');
			// 	$laporan_hasil_skm = $this->mbackend->getdata_laporan('beranda_hasil_skm', 'result_array');

			// 	$retribusi_sampah = $this->mbackend->getdata_laporan(
			// 		'dashboard_retribusi_sampah',
			// 		'result_array'
			// 	);

			// 	$pegawai_kel_kec = $this->mbackend->getdata_laporan(
			// 		'dashboard_pegawai_kel_kec',
			// 		'result_array'
			// 	);

			// 	$ktp_tercetak = $this->mbackend->getdata_laporan(
			// 		'dashboard_ktp_tercetak',
			// 		'result_array'
			// 	);

			// 	$range_umur = $this->mbackend->getdata_laporan(
			// 		'dashboard_range_umur',
			// 		'result_array'
			// 	);

			// 	$status_data  = $this->mbackend->getdata_laporan('dashboard_status_data', 'result_array');
			// 	$status_kawin = $this->mbackend->getdata_laporan('dashboard_status_kawin', 'result_array');
			// 	$pendidikan   = $this->mbackend->getdata_laporan('dashboard_pendidikan', 'result_array');

			// 	// ===============================
			// 	// NORMALISASI SEMUA DASHBOARD_*
			// 	// ===============================
			// 	$jenis_kelamin     = $this->normalize_dashboard($jenis_kelamin);
			// 	$agama             = $this->normalize_dashboard($agama);
			// 	$pendidikan        = $this->normalize_dashboard($pendidikan);
			// 	$sekolah           = $this->normalize_dashboard($sekolah);
			// 	$ibadah            = $this->normalize_dashboard($ibadah);
			// 	$umkm              = $this->normalize_dashboard($umkm);
			// 	$kebersihan        = $this->normalize_dashboard($kebersihan);
			// 	$pkl               = $this->normalize_dashboard($pkl);
			// 	$wamis             = $this->normalize_dashboard($wamis);
			// 	$longwis           = $this->normalize_dashboard($longwis);
			// 	$dasawisma         = $this->normalize_dashboard($dasawisma);
			// 	$rt_rw             = $this->normalize_dashboard($rt_rw);
			// 	$retribusi_sampah  = $this->normalize_dashboard($retribusi_sampah);
			// 	$pegawai_kel_kec   = $this->normalize_dashboard($pegawai_kel_kec);
			// 	$ktp_tercetak      = $this->normalize_dashboard($ktp_tercetak);
			// 	$range_umur        = $this->normalize_dashboard($range_umur);
			// 	$status_data       = $this->normalize_dashboard($status_data);
			// 	$status_kawin      = $this->normalize_dashboard($status_kawin);


			// 	// ===============================
			// 	// DATA KE VIEW
			// 	// ===============================
			// 	$data = array(
			// 		'jumlah_penduduk' => $jumlah_penduduk,
			// 		'jumlah_kk'       => $jumlah_kk,
			// 		'jumlah_surat'    => $jumlah_surat,
			// 		'jumlah_surat_masuk' => $jumlah_surat_masuk,

			// 		'summary_persuratan' => $summary_persuratan,
			// 		'summary_penduduk'   => $summary_penduduk,

			// 		'jenis_kelamin' => $jenis_kelamin,
			// 		'agama'         => $agama,
			// 		'sekolah'       => $sekolah,
			// 		'ibadah'        => $ibadah,
			// 		'umkm'          => $umkm,
			// 		'kebersihan'    => $kebersihan,
			// 		'pkl'           => $pkl,
			// 		'wamis'         => $wamis,
			// 		'longwis'       => $longwis,
			// 		'dasawisma'     => $dasawisma,
			// 		'rt_rw'         => $rt_rw,

			// 		'retribusi_sampah' => $retribusi_sampah,
			// 		'pegawai_kel_kec'  => $pegawai_kel_kec,
			// 		'ktp_tercetak'     => $ktp_tercetak,
			// 		'range_umur'       => $range_umur,

			// 		'status_data'  => $status_data,
			// 		'status_kawin' => $status_kawin,
			// 		'pendidikan'   => $pendidikan,
			// 		'skm' => $laporan_hasil_skm
			// 	);

			// 	$this->nsmarty->assign('data', $data);

			// 	// ===============================
			// 	// KHUSUS ADMIN
			// 	// ===============================
			// 	$this->nsmarty->assign(
			// 		'cl_kelurahan_desa_id',
			// 		$this->lib->fillcombo('cl_kelurahan_desa', 'return')
			// 	);

			// break;

			case "beranda":

				$array_penduduk = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],

					'status_data' => 'AKTIF'

				);

				$array_kk = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],

				);

				$array_surat = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],

					'year(tgl_surat)' => $this->auth['tahun'],


				);

				// $jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', $array_penduduk)->num_rows();

				// Ambil data session
				$session_data = unserialize(base64_decode($this->session->userdata('s3ntr4lb0')));
				$tahun_login = isset($session_data['tahun']) ? $session_data['tahun'] : date('Y'); // fallback jika tidak ada

				// Tambahkan ke query
				$this->db->where('YEAR(create_date) <=', $tahun_login);
				$jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', $array_penduduk)->num_rows();

				$jumlah_kk = $this->db->get_where('tbl_kartu_keluarga', $array_kk)->num_rows();

				$jumlah_surat = $this->db->get_where('tbl_data_surat', $array_surat)->num_rows();

				$jumlah_surat_masuk = $this->db->get_where('tbl_data_surat_masuk', $array_surat)->num_rows();


				$summary_persuratan = $this->mbackend->getdata('summary_persuratan', 'result_array');


				$jenis_kelamin = $this->mbackend->getdata_laporan('dashboard_jenis_kelamin', 'result_array', $tahun_login);
				// $jenis_kelamin = $this->mbackend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');

				$agama = $this->mbackend->getdata_laporan('dashboard_agama', 'result_array');

				$sekolah = $this->mbackend->getdata_laporan('dashboard_sekolah', 'result_array');

				$ibadah = $this->mbackend->getdata_laporan('dashboard_ibadah', 'result_array');

				$umkm = $this->mbackend->getdata_laporan('dashboard_umkm', 'result_array');

				$kebersihan = $this->mbackend->getdata_laporan('dashboard_kebersihan', 'result_array');

				$pkl = $this->mbackend->getdata_laporan('dashboard_pkl', 'result_array');

				$wamis = $this->mbackend->getdata_laporan('dashboard_wamis', 'result_array');

				$longwis = $this->mbackend->getdata_laporan('dashboard_longwis', 'result_array');

				$dasawisma = $this->mbackend->getdata_laporan('dashboard_dasawisma', 'result_array');

				$rt_rw = $this->mbackend->getdata_laporan('dashboard_rt_rw', 'result_array');

				$notif = $this->mbackend->getdata_laporan('dashboard_notif', 'result_array');

				$retribusi_sampah = $this->mbackend->getdata_laporan('dashboard_retribusi_sampah', 'result_array');

				$pegawai_kel_kec = $this->mbackend->getdata_laporan('dashboard_pegawai_kel_kec', 'result_array');

				$ktp_tercetak = $this->mbackend->getdata_laporan('dashboard_ktp_tercetak', 'result_array');

				$range_umur = $this->mbackend->getdata_laporan('dashboard_range_umur', 'result_array');


				$status_data = $this->mbackend->getdata_laporan('dashboard_status_data', 'result_array');

				$status_kawin = $this->mbackend->getdata_laporan('dashboard_status_kawin', 'result_array');

				$pendidikan = $this->mbackend->getdata_laporan('dashboard_pendidikan', 'result_array');

				$broadcast = $this->mbackend->getdata_laporan('dashboard_broadcast', 'result_array');

				$laporan_hasil_skm = $this->mbackend->getdata_laporan('beranda_hasil_skm', 'result_array');

				$data = array(

					'jumlah_penduduk' => $jumlah_penduduk,

					'jumlah_kk' => $jumlah_kk,

					'jumlah_surat' => $jumlah_surat,

					'jumlah_surat_masuk' => $jumlah_surat_masuk,

					'summary_persuratan' => $summary_persuratan,

					'jenis_kelamin' => $jenis_kelamin,

					'agama' => $agama,

					'sekolah' => $sekolah,

					'umkm' => $umkm,

					'kebersihan' => $kebersihan,

					'ibadah' => $ibadah,

					'pkl' => $pkl,

					'wamis' => $wamis,

					'longwis' => $longwis,

					'dasawisma' => $dasawisma,

					'rt_rw' => $rt_rw,

					'notif' => $notif,

					'retribusi_sampah' => $retribusi_sampah,

					'pegawai_kel_kec' => $pegawai_kel_kec,

					'ktp_tercetak' => $ktp_tercetak,

					'range_umur' => $range_umur,


					'status_data' => $status_data,

					'status_kawin' => $status_kawin,

					'pendidikan' => $pendidikan,

					'skm' => $laporan_hasil_skm

				);
				$data2 = array(
					'broadcast' => $broadcast
				);
				$this->nsmarty->assign('data', $data);
				$this->nsmarty->assign('data2', $data2);

				break;

			case "identitas_desa":

				$arraynya = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],


				);


				$cekdata = $this->db->get_where('tbl_setting_apps', $arraynya)->row_array();
				$this->nsmarty->assign("auth", $arraynya);
				if ($cekdata) {

					$sts = "edit";

					$this->nsmarty->assign('data', $cekdata);


					$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", ($sts == "edit" ? $cekdata["cl_provinsi_id"] : "")));

					$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", ($sts == "edit" ? $cekdata["cl_kab_kota_id"] : "")));

					$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", ($sts == "edit" ? $cekdata["cl_kecamatan_id"] : "")));

					$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $cekdata["cl_kelurahan_desa_id"] : "")));
				} else {

					$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", $this->auth["cl_provinsi_id"]));

					$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", $this->auth["cl_kab_kota_id"]));

					$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", $this->auth["cl_kecamatan_id"]));

					$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", $this->auth["cl_kelurahan_desa_id"]));
				}

				break;

			case "form_surat":

				$idx = $this->input->post('idx');

				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $idx))->row_array();
				switch ($idx) {

					case "155":

						$this->nsmarty->assign("jenis_domisili_id", $this->lib->fillcombo("jenis_domisili", "return"));

						$this->nsmarty->assign("jenis_kelamin_domisili", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("agama_domisili", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "154":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;


					case "153":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "152":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "151":

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("pekerjaan_anak", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "150":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("rubah_agama", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("rubah_status", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("rubah_pekerjaan", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "147":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("alasan_izin_pegawai_id", $this->lib->fillcombo("alasan_izin_pegawai", "return", ""));

						break;

					case "146":

						$this->nsmarty->assign("nik_id2", $this->lib->fillcombo("nik_beri_pernyataan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("pil_jenis_menumpang", $this->lib->fillcombo("pil_jenis_menumpang", "return"));

						break;

					case "145":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "144":

						$this->nsmarty->assign("pendidikan_pemohon", $this->lib->fillcombo("jenis_pendidikan", "return"));

						$this->nsmarty->assign("jabatan_pemohon", $this->lib->fillcombo("pilih_tingkat_jabatan", "return"));

						break;

					case "143":

						$this->nsmarty->assign("agama_sktm", $this->lib->fillcombo("cl_agama", "return"));

						$this->nsmarty->assign("pendidikan_sktm", $this->lib->fillcombo("cl_pendidikan", "return"));

						$this->nsmarty->assign("jns_kelamin_sktm", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("status_sktm", $this->lib->fillcombo("cl_status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_sktm", $this->lib->fillcombo("cl_jenis_pekerjaan", "return"));


						break;

					case "141":

						$this->nsmarty->assign("pekerjaan_catin_laki", $this->lib->fillcombo("jenis_pekerjaan", "return"));
						$this->nsmarty->assign("agama_catin_laki", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("pekerjaan_catin_wanita", $this->lib->fillcombo("jenis_pekerjaan", "return"));
						$this->nsmarty->assign("agama_catin_wanita", $this->lib->fillcombo("agama", "return"));

						break;

					case "140":

						$this->nsmarty->assign("nik_id2", $this->lib->fillcombo("nik_beri_pernyataan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return"));

					case "139":

						$this->nsmarty->assign("jenis_kelamin_beri_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return"));
						$this->nsmarty->assign("agama_beri_pernyataan", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));
						break;

					case "138":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("nik_laki_laki", "return"));
						$this->nsmarty->assign("nik_id2", $this->lib->fillcombo("nik_perempuan", "return"));

						break;

					case "137":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					case "136":

						$this->nsmarty->assign("pendidikan_pemohon", $this->lib->fillcombo("jenis_pendidikan", "return"));

						break;

					case "135":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("agama_ket_kec", $this->lib->fillcombo("agama", "return"));

						break;

					case "134":

						$this->nsmarty->assign("agama_pemohon", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("pekerjaan_pemohon", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("jenis_kelamin_pemohon", $this->lib->fillcombo("jenis_kelamin", "return"));
						break;

					case "133":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("agama_ket_kec", $this->lib->fillcombo("agama", "return"));

						break;

					case "132":

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return"));

						break;

					case "130":

						$this->nsmarty->assign("status_bangunan_masjid_id", $this->lib->fillcombo("status_bangunan_masjid", "return"));

						break;

					case "129":

						$this->nsmarty->assign("status_bangunan_masjid_id", $this->lib->fillcombo("status_bangunan_masjid", "return"));

						break;

					case "126":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "125":

						$this->nsmarty->assign("alasan_pindah_id", $this->lib->fillcombo("alasan_pindah", "return"));

						$this->nsmarty->assign("jenis_permohonan_id", $this->lib->fillcombo("jenis_permohonan", "return"));

						$this->nsmarty->assign("klasifikasi_pindah_id", $this->lib->fillcombo("klasifikasi_pindah", "return"));

						$this->nsmarty->assign("jenis_kepindahan_id", $this->lib->fillcombo("jenis_kepindahan", "return"));

						$this->nsmarty->assign("status_kk_tdk_pindah_id", $this->lib->fillcombo("status_kk_tdk_pindah", "return"));

						$this->nsmarty->assign("status_kk_pindah_id", $this->lib->fillcombo("status_kk_pindah", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "124":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return"));

						break;

					case "123":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("no_passport_id", $this->lib->fillcombo("data_penduduk_asing", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "121":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "120":

						$this->nsmarty->assign("pil_surat_survey", $this->lib->fillcombo("pil_surat_survey", "return"));

						break;

					case "118":

						$this->nsmarty->assign("pil_pernyataan_tugas", $this->lib->fillcombo("pil_pernyataan_tugas", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "117":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "116":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "115":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "114":

						$this->nsmarty->assign("pil_surat_teguran", $this->lib->fillcombo("pil_surat_teguran", "return"));

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "112":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "109":

						$this->nsmarty->assign("cl_sifat_surat", $this->lib->fillcombo("cl_sifat_surat2", "return"));

						break;

					case "103":

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "101":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "99":

						$this->nsmarty->assign("pil_surat_teguran", $this->lib->fillcombo("pil_surat_teguran", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "96":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return"));

						break;

					case "95":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return"));

						break;

					case "88":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return"));

						break;

					case "87":

						$this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("jenis_domisili_id", $this->lib->fillcombo("jenis_domisili", "return"));

						$this->nsmarty->assign("no_passport_id", $this->lib->fillcombo("data_penduduk_asing", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "86":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "85":

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("jenis_kelamin_anak_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "84":

						$this->nsmarty->assign("pilih_judul", $this->lib->fillcombo("pilih_judul", "return"));

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "83":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "82":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "81":

						// $this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						// $this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						// $this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "80":

						$this->nsmarty->assign("jenis_kelamin_alm", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "79":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));
						break;

					case "78":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "77":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return"));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "75":

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "74":

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "73":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "72":

						$this->nsmarty->assign("pil_surat_usulan", $this->lib->fillcombo("pil_surat_usulan", "return"));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						break;

					case "70":

						$this->nsmarty->assign("kondisi_kendaraan", $this->lib->fillcombo("kondisi_kendaraan", "return"));

						$this->nsmarty->assign("kelayakan_kendaraan", $this->lib->fillcombo("kelayakan_kendaraan", "return"));

						$this->nsmarty->assign("nama_sopir", $this->lib->fillcombo("nama_sopir", "return"));

						$this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return", ""));

						break;


					case "69":

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return"));

						break;
					case "68":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golonganx", "return"));

						$this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return", ""));

						break;

					case "67":

						$this->nsmarty->assign("jenis_teguran", $this->lib->fillcombo("jenis_teguran", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("jns_surat_teguran", $this->lib->fillcombo("jns_surat_teguran", "return", ""));

						$this->nsmarty->assign("jns_surat_teguran_rt", $this->lib->fillcombo("jns_surat_teguran_rt", "return", ""));

						$this->nsmarty->assign("jns_surat_pegawai", $this->lib->fillcombo("jns_surat_pegawai", "return", ""));

						break;

					case "66":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						// $this->nsmarty->assign("penentu_kematian_id", $this->lib->fillcombo("penentu_kematian", "return", ""));

						break;

					case "62":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ""));

						break;

					case "60":

						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return"));

						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return"));

						$this->nsmarty->assign("agama", $this->lib->fillcombo("waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						break;

					case "59":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ""));

						break;

					case "58":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ""));

						break;


					case "55":

						$this->nsmarty->assign("nama_sopir", $this->lib->fillcombo("nama_sopir", "return"));

						$this->nsmarty->assign("asal_kelurahan", $this->lib->fillcombo("asal_kelurahan", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					case "54":

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					case "52":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					case "51":

						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return"));

						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return"));

						$this->nsmarty->assign("agama", $this->lib->fillcombo("waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						// $this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						break;

					case "49":

						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return"));

						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						$this->nsmarty->assign("agama", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return"));

						break;

					case "47":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("bangunan_usaha_id", $this->lib->fillcombo("bangunan_usaha", "return", ""));

						$this->nsmarty->assign("status_tanah_id", $this->lib->fillcombo("status_tanah", "return", ""));

						break;

					case "46":
						$this->nsmarty->assign("klasifikasi_pindah_id", $this->lib->fillcombo("klasifikasi_pindah", "return"));

						$this->nsmarty->assign("jenis_kepindahan_id", $this->lib->fillcombo("jenis_kepindahan", "return"));

						$this->nsmarty->assign("status_kk_tdk_pindah_id", $this->lib->fillcombo("status_kk_tdk_pindah", "return"));

						$this->nsmarty->assign("status_kk_pindah_id", $this->lib->fillcombo("status_kk_pindah", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "45":

						$this->nsmarty->assign("agama_pernyataan", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("pekerjaan_pernyataan", $this->lib->fillcombo("jenis_pekerjaan", "return"));

						$this->nsmarty->assign("pil_pernyataan_umum", $this->lib->fillcombo("pil_pernyataan_umum", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					case "43":

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						// $this->nsmarty->assign("jenis_kelamin_anak_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "42":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "37":

						$this->nsmarty->assign("jabatan_tugas", $this->lib->fillcombo("jabatan_tugas", "return"));

						break;

					case "35":

						$this->nsmarty->assign("pil_keterangan_umum", $this->lib->fillcombo("pil_keterangan_umum", "return"));

						$this->nsmarty->assign("jenis_kelamin_anak_mobil", $this->lib->fillcombo("jenis_kelamin", "return"));
						$this->nsmarty->assign("agama_anak_mobil", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("jenis_kelamin_anak_pulsa", $this->lib->fillcombo("jenis_kelamin", "return"));
						$this->nsmarty->assign("agama_anak_pulsa", $this->lib->fillcombo("agama", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));
						break;

					case "34":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return"));

						break;

					case "33":

						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return"));

						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return"));

						$this->nsmarty->assign("agama", $this->lib->fillcombo("waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						$this->nsmarty->assign("pil_status_pewaris", $this->lib->fillcombo("pil_status_pewaris", "return"));

						// $this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return"));

						break;

					case "30":

						// $this->nsmarty->assign("pilih_judul", $this->lib->fillcombo("pilih_judul", "return"));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "29":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						break;

					case "24":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "22":

						$this->nsmarty->assign("bunyi_bunyian", $this->lib->fillcombo("ya_atau_tidak", "return"));

						$this->nsmarty->assign("jalan_lorong", $this->lib->fillcombo("ya_atau_tidak", "return"));

						$this->nsmarty->assign("tutup_sementara", $this->lib->fillcombo("ya_atau_tidak", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "21":

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "19":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						break;


					case "15":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("status_usaha_id", $this->lib->fillcombo("status_usaha", "return"));

						break;


					case "12":

						$this->nsmarty->assign("data_pindah_penduduk_id", $this->lib->fillcombo("data_pindah_penduduk_id", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "10":

						$this->nsmarty->assign("status_perkawinan", $this->lib->fillcombo("status_perkawinan", "return", ""));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_belum_menikah", "return"));

						break;

					case "9":

						// $this->nsmarty->assign("jenis_kelamin_alm", $this->lib->fillcombo("jenis_kelamin", "return"));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "8":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "7":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_anak", "return"));

						break;

					case "6":

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "4":

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						break;

					case "2":

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("masa_berlaku_id", $this->lib->fillcombo("masa_berlaku", "return"));

						$this->nsmarty->assign("jenis_domisili_id", $this->lib->fillcombo("jenis_domisili", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						break;

					case "1":

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_belum_menikah", "return"));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

						break;

					default:

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));

						$this->nsmarty->assign("no_passport_id", $this->lib->fillcombo("data_penduduk_asing", "return"));



						break;
				}
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return"));

				$this->nsmarty->assign('idx', $idx);

				$this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']));

				break;

			case "buat_surat":

				$a = $this->auth['cl_user_group_id'];

				// $jenis_surat = $this->db->get_where('cl_jenis_surat', array('cl_user_group_id' => $a))->result_array();
				$jenis_surat = $this->db->select("a.*,IFNULL(c.user_id,0)user_id,b.id as cl_nomor_surat_id,b.cl_kecamatan_id,b.cl_kelurahan_desa_id,a.id as cl_jenis_surat_id,b.kode_surat as format_nomor_result,b.nomor,b.bulan,b.tahun,b.p1,b.p2,b.p3,b.p4,b.format_nomor,b.param_nomor")->where(array(
					'a.cl_user_group_id' => $a
				))
					->join('cl_nomor_surat b', 'a.id=b.cl_jenis_surat_id AND b.cl_kelurahan_desa_id=\'' . $this->auth['cl_kelurahan_desa_id'] . '\'', 'left')
					->join('cl_jenis_surat_favorit c', 'a.id=c.id and c.user_id=\'' . $this->auth['id'] . '\'', 'left')
					->get('cl_jenis_surat a')->result_array();

				$this->nsmarty->assign('jenis_surat', $jenis_surat);

				break;

			case "data_surat":

				if ($sts == 'edit') {

					// $data = $this->db->get_where('tbl_data_surat', array('id' => $this->input->post('id')))->row_array();
					$data = $this->db->query("SELECT id,cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,
					cl_kelurahan_desa_id,no_surat,nik_lama,cl_jenis_surat_id,nama_pemohon,nik,
					DATE_FORMAT(tgl_surat,'%d-%m-%Y') AS tgl_surat,info_tambahan,create_date,
					create_by,update_date,update_by,no_hp,email,tbl_data_penduduk_id,flag_reg,
					tbl_registrasi_id,nip,masa_berlaku,status_usaha,FILE,arsip,uraian,data_surat,
					status_esign,file_src_esign,file_approved_esign,stamp_esign,nip_pemeriksa_esign,
					nama_pemeriksa_esign,nip_pernyataan,nomor,bulan,tahun,p1,p2,p3,p4,format_nomor,
					param_nomor,param_bulan,param_tahun,param_p1,param_p2,param_p3,param_p4 
					FROM tbl_data_surat
					where id='" . $this->input->post('id') . "' ")->row_array();


					if ($data['info_tambahan'] != "") {

						$data_info = json_decode($data['info_tambahan'], true);

						$this->nsmarty->assign('data_info', $data_info);
					}



					$this->nsmarty->assign('data', $data);
				}


				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $data['cl_jenis_surat_id']))->row_array();


				switch ($data['cl_jenis_surat_id']) {

					case "155":

						$this->nsmarty->assign("jenis_domisili_id", $this->lib->fillcombo("jenis_domisili", "return", ($sts == "edit" ? $data_info["jenis_domisili"] : "")));

						$this->nsmarty->assign("jenis_kelamin_domisili", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_domisili"] : "")));

						$this->nsmarty->assign("agama_domisili", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_domisili"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						break;

					case "154":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "153":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "152":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "151":

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_anak"] : "")));

						$this->nsmarty->assign("pekerjaan_anak", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_anak"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "150":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("rubah_agama", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? (isset($data_info['rubah_agama']) ? $data_info['rubah_agama'] : "") : "")));
						$this->nsmarty->assign("rubah_status", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? (isset($data_info['rubah_status']) ? $data_info['rubah_status'] : "") : "")));
						$this->nsmarty->assign("rubah_pekerjaan", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? (isset($data_info['rubah_pekerjaan']) ? $data_info['rubah_pekerjaan'] : "") : "")));



						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "147":

						$this->nsmarty->assign("alasan_izin_pegawai_id", $this->lib->fillcombo("alasan_izin_pegawai", "return", ($sts == "edit" ? $data_info["alasan_izin_pegawai"] : "")));

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "146":

						$this->nsmarty->assign("pil_jenis_menumpang", $this->lib->fillcombo("pil_jenis_menumpang", "return", ($sts == "edit" ? $data_info["pil_jenis_menumpang"] : "")));

						$this->nsmarty->assign("pil_jenis_menumpangx",  $data_info["pil_jenis_menumpang"]);

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_pemilik_rumah", $this->lib->fillcombo("nik_pemilik_rumah", "return", ($sts == "edit" ? $data_info["nik_pemilik_rumah"] : "")));

						break;

					case "145":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_imam", ($data_info['ceklis_ttd_imam'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "144":
						$this->nsmarty->assign("ceklis_kop_ns", ($data_info['ceklis_kop_ns'] == true ? 'checked=true' : ''));
						$this->nsmarty->assign("pendidikan_pemohon", $this->lib->fillcombo("jenis_pendidikan", "return", ($sts == "edit" ? $data_info["pendidikan_pemohon"] : "")));
						break;

					case "143":
						$agama_sktm = [];
						$pendidikan_sktm = [];
						$jns_kelamin_sktm = [];
						$status_sktm = [];
						$pekerjaan_sktm = [];
						for ($i = 0; $i < count($data_info['pemohon']); $i++) {
							// echo json_encode($data_info['pemohon'][$i]);
							$agama_sktm[] = $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data_info['pemohon'][$i]["agama_sktm"] : ""));
							$pendidikan_sktm[] = $this->lib->fillcombo("cl_pendidikan", "return", ($sts == "edit" ? $data_info['pemohon'][$i]["pendidikan_sktm"] : ""));
							$jns_kelamin_sktm[] = $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info['pemohon'][$i]["jns_kelamin_sktm"] : ""));
							$status_sktm[] = $this->lib->fillcombo("cl_status_kawin", "return", ($sts == "edit" ? $data_info['pemohon'][$i]["status_sktm"] : ""));
							$pekerjaan_sktm[] = $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data_info['pemohon'][$i]["pekerjaan_sktm"] : ""));
						}
						// exit;
						$this->nsmarty->assign("agama_sktm", $agama_sktm);

						$this->nsmarty->assign("pendidikan_sktm", $pendidikan_sktm);

						$this->nsmarty->assign("jns_kelamin_sktm", $jns_kelamin_sktm);

						$this->nsmarty->assign("status_sktm", $status_sktm);

						$this->nsmarty->assign("pekerjaan_sktm", $pekerjaan_sktm);

						$this->nsmarty->assign("jns_kelamin_sktm_edit", $this->lib->fillcombo("jenis_kelamin", "return"));
						$this->nsmarty->assign("status_sktm_edit", $this->lib->fillcombo("cl_status_kawin", "return"));
						$this->nsmarty->assign("agama_sktm_edit", $this->lib->fillcombo("cl_agama", "return"));
						$this->nsmarty->assign("pendidikan_sktm_edit", $this->lib->fillcombo("cl_pendidikan", "return"));
						$this->nsmarty->assign("pekerjaan_sktm_edit", $this->lib->fillcombo("cl_jenis_pekerjaan", "return"));

						break;
					case "141":

						$this->nsmarty->assign("agama_catin_laki", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_catin_laki"] : "")));
						$this->nsmarty->assign("pekerjaan_catin_laki", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_catin_laki"] : "")));

						$this->nsmarty->assign("agama_catin_wanita", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_catin_wanita"] : "")));
						$this->nsmarty->assign("pekerjaan_catin_wanita", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_catin_wanita"] : "")));

						break;

					case "140":

						$this->nsmarty->assign("nik_beri_pernyataan", $this->lib->fillcombo("nik_beri_pernyataan", "return", ($sts == "edit" ? $data_info["nik_beri_pernyataan"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" && isset($data_info["nip_pernyataan"]) ? $data_info["nip_pernyataan"] : "")));

						break;

					case "139":

						$this->nsmarty->assign("jenis_kelamin_beri_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_beri_pernyataan"] : "")));
						$this->nsmarty->assign("agama_beri_pernyataan", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_beri_pernyataan"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "138":

						$this->nsmarty->assign("nik_laki_laki", $this->lib->fillcombo("nik_laki_laki", "return", ($sts == "edit" ? $data_info["nik_laki_laki"] : "")));

						$this->nsmarty->assign("nik_perempuan", $this->lib->fillcombo("nik_perempuan", "return", ($sts == "edit" ? $data_info["nik_perempuan"] : "")));

						break;

					case "137":

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" && isset($data_info["nip_pernyataan"]) ? $data_info["nip_pernyataan"] : "")));

						break;

					case "136":

						$this->nsmarty->assign("pendidikan_pemohon", $this->lib->fillcombo("jenis_pendidikan", "return", ($sts == "edit" ? $data_info["pendidikan_pemohon"] : "")));

						break;

					case "135":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						$this->nsmarty->assign("agama_ket_kec", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_ket_kec"] : "")));

						break;

					case "134":

						$this->nsmarty->assign("agama_pemohon", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_pemohon"] : "")));

						$this->nsmarty->assign("pekerjaan_pemohon", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_pemohon"] : "")));

						$this->nsmarty->assign("jenis_kelamin_pemohon", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_pemohon"] : "")));

						break;

					case "133":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						$this->nsmarty->assign("agama_ket_kec", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_ket_kec"] : "")));

						break;

					case "132":

						$this->nsmarty->assign("ceklis_ttd_pelapor", ($data_info['ceklis_ttd_pelapor'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_bayi"] : "")));

						break;

					case "130":

						$this->nsmarty->assign("status_bangunan_masjid_id", $this->lib->fillcombo("status_bangunan_masjid", "return", ($sts == "edit" ? $data_info["status_bangunan_masjid"] : "")));

						break;

					case "129":

						$this->nsmarty->assign("status_bangunan_masjid_id", $this->lib->fillcombo("status_bangunan_masjid", "return", ($sts == "edit" ? $data_info["status_bangunan_masjid"] : "")));

						break;


					case "128":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						break;

					case "126":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "125":

						$this->nsmarty->assign("alasan_pindah_id", $this->lib->fillcombo("alasan_pindah", "return", ($sts == "edit" ? $data_info["alasan_pindah"] : "")));

						$this->nsmarty->assign("jenis_permohonan_id", $this->lib->fillcombo("jenis_permohonan", "return", ($sts == "edit" ? $data_info["jenis_permohonan"] : "")));

						$this->nsmarty->assign("klasifikasi_pindah_id", $this->lib->fillcombo("klasifikasi_pindah", "return", ($sts == "edit" ? $data_info["klasifikasi_pindah"] : "")));

						$this->nsmarty->assign("jenis_kepindahan_id", $this->lib->fillcombo("jenis_kepindahan", "return", ($sts == "edit" ? $data_info["jenis_kepindahan"] : "")));

						$this->nsmarty->assign("status_kk_tdk_pindah_id", $this->lib->fillcombo("status_kk_tdk_pindah", "return", ($sts == "edit" ? $data_info["status_kk_tdk_pindah"] : "")));

						$this->nsmarty->assign("status_kk_pindah_id", $this->lib->fillcombo("status_kk_pindah", "return", ($sts == "edit" ? $data_info["status_kk_pindah"] : "")));

						break;

					case "124":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data_info["cl_sifat_surat"] : "")));

						break;


					case "123":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("no_passport_id", $this->lib->fillcombo("data_penduduk_asing", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						break;


					case "121":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_imam", ($data_info['ceklis_ttd_imam'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "120":

						$this->nsmarty->assign("pil_surat_survey", $this->lib->fillcombo("pil_surat_survey", "return", ($sts == "edit" ? $data_info["pil_surat_survey"] : "")));

						$this->nsmarty->assign("pil_surat_surveyx",  $data_info["pil_surat_survey"]);

						break;

					case "118":

						$this->nsmarty->assign("pil_pernyataan_tugas", $this->lib->fillcombo("pil_pernyataan_tugas", "return", ($sts == "edit" ? $data_info["pil_pernyataan_tugas"] : "")));
						$this->nsmarty->assign("pil_pernyataan_tugasx",  $data_info["pil_pernyataan_tugas"]);

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "117":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat2"] : "")));

						break;

					case "116":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat2"] : "")));

						break;

					case "115":
						$pilih_golongan = $this->db->select("a.*,concat(pangkat,', ',nm_golongan) as gabung")->get("cl_golongan a")->result_array();
						$this->nsmarty->assign("pilih_golongan", $pilih_golongan);

						break;

					case "114":

						$this->nsmarty->assign("pil_surat_teguran", $this->lib->fillcombo("pil_surat_teguran", "return", ($sts == "edit" ? $data_info["pil_surat_teguran"] : "")));

						$this->nsmarty->assign("pil_surat_teguranx",  $data_info["pil_surat_teguran"]);

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data_info["cl_sifat_surat"] : "")));

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "112":

						$this->nsmarty->assign("pilih_golonganx", $this->lib->fillcombo("pilih_golonganx", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "109":

						$this->nsmarty->assign("cl_sifat_surat", $this->lib->fillcombo("cl_sifat_surat2", "return", ($sts == "edit" ? $data_info["cl_sifat_surat"] : "")));

						break;

					case "104":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "103":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" && isset($data_info["nip_pernyataan"]) ? $data_info["nip_pernyataan"] : "")));

						break;

					case "101":

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "99":

						$this->nsmarty->assign("pil_surat_teguran", $this->lib->fillcombo("pil_surat_teguran", "return", ($sts == "edit" ? $data_info["pil_surat_teguran"] : "")));

						$this->nsmarty->assign("pil_surat_teguranx",  $data_info["pil_surat_teguran"]);

						$this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data_info["pangkat"] : "")));

						break;

					case "97":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						break;

					case "96":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data_info["cl_sifat_surat"] : "")));

						break;

					case "95":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return", ($sts == "edit" ? $data_info["pil_surat_penelitian"] : "")));

						$this->nsmarty->assign("pil_surat_penelitianx",  $data_info["pil_surat_penelitian"]);

						break;

					case "89":

						$this->nsmarty->assign("ceklis_cetak_perwali", ($data_info['ceklis_cetak_perwali'] == true ? 'checked=true' : ''));

						break;

					case "88":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return", ($sts == "edit" ? $data_info["pil_surat_penelitian"] : "")));

						$this->nsmarty->assign("pil_surat_penelitianx",  $data_info["pil_surat_penelitian"]);

						break;


					case "87":

						$this->nsmarty->assign("no_passport_id", $this->lib->fillcombo("data_penduduk_asing", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						break;

					case "86":

						$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data_info["cl_sifat_surat"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "85":

						$this->nsmarty->assign("jenis_kelamin_anak_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak_pernyataan"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "84":

						$this->nsmarty->assign("pilih_judul", $this->lib->fillcombo("pilih_judul", "return", ($sts == "edit" ? $data_info["pilih_judul"] : "")));

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "83":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "82":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "81":

						// $this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						// $this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						// $this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "80":

						$this->nsmarty->assign("jenis_kelamin_alm", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_alm"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "79":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "78":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "77":

						$this->nsmarty->assign("agama_wali", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_wali"] : "")));

						$this->nsmarty->assign("status_wali", $this->lib->fillcombo("status_kawin", "return", ($sts == "edit" ? $data_info["status_wali"] : "")));

						$this->nsmarty->assign("pekerjaan_wali", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_wali"] : "")));

						$this->nsmarty->assign("ceklis_ttd_imam", ($data_info['ceklis_ttd_imam'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "75":

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "74":

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak"] : "")));

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_anak"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "73":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return", ($sts == "edit" ? $data_info["pil_surat_penelitian"] : "")));

						$this->nsmarty->assign("pil_surat_penelitianx",  $data_info["pil_surat_penelitian"]);

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_belum_menikah", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "72":

						$this->nsmarty->assign("pil_surat_usulan", $this->lib->fillcombo("pil_surat_usulan", "return", ($sts == "edit" ? $data_info["pil_surat_usulan"] : "")));

						$this->nsmarty->assign("pil_surat_usulanx",  $data_info["pil_surat_usulan"]);

						$pilih_golongan = $this->db->select("a.*,concat(pangkat,', ',nm_golongan) as gabung")->get("cl_golongan a")->result_array();
						$this->nsmarty->assign("pilih_golonganx", $pilih_golongan);

						break;

					case "70":

						$this->nsmarty->assign("kondisi_kendaraan", $this->lib->fillcombo("kondisi_kendaraan", "return", ($sts == "edit" ? $data_info["kondisi_kendaraan"] : "")));

						// $this->nsmarty->assign("kelayakan_kendaraan", $this->lib->fillcombo("kelayakan_kendaraan", "return", ($sts == "edit" ? $data_info["kelayakan_kendaraan"] : "")));

						$this->nsmarty->assign("nama_sopir", $this->lib->fillcombo("nama_sopir", "return", ($sts == "edit" ? $data_info["nopol"] : "")));

						// $this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return", ($sts == "edit" ? $data_info["bulan"] : "")));

						break;

					case "69":

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_bayi"] : "")));

						break;

					case "68":

						$pilih_golongan = $this->db->select("a.*,concat(pangkat,', ',nm_golongan) as gabung")->get("cl_golongan a")->result_array();
						$this->nsmarty->assign("pilih_golongan", $pilih_golongan);

						$this->nsmarty->assign("bulan", $this->lib->fillcombo("bulan", "return", ($sts == "edit" ? $data_info["bulan"] : "")));

						break;
					case "67":

						$this->nsmarty->assign("jenis_teguran", $this->lib->fillcombo("jenis_teguran", "return", ($sts == "edit" ? $data_info["jenis_teguran"] : "")));

						$this->nsmarty->assign("jns_surat_teguran", $this->lib->fillcombo("jns_surat_teguran", "return", ($sts == "edit" ? $data_info["jns_surat_teguran"] : "")));

						$this->nsmarty->assign("jns_surat_teguran_rt", $this->lib->fillcombo("jns_surat_teguran_rt", "return", ($sts == "edit" ? $data_info["jns_surat_teguran_rt"] : "")));

						$this->nsmarty->assign("jns_surat_pegawai", $this->lib->fillcombo("jns_surat_pegawai", "return", ($sts == "edit" ? $data_info["jns_surat_pegawai"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "66":

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						// $this->nsmarty->assign("penentu_kematian_id", $this->lib->fillcombo("penentu_kematian", "return", ($sts == "edit" ? $data_info["penentu_kematian"] : "")));

						break;

					case "62":

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ($sts == "edit" ? $data_info["status_tanah2"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "60":

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data["nip"] : "")));

						$hubungan_waris = [
							'SUAMI',
							'ISTRI',
							'ANAK',
							'CUCU',
							'IBU KANDUNG',
							'BAPAK KANDUNG',
							'SAUDARA KANDUNG',
						];
						$this->nsmarty->assign("res_hubungan_waris", $hubungan_waris);
						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return", ''));

						$status_waris = [
							'HIDUP',
							'MENINGGAL DUNIA'
						];
						$this->nsmarty->assign("res_status_waris", $status_waris);
						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return", ''));

						break;

					case "59":

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ($sts == "edit" ? $data_info["status_tanah2"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "58":

						$this->nsmarty->assign("status_tanah2_id", $this->lib->fillcombo("status_tanah2", "return", ($sts == "edit" ? $data_info["status_tanah2"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "55":

						$this->nsmarty->assign("nama_sopir", $this->lib->fillcombo("nama_sopir", "return", ($sts == "edit" ? $data_info["nopol"] : "")));

						$this->nsmarty->assign("asal_kelurahan", $this->lib->fillcombo("asal_kelurahan", "return", ($sts == "edit" ? $data_info["asal_kelurahan"] : "")));

						break;

					case "54":

						$this->nsmarty->assign("ceklis_3ttd", ($data_info['ceklis_3ttd'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data_info["nip_pernyataan"] : "")));

						break;

					case "52":
						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data_info["nip_pernyataan"] : "")));

						// $this->nsmarty->assign("pilih_ttd_pernyataan", $this->lib->fillcombo("pilih_ttd", "return", ($sts == "edit" ? $data_info["nip_pernyataan"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "51":


						// $this->nsmarty->assign("ahli_waris", $this->lib->fillcombo("data_ahli_waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data["nip"] : "")));

						$hubungan_waris = [
							'SUAMI',
							'ISTRI',
							'ANAK',
							'CUCU',
							'IBU KANDUNG',
							'BAPAK KANDUNG',
							'SAUDARA KANDUNG',
						];
						$this->nsmarty->assign("res_hubungan_waris", $hubungan_waris);
						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return", ''));

						$status_waris = [
							'HIDUP',
							'MENINGGAL DUNIA'
						];
						$this->nsmarty->assign("res_status_waris", $status_waris);
						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return", ''));

						break;

					case "49":

						$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin"] : "")));

						$this->nsmarty->assign("agama", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama"] : "")));

						$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["cl_jenis_pekerjaan_id"] : "")));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data["nip"] : "")));

						$hubungan_waris = [
							'SUAMI',
							'ISTRI',
							'ANAK',
							'CUCU',
							'IBU KANDUNG',
							'BAPAK KANDUNG',
							'SAUDARA KANDUNG',
						];
						$this->nsmarty->assign("res_hubungan_waris", $hubungan_waris);
						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return", ''));

						$status_waris = [
							'HIDUP',
							'MENINGGAL DUNIA'
						];
						$this->nsmarty->assign("res_status_waris", $status_waris);
						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return", ''));

						break;

					case "47":

						$this->nsmarty->assign("bangunan_usaha_id", $this->lib->fillcombo("bangunan_usaha", "return", ($sts == "edit" ? $data_info["bangunan_usaha"] : "")));

						$this->nsmarty->assign("status_tanah_id", $this->lib->fillcombo("status_tanah", "return", ($sts == "edit" ? $data_info["status_tanah"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "46":

						$this->nsmarty->assign("klasifikasi_pindah_id", $this->lib->fillcombo("klasifikasi_pindah", "return", ($sts == "edit" ? $data_info["klasifikasi_pindah"] : "")));

						$this->nsmarty->assign("jenis_kepindahan_id", $this->lib->fillcombo("jenis_kepindahan", "return", ($sts == "edit" ? $data_info["jenis_kepindahan"] : "")));

						$this->nsmarty->assign("status_kk_tdk_pindah_id", $this->lib->fillcombo("status_kk_tdk_pindah", "return", ($sts == "edit" ? $data_info["status_kk_tdk_pindah"] : "")));

						$this->nsmarty->assign("status_kk_pindah_id", $this->lib->fillcombo("status_kk_pindah", "return", ($sts == "edit" ? $data_info["status_kk_pindah"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "45":

						$this->nsmarty->assign("agama_pernyataan", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_pernyataan"] : "")));

						$this->nsmarty->assign("pekerjaan_pernyataan", $this->lib->fillcombo("jenis_pekerjaan", "return", ($sts == "edit" ? $data_info["pekerjaan_pernyataan"] : "")));

						$this->nsmarty->assign("pil_pernyataan_umum", $this->lib->fillcombo("pil_pernyataan_umum", "return", ($sts == "edit" ? $data_info["pil_pernyataan_umum"] : "")));
						$this->nsmarty->assign("pil_pernyataan_umumx",  preg_replace('/\s+/', '_', $data_info["pil_pernyataan_umum"]));

						$this->nsmarty->assign("nip_pernyataan", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data_info["nip_pernyataan"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "42":

						$this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "43":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						// $this->nsmarty->assign("jenis_kelamin_anak_pernyataan", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak_pernyataan"] : "")));

						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak"] : "")));

						$this->nsmarty->assign("agama_anak", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_anak"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "37":

						$this->nsmarty->assign("jabatan_tugas", $this->lib->fillcombo("jabatan_tugas", "return"));

						break;

					case "35":

						$this->nsmarty->assign("pil_keterangan_umum", $this->lib->fillcombo("pil_keterangan_umum", "return", ($sts == "edit" ? $data_info["pil_keterangan_umum"] : "")));
						$this->nsmarty->assign("pil_keterangan_umumx",  preg_replace('/\s+/', '_', $data_info["pil_keterangan_umum"]));

						$this->nsmarty->assign("jenis_kelamin_anak_mobil", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak_mobil"] : "")));
						$this->nsmarty->assign("agama_anak_mobil", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_anak_mobil"] : "")));

						$this->nsmarty->assign("jenis_kelamin_anak_pulsa", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_anak_pulsa"] : "")));
						$this->nsmarty->assign("agama_anak_pulsa", $this->lib->fillcombo("agama", "return", ($sts == "edit" ? $data_info["agama_anak_pulsa"] : "")));

						$this->nsmarty->assign("status_kop_keterangan", ($data_info['status_kop_keterangan'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "34":

						$this->nsmarty->assign("pil_surat_penelitian", $this->lib->fillcombo("pil_surat_penelitian", "return", ($sts == "edit" ? $data_info["pil_surat_penelitian"] : "")));

						$this->nsmarty->assign("pil_surat_penelitianx",  $data_info["pil_surat_penelitian"]);

						break;

					case "33":


						// $this->nsmarty->assign("ahli_waris", $this->lib->fillcombo("data_ahli_waris", "return"));

						$this->nsmarty->assign("nama_alm", $this->lib->fillcombo("waris", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data["nip"] : "")));

						$hubungan_waris = [
							'SUAMI',
							'ISTRI',
							'ANAK',
							'CUCU',
							'IBU KANDUNG',
							'BAPAK KANDUNG',
							'SAUDARA KANDUNG',
						];
						$this->nsmarty->assign("res_hubungan_waris", $hubungan_waris);
						$this->nsmarty->assign("hubungan_waris", $this->lib->fillcombo("hubungan_waris", "return", ''));

						$status_waris = [
							'HIDUP',
							'MENINGGAL DUNIA'
						];
						$this->nsmarty->assign("res_status_waris", $status_waris);
						$this->nsmarty->assign("status_waris", $this->lib->fillcombo("status_waris", "return", ''));

						$this->nsmarty->assign("pil_status_pewaris", $this->lib->fillcombo("pil_status_pewaris", "return", ($sts == "edit" ? $data_info["pil_status_pewaris"] : "")));

						$this->nsmarty->assign("pil_status_pewarisx",  $data_info["pil_status_pewaris"]);

						break;

					case "30":

						// $this->nsmarty->assign("pilih_judul", $this->lib->fillcombo("pilih_judul", "return", ($sts == "edit" ? $data_info["pilih_judul"] : "")));

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "29":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "24":

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "22":

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("bunyi_bunyian", $this->lib->fillcombo("ya_atau_tidak", "return", ($sts == "edit" ? $data_info["bunyi_bunyian"] : "")));

						$this->nsmarty->assign("jalan_lorong", $this->lib->fillcombo("ya_atau_tidak", "return", ($sts == "edit" ? $data_info["jalan_lorong"] : "")));

						$this->nsmarty->assign("tutup_sementara", $this->lib->fillcombo("ya_atau_tidak", "return", ($sts == "edit" ? $data_info["tutup_sementara"] : "")));

						break;

					case "21":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("judul_tidak_bekerja", ($data_info['judul_tidak_bekerja'] == true ? 'checked=true' : ''));

						break;

					case "19":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("status_ttd_pemilik_usaha", ($data_info['status_ttd_pemilik_usaha'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("status_ttd_pemilik_usaha2", ($data_info['status_ttd_pemilik_usaha2'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "15":

						$this->nsmarty->assign("status_ttd_pemilik_usaha", ($data_info['status_ttd_pemilik_usaha'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "12":

						$this->nsmarty->assign("data_pindah_penduduk_id", $this->lib->fillcombo("data_pindah_penduduk_id", "return"));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "10":

						$this->nsmarty->assign("ceklis_ttd_imam", ($data_info['ceklis_ttd_imam'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("status_perkawinan", $this->lib->fillcombo("status_perkawinan", "return", ($sts == "edit" ? $data_info["status_perkawinan"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_belum_menikah", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "9":

						// $this->nsmarty->assign("jenis_kelamin_alm", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_alm"] : "")));

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "7":

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "6":

						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data_info["jenis_kelamin_bayi"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;


					case "4":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("ceklis_psktm", ($data_info['ceklis_psktm'] == true ? 'checked=true' : ''));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					case "2":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("jenis_domisili_id", $this->lib->fillcombo("jenis_domisili", "return", ($sts == "edit" ? $data_info["jenis_domisili"] : "")));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						$this->nsmarty->assign("nik_id_penjamin", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? @$data_info["id_penjamin"] : "")));

						break;

					case "1":

						// $this->nsmarty->assign("ceklis_ttd_pejabat", ($data_info['ceklis_ttd_pejabat'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("ceklis_ttd_imam", ($data_info['ceklis_ttd_imam'] == true ? 'checked=true' : ''));

						// $this->nsmarty->assign("pil_jenis_surat", $this->lib->fillcombo("pil_jenis_surat", "return", ($sts == "edit" ? $data_info["pil_jenis_surat"] : "")));

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_belum_menikah", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));

						break;

					default:

						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "")));


						break;
				}
				$this->nsmarty->assign("nip_id", $this->lib->fillcombo("data_penandatanganan", "return", ($sts == "edit" ? $data["nip"] : "")));
				$this->nsmarty->assign("masa_berlaku_id", $this->lib->fillcombo("masa_berlaku", "return", ($sts == "edit" ? $data["masa_berlaku"] : "")));
				$this->nsmarty->assign("status_usaha_id", $this->lib->fillcombo("status_usaha", "return", ($sts == "edit" ? @$data_info["status_usaha"] : "")));
				// $this->nsmarty->assign("hari_kematian_id", $this->lib->fillcombo("hari_kematian", "return", ($sts == "edit" ? @$data_info["hari_kematian"] : "")));
				$penduduk = $this->db->get_where('tbl_data_penduduk', array('cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']))->result_array();
				$this->nsmarty->assign("data_penduduk", $penduduk);


				$this->nsmarty->assign("cl_jenis_surat_id", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["cl_jenis_surat_id"] : "")));

				$this->nsmarty->assign('idx', $data['cl_jenis_surat_id']);

				$this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']));

				break;


			case "data_surat_edit":
				$data = $this->db->get_where('tbl_data_surat', array('id' => $this->input->post('id')))->row_array();
				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $data['cl_jenis_surat_id']))->row_array();
				$this->nsmarty->assign('id', $data['id']);
				$this->nsmarty->assign('cl_jenis_surat_id', $data['cl_jenis_surat_id']);
				$this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']));
				break;
			case "surat_masuk":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_surat_masuk', array('id' => $this->input->post('id')))->row_array();

					if ($data['info_tambahan'] != "") {

						$data_info = json_decode($data['info_tambahan'], true);

						$this->nsmarty->assign('data_info', $data_info);
					}

					$this->nsmarty->assign('data', $data);
				}


				// $jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $data['cl_jenis_surat_id']))->row_array();

				// $this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data["cl_sifat_surat_masuk_id"] : "")));
				$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data["cl_sifat_surat_masuk_id"] : "")));

				$this->nsmarty->assign("jenis_suratx", $this->lib->fillcombo("cl_jenis_surat_masuk", "return", ($sts == "edit" ? $data["cl_jenis_surat_masuk_id"] : "")));

				// $this->nsmarty->assign('idx', $data['cl_jenis_surat_id']);

				// $this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']));

				break;

			case "surat_lain":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_surat_lain', array('id' => $this->input->post('id')))->row_array();

					if ($data['info_tambahan'] != "") {

						$data_info = json_decode($data['info_tambahan'], true);

						$this->nsmarty->assign('data_info', $data_info);
					}


					$this->nsmarty->assign('data', $data);
				}

				break;

			case "surat_himbauan":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_surat_himbauan', array('id' => $this->input->post('id')))->row_array();

					if ($data['info_tambahan'] != "") {

						$data_info = json_decode($data['info_tambahan'], true);

						$this->nsmarty->assign('data_info', $data_info);
					}

					$this->nsmarty->assign('data', $data);
				}
				$this->nsmarty->assign("sifat_suratx", $this->lib->fillcombo("cl_sifat_surat", "return", ($sts == "edit" ? $data["id"] : "")));

				$this->nsmarty->assign("jenis_suratx", $this->lib->fillcombo("cl_jenis_surat_masuk", "return", ($sts == "edit" ? $data["cl_jenis_surat_masuk_id"] : "")));

				$this->nsmarty->assign("pilih_ttd", $this->lib->fillcombo("pilih_ttd", "return", ($sts == "edit" ? $data["nip"] : "")));


				break;

			case "broadcast":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_broadcast', array('id' => $this->input->post('id')))->row_array();

					$kelurahan = $this->mbackend->getdata_laporan('data_nama_kelurahan_broadcast', 'result_array');

					$data2 = array(


						'kelurahan' => $kelurahan

					);

					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('data2', $data2);

					// $this->nsmarty->assign("kelurahan", ($data2['kelurahan'] == true ? 'checked=true' : ''));
				}

				$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				$kelurahan = $this->mbackend->getdata_laporan('data_nama_kelurahan_broadcast', 'result_array');

				$notif = $this->db->get_where('tbl_data_broadcast')->num_rows();

				$data2 = array(


					'kelurahan' => $kelurahan,
					'notif' => $notif,

				);


				$this->nsmarty->assign('data2', $data2);

				break;


			case "data_keluarga":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_kartu_keluarga', array('id' => $this->input->post('id')))->row_array();

					if ($data) {

						$data_detail = $this->db->get_where('tbl_data_penduduk', array('no_kk' => $data['no_kk']))->result_array();

						$this->nsmarty->assign('detail', $data_detail);
					}

					$this->nsmarty->assign('data', $data);
				}
				if ($sts == 'edit') {

					$array_penduduk = array(

						'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']

					);
				} else {
					$array_penduduk = " status_data='AKTIF' and no_kk NOT IN (SELECT no_kk FROM tbl_kartu_keluarga WHERE no_kk=a.no_kk) and cl_kelurahan_desa_id ='" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}



				$data_hubungan = $this->db->get_where('cl_hubungan_keluarga')->result_array();

				$data_penduduk = $this->db->get_where('tbl_data_penduduk a', $array_penduduk)->result_array();



				$this->nsmarty->assign("data_keluarga_id", $data_penduduk);

				$this->nsmarty->assign("combo_penduduk", $this->lib->fillcombo("data_keluarga_id", "return"));

				$this->nsmarty->assign("hubungan_keluarga", $data_hubungan);

				$this->nsmarty->assign("combo_hubungan_keluarga", $this->lib->fillcombo("hubungan_keluarga", "return"));

				break;

			case "data_penduduk":

				if ($sts == 'edit') {

					// $data = $this->db->get_where('tbl_data_penduduk', array('id' => $this->input->post('id')))->row_array();
					$data = $this->db->query("SELECT id,no_kk,cl_status_hubungan_keluarga_id,nik,nama_lengkap,tempat_lahir,
							DATE_FORMAT(tgl_lahir,'%d-%m-%Y') AS tgl_lahir,jenis_kelamin,agama,status_kawin,pendidikan,gol_darah,cl_jenis_pekerjaan_id,
							golongan_darah,cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,cl_kelurahan_desa_id,rt,rw,alamat,kode_pos,nop,
							status_data,create_date,create_by,update_date,update_by,file
							FROM tbl_data_penduduk
							WHERE id = '" . $this->input->post('id') . "' ")->row_array();

					// where id='" . $this->input->post('id') . "' ")->row_array();

					$this->nsmarty->assign('data', $data);
				}


				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "")));

				$this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin", "return", ($sts == "edit" ? $data["status_kawin"] : "")));

				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data["agama"] : "")));

				$this->nsmarty->assign("cl_status_hubungan_keluarga_id", $this->lib->fillcombo("hubungan_keluarga", "return", ($sts == "edit" ? $data["cl_status_hubungan_keluarga_id"] : "")));

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				$this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan", "return", ($sts == "edit" ? $data["pendidikan"] : "")));

				$this->nsmarty->assign("gol_darah", $this->lib->fillcombo("gol_darah", "return", ($sts == "edit" ? $data["gol_darah"] : "")));

				$this->nsmarty->assign("status_penduduk", $this->lib->fillcombo("status_penduduk", "return", ($sts == "edit" ? $data["status_data"] : "")));

				break;

			case "data_penduduk_asing":

				if ($sts == 'edit') {

					// $data = $this->db->get_where('tbl_data_penduduk_asing', array('id' => $this->input->post('id')))->row_array();

					$data = $this->db->query("SELECT id,no_passport,no_pengenalan,nama_lengkap,tempat_lahir,DATE_FORMAT(tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
					jenis_kelamin,kewarganegaraan,agama,cl_jenis_pekerjaan_id,DATE_FORMAT(tgl_kel_passport,'%d-%m-%Y') AS tgl_kel_passport,DATE_FORMAT(tgl_akhir_passport,'%d-%m-%Y') AS tgl_akhir_passport,
					rt,rw,alamat,alamat_asal,kode_pos,keperluan,jenis_passport,cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,cl_kelurahan_desa_id,create_date,create_by,update_date,update_by,file 
					from tbl_data_penduduk_asing
					where id='" . $this->input->post('id') . "' ")->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data["agama"] : "")));

				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "")));

				$this->nsmarty->assign("keperluan_passport", $this->lib->fillcombo("keperluan_passport", "return", ($sts == "edit" ? $data["keperluan"] : "")));

				$this->nsmarty->assign("jenis_passport", $this->lib->fillcombo("jenis_passport", "return", ($sts == "edit" ? $data["jenis_passport"] : "")));

				break;

			case "data_ktp":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_rekam_ktp', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "")));

				$this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin", "return", ($sts == "edit" ? $data["status_kawin"] : "")));

				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data["agama"] : "")));

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				$this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan", "return", ($sts == "edit" ? $data["pendidikan"] : "")));


				break;

			case "data_pegawai_kel_kec":
				$data['file'] = [];
				$this->nsmarty->assign('data', $data);
				if ($sts == 'edit') {
					$data = $this->db->get_where('tbl_data_pegawai_kel_kec', array('id' => $this->input->post('id')))->row_array();
					$files = $data['file'];
					$data['file'] = [];
					if (json_decode($files) != null) {
						foreach (json_decode($files) as $row) {
							$data['file'][] = $row->files;
						}
					}
					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("ceklis_laskar", isset($data['jabatan']) && strpos(strtolower($data['jabatan']), 'laskar') !== FALSE ? 'checked=true' : '');

				$this->nsmarty->assign("pilih_golongan_id", $this->lib->fillcombo("pilih_golongan_id", "return", ($sts == "edit" ? $data["golongan_id"] : "")));

				$this->nsmarty->assign("pilih_status", $this->lib->fillcombo("pilih_status", "return", ($sts == "edit" ? $data["status"] : "")));

				break;


			case "data_dasawisma":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_dasawisma', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}



				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan", "return", ($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "")));

				$this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin", "return", ($sts == "edit" ? $data["status_kawin"] : "")));

				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data["agama"] : "")));

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				$this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan", "return", ($sts == "edit" ? $data["pendidikan"] : "")));

				$this->nsmarty->assign("status_data", $this->lib->fillcombo("status", "return", ($sts == "edit" ? $data["status_data"] : "")));



				break;

			case "data_jenis_persuratan":

				if ($sts == 'edit') {

					$data = $this->db->get_where('cl_jenis_surat', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}



				$this->nsmarty->assign("jenis_surat", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["jenis_surat"] : "")));


				$this->nsmarty->assign("teks_1", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["teks_1"] : "")));

				$this->nsmarty->assign("teks_2", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["teks_2"] : "")));

				$this->nsmarty->assign("teks_3", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["teks_3"] : "")));

				break;

			case "data_lorong":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_lorong', array('id' => $this->input->post('id')))->row_array();

					$koordinat = $data['lat'] . ', ' . $data['long'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}
				$kec = $this->db->get_where('cl_kecamatan')->row_array();
				$this->nsmarty->assign('kec', $kec);

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				break;

			case "data_rekap_bulan":

				if ($sts == 'edit') {
					$data = $this->db->get_where('tbl_data_rekap_bulanan', array('id' => $this->input->post('id')))->row_array();
					$this->nsmarty->assign('data', $data);
				} else {

					// mode add → ambil data bulan sebelumnya
					$id_kel = $this->auth['cl_kelurahan_desa_id'];

					$prev = $this->db->query("
						SELECT *
						FROM tbl_data_rekap_bulanan
						WHERE cl_kelurahan_desa_id = '{$id_kel}'
						ORDER BY id DESC
						LIMIT 1
					")->row_array();

					if (!empty($prev)) {

						// HITUNG NILAI AKHIR BULAN SEBELUMNYA (bukan alias!)
						$jml_akhir_lk_wni = $prev['jml_lk_wni']
							+ $prev['lahir_lk_wni']
							- $prev['mati_lk_wni']
							+ $prev['datang_lk_wni']
							- $prev['pindah_lk_wni'];

						$jml_akhir_pr_wni = $prev['jml_pr_wni']
							+ $prev['lahir_pr_wni']
							- $prev['mati_pr_wni']
							+ $prev['datang_pr_wni']
							- $prev['pindah_pr_wni'];

						$jml_akhir_lk_wna = $prev['jml_lk_wna']
							+ $prev['lahir_lk_wna']
							- $prev['mati_lk_wna']
							+ $prev['datang_lk_wna']
							- $prev['pindah_lk_wna'];

						$jml_akhir_pr_wna = $prev['jml_pr_wna']
							+ $prev['lahir_pr_wna']
							- $prev['mati_pr_wna']
							+ $prev['datang_pr_wna']
							- $prev['pindah_pr_wna'];

						// SET AWAL BULAN BARU
						$data['jml_lk_wni'] = $jml_akhir_lk_wni;
						$data['jml_pr_wni'] = $jml_akhir_pr_wni;
						$data['jml_lk_wna'] = $jml_akhir_lk_wna;
						$data['jml_pr_wna'] = $jml_akhir_pr_wna;
					} else {
						// DATA PERTAMA → default 0
						$data['jml_lk_wni'] = 0;
						$data['jml_pr_wni'] = 0;
						$data['jml_lk_wna'] = 0;
						$data['jml_pr_wna'] = 0;
					}

					$this->nsmarty->assign('data', $data);
				}

				$kec = $this->db->get_where('cl_kecamatan')->row_array();
				$this->nsmarty->assign('kec', $kec);

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ($sts == "edit" ? $data["bulan"] : "")));

				break;

			case "data_ekspedisi":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_ekspedisi', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}
				$kec = $this->db->get_where('cl_kecamatan')->row_array();
				$this->nsmarty->assign('kec', $kec);

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				break;

			case "data_rekap_imb":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_rekap_imb', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}
				$kec = $this->db->get_where('cl_kecamatan')->row_array();
				$this->nsmarty->assign('kec', $kec);

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				break;

			case "data_wamis":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_wamis', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);


				// $this->nsmarty->assign("data_penduduk", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["nik"] : "")));

				$this->nsmarty->assign("data_penduduk_id", $this->lib->fillcombo("data_penduduk_id", "return", ($sts == "edit" ? $data["nik"] : "")));

				// $this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin","return",($sts == "edit" ? $data["status_kawin"] : "") ));

				$this->nsmarty->assign("jenis_wamis", $this->lib->fillcombo("cl_jenis_wamis", "return", ($sts == "edit" ? $data["jenis_wamis"] : "")));

				// $this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin","return",($sts == "edit" ? $data["jenis_kelamin"] : "") ));

				// $this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan","return",($sts == "edit" ? $data["pendidikan"] : "") ));

				// $this->nsmarty->assign("status_data", $this->lib->fillcombo("status","return",($sts == "edit" ? $data["status_data"] : "") ));

				break;

			case "data_pkl":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_pkl', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				// $this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin","return",($sts == "edit" ? $data["status_kawin"] : "") ));

				// $this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama","return",($sts == "edit" ? $data["agama"] : "") ));

				// $this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin","return",($sts == "edit" ? $data["jenis_kelamin"] : "") ));

				// $this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan","return",($sts == "edit" ? $data["pendidikan"] : "") ));

				// $this->nsmarty->assign("status_data", $this->lib->fillcombo("status","return",($sts == "edit" ? $data["status_data"] : "") ));

				break;

			case "data_retribusi_sampah":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_retribusi_sampah', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return", ($sts == "edit" ? $data["tahun"] : "")));

				$this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ($sts == "edit" ? $data["bulan"] : "")));

				break;

			case "data_tempat_ibadah":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_tempat_ibadah', array('id' => $this->input->post('id')))->row_array();
					$koordinat = $data['lat'] . ', ' . $data['long'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);

				$this->nsmarty->assign("tempat_ibadah", $this->lib->fillcombo("tempat_ibadah", "return", ($sts == "edit" ? $data["jns_tempat_ibadah"] : "")));

				break;

			case "data_sekolah":

				if ($sts == 'edit') {

					$data = $this->db->get_where('cl_master_pendidikan', array('id' => $this->input->post('id')))->row_array();

					$koordinat = $data['lat'] . ', ' . $data['long'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);

				$this->nsmarty->assign("jenjang_sekolah", $this->lib->fillcombo("jenjang_sekolah", "return", ($sts == "edit" ? $data["bp"] : "")));
				$this->nsmarty->assign("status_sekolah", $this->lib->fillcombo("status_sekolah", "return", ($sts == "edit" ? $data["status"] : "")));

				break;

			case "data_detail_sekolah":

				if ($sts == 'edit') {

					$data = $this->db->get_where('cl_master_dapodik', array('id' => $this->input->post('id')))->row_array();
					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("list_npsn", $this->lib->fillcombo("list_npsn", "return", ($sts == "edit" ? $data["npsn"] : "")));

				break;

			case "data_faskes":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_rs', array('id' => $this->input->post('id')))->row_array();
					$koordinat = $data['lat'] . ', ' . $data['long'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);

				$this->nsmarty->assign("jenis_rs", $this->lib->fillcombo("jenis_rs", "return", ($sts == "edit" ? $data["jenis"] : "")));
				$this->nsmarty->assign("kelas_rs", $this->lib->fillcombo("kelas_rs", "return", ($sts == "edit" ? $data["kelas"] : "")));
				$this->nsmarty->assign("jenis_pelayanan", $this->lib->fillcombo("jenis_pelayanan", "return", ($sts == "edit" ? $data["jenis_pelayanan"] : "")));
				$this->nsmarty->assign("akreditasi", $this->lib->fillcombo("akreditasi", "return", ($sts == "edit" ? $data["akreditasi"] : "")));

				break;

			case "data_penandatanganan":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_penandatanganan', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("pilih_tingkat_jabatan", $this->lib->fillcombo("pilih_tingkat_jabatan", "return", ($sts == "edit" ? $data["tingkat_jabatan"] : "")));

				$this->nsmarty->assign("pilih_status", $this->lib->fillcombo("pilih_status", "return", ($sts == "edit" ? $data["status"] : "")));

				$this->nsmarty->assign("pilih_jabatan", $this->lib->fillcombo("pilih_jabatan", "return", ($sts == "edit" ? $data["jabatan"] : "")));

				// $this->nsmarty->assign("pilih_golongan", $this->lib->fillcombo("pilih_golongan", "return", ($sts == "edit" ? $data["pangkat"] : "")));

				$this->nsmarty->assign("pilih_pangkatx", $this->lib->fillcombo("pilih_pangkatx", "return", ($sts == "edit" ? $data["pangkat"] : "")));
				// var_dump('masuk');
				// exit();

				break;

			case "daftar_agenda_kegiatan":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_daftar_agenda', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("jenis_agenda", $this->lib->fillcombo("jenis_agenda", "return", ($sts == "edit" ? $data["jenis_agenda"] : "")));

				break;

			// case "laporan_hasil_kegiatan":

			// 	if ($sts == 'edit') {

			// 		$data = $this->db->get_where('tbl_data_hasil_agenda', array('id' => $this->input->post('id')))->row_array();

			// 		$this->nsmarty->assign('data', $data);
			// 	}

			// 	$this->nsmarty->assign("perihal_hasil_agenda", $this->lib->fillcombo("perihal_hasil_agenda", "return"));

			// break;

			case "laporan_hasil_kegiatan":

				// ================= DEFAULT =================
				$data   = [];
				$agenda = [];
				$sts    = 'add';

				$id = $this->input->post('id'); // 🔥 ID DARI GRID (WAJIB)

				if ($id) {

					/* ================== CEK APAKAH ID INI HASIL AGENDA ================== */
					$hasil = $this->db
						->get_where(
							'tbl_data_hasil_agenda',
							['id' => $id]
						)
						->row_array();

					if ($hasil) {
						// ===== EDIT HASIL =====
						$sts  = 'edit';
						$data = $hasil;

						// ambil agenda dari hasil
						$agenda = $this->db
							->get_where(
								'tbl_data_daftar_agenda',
								['id' => $hasil['perihal_hasil_agenda']]
							)
							->row_array();

					} else {
						// ===== ADD HASIL (ID = AGENDA) =====
						$agenda = $this->db
							->get_where(
								'tbl_data_daftar_agenda',
								['id' => $id]
							)
							->row_array();

						if ($agenda) {
							$data = [
								'id'                  => '',
								'perihal_hasil_agenda'=> $agenda['id'],
								'tgl_hasil_agenda'    => '',
								'notulen_hasil_agenda'=> '',
								'ket_hasil_agenda'    => '',
								'file'                => ''
							];
						}
					}
				}

				$data['sts'] = $sts;

				/* ================== ASSIGN KE VIEW ================== */
				$this->nsmarty->assign('data', $data);
				$this->nsmarty->assign('agenda', $agenda);

				/* ================== COMBO PERIHAL (DIKUNCI) ================== */
				if (!empty($agenda)) {
					$this->nsmarty->assign(
						"perihal_hasil_agenda",
						"<option value='{$agenda['id']}' selected>{$agenda['perihal_kegiatan']}</option>"
					);
				} else {
					$this->nsmarty->assign(
						"perihal_hasil_agenda",
						$this->lib->fillcombo("perihal_hasil_agenda", "return")
					);
				}

			break;

			case "data_kendaraan":

				$this->nsmarty->assign('cl_user_group_id', $this->auth["cl_user_group_id"]);

				$data['file'] = [];
				$this->nsmarty->assign('data', $data);
				if ($sts == 'edit') {
					$data = $this->db->get_where('tbl_data_kendaraan', array('id' => $this->input->post('id')))->row_array();
					$files = $data['file'];
					$data['file'] = [];
					if (json_decode($files) != null) {
						foreach (json_decode($files) as $row) {
							$data['file'][] = $row->files;
						}
					}
					$this->nsmarty->assign('data', $data);
				}
				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);
				// if ($sts == 'edit') {

				// 	$data = $this->db->get_where('tbl_data_kendaraan', array('id' => $this->input->post('id')))->row_array();

				// 	$this->nsmarty->assign('data', $data);
				// }

				$this->nsmarty->assign("kd_brg", $this->lib->fillcombo("kd_brg", "return", ($sts == "edit" ? $data["kode_barang"] : "")));

				$this->nsmarty->assign("asal_kelurahan", $this->lib->fillcombo("asal_kelurahan", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("pilih_tahun_perolehan", $this->lib->fillcombo("pilih_tahun_perolehan", "return", ($sts == "edit" ? $data["tahun_perolehan"] : "")));
				// var_dump('masuk');
				// exit();

				break;

			case "data_indikator_skm":

				$this->nsmarty->assign('cl_user_group_id', $this->auth["cl_user_group_id"]);

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_indikator_skm', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return", ($sts == "edit" ? $data["tahun"] : "")));

				// var_dump('masuk');
				// exit();

				break;

			case "data_penilaian_skm":

				$this->nsmarty->assign('cl_user_group_id', $this->auth["cl_user_group_id"]);

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_penilaian_skm', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "")));

				// var_dump('masuk');
				// exit();

				break;

			// case "form_sub_indikator_rt_rw":

			// 	if ($sts == 'edit') {

			// 		$data = $this->db->get_where('tbl_kategori_penilaian_rt_rw', array('id' => $this->input->post('id')))->row_array();

			// 		$this->nsmarty->assign('data', $data);
			// 	}

			// 	$this->nsmarty->assign("data_kategori_id", $this->lib->fillcombo("data_kategori_id", "return", ($sts == "edit" ? $data["kategori"] : "")));

			// 	$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return", ($sts == "edit" ? $data["tahun"] : "")));
			// 	// var_dump('masuk');
			// 	// exit();

			// break;	
			case "form_sub_indikator_rt_rw":

				$this->nsmarty->assign("data_kategori_id", $this->lib->fillcombo("data_kategori_id", "return"));

				$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return"));

				// var_dump('masuk');
				// exit();

				break;

			case "data_sub_indikator_rt_rw":
				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_kategori_penilaian_rt_rw', array('id' => $this->input->post('id')))->row_array();
				}

				$this->nsmarty->assign("data_kategori_id", $this->lib->fillcombo("data_kategori_id", "return", ($sts == "edit" ? $data["kategori"] : "")));

				$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return", ($sts == "edit" ? $data["tahun"] : "")));

				// var_dump('masuk');
				// exit();

				break;

			case "data_petugas_kebersihan":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_petugas_kebersihan', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}
				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				$this->nsmarty->assign("status_pegawai", $this->lib->fillcombo("status_pegawai", "return", ($sts == "edit" ? $data["status_pegawai"] : "")));

				break;

			case "data_umkm":

				if ($sts == 'edit') {

					$data = $this->db->get_where('cl_master_umkm', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
					$koordinat = $data['latitude'] . ', ' . $data['longitude'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}
				$kec = $this->db->get_where('cl_kecamatan')->row_array();
				$this->nsmarty->assign('kecamatan', $kec);

				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kelurahan', $kel);

				$this->nsmarty->assign("modal", $this->lib->fillcombo("modal_obzet", "return", ($sts == "edit" ? $data["modal"] : "")));
				$this->nsmarty->assign("obzet", $this->lib->fillcombo("modal_obzet", "return", ($sts == "edit" ? $data["obzet"] : "")));

				$this->nsmarty->assign("jenis_umkm", $this->lib->fillcombo("jenis_umkm", "return", ($sts == "edit" ? $data["jenis"] : "")));
				$this->nsmarty->assign("sertifikat", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["sertifikat"] : "")));
				$this->nsmarty->assign("nib", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["nib"] : "")));
				$this->nsmarty->assign("uimk", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["uimk"] : "")));
				$this->nsmarty->assign("situ", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["situ"] : "")));
				$this->nsmarty->assign("tdp", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["tdp"] : "")));
				$this->nsmarty->assign("pirt", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["pirt"] : "")));
				$this->nsmarty->assign("s_halal", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["s_halal"] : "")));
				$this->nsmarty->assign("website", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["website"] : "")));
				$this->nsmarty->assign("facebook", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["facebook"] : "")));
				$this->nsmarty->assign("instagram", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["instagram"] : "")));
				$this->nsmarty->assign("tokopedia", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["tokopedia"] : "")));
				$this->nsmarty->assign("shopee", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["shopee"] : "")));
				$this->nsmarty->assign("bukalapak", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["bukalapak"] : "")));
				$this->nsmarty->assign("lazada", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["lazada"] : "")));
				$this->nsmarty->assign("blibli", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["blibli"] : "")));
				$this->nsmarty->assign("jd_id", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["jd_id"] : "")));
				$this->nsmarty->assign("grab", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["grab"] : "")));
				$this->nsmarty->assign("gojek", $this->lib->fillcombo("punya_atau_tidak", "return", ($sts == "edit" ? $data["gojek"] : "")));

				break;

			case "data_rt_rw":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_rt_rw', array('id' => $this->input->post('id')))->row_array();

					$koordinat = $data['lat'] . ', ' . $data['long'];
					$this->nsmarty->assign('data', $data);
					$this->nsmarty->assign('kor', $koordinat);
				}
				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);

				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama", "return", ($sts == "edit" ? $data["agama"] : "")));

				$this->nsmarty->assign("jab_rt_rw", $this->lib->fillcombo("jab_rt_rw", "return", ($sts == "edit" ? $data["jab_rt_rw"] : "")));

				$this->nsmarty->assign("pilih_status", $this->lib->fillcombo("pilih_status", "return", ($sts == "edit" ? $data["status"] : "")));

				$this->nsmarty->assign("pilih_tahun", $this->lib->fillcombo("pilih_tahun", "return", ($sts == "edit" ? $data["pilih_tahun"] : "")));

				$this->nsmarty->assign("data_penduduk_id", $this->lib->fillcombo("data_penduduk_id", "return", ($sts == "edit" ? $data["nik"] : "")));

				break;

			// case "usulan_penilaian_rt_rw":

			// 	$data['bulan'] = '';

			// 	if ($sts == 'edit') {

			// 		$data = $this->db->get_where('tbl_usulan_penilaian_rt_rw', array('id' => $this->input->post('id')))->row_array();

			// 		$this->nsmarty->assign('data', $data);
			// 	}

			// 	$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
			// 	$this->nsmarty->assign('kel', $kel);

			// 	$this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ($sts == "edit" ? $data["bulan"] : "")));

			// break;
			case "penilaian_rt_rw":

				$data['bulan'] = '';
				$data['nik'] = '';

				if ($sts == 'edit') {
					if ($this->input->post('rt_rw_id') != '' && $this->input->post('id') == '') {
						$data = $this->db->select("id tbl_data_rt_rw_id,nik,nama_lengkap")->where([
							'id' => $this->input->post('rt_rw_id'),
						])->get('tbl_data_rt_rw')->row_array();
						$data['tgl_surat'] = '';
						$bln = $this->input->post('bulan');

						// $this->nsmarty->assign("bulan", "<option value=\"$data[bulan]\">" . getBulan($data['bulan']) . "</option>");
						// $this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ""));
						$data['penilaian_id'] = '';
						$sts = 'add';
						$data['sts'] = 'add';
						$this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ($sts == "add" ? $bln : "")));
					} else {
						$data = $this->db->get_where('tbl_penilaian_rt_rw', array('penilaian_id' => $this->input->post('id')))->row_array();
						$data['tgl_surat'] = date('d-m-Y', strtotime($data['tgl_surat']));
						$this->nsmarty->assign("bulan", "<option value=\"$data[bulan]\">" . getBulan($data['bulan']) . "</option>");
						$sts = 'edit';
						$data['sts'] = 'edit';
					}
					$this->nsmarty->assign('data', $data);
				}


				if ($sts == 'edit') {
					if ($this->input->post('rt_rw_id') != '' && $this->input->post('id') != '') {
						$this->nsmarty->assign("data_rt_rw_id", "<option value=\"$data[tbl_data_rt_rw_id]\">$data[nik]-$data[nama_lengkap]</option>");

						$kategori_penilaian = $this->db->query("SELECT a.id,a.kategori,a.uraian,a.satuan,ifnull(b.target,'')target,ifnull(b.capaian,'')capaian,ifnull(b.nilai,'')nilai 
									from tbl_kategori_penilaian_rt_rw a
									left join(
									select penilaian_id,kategori_penilaian_rt_rw_id,satuan,target,capaian,nilai from tbl_penilaian_rt_rw where penilaian_id='$data[penilaian_id]'
									)b on a.id=b.kategori_penilaian_rt_rw_id  
									group by a.id")->result_array();
						$sts = 'edit';
						$data['sts'] = 'edit';
					}
				} else {
					// var_dump($sts);
					// exit();
					$kategori_penilaian = $this->db->query("SELECT a.id,a.kategori,a.uraian,a.satuan,('')target,('')capaian,(0)nilai 
									from tbl_kategori_penilaian_rt_rw a")->result_array();
					// $this->nsmarty->assign("bulan", $this->lib->fillcombo("pilih_bulan", "return", ($sts == "edit" ? $data["bulan"] : "")));
					$this->nsmarty->assign("data_rt_rw_id", "<option value=\"$data[tbl_data_rt_rw_id]\">$data[nik]-$data[nama_lengkap]</option>");
				}

				$this->nsmarty->assign("kategori_penilaian", $kategori_penilaian);
				$kel = $this->db->get_where('cl_kelurahan_desa', array('id' => $this->auth['cl_kelurahan_desa_id']))->row_array();
				$this->nsmarty->assign('kel', $kel);


				break;

			case "data_kunjungan_rumah":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_kunjungan_rumah', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}


				$this->nsmarty->assign("cl_kelurahan_desa", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin", "return", ($sts == "edit" ? $data["jenis_kelamin"] : "")));

				break;

			case "data_kerja_bakti":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_kerja_bakti', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}



				$this->nsmarty->assign("cl_kelurahan_desa", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				break;

			case "data_notulen_rapat":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_data_notulen_rapat', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}



				$this->nsmarty->assign("cl_kelurahan_desa", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				break;

			case "form_user_role":

				$id_role = $this->input->post('id');

				$array = array();

				$dataParent = $this->mbackend->getdata('menu_parent', 'result_array');

				foreach ($dataParent as $k => $v) {

					$dataChild = $this->mbackend->getdata('menu_child', 'result_array', $v['id']);

					$dataPrev = $this->mbackend->getdata('previliges_menu', 'row_array', $v['id'], $id_role);



					$array[$k]['id'] = $v['id'];

					$array[$k]['nama_menu'] = $v['nama_menu'];

					$array[$k]['type_menu'] = $v['type_menu'];

					$array[$k]['id_prev'] = (isset($dataPrev['id']) ? $dataPrev['id'] : 0);

					$array[$k]['buat'] = (isset($dataPrev['buat']) ? $dataPrev['buat'] : 0);

					$array[$k]['baca'] = (isset($dataPrev['baca']) ? $dataPrev['baca'] : 0);

					$array[$k]['ubah'] = (isset($dataPrev['ubah']) ? $dataPrev['ubah'] : 0);

					$array[$k]['hapus'] = (isset($dataPrev['hapus']) ? $dataPrev['hapus'] : 0);

					$array[$k]['child_menu'] = array();

					$jml = 0;

					foreach ($dataChild as $y => $t) {

						$dataPrevChild = $this->mbackend->getdata('previliges_menu', 'row_array', $t['id'], $id_role);

						$array[$k]['child_menu'][$y]['id_child'] = $t['id'];

						$array[$k]['child_menu'][$y]['nama_menu_child'] = $t['nama_menu'];

						$array[$k]['child_menu'][$y]['type_menu'] = $t['type_menu'];

						$array[$k]['child_menu'][$y]['id_prev'] = (isset($dataPrevChild['id']) ? $dataPrevChild['id'] : 0);

						$array[$k]['child_menu'][$y]['buat'] = (isset($dataPrevChild['buat']) ? $dataPrevChild['buat'] : 0);

						$array[$k]['child_menu'][$y]['baca'] = (isset($dataPrevChild['baca']) ? $dataPrevChild['baca'] : 0);

						$array[$k]['child_menu'][$y]['ubah'] = (isset($dataPrevChild['ubah']) ? $dataPrevChild['ubah'] : 0);

						$array[$k]['child_menu'][$y]['hapus'] = (isset($dataPrevChild['hapus']) ? $dataPrevChild['hapus'] : 0);

						$jml++;



						if ($t['type_menu'] == 'CHC') {

							$array[$k]['child_menu'][$y]['sub_child_menu'] = array();

							$dataSubChild = $this->mbackend->getdata('menu_child_2', 'result_array', $t['id']);

							$jml_sub_child = 0;

							foreach ($dataSubChild as $x => $z) {

								$dataPrevSubChild = $this->mbackend->getdata('previliges_menu', 'row_array', $z['id'], $id_role);

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['id_sub_child'] = $z['id'];

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['nama_menu_sub_child'] = $z['nama_menu'];

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['id_prev'] = (isset($dataPrevSubChild['id']) ? $dataPrevSubChild['id'] : 0);

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['buat'] = (isset($dataPrevSubChild['buat']) ? $dataPrevSubChild['buat'] : 0);

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['baca'] = (isset($dataPrevSubChild['baca']) ? $dataPrevSubChild['baca'] : 0);

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['ubah'] = (isset($dataPrevSubChild['ubah']) ? $dataPrevSubChild['ubah'] : 0);

								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['hapus'] = (isset($dataPrevSubChild['hapus']) ? $dataPrevSubChild['hapus'] : 0);

								$jml_sub_child++;
							}
						}
					}

					$array[$k]['total_child'] = $jml;
				}



				$this->nsmarty->assign('role', $array);

				$this->nsmarty->assign('id_group', $id_role);

				break;



			case "user_group":

				if ($sts == 'edit') {

					$data = $this->db->get_where('cl_user_group', array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				break;

			case "user_mng":

				if ($sts == 'edit') {

					$data = $this->db->get_where('tbl_user', array('id' => $this->input->post('id')))->row_array();

					$data["password"] = $this->encrypt->decode($data["password"]);

					$this->nsmarty->assign('data', $data);
				}



				$this->nsmarty->assign("cl_user_group_id", $this->lib->fillcombo("cl_user_group", "return", ($sts == "edit" ? $data["cl_user_group_id"] : "")));

				$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", ($sts == "edit" ? $data["cl_provinsi_id"] : "")));

				$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", ($sts == "edit" ? $data["cl_kab_kota_id"] : "")));

				$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", ($sts == "edit" ? $data["cl_kecamatan_id"] : "")));

				$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "")));

				break;

			default:

				if ($sts == 'edit') {

					$table = $this->input->post("ts");

					$data = $this->db->get_where("tbl_" . $table, array('id' => $this->input->post('id')))->row_array();

					$this->nsmarty->assign('data', $data);
				}

				break;
		}



		if (isset($sts)) {

			$this->nsmarty->assign('sts', $sts);
		} else {

			$sts = "";
		}



		$this->nsmarty->assign('mod', $mod);

		$this->nsmarty->assign('temp', $temp);



		if (!file_exists($this->config->item('appl') . APPPATH . 'views/' . $temp)) {
			$this->nsmarty->display('konstruksi.html');
		} else {
			$this->nsmarty->display($temp);
		}
	}

	function getdisplay($type)
	{

		switch ($type) {

			case "combo_penduduk":

				echo $this->lib->fillcombo("data_penduduk", "return");

				break;

			case "hapusfoto_galeri":

				$id = htmlspecialchars($this->input->post('id'), ENT_QUOTES);

				$filename = htmlspecialchars($this->input->post('filename'), ENT_QUOTES);

				$upload_path = "./__repository/gallery/";



				if (file_exists($upload_path . $filename)) {

					$this->db->delete('tbl_gallery_detail', array('tbl_gallery_id' => $id));

					unlink($upload_path . $filename);

					echo 1;
				}

				break;

			case "hapusfoto_lelang":

				$id = htmlspecialchars($this->input->post('id'), ENT_QUOTES);

				$filename = htmlspecialchars($this->input->post('filename'), ENT_QUOTES);

				$upload_path = "./__repository/lelang/";



				if (file_exists($upload_path . $filename)) {

					$this->db->delete('tbl_lelang_foto', array('tbl_lelang_id' => $id));

					unlink($upload_path . $filename);

					echo 1;
				}

				break;

			case "ceksession":

				if ($this->auth) {

					echo 1;
				} else {

					echo 2;
				}

				break;
		}
	}



	function getdata($p1, $p2 = "", $p3 = "")
	{

		echo $this->mbackend->getdata($p1, 'json', $p3);
	}



	function simpandata($p1 = "", $p2 = "")
	{

		// print_r($_POST);exit;



		if ($this->input->post('mod')) $p1 = $this->input->post('mod');

		$post = array();
		foreach ($_POST as $k => $v) {

			if ($this->input->post($k) != "") {

				$post[$k] = $this->input->post($k);
			} else {

				$post[$k] = null;
			}



			//$post[$k] = str_replace("'", "’", $post[$k]);

			$post[$k] = str_replace("'", "&#96;", $post[$k]);

			$post[$k] = str_replace("’", "&#96;", $post[$k]);
		}



		if (isset($post['editstatus'])) {
			$editstatus = $post['editstatus'];
			unset($post['editstatus']);
		} else $editstatus = $p2;

		echo $this->mbackend->simpandata($p1, $post, $editstatus, $tabel2 = "", $data2 = array());
	}



	function test()
	{

		echo $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']);
		exit;
	}

	function combo_option($mod)
	{

		$opt = "";

		switch ($mod) {
			case "data_user_pemeriksa_esign":
				$sql = $this->db->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id'])
					->where('status', 'Aktif')
					// ->where('nip_pegawai is not null')
					->get('tbl_data_penandatanganan');
				$opt .= "<option value=''>Pilih..</option>";

				foreach ($sql->result() as $row) {
					$opt .= "<option value='$row->nip'>$row->nama</option>";
				}

				break;

			case "data_surat":

				$opt .= "<option value='C.jenis_surat'>Jenis Surat</option>";

				$opt .= "<option value='A.nama_pemohon'>Nama Pemohon</option>";

				$opt .= "<option value='B.nik'>NIK</option>";

				$opt .= "<option value='A.info_tambahan'>Lainnya</option>";

				break;

			case "data_esign":

				$opt .= "<option value='A.nama_pemohon'>Nama Pemohon</option>";

				$opt .= "<option value='C.jenis_surat'>Jenis Surat</option>";

				break;
			case "laporan_persuratan":

				$opt .= "<option value='b.jenis_surat'>Jenis Surat</option>";

				break;
			case "laporan_rekap_usaha":

				$opt .= "<option value='b.jenis_surat'>Jenis Surat</option>";

				break;

			case "laporan_rekap_pengantar_kendaraan":

				$opt .= "<option value='b.jenis_surat'>Jenis Surat</option>";

				break;

			case "laporan_persuratan_masuk":

				$opt .= "<option value='b.jenis_surat'>Jenis Surat</option>";

				break;

			case "data_keluarga":

				$opt .= "<option value='A.no_kk'>No. KK</option>";

				$opt .= "<option value='B.nama_lengkap'>Nama Kepala Keluarga</option>";

				$opt .= "<option value='B.rw'>RW</option>";

				break;

			case "data_penduduk":

				$opt .= "<option value='A.nik'>NIK</option>";

				$opt .= "<option value='A.nama_lengkap'>Nama Lengkap</option>";

				$opt .= "<option value='A.no_kk'>No. KK</option>";

				$opt .= "<option value='A.status_data'>Status Data</option>";

				$opt .= "<option value='A.rw'>RW</option>";


				break;

			case "data_penduduk_asing":

				$opt .= "<option value='A.no_passport'>No. Passport</option>";

				$opt .= "<option value='A.nama_lengkap'>Nama Lengkap</option>";

				$opt .= "<option value='A.no_pengenalan'>No. Pengenalan</option>";

				break;

			case "data_ktp":

				$opt .= "<option value='A.nik'>NIK</option>";

				$opt .= "<option value='A.nama_lengkap'>Nama Lengkap</option>";

				break;

			case "data_pegawai_kel_kec":

				$opt .= "<option value='A.nip'>NIP</option>";

				$opt .= "<option value='A.nama'>Nama</option>";

				$opt .= "<option value='A.pangkat'>Pangkat</option>";

				$opt .= "<option value='A.jabatan'>Jabatan</option>";

				break;

			case "data_dasawisma":

				$opt .= "<option value='A.nik'>NIK</option>";

				$opt .= "<option value='A.nama_lengkap'>Nama Lengkap</option>";

				$opt .= "<option value='A.no_kk'>No. KK</option>";

				break;


			case "data_penandatanganan":
				$opt .= "<option value='nip'>NIP</option>";
				$opt .= "<option value='nama'>Nama</option>";
				$opt .= "<option value='pangkat'>Pangkat</option>";
				break;

			case "daftar_agenda_kegiatan":
				$opt .= "<option value='a.lokasi_kegiatan'>Lokasi</option>";
				$opt .= "<option value='a.instansi_pengirim'>Pengirim</option>";
				$opt .= "<option value='a.perihal_kegiatan'>Perihal</option>";
				$opt .= "<option value='a.pj_kegiatan'>Penanggung Jawab</option>";
				break;

			case "laporan_hasil_kegiatan":
				$opt .= "<option value='d.agenda'>Agenda</option>";
				break;

			case "data_kendaraan":
				$opt .= "<option value='nama_sopir'>Nama Pengemudi</option>";
				$opt .= "<option value='nopol'>Nomor Polisi</option>";

				break;

			case "data_jenis_persuratan":
				$opt .= "<option value='jenis_surat'>Jenis Surat</option>";
				break;

			case "data_lorong":

				$opt .= "<option value='A.nama_lorong'>Nama Lorong</option>";
				$opt .= "<option value='A.nama_pj'>Nama Penanggung Jawab</option>";

				break;

			case "data_rekap_bulan":

				$opt .= "<option value='nama_bulan'>Bulan</option>";

				break;

			case "data_umkm":

				$opt .= "<option value='A.pemilik'>Nama Pemilik</option>";

				$opt .= "<option value='A.nama_umkm'>Nama UKM</option>";

				$opt .= "<option value='A.alamat'>Alamat</option>";

				break;

			case "data_pkl":

				$opt .= "<option value='A.nama'>Nama</option>";

				$opt .= "<option value='A.jenis_usaha'>Jenis Usaha</option>";

				$opt .= "<option value='A.alamat'>Alamat</option>";

				$opt .= "<option value='A.kelurahan'>Kelurahan</option>";

				break;

			case "data_petugas_kebersihan":

				$opt .= "<option value='A.nama_petugas_keb'>Nama</option>";

				$opt .= "<option value='A.pekerjaan'>Pekerjaan</option>";

				$opt .= "<option value='A.jenis_kelamin'>Jenis Kelamin</option>";

				$opt .= "<option value='A.status_pegawai'>Status</option>";

				break;

			case "data_retribusi_sampah":

				$opt .= "<option value='kelurahan'>Kelurahan</option>";

				$opt .= "<option value='nama_bulan'>Bulan</option>";

				break;

			case "data_rt_rw":

				$opt .= "<option value='a.nama_lengkap'>Nama</option>";

				$opt .= "<option value='a.nik'>NIK</option>";

				$opt .= "<option value='a.jab_rt_rw'>Jabatan</option>";

				break;

			case "penilaian_rt_rw":

				$opt .= "<option value='a.nama_lengkap'>Nama</option>";

				$opt .= "<option value='a.nik'>NIK</option>";

				// $opt .= "<option value='a.jab_rt_rw'>Jabatan</option>";

				break;

			case "rekap_penilaian_kelrtrw":

				$opt .= "<option value='a.nama_lengkap'>Nama</option>";

				$opt .= "<option value='a.nik'>NIK</option>";

				// $opt .= "<option value='a.jab_rt_rw'>Jabatan</option>";

				break;


			case "data_sekolah":

				$opt .= "<option value='A.bp'>Jenjang</option>";

				$opt .= "<option value='A.nama_sekolah'>Nama Sekolah</option>";

				break;

			case "data_detail_sekolah":

				$query = $this->db->query("select thn_ajar from cl_master_dapodik  GROUP BY thn_ajar");

				$opt .= "<option value=''>Pilih..</option>";

				foreach ($query->result() as $row) {
					$opt .= "<option value='$row->thn_ajar'>$row->thn_ajar</option>";
				}


				break;

			case "data_faskes":


				$opt .= "<option value='A.nama'>Nama RS</option>";

				$opt .= "<option value='A.jenis'>Jenis</option>";



				break;

			case "data_tempat_ibadah":


				$opt .= "<option value='A.jns_tempat_ibadah'>Tempat Ibadah</option>";

				$opt .= "<option value='A.nama_tempat_ibadah'>Nama</option>";

				$opt .= "<option value='A.alamat'>Alamat</option>";

				$opt .= "<option value='A.ketua_pengurus'>Pengurus</option>";

				break;

			case "data_wamis":

				$opt .= "<option value='A.nama'>Nama</option>";

				$opt .= "<option value='A.no_peserta'>No. Peserta</option>";

				$opt .= "<option value='A.jenis_wamis'>Jenis Wamis</option>";

				$opt .= "<option value='b.rw'>RW</option>";

				break;

			case "data_kunjungan_rumah":



				$opt .= "<option value='A.nama_kk'>Nama Kepala Keluarga</option>";

				$opt .= "<option value='A.no_kk'>No. Kepala Keluarga</option>";

				$opt .= "<option value='A.rt'>RT</option>";

				$opt .= "<option value='A.rw'>RW</option>";

				break;

			case "data_kerja_bakti":



				$opt .= "<option value='A.lokasi'>Lokasi</option>";

				$opt .= "<option value='A.tanggal'>Tanggal</option>";

				break;

			case "data_notulen_rapat":



				$opt .= "<option value='A.tanggal'>Tanggal</option>";

				break;

			case "user_group":

				$opt .= "<option value='A.user_group'>User Group Name</option>";

				break;

			case "user_mng":

				$opt .= "<option value='A.username'>Username</option>";

				$opt .= "<option value='A.nama_lengkap'>Real Name</option>";

				break;

			case "surat_masuk":

				$opt .= "<option value='B.jenis_surat'>Jenis Surat Masuk</option>";
				$opt .= "<option value='B.sifat_surat'>Sifat Surat Masuk</option>";

				break;

			case "surat_lain":

				$opt .= "<option value='A.no_surat'>No Surat</option>";
				$opt .= "<option value='A.tgl_surat'>Tanggal Surat</option>";

				break;

			case "surat_himbauan":

				$opt .= "<option value='B.jenis_surat'>Jenis Surat Masuk</option>";
				$opt .= "<option value='B.sifat_surat'>Sifat Surat Masuk</option>";

				break;

			case "broadcast":

				$opt .= "<option value='B.tgl_broadcast'>Tanggal</option>";
				$opt .= "<option value='B.cl_kelurahan_desa_id'>Tujuan Broadcast</option>";

				break;

			case "notif_broadcast":

				$opt .= "<option value='B.tgl_broadcast'>Tanggal</option>";
				$opt .= "<option value='B.subjek'>Perihal</option>";

				break;

			default:

				$txt = str_replace("_", " ", $mod);

				$opt .= "<option value='A." . $mod . "'>" . strtoupper($txt) . "</option>";

				break;
		}

		return $opt;
	}


	function cetak($mod, $p1 = "", $p2 = "", $p3 = "", $p4 = "")
	{
		ini_set("memory_limit", "5000M");
		ini_set("max_execution_time", "-1");
		switch ($mod) {

			case "laporan_penduduk":

				$data = $this->mbackend->getdata('laporan_penduduk', 'result_array');

				$filename = "laporan-penduduk-" . date('YmdHis');

				$temp = "backend/cetak/laporan_penduduk.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, "LEGAL-L");
				// $this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;

			case "laporan_penduduk_asing":

				$data = $this->mbackend->getdata('laporan_penduduk_asing', 'result_array');

				$filename = "laporan_penduduk_asing-" . date('YmdHis');

				$temp = "backend/cetak/laporan_penduduk_asing.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;

			case "laporan_cetak_usia":

				$data = $this->mbackend->getdata('laporan_cetak_usia', 'result_array');

				$filename = "laporan_cetak_usia-" . date('YmdHis');

				$temp = "backend/cetak/laporan_cetak_usia.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;

			case "laporan_cetak_kelamin":

				$data = $this->mbackend->getdata('laporan_cetak_kelamin', 'result_array');

				$filename = "laporan_cetak_warga-" . date('YmdHis');

				$temp = "backend/cetak/laporan_cetak_kelamin.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;

			case "laporan_cetak_kawin":

				$data = $this->mbackend->getdata('laporan_cetak_kawin', 'result_array');

				$filename = "laporan_cetak_kawin-" . date('YmdHis');

				$temp = "backend/cetak/laporan_cetak_kelamin.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;

			case "laporan_ktp":

				$data = $this->mbackend->getdata('laporan_ktp', 'result_array');

				$filename = "laporan-ktp-" . date('YmdHis');

				$temp = "backend/cetak/laporan_ktp.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");



				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_wamis":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_wamis', 'result_array');

				$filename = "laporan-wamis-" . date('YmdHis');

				$temp = "backend/cetak/laporan_wamis.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_umkm":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);

				$data = $this->mbackend->getdata('laporan_umkm', 'result_array');

				$filename = "laporan-umkm-" . date('YmdHis');

				$temp = "backend/cetak/laporan_umkm.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");



				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_dasawisma":
				// $nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					// 'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_dasawisma', 'result_array');

				$filename = "laporan-dasawisma-" . date('YmdHis');

				$temp = "backend/cetak/laporan_dasawisma.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_rt_rw":
				// $nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					// 'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_rt_rw', 'result_array');

				$filename = "laporan-rt_rw-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rt_rw.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_penilaian_rt_rw":
				$rt_rw_id = $this->input->get('rt_rw_id');
				$bulan = $this->input->get('bulan');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$nip = $this->input->get('nip');
				$nik_lsm = $this->input->get('nik_lsm');
				$nik_pembuat = $this->input->get('nik_pembuat');
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					'b.id' => $rt_rw_id,
					// 'c.id' => $id

				);


				$this->db
					->select('a.*,b.nik,b.nama_lengkap,b.tgl_mulai_jabat,b.jab_rt_rw as jabatan,b.alamat,c.tgl_surat,c.bulan,b.rt,b.rw,d.nip as nip_camat,d.nama as nama_camat,d.jabatan as jabatan_camat,d.pangkat as pangkat_camat,e.nama as nama_lsm,e.nip as nik_lsm,e.jabatan as jabatan_lsm,f.nip as nik_pembuat,f.nama as nama_pembuat,f.jabatan as jabatan_pembuat,g.nip as nip_lurah,g.nama as nama_lurah,g.jabatan as jabatan_lurah,g.pangkat as pangkat_lurah')
					->where($array_setting)
					->join('tbl_data_rt_rw b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->join('tbl_penilaian_rt_rw c', "b.nik=c.nik", 'inner')
					->join('tbl_data_penandatanganan d', "a.cl_kecamatan_id=d.cl_kecamatan_id AND d.tingkat_jabatan='1.1' AND d.nip='$nip'", 'left')
					->join('tbl_data_penandatanganan e', "a.cl_kecamatan_id=e.cl_kecamatan_id and a.cl_kelurahan_desa_id=e.cl_kelurahan_desa_id AND e.tingkat_jabatan='3.1' AND e.nip='$nik_lsm'", 'left')
					->join('tbl_data_penandatanganan f', "a.cl_kecamatan_id=f.cl_kecamatan_id and a.cl_kelurahan_desa_id=f.cl_kelurahan_desa_id and f.tingkat_jabatan='2.4' AND f.nip='$nik_pembuat'", 'left')
					->join('tbl_data_penandatanganan g', "a.cl_kecamatan_id=g.cl_kecamatan_id and a.cl_kelurahan_desa_id=g.cl_kelurahan_desa_id and g.tingkat_jabatan='2.1' AND g.status='Aktif'", 'left')
					->group_by("a.id");

				if ($nip != '') {
					$this->db->where('d.nip', $nip);
				}
				if ($nik_lsm != '') {
					$this->db->where('e.nip', $nik_lsm);
				}
				if ($nik_pembuat != '') {
					$this->db->where('f.nip', $nik_pembuat);
				}
				if ($bulan != '') {
					$this->db->where('c.bulan', $bulan);
				}
				$this->setting = $this->db->get("tbl_setting_apps a")->row_array();

				$ttd = array(
					'camat' => $nip,
					'lsm' => $nik_lsm,
					'pembuat' => $nik_pembuat,
				);
				$this->nsmarty->assign("ttd", $ttd);
				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data['penilaian'] = $this->mbackend->getdata('laporan_penilaian_rt_rw', 'result_array');


				$filename = "laporan_penilaian_rt_rw-" . date('YmdHis');

				$temp = "backend/cetak/laporan_penilaian_rt_rw.html";
				// $this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");
				$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
					'mode' => 'utf-8',
					'format' => 'LEGAL-P',
					'margin_left' => 5,
					'margin_right' => 5,
					'margin_top' => 5,
					'margin_bottom' => 5,
					'margin_header' => 0,
					'margin_footer' => 0,
					'default_font_size' => '12pt',
					'default_font' => '',
				]);

				//echo "<pre>";

				//print_r($data);exit;

				break;


			case "laporan_rekap_penilaian_rt_rw":
				ini_set('memory_limit', -1);
				ini_set('max_execution_time', -1);
				$nip = $this->input->get('nip');

				$nik_lsm = $this->input->get('nik_lsm');
				$nik_pembuat = $this->input->get('nik_pembuat');
				$bulan = $this->input->get('bulan'); // contoh: '05' dari Mei
				$rw = $this->input->get('rw');
				$kelurahan_id = $this->input->get('kelurahan_id');
				// var_dump($kelurahan_id);
				// exit();
				$tinggi_baris = $this->input->get('tinggi_baris');

				$tahun = $this->input->get('tahun'); // opsional, tambahkan di form kalau perlu
				$tahun = $tahun ?: date('Y'); // default: tahun sekarang

				$tgl_cetak = $this->input->get('tgl_cetak');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));

				$sql_kel = $this->db->query("SELECT * FROM cl_kelurahan_desa WHERE id = '$kelurahan_id'")->row();
				$data['nama_kelurahan'] = $sql_kel->nama;


				// $array_setting = array(
				// 	'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
				// 	'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
				// 	'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
				// 	// 'd.nip' => $nip,
				// 	'f.nip' => $nik_lsm,
				// 	'g.nip' => $nik_pembuat,
				// );
				if ($this->auth['cl_user_group_id'] == 2) {
					// Awal array setting (wajib wilayah)
					$array_setting = array(
						'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
						'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
						'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
						'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					);
				} else {
					$array_setting = array(
						'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
						'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
						'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					);
				}

				// if ($nip!='') {
				// 	$this->db->where('d.nip',$nip);
				// }
				// if ($nik_lsm!='') {
				// 	$this->db->where('e.nip',$nik_lsm);
				// }
				// if ($nik_pembuat!='') {
				// 	$this->db->where('f.nip',$nik_pembuat);
				// }

				// if (!empty($kelurahan_id)) {
				// 	$array_setting['a.cl_kelurahan_desa_id'] = $kelurahan_id;
				// }



				// mulai query
				$this->db->select('a.*,d.nip as nip_camat,d.nama as nama_camat,d.jabatan as jabatan_camat,d.pangkat as pangkat_camat,e.nama as nama_lsm,e.nip as nik_lsm,e.jabatan as jabatan_lsm,f.nip as nik_pembuat,f.nama as nama_pembuat,f.jabatan as jabatan_pembuat,g.nip as nip_lurah,g.nama as nama_lurah,g.jabatan as jabatan_lurah,g.pangkat as pangkat_lurah')
					->where($array_setting)
					->join('tbl_data_penandatanganan d', "a.cl_kecamatan_id=d.cl_kecamatan_id AND d.tingkat_jabatan='1.1' AND d.nip='$nip'", 'left')
					->join('tbl_data_penandatanganan e', "a.cl_kecamatan_id=e.cl_kecamatan_id and a.cl_kelurahan_desa_id=e.cl_kelurahan_desa_id AND e.tingkat_jabatan='3.1' AND e.nip='$nik_lsm'", 'left')
					->join('tbl_data_penandatanganan f', "a.cl_kecamatan_id=f.cl_kecamatan_id and a.cl_kelurahan_desa_id=f.cl_kelurahan_desa_id and f.tingkat_jabatan='2.4' AND f.nip='$nik_pembuat'", 'left')
					->join('tbl_data_penandatanganan g', "a.cl_kecamatan_id=g.cl_kecamatan_id and a.cl_kelurahan_desa_id=g.cl_kelurahan_desa_id and g.tingkat_jabatan='2.1' AND g.status='Aktif'", 'left');


				$this->setting = $this->db->get("tbl_setting_apps a")->row_array();

				$ttd = array(
					'camat' => $nip,
					'lsm' => $nik_lsm,
					'pembuat' => $nik_pembuat,
				);
				$bulan = getBulan($bulan);
				$this->nsmarty->assign("ttd", $ttd);
				$this->nsmarty->assign("bulan", $bulan);
				$this->nsmarty->assign("rw", $rw);
				// $this->nsmarty->assign("ttd", $ttd);
				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$this->nsmarty->assign("tinggi_baris", $tinggi_baris);
				$data['penilaian'] = $this->mbackend->getdata('laporan_rekap_penilaian_rt_rw', 'result_array');


				$filename = "laporan_rekap_penilaian_rt_rw-" . date('YmdHis');
				$temp = "backend/cetak/laporan_rekap_penilaian_rt_rw.html";

				$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
					'mode' => 'utf-8',
					'format' => 'LEGAL-L',
					'margin_left' => 5,
					'margin_right' => 5,
					'margin_top' => 5,
					'margin_bottom' => 5,
					'margin_header' => 0,
					'margin_footer' => 0,
					'default_font_size' => '12pt',
					'default_font' => '',
				]);

				break;
			// case "laporan_usulan_penilaian_rt_rw":

			// 	$bulan = $this->input->get('bulan'); // contoh: '05' dari Mei

			// 	$tahun = $this->input->get('tahun'); // opsional, tambahkan di form kalau perlu
			// 	$tahun = $tahun ?: date('Y'); // default: tahun sekarang

			// 	$tgl_cetak = $this->input->get('tanggal');
			// 	$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));

			// 	$array_setting = array(
			// 		'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
			// 		'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
			// 		'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

			// 	);

			// 	// mulai query
			// 	$this->db->select('a.*,a.nama_kecamatan,a.nama_desa,e.nip as nip_camat,e.nama as nama_camat,e.jabatan as jabatan_camat,e.pangkat as pangkat_camat,d.nip as nip_lurah,d.nama as nama_lurah,d.jabatan as jabatan_lurah,d.pangkat as pangkat_lurah,f.nama as nama_lsm,f.nip as nik_lsm,f.jabatan as jabatan_lsm,g.nip as nik_pembuat,g.nama as nama_pembuat,g.jabatan as jabatan_pembuat,b.alamat,b.tgl_mulai_jabat,b.jab_rt_rw as jabatan,c.bulan,c.tgl_surat,c.bulan')
			// 		->from('tbl_setting_apps a')
			// 		->join('tbl_data_rt_rw b', "a.cl_kecamatan_id=b.cl_kecamatan_id", 'left')
			// 		->join('tbl_penilaian_rt_rw c', "b.nik=c.nik", 'inner')
			// 		->join('tbl_data_penandatanganan d', "a.cl_kecamatan_id=d.cl_kecamatan_id and a.cl_kelurahan_desa_id=d.cl_kelurahan_desa_id", 'left')
			// 		->join('tbl_data_penandatanganan e', "a.cl_kecamatan_id=e.cl_kecamatan_id", 'left')
			// 		->join('tbl_data_penandatanganan f', "a.cl_kecamatan_id=f.cl_kecamatan_id and a.cl_kelurahan_desa_id=f.cl_kelurahan_desa_id", 'left')
			// 		->join('tbl_data_penandatanganan g', "a.cl_kecamatan_id=g.cl_kecamatan_id and a.cl_kelurahan_desa_id=g.cl_kelurahan_desa_id", 'left')
			// 		->where($array_setting);


			// 	$this->setting = $this->db->get()->row_array();
			// 	$bulan = getBulan($bulan);
			// 	$this->nsmarty->assign("bulan", $bulan);
			// 	$this->nsmarty->assign("tahun", $tahun);


			// 	$this->nsmarty->assign("setting", $this->setting);

			// 	$this->nsmarty->assign("tgl_cetak", $tgl_cetak);

			// 	$data['penilaian'] = $this->mbackend->getdata('laporan_rekap_penilaian_rt_rw', 'result_array');

			// 	$filename = "laporan_rekap_penilaian_rt_rw-" . date('YmdHis');
			// 	$temp = "backend/cetak/laporan_rekap_penilaian_rt_rw.html";

			// 	$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
			// 		'mode' => 'utf-8',
			// 		'format' => 'LEGAL-L',
			// 		'margin_left' => 5,
			// 		'margin_right' => 5,
			// 		'margin_top' => 5,
			// 		'margin_bottom' => 5,
			// 		'margin_header' => 0,
			// 		'margin_footer' => 0,
			// 		'default_font_size' => '12pt',
			// 		'default_font' => '',
			// 	]);

			// 	break;

			case "laporan_hasil_skm":
				$tahun = $this->input->get('tahun');
				$kelurahan_id = $this->input->get('kelurahan_id');
				$kecamatan_id = $this->input->get('kecamatan_id'); // dari JS

				if (empty($tahun) || !is_numeric($tahun)) {
					$tahun = date('Y');
				}
				if (!empty($kelurahan_id)) {
					$array_setting['a.cl_kelurahan_desa_id'] = $kelurahan_id;
				}
				if (!empty($kecamatan_id)) {
					$array_setting['a.cl_kecamatan_id'] = $kecamatan_id;
				}


				// Debug log ke log file atau browser
				log_message('debug', "SKM PARAM: tahun=$tahun, kec=$kecamatan_id, kel=$kelurahan_id");

				$data['laporan_hasil_skm'] =  $this->mbackend->getdata('laporan_hasil_skm', 'result_array', $tahun, $kecamatan_id, $kelurahan_id);

				$filename = "laporan-hasil-skm-" . date('YmdHis');
				$temp = "backend/cetak/laporan_hasil_skm.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-P");

				break;

			case "laporan_hasil_skm2":
				$tahun = $this->input->get('tahun');
				$kelurahan_id = $this->input->get('kelurahan_id');
				$kecamatan_id = $this->input->get('kecamatan_id'); // dari JS

				if (empty($tahun) || !is_numeric($tahun)) {
					$tahun = date('Y');
				}
				if (!empty($kelurahan_id)) {
					$array_setting['a.cl_kelurahan_desa_id'] = $kelurahan_id;
				}
				if (!empty($kecamatan_id)) {
					$array_setting['a.cl_kecamatan_id'] = $kecamatan_id;
				}


				// Debug log ke log file atau browser
				log_message('debug', "SKM PARAM: tahun=$tahun, kec=$kecamatan_id, kel=$kelurahan_id");

				$data['laporan_hasil_skm2'] =  $this->mbackend->getdata('laporan_hasil_skm2', 'result_array', $tahun, $kecamatan_id, $kelurahan_id);

				$filename = "laporan-hasil-skm2-" . date('YmdHis');
				$temp = "backend/cetak/laporan_hasil_skm2.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-P");

				break;

			case "laporan_staff":

				$nip = $this->input->get('nip');
				$keg_pegawai = $this->input->get('keg_pegawai');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));

				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id and b.tingkat_jabatan='2.1' AND b.status='Aktif'", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$this->nsmarty->assign("keg_pegawai", $keg_pegawai);

				$data = $this->mbackend->getdata('laporan_staff', 'result_array');

				$filename = "laporan-staff-" . date('YmdHis');

				$temp = "backend/cetak/laporan_staff.html";

				// $this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");
				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_bpjs":

				$nip = $this->input->get('nip');
				$keg_pegawai = $this->input->get('keg_pegawai');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));

				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$this->nsmarty->assign("keg_pegawai", $keg_pegawai);

				$data = $this->mbackend->getdata('laporan_bpjs', 'result_array');

				$filename = "laporan-bpjs-" . date('YmdHis');

				$temp = "backend/cetak/laporan_bpjs.html";

				// $this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");
				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_retribusi":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_retribusi', 'result_array');

				$filename = "laporan-retribusi-" . date('YmdHis');

				$temp = "backend/cetak/laporan_retribusi.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_keluarga":

				$rw = $this->input->get('rw');
				$rt = $this->input->get('rt');
				$desa_id = $this->input->get('kelurahan');

				$data = $this->mbackend->getdata('laporan_keluarga', 'result_array', [
					'rw' => $rw,
					'rt' => $rt,
					'kelurahan' => $desa_id
				]);

				$filename = "laporan-keluarga-" . date('YmdHis');

				$temp = "backend/cetak/laporan_keluarga.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_keluarga_excel":

				$rw = $this->input->get('rw');
				$rt = $this->input->get('rt');
				$desa_id = $this->input->get('kelurahan');

				$data = $this->mbackend->getdata('laporan_keluarga', 'result_array', [
					'rw' => $rw,
					'rt' => $rt,
					'kelurahan' => $desa_id
				]);

				$filename = "laporan-keluarga-" . date('YmdHis');

				$temp = "backend/cetak/laporan_keluarga.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, 'utf-8', array(215, 330));

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_kebersihan":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_kebersihan', 'result_array');

				$filename = "laporan-kebersihan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_kebersihan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_sekolah":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);

				$data = $this->mbackend->getdata('laporan_sekolah', 'result_array');

				$filename = "laporan-sekolah-" . date('YmdHis');

				$temp = "backend/cetak/laporan_sekolah.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_ibadah":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_ibadah', 'result_array');

				$filename = "laporan-ibadah-" . date('YmdHis');

				$temp = "backend/cetak/laporan_ibadah.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_pkl":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_pkl', 'result_array');

				$filename = "laporan-pkl-" . date('YmdHis');

				$temp = "backend/cetak/laporan_pkl.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_penandatanganan":

				$data = $this->mbackend->getdata('laporan_penandatanganan', 'result_array');

				$filename = "laporan-penandatanganan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_penandatanganan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_kendaraan":

				$data = $this->mbackend->getdata('laporan_kendaraan', 'result_array');

				$filename = "laporan-kendaraan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_kendaraan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_lorong":
				$nip = $this->input->get('nip');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip
				);

				$this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,b.jabatan')->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);
				$data = $this->mbackend->getdata('laporan_lorong', 'result_array');

				$filename = "laporan-lorong-" . date('YmdHis');

				$temp = "backend/cetak/laporan_lorong.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;


			case "laporan_rekap_bulan":
				$nip = $this->input->get('nip');
				$bulan = $this->input->get('bulan');
				$tgl_cetak = $this->input->get('tanggal');
				$tgl_cetak = $tgl_cetak ? (date('Y-m-d', strtotime($tgl_cetak))) : (date("Y-m-d"));
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip' => $nip,

				);

				// $this->setting = $this->db->select('a.*,b.nip,b.nama,b.pangkat,if(jabatan="Lurah","Lurah","a.n Lurah")jabatan')->where($array_setting)
				// 	->join('tbl_data_penandatanganan b', "a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
				// 	->get("tbl_setting_apps a")->row_array();
				$this->setting = $this->db->select('
									a.*, 
									b.nip,
									b.nama,
									b.pangkat,
									IF(b.jabatan = "Lurah", "Lurah", "a.n Lurah") AS jabatan_ttd,
									b.jabatan AS jabatan_asli
								')
					->where($array_setting)
					->join(
						'tbl_data_penandatanganan b',
						"a.cl_kecamatan_id=b.cl_kecamatan_id 
											AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id 
											AND b.status='Aktif'",
						'left'
					)
					->get("tbl_setting_apps a")
					->row_array();

				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_cetak", $tgl_cetak);

				// $data = $this->mbackend->getdata('laporan_rekap_bulan', 'result_array');
				// $data = $this->mbackend->getdata('laporan_rekap_bulan', 'result_array', [
				// 			'bulan' => $bulan,
				// 			'id' => $this->input->get('id')
				// 		]);

				$data = $this->mbackend->getdata('laporan_rekap_bulan', 'result_array', $p3);

				if (!empty($data)) {
					// ambil bulan dari data pertama (atau sesuaikan indeksnya)
					$bulan_angka = isset($data[0]['bulan']) ? (int)$data[0]['bulan'] : null;

					// daftar nama bulan
					$nama_bulan = [
						1 => 'Januari',
						2 => 'Februari',
						3 => 'Maret',
						4 => 'April',
						5 => 'Mei',
						6 => 'Juni',
						7 => 'Juli',
						8 => 'Agustus',
						9 => 'September',
						10 => 'Oktober',
						11 => 'November',
						12 => 'Desember'
					];

					// ambil nama bulan berdasarkan angka
					$data[0]['nama_bulan'] = isset($nama_bulan[$bulan_angka]) ? $nama_bulan[$bulan_angka] : '-';

					// tahun ambil dari tgl_cetak atau dari data
					$tahun = date('Y');
					$data[0]['periode_bulan_tahun'] = strtoupper($data[0]['nama_bulan'] . ' ' . $tahun);
				}

				$filename = "laporan-rekap-bulan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_bulan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				// print_r($data);exit;

				break;

			case "laporan_daftar_agenda":

				// === ambil parameter filter ===
				$tgl_mulai   = $this->input->get('tgl_mulai');
				$tgl_selesai = $this->input->get('tgl_selesai');
				$nip         = $this->input->get('nip');

				// fallback kalau kosong
				if ($tgl_mulai && $tgl_selesai) {
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));
				} else {
					// default hari ini
					$tgl_mulai = $tgl_selesai = date('Y-m-d');
				}

				// === setting wilayah & ttd ===
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip'             => $nip,
				);

				$this->setting = $this->db->select('
						a.*, 
						b.nip,
						b.nama,
						b.pangkat,
						IF(b.jabatan = "Lurah", "Lurah", "a.n Lurah") AS jabatan_ttd,
						b.jabatan AS jabatan_asli,
						c.nama AS nama_kecamatan,
						d.nama AS nama_kelurahan
					')
					->where($array_setting)
					->join(
						'tbl_data_penandatanganan b',
						"a.cl_kecamatan_id=b.cl_kecamatan_id 
						AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id 
						AND b.status='Aktif'",
						'left'
					)
					->join('cl_kecamatan c', 'c.id = a.cl_kecamatan_id', 'left')
					->join('cl_kelurahan_desa d', 'd.id = a.cl_kelurahan_desa_id', 'left')
					->get("tbl_setting_apps a")
					->row_array();

				// === helper format tanggal ===
				if (!function_exists('format_hari')) {
					function format_hari($tanggal)
					{
						if (!$tanggal) return '-';
						$hari  = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
						$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
						$ts = strtotime($tanggal);
						return $hari[date('w', $ts)] . ", " . date('d', $ts) . " " . $bulan[date('n', $ts) - 1] . " " . date('Y', $ts);
					}
				}

				if (!function_exists('format_jam')) {
					function format_jam($jam)
					{
						if (!$jam) return "-";
						return str_replace(":", ".", substr($jam, 0, 5)) . " - selesai";
					}
				}

				$this->nsmarty->registerPlugin('modifier', 'hariindo', 'format_hari');
				$this->nsmarty->registerPlugin('modifier', 'jamindo', 'format_jam');

				// === assign ke template ===
				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_mulai", $tgl_mulai);
				$this->nsmarty->assign("tgl_selesai", $tgl_selesai);

				$this->nsmarty->assign(
					"nama_kecamatan",
					!empty($this->setting['nama_kecamatan']) ? $this->setting['nama_kecamatan'] : "-"
				);
				$this->nsmarty->assign(
					"nama_kelurahan",
					!empty($this->setting['nama_kelurahan']) ? $this->setting['nama_kelurahan'] : "-"
				);

				// === ambil data agenda (FILTER TANGGAL) ===
				$data = $this->mbackend->getdata(
					'laporan_daftar_agenda',
					'result_array',
					array(
						'tgl_mulai'   => $tgl_mulai,
						'tgl_selesai' => $tgl_selesai
					)
				);

				$filename = "laporan-daftar-agenda-" . date('YmdHis');
				$temp     = "backend/cetak/laporan_daftar_agenda.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");
				break;

			case "laporan_hasil_agenda":

				$nip = $this->input->get('nip');
				$tgl_hasil_agenda = $this->input->get('tanggal');
				$tgl_hasil_agenda = $tgl_hasil_agenda
					? date('Y-m-d', strtotime($tgl_hasil_agenda))
					: date("Y-m-d");
				$tgl_mulai   = $this->input->get('tgl_mulai');
				$tgl_selesai = $this->input->get('tgl_selesai');

				if ($tgl_mulai && $tgl_selesai) {
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));
				}

				// Filter setting (pakai array() kalau php tua ingin aman, tapi [] ok untuk >=5.4)
				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'b.nip'             => $nip,
				);

				// Ambil setting + kecamatan + kelurahan
				$this->setting = $this->db->select('
						a.*, 
						b.nip,
						b.nama,
						b.pangkat,
						IF(b.jabatan = "Lurah", "Lurah", "a.n Lurah") AS jabatan_ttd,
						b.jabatan AS jabatan_asli,
						c.nama AS nama_kecamatan,
						d.nama AS nama_kelurahan
					')
					->where($array_setting)
					->join(
						'tbl_data_penandatanganan b',
						"a.cl_kecamatan_id=b.cl_kecamatan_id 
						AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id 
						AND b.status='Aktif'",
						'left'
					)
					->join('cl_kecamatan c', 'c.id = a.cl_kecamatan_id', 'left')
					->join('cl_kelurahan_desa d', 'd.id = a.cl_kelurahan_desa_id', 'left')
					->get("tbl_setting_apps a")
					->row_array();

				// Jangan redeclare fungsi jika sudah ada
				if (!function_exists('format_hari')) {
					function format_hari($tanggal)
					{
						if (!$tanggal) return '-';
						$hari  = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
						$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

						$ts = strtotime($tanggal);
						if ($ts === false) return '-';
						return $hari[date('w', $ts)] . ", " . date('d', $ts) . " " . $bulan[date('n', $ts) - 1] . " " . date('Y', $ts);
					}
				}

				// register modifier untuk smarty
				$this->nsmarty->registerPlugin('modifier', 'hariindo', 'format_hari');

				// Assign variable ke template
				$this->nsmarty->assign("setting", $this->setting);
				$this->nsmarty->assign("tgl_hasil_agenda", $tgl_hasil_agenda);

				// jangan pakai ??, gunakan isset
				$this->nsmarty->assign("nama_kecamatan", isset($this->setting['nama_kecamatan']) && $this->setting['nama_kecamatan'] != '' ? $this->setting['nama_kecamatan'] : "-");
				$this->nsmarty->assign("nama_kelurahan", isset($this->setting['nama_kelurahan']) && $this->setting['nama_kelurahan'] != '' ? $this->setting['nama_kelurahan'] : "-");

				// Ambil data tabel agenda
				$data = $this->mbackend->getdata(
					'laporan_hasil_agenda',
					'result_array',
					array(
						'tgl_mulai'   => $tgl_mulai,
						'tgl_selesai' => $tgl_selesai
					)
				);

				$filename = "laporan-hasil-agenda-" . date('YmdHis');
				$temp = "backend/cetak/laporan_hasil_agenda.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");
				break;
			case "laporan_ekspedisi":

				$data = $this->mbackend->getdata('laporan_ekspedisi', 'result_array');

				$filename = "laporan-ekspedisi-" . date('YmdHis');

				$temp = "backend/cetak/laporan_ekspedisi.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_rekap_imb":

				$data = $this->mbackend->getdata('laporan_rekap_imb', 'result_array');

				$filename = "laporan-rekap-imb-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_imb.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "laporan_persuratan":

				$data = $this->mbackend->getdata('laporan_persuratan', 'result_array');

				$filename = "laporan-persuratan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_persuratan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				break;

			case "laporan_persuratan_excel":

				$data = $this->mbackend->getdata('laporan_persuratan', 'result_array');

				$filename = "laporan-persuratan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_persuratan.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, 'utf-8', array(215, 330));

				break;

			case "laporan_rekap_pengantar_kendaraan":

				$res = $this->mbackend->getdata('laporan_rekap_pengantar_kendaraan', 'result_array');
				$data = [];
				foreach ($res as $row) {
					foreach (json_decode($row['info_tambahan']) as $key => $value) {
						$row[$key] = $value;
					}
					unset($row['data_surat']);
					$data[] = $row;
				}
				$filename = "laporan-rekap-pengantar-kendaraan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_pengantar_kendaraan.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				break;

			case "laporan_rekap_pengantar_kendaraan_excel":

				$res = $this->mbackend->getdata('laporan_rekap_pengantar_kendaraan', 'result_array');
				$data = [];
				foreach ($res as $row) {
					foreach (json_decode($row['info_tambahan']) as $key => $value) {
						$row[$key] = $value;
					}
					unset($row['data_surat']);
					$data[] = $row;
				}
				$filename = "laporan-rekap-pengantar-kendaraan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_pengantar_kendaraan.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, 'utf-8', array(215, 330));

				break;

			case "laporan_rekap_usaha":

				$res = $this->mbackend->getdata('laporan_rekap_usaha', 'result_array');
				$data = [];
				foreach ($res as $row) {
					foreach (json_decode($row['info_tambahan']) as $key => $value) {
						$row[$key] = $value;
					}
					unset($row['data_surat']);
					$data[] = $row;
				}
				$filename = "laporan-rakap-usaha-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_usaha.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				break;

			case "laporan_rekap_usaha_excel":

				$data = $this->mbackend->getdata('laporan_rekap_usaha', 'result_array');

				$filename = "laporan-rekap-usaha-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rekap_usaha.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, 'utf-8', array(215, 330));

				break;

			case "laporan_rt_rw_excel":

				$data = $this->mbackend->getdata('laporan_rt_rw_excel', 'result_array');

				// ⛑️ AMAN PHP 5.x
				if (!isset($data['data_surat'])) {
					$data['data_surat'] = array();
				}

				$filename = "laporan-rt-rw-excel-" . date('YmdHis');

				$temp = "backend/cetak/laporan_rt_rw_excel.html";

				$this->hasil_output(
					'excel',
					$mod,
					$data,
					$filename,
					$temp,
					'utf-8',
					array(215, 330)
				);

				break;

			case "laporan_persuratan_rt_rw":

				$data = $this->mbackend->getdata('laporan_persuratan_rt_rw', 'result_array');

				$filename = "laporan-persuratan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_persuratan_rt_rw.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				break;

			case "laporan_persuratan_masuk":

				$data = $this->mbackend->getdata('laporan_persuratan_masuk', 'result_array');

				$filename = "laporan-persuratan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_persuratan_masuk.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				break;
			case "laporan_persuratan_masuk_excel":

				$data = $this->mbackend->getdata('laporan_persuratan_masuk', 'result_array');

				$filename = "laporan-persuratan-" . date('YmdHis');

				$temp = "backend/cetak/laporan_persuratan_masuk.html";

				$this->hasil_output('excel', $mod, $data, $filename, $temp, 'utf-8', array(215, 330));

				break;

			case "cetak_surat":

				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'c.id'              => $p3
				);

				$this->setting = $this->db->select('a.*')
					->where($array_setting)

					->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id", 'left')
					->join('tbl_data_surat c', 'a.cl_kelurahan_desa_id=c.cl_kelurahan_desa_id and c.id_penandatanganan', 'inner')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);

				$this->db->select('kode_unik');
				$this->db->where('id', $p3);
				$query = $this->db->get('tbl_data_surat');
				if ($query->row('kode_unik') == '') {
					$kode_unik = generate_unique_string(8);
					$this->db->update('tbl_data_surat', array('kode_unik' => $kode_unik), array('id' => $p3));
				}

				$data = $this->mbackend->getdata('cetak_surat', 'variable', $p1, $p2, $p3);

				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $p1))->row_array();

				$filename = str_replace(" ", "_", $jenis_surat['jenis_surat']) . "_" . $p2;

				$temp = "backend/cetak/surat_" . $p1 . ".html";
				if ($p1 == '143') {
					$this->load->helper('inflector');
					$uniq = generate_unique_string(8);
					$uniq_path = sys_get_temp_dir() . '/' . $uniq;
					if (!file_exists($uniq_path)) {
						mkdir($uniq_path, 0755, true);
					}
					foreach ($data['surat']['info_tambahan']['pemohon'] as $row) {
						$data_temp = $data;
						$row['nama_status_sktm'] = $this->db->where('id', $row['status_sktm'])->get('cl_status_kawin')->row('nama_status_kawin');
						$row['nama_agama_sktm'] = $this->db->where('id', $row['agama_sktm'])->get('cl_agama')->row('nama_agama');
						$row['nama_pekerjaan_sktm'] = $this->db->where('id', $row['pekerjaan_sktm'])->get('cl_jenis_pekerjaan')->row('nama_pekerjaan');
						$data_temp['surat']['info_tambahan']['pemohon'] = $row;
						$this->nsmarty->assign('mod', $mod);
						$this->nsmarty->assign('data', $data_temp);
						$htmlcontent = $this->nsmarty->fetch($temp);
						$dataSurat = $this->nsmarty->getTemplateVars('data');
						$filename = underscore($jenis_surat['jenis_surat']) . '_' . underscore($row['nama_sktm']) . '_' . time();
						$this->hasil_output3($htmlcontent, $filename, $uniq_path, $dataSurat);
					}
					$zip_filename = $uniq . "_" . time() . ".zip";
					$outputZip = sys_get_temp_dir() . '/' . $zip_filename;

					if ($this->zipFolder($uniq_path, $outputZip)) {
						if (ob_get_length()) ob_end_clean(); // bersihkan buffer

						header('Content-Type: application/zip');
						header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
						header('Content-Length: ' . filesize($outputZip));
						header('Content-Transfer-Encoding: binary');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');

						flush();
						readfile($outputZip);
						flush();

						unlink($outputZip);
						rmdir($uniq_path);
						exit;
					} else {
						echo "Gagal membuat $zip_filename";
					}
				} else if (in_array($p1, ['66', '69', '125'])) {
					$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
						'mode' => 'utf-8',
						'format' => 'LEGAL-L',
						'margin_left' => 5,
						'margin_right' => 5,
						'margin_top' => 5,
						'margin_bottom' => 5,
						'margin_header' => 0,
						'margin_footer' => 0,
						'default_font_size' => '12pt',
						'default_font' => '',
					]);
				} elseif (in_array($p1, ['70'])) {
					$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
						'mode' => 'utf-8',
						'format' => 'A5',
						'margin_left' => 5,
						'margin_right' => 5,
						'margin_top' => 5,
						'margin_bottom' => 5,
						'margin_header' => 0,
						'margin_footer' => 0,
						'default_font_size' => '12pt',
						'default_font' => '',
					]);
				} elseif (in_array($p1, ['124'])) {
					$this->hasil_output2('pdf', $mod, $data, $filename, $temp, [
						'mode' => 'utf-8',
						'format' => 'A4-L',
						'margin_left' => 5,
						'margin_right' => 5,
						'margin_top' => 5,
						'margin_bottom' => 5,
						'margin_header' => 0,
						'margin_footer' => 0,
						'default_font_size' => '12pt',
						'default_font' => '',
					]);
				} else {
					$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));
				}

				break;

			// case "daftar_esign":

			// 	/* ================== SETTING & DATA ================== */
			// 	$array_setting = array(
			// 		'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
			// 		'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
			// 		'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
			// 		'c.id'              => $p3
			// 	);

			// 	$this->setting = $this->db->where($array_setting)
			// 		->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
			// 		->join('tbl_data_surat c', 'a.cl_kelurahan_desa_id=c.cl_kelurahan_desa_id', 'inner')
			// 		->get("tbl_setting_apps a")->row_array();

			// 	$this->nsmarty->assign("setting", $this->setting);
			// 	$data = $this->mbackend->getdata('cetak_surat', 'variable', $p1, $p2, $p3);
			// 	$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $p1))->row_array();


			// 	/* ================== FILE PDF ================== */
			// 	$dir  = date('Ymd');
			// 	if (!is_dir('./__data/' . $dir)) {
			// 		mkdir('./__data/' . $dir, 0755);
			// 	}

			// 	$filename = "__data/" . $dir . "/" . str_replace(" ", "_", $jenis_surat['jenis_surat']) . "_" . $p2 . "_" . date('YmdHis') . '.pdf';

			// 	$temp = "backend/cetak/surat_" . $p1 . ".html";
			// 	$status_esign = $this->input->post('status_esign');
			// 	if ($status_esign != 2) {
			// 		$filename = $data['surat']['file_src_esign'];
			// 	} else {
			// 		$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330), 'F', true, '');
			// 	}

			// 	if (($this->input->post('nip_pemeriksa_esign') == '' || $this->input->post('nip_pemeriksa_esign') == null) && $status_esign == 2) {
			// 		$status_esign = 3;
			// 	}


			// 	/* ================== APPROVAL ESIGN ================== */
			// 	$file_approved_esign = '';
			// 	$data_register['stamp_esign'] = '';

			// 	if ($status_esign == 1) {
			// 		if ($this->input->post('nik_esign') == '' || $this->input->post('passphrase_esign') == '') {
			// 			echo json_encode([
			// 				'stat' => false,
			// 				'msg' => 'NIK atau password salah',
			// 			]);
			// 			return;
			// 		}

			// 		$id = $this->db->select("IFNULL(MAX(id), 0)+1 as id")->get('tbl_register_esign')->row('id');
			// 		$subregister = $this->db->select("IFNULL(MAX(CAST(RIGHT(nomor_register, 3) AS UNSIGNED)), 0)+1 as subregister")->where("LEFT(nomor_register,6)", date('Ym'))->get('tbl_register_esign')->row('subregister');
			// 		$nomor_register = date('Ym') . sprintf("%03d", $subregister);
			// 		$stamp_esign = $this->generate_esign($nomor_register, $this->auth['cl_kelurahan_desa_id']);
			// 		$filename_temp = "__data/" . $dir . "/" . str_replace(" ", "_", $jenis_surat['jenis_surat']) . "_" . $p2 . "_" . date('YmdHis') . '.pdf';
			// 		$this->hasil_output('pdf', $mod, $data, $filename_temp, $temp, array(215, 330), 'F', false, 'ESIGN');
			// 		$file_approved_esign = $this->generate_pdf_esign($stamp_esign, $filename_temp, $this->input->post('nik_esign'), $this->input->post('passphrase_esign'));
			// 		unlink($filename_temp);

			// 		if ($stamp_esign == false) {
			// 			echo json_encode([
			// 				'stat' => false,
			// 				'msg' => 'Terjadi kesalahan pada QR, coba lagi!',
			// 			]);
			// 			return;
			// 		}

			// 		if ($file_approved_esign == false) {
			// 			echo json_encode([
			// 				'stat' => false,
			// 				'msg' => 'Terjadi kesalahan approval, coba lagi!',
			// 			]);
			// 			return;
			// 		}

			// 		$data_register = [
			// 			'id' => $id,
			// 			'nomor_register' => $nomor_register,
			// 			'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
			// 			'tbl_data_surat_id' => $data['surat']['id'],
			// 			'jenis_surat' => $jenis_surat['jenis_surat'],
			// 			'nama_lembaga' => $this->db->where('id', $this->auth['cl_kelurahan_desa_id'])->get('cl_kelurahan_desa')->row('nama'),
			// 			'nip_penandatanganan' => $data['surat']['nip'],
			// 			'nama_penandatanganan' => $this->db->where('nip', $data['surat']['nip'])->get('tbl_data_penandatanganan')->row('nama'),
			// 			'tanggal_register' => date('Y-m-d H:i:s'),
			// 			'stamp_esign' => $stamp_esign,
			// 			'file_lampiran' => $file_approved_esign,
			// 			'created_by' => $this->auth['nama_lengkap'],
			// 		];
			// 		$this->db->insert('tbl_register_esign', $data_register);
			// 	}

			// 	$data_riwayat = [
			// 		'tbl_data_surat_id' => $data['surat']['id'],
			// 		'status_esign' => $status_esign,
			// 		'file_src' => ($status_esign == 1 ? $file_approved_esign : $filename),
			// 		'cl_user_group_id' => $this->auth['cl_user_group_id'],
			// 		'tbl_user_id' => $this->auth['id'],
			// 		'nip_pegawai' => ($this->auth['nip_pegawai'] == null ? '' : $this->auth['nip_pegawai']),
			// 		'nama_pegawai' => ($this->auth['nama_pegawai'] == null ? $this->auth['nama_lengkap'] : $this->auth['nama_pegawai']),
			// 		'catatan' => htmlspecialchars($this->input->post('catatan_esign'), ENT_QUOTES),
			// 		'created_by' => $this->auth['nama_lengkap'],
			// 	];

			// 	$this->db->insert('tbl_riwayat_esign', $data_riwayat);

			// 	$data_surat = [
			// 		'status_esign' => $status_esign,
			// 		'file_src_esign' => $filename,
			// 		'stamp_esign' => $data_register['stamp_esign'],
			// 		'file_approved_esign' => $file_approved_esign,
			// 	];
			// 	if ($this->input->post('nip_pemeriksa_esign') != '' && $this->input->post('nip_pemeriksa_esign') != null) {
			// 		$data_surat['nip_pemeriksa_esign'] = $this->input->post('nip_pemeriksa_esign');
			// 		$data_surat['nama_pemeriksa_esign'] = $this->db->where('nip_pegawai', $this->input->post('nip_pemeriksa_esign'))->where('cl_user_group_id', 5)->get('tbl_user')->row('nama_pegawai');
			// 	}
			// 	$sql = $this->db->where('id', $data['surat']['id'])->update('tbl_data_surat', $data_surat);
			// 	if ($sql) {
			// 		echo json_encode([
			// 			'stat' => true,
			// 			'msg' => 'Data tersimpan',
			// 		]);
			// 	} else {
			// 		echo json_encode([
			// 			'stat' => false,
			// 			'msg' => $this->db->error(),
			// 		]);
			// 	}


			// break;
			case "daftar_esign":

                header('Content-Type: application/json');
            
                /* ================== SETTING & DATA ================== */
                $array_setting = [
                    'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
                    'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
                    'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
                    'c.id'              => $p3
                ];
            
                $this->setting = $this->db->where($array_setting)
                    ->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip 
                        AND a.cl_kecamatan_id=b.cl_kecamatan_id 
                        AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id", 'left')
                    ->join('tbl_data_surat c', 'a.cl_kelurahan_desa_id=c.cl_kelurahan_desa_id', 'inner')
                    ->get("tbl_setting_apps a")
                    ->row_array();
            
                $this->nsmarty->assign("setting", $this->setting);
            
                $data = $this->mbackend->getdata('cetak_surat', 'variable', $p1, $p2, $p3);
                $jenis_surat = $this->db->get_where('cl_jenis_surat', ['id' => $p1])->row_array();
            
                /* ================== VALIDASI ================== */
                if ((int)$jenis_surat['identitas_surat'] !== 1) {
                    echo json_encode(['stat' => false, 'msg' => 'Surat tidak memenuhi syarat E-Sign']);
                    return;
                }
            
                /* ================== FILE PDF ================== */
                $dir = date('Ymd');
                if (!is_dir(FCPATH . "__data/$dir")) {
                    mkdir(FCPATH . "__data/$dir", 0755, true);
                }
            
                $nama_surat_safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $jenis_surat['jenis_surat']);
                $filename = "__data/$dir/{$nama_surat_safe}_{$p2}_" . date('YmdHis') . ".pdf";
                $temp = "backend/cetak/surat_" . $p1 . ".html";
            
                $status_esign = (int)$this->input->post('status_esign');
            
                if ($status_esign != 2 && !empty($data['surat']['file_src_esign'])) {
                    $filename = $data['surat']['file_src_esign'];
                } else {
                    $this->hasil_output('pdf', $mod, $data, $filename, $temp, [215,330], 'F', true, '');
                }
            
                if (empty($this->input->post('nip_pemeriksa_esign')) && $status_esign == 2) {
                    $status_esign = 3;
                }
            
                /* ================== APPROVAL ESIGN ================== */
                $file_approved_esign = '';
                $stamp_esign = '';
            
                if ($status_esign == 1) {
            
                    if (!$this->input->post('nik_esign') || !$this->input->post('passphrase_esign')) {
                        echo json_encode(['stat' => false, 'msg' => 'NIK / Passphrase wajib diisi']);
                        return;
                    }
            
                    $subregister = $this->db
                        ->select("IFNULL(MAX(CAST(RIGHT(nomor_register,3) AS UNSIGNED)),0)+1 AS sub")
                        ->where("LEFT(nomor_register,6)", date('Ym'))
                        ->get('tbl_register_esign')
                        ->row('sub');
            
                    $nomor_register = date('Ym') . sprintf('%03d', $subregister);
                    $stamp_esign = $this->generate_esign($nomor_register, $this->auth['cl_kelurahan_desa_id']);
            
                    $tmp = "__data/$dir/tmp_" . time() . ".pdf";
                    $this->hasil_output('pdf', $mod, $data, $tmp, $temp, [215,330], 'F', false, 'ESIGN');
            
                    $file_approved_esign = $this->generate_pdf_esign(
                        $stamp_esign,
                        $tmp,
                        $this->input->post('nik_esign'),
                        $this->input->post('passphrase_esign')
                    );
            
                    @unlink($tmp);
            
                    if (!$file_approved_esign) {
                        echo json_encode(['stat' => false, 'msg' => 'Proses E-Sign gagal']);
                        return;
                    }
            
                    $this->db->insert('tbl_register_esign', [
                        'nomor_register'       => $nomor_register,
                        'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
                        'tbl_data_surat_id'    => $data['surat']['id'],
                        'jenis_surat'          => $jenis_surat['jenis_surat'],
                        'tanggal_register'    => date('Y-m-d H:i:s'),
                        'stamp_esign'          => $stamp_esign,
                        'file_lampiran'        => $file_approved_esign,
                        'created_by'           => $this->auth['nama_lengkap']
                    ]);
                }
            
                /* ================== RIWAYAT ================== */
                $this->db->where('tbl_data_surat_id', $data['surat']['id'])->delete('tbl_riwayat_esign');
            
                $this->db->insert('tbl_riwayat_esign', [
                    'tbl_data_surat_id' => $data['surat']['id'],
                    'status_esign'      => $status_esign,
                    'file_src'          => ($status_esign == 1 ? $file_approved_esign : $filename),
                    'cl_user_group_id'  => $this->auth['cl_user_group_id'],
                    'tbl_user_id'       => $this->auth['id'],
                    'nama_pegawai'      => $this->auth['nama_lengkap'],
                    'catatan'           => htmlspecialchars($this->input->post('catatan_esign'), ENT_QUOTES),
                    'created_by'        => $this->auth['nama_lengkap']
                ]);
            
                /* ================== UPDATE SURAT ================== */
                $file_final = ($status_esign == 1) ? $file_approved_esign : $filename;
            
                $this->db->where('id', $data['surat']['id'])->update('tbl_data_surat', [
                    'status_esign'        => $status_esign,
                    'file_src_esign'      => $filename,
                    'stamp_esign'         => $stamp_esign,
                    'file_approved_esign' => $file_approved_esign
                ]);
            
                /* ================== COPY KE MOBILE ================== */
                $file_asal = FCPATH . $file_final;
                if (file_exists($file_asal)) {
            
                    $base_mobile = dirname(FCPATH) . '/mobile/uploads/ttd';
                    $target_dir = $base_mobile
                        . '/_' . $this->auth['cl_kecamatan_id']
                        . '/_' . $this->auth['cl_kelurahan_desa_id']
                        . '/_' . date('Ymd');
            
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
            
                    copy($file_asal, $target_dir . '/' . basename($file_asal));
            
                    $path_mobile = 'uploads/ttd'
                        . '/_' . $this->auth['cl_kecamatan_id']
                        . '/_' . $this->auth['cl_kelurahan_desa_id']
                        . '/_' . date('Ymd')
                        . '/' . basename($file_asal);
            
                    if ($this->db->field_exists('file_src_mobile', 'tbl_data_surat')) {
                        $this->db->where('id', $data['surat']['id'])
                            ->update('tbl_data_surat', ['file_src_mobile' => $path_mobile]);
                    }
            
                    if ($this->db->field_exists('file_src_mobile', 'tbl_riwayat_esign')) {
                        $this->db->where('tbl_data_surat_id', $data['surat']['id'])
                            ->order_by('id', 'DESC')->limit(1)
                            ->update('tbl_riwayat_esign', ['file_src_mobile' => $path_mobile]);
                    }
                }
            
                echo json_encode([
                    'stat' => true,
                    'msg'  => 'E-Sign berhasil, file desktop & mobile sinkron'
                ]);
                return;
            
            break;
			case "cetak_himbauan":

				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'c.id'              => $p2
				);

				$this->setting = $this->db->where($array_setting)
					->join('tbl_data_penandatanganan b', "a.nip_kepala_desa=b.nip and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')
					->join('tbl_data_surat_himbauan c', 'a.cl_kelurahan_desa_id=c.cl_kelurahan_desa_id', 'inner')
					->get("tbl_setting_apps a")->row_array();

				$this->nsmarty->assign("setting", $this->setting);

				$data = $this->mbackend->getdata('cetak_himbauan', 'variable', $p1);

				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id' => $p1))->row_array();

				$filename = str_replace(" ", "_", $jenis_surat['jenis_surat']) . "_" . $p2;

				$temp = "backend/cetak/surat_himbauan.html";


				$this->hasil_output('pdf', $mod, $data, $filename, $temp, array(215, 330));

				// echo $temp;

				//echo "<pre>";



				break;

			case "qrcode":

				$this->load->library("encrypt");

				$p1 = $this->lib->base64url_decode($p1);

				if ($p1) {

					$data = $this->mbackend->getdata('tbl_metering', 'row_array', $p1);

					$filename = $data["no_serial"];

					$this->hasil_output('pdf', $mod, $data, $filename, "A7-L");
				} else {

					echo "Invalid ID : Tutup Tab ini pada Browser Dan Generate Kembali";
				}

				break;
			case 'laporan_konsolidasi_penilaian_rt_rw':

				$bulan = $this->input->get('bulan');
				$nip = $this->input->get('nip');
				$nik_lsm = $this->input->get('nik_lsm');
				$nik_pembuat = $this->input->get('nik_pembuat');
				// $bulan = $this->input->get('bulan'); // contoh: '05' dari Mei
				$kelurahan_id = $this->input->get('kelurahan_id');
				$xrw = $this->input->get('rw');


				$tahun = $this->input->get('tahun'); // opsional, tambahkan di form kalau perlu
				$tahun = $tahun ?: date('Y'); // default: tahun sekarang

				$tgl_cetak = $this->input->get('tgl_cetak');
				if ($tgl_cetak == '') {
					$tgl_cetak = date("Y-m-d");
				} else {
					$tgl_cetak = date('Y-m-d', strtotime($tgl_cetak));
				}

				$array_setting = array(
					'a.cl_provinsi_id'  => $this->auth['cl_provinsi_id'],
					'a.cl_kab_kota_id'  => $this->auth['cl_kab_kota_id'],
					'a.cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
				);

				if ($nip != '') {
					$this->db->where('e.nip', $nip);
				}

				if ($xrw != '') {
					$this->db->where('b.rw', $xrw);
				}


				// Jika nik_lsm diisi, tambahkan ke where
				/* if (!empty($bulan)) {
					$array_setting['c.bulan'] = $bulan;
				} */
				if (!empty($nik_lsm)) {
					$array_setting['f.nip'] = $nik_lsm;
				}

				// Jika nik_pembuat diisi, tambahkan ke where
				if (!empty($nik_pembuat)) {
					$array_setting['g.nip'] = $nik_pembuat;
				}

				if (!empty($kelurahan_id)) {
					$array_setting['a.cl_kelurahan_desa_id'] = $kelurahan_id;
				}

				// mulai query
				$this->db->select('a.cl_kelurahan_desa_id,a.nama_kecamatan,a.nama_desa,e.nip as nip_camat,e.nama as nama_camat,e.jabatan as jabatan_camat,e.pangkat as pangkat_camat,d.nip as nip_lurah,d.nama as nama_lurah,d.jabatan as jabatan_lurah,d.pangkat as pangkat_lurah,f.nama as nama_lsm,f.nip as nik_lsm,f.jabatan as jabatan_lsm,g.nip as nik_pembuat,g.nama as nama_pembuat,g.jabatan as jabatan_pembuat,b.alamat,b.tgl_mulai_jabat,b.jab_rt_rw as jabatan,c.tgl_surat,c.bulan')
					->where($array_setting)
					->join('tbl_data_rt_rw b', "a.cl_kecamatan_id=b.cl_kecamatan_id", 'left')
					->join('tbl_penilaian_rt_rw c', "b.id=c.tbl_data_rt_rw_id", 'inner')
					->join('tbl_data_penandatanganan d', "a.cl_kecamatan_id=d.cl_kecamatan_id and a.cl_kelurahan_desa_id=d.cl_kelurahan_desa_id and d.tingkat_jabatan='2.1' and d.status='Aktif'", 'left')
					->join('tbl_data_penandatanganan e', "a.cl_kecamatan_id=e.cl_kecamatan_id AND e.tingkat_jabatan='1.1'", 'left')
					->join('tbl_data_penandatanganan f', "a.cl_kecamatan_id=f.cl_kecamatan_id and a.cl_kelurahan_desa_id=f.cl_kelurahan_desa_id AND f.tingkat_jabatan='3.1'", 'left')
					->join('tbl_data_penandatanganan g', "a.cl_kecamatan_id=g.cl_kecamatan_id and a.cl_kelurahan_desa_id=g.cl_kelurahan_desa_id", 'left');

				$setting = $this->db->group_by("a.cl_kelurahan_desa_id,a.nama_kecamatan,a.nama_desa,e.nip,e.nama,e.jabatan,e.pangkat,d.nip,d.nama,d.jabatan,d.pangkat,f.nama,f.nip,f.jabatan,g.nip,g.nama,g.jabatan,b.alamat,b.tgl_mulai_jabat,b.jab_rt_rw,c.tgl_surat,c.bulan")
					->get("tbl_setting_apps a")->row_array();
				$data['setting'] = $setting;
				$data['tgl_cetak'] = $tgl_cetak;
				$data['bulan'] = $bulan;

				$ttd = array(
					'lurah' => $nip,
					'lsm' => $nik_lsm,
					'pembuat' => $nik_pembuat,
				);
				if ($kelurahan_id != '') {
					$kelurahanxid = $kelurahan_id;
				} else {
					$kelurahanxid = $this->auth['cl_kelurahan_desa_id'];
				}
				$vrw = $this->db->get_where('tbl_data_rt_rw', array('id' => $xrw))->row('rw');

				$ctot = "SELECT COUNT(nik)tot FROM (
									SELECT (b.id)xid,b.nik
									FROM tbl_penilaian_rt_rw a
											INNER JOIN tbl_data_rt_rw b ON a.tbl_data_rt_rw_id=b.id
											,(SELECT @nor:=0)nr
									WHERE a.cl_kelurahan_desa_id='$kelurahanxid' AND b.status='Aktif' AND b.rw='$xrw' AND a.bulan='$bulan'
									GROUP BY b.nik,b.rw,b.rt,b.id
								)a;";
				$xdtot = $this->db->query($ctot)->row();

				$limit = 5; // jumlah data per batch
				$offset = 0;
				$total_data = $xdtot->tot;

				$cRet = '';
				$filename = "laporan_konsolidasi_penilaian_rt_rw-" . date('YmdHis');
				while ($offset < $total_data) {
					$query = "CALL sp_lap_penilaian_rt_rw('$xrw','$bulan',$kelurahanxid,$offset,$limit)";
					$detail = $this->db->query($query);


					$data['header'] = $this->db->get('temp_rtrw')->result_array();
					$data['detail'] = $this->db->get('temp_detail')->result_array();
					if ($offset + $limit >= $total_data) {
						$data['break'] = 0;
					} else {
						$data['break'] = 1;
					}
					$this->nsmarty->assign('data', $data);

					$temp = "backend/cetak/laporan_konsolidasi_penilaian_rt_rw.html";
					$htmlcontent = $this->nsmarty->fetch($temp);
					$htmlcontent = preg_replace('/<pagebreak><\/pagebreak>(?=[^<]*<\/pagebreak>)/', '', $htmlcontent, 1);
					$cRet .= $htmlcontent;


					$offset += $limit;
				}
				if (isset($_GET['view'])) {
					echo $cRet;
				} else {
					$mpdf = new \Mpdf\Mpdf([
						'mode' => 'utf-8',
						'format' => 'LEGAL-P',
						'margin_left' => 5,
						'margin_right' => 5,
						'margin_top' => 5,
						'margin_bottom' => 5,
						'margin_header' => 0,
						'margin_footer' => 0,
						'default_font_size' => '6pt',
						'default_font' => '',
					]);
					$cFoot = "
						<table style=\"font-size:8;\">
							<tr>
								<td style=\"text-align: right; font-size: 8px;\">
										<b>.::PRINTED BY SIMLURAH::.</b>
								</td>
								</tr>
								<tr>
									<td colspan=\"4\">&nbsp;</td>
								</tr>
						</table>";
					$mpdf->SetHTMLFooter($cFoot);
					$mpdf->WriteHTML($cRet);
					$mpdf->Output($filename . '.pdf', 'I');
				}


				break;
		}
	}

	function hasil_output($p1, $mod, $data, $filename, $temp, $ukuran = "LEGAL", $output = 'I', $draft = false, $jenis_ttd = '')
	{
		switch ($p1) {

			case "pdf":
				// var_dump('exit');
				// exit();
				$this->load->library('mlpdf');

				$pdf = $this->mlpdf->load();

				if ($jenis_ttd != '') {
					$data['jenis_ttd'] = $jenis_ttd;
				}
				$this->nsmarty->assign('data', $data);

				$this->nsmarty->assign('mod', $mod);

				$dataSurat = $this->nsmarty->getTemplateVars('data');


				if ($data['data_surat'] <> '') {
					$htmlcontent = $data['data_surat'];
				} else {
					$htmlcontent = $this->nsmarty->fetch($temp);
				}


				if (isset($_GET['view'])) {
					// header("Cache-Control: no-cache, no-store, must-revalidate");
					// header("Content-Type: application/vnd.ms-word");
					// header("Content-Disposition: attachment; filename= preview.doc");
					echo $htmlcontent;
					exit;
				}


				$spdf = new mPDF('', $ukuran, 0, '', 10, 10, 10, 15, 0, 0, 'P');

				$spdf->showImageErrors = false;

				$this->showImageErrors = false;

				$spdf->ignore_invalid_utf8 = true;

				ob_clean();
				ini_set('memory_limit', -1);
				ini_set('max_execution_time', -1);

				// bukan sulap bukan sihir sim salabim jadi apa prok prok prok

				$spdf->allow_charset_conversion = true;     // which is already true by default

				$spdf->charset_in = 'iso-8859-1';  // set content encoding to iso

				$spdf->SetDisplayMode('fullpage');

				//$spdf->SetHTMLHeader($htmlheader);

				//$spdf->keep_table_proportions = true;

				$spdf->useSubstitutions = false;

				$spdf->simpleTables = true;

				$arraynya = array(

					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],


				);


				$setting = $this->db->get_where('tbl_setting_apps', $arraynya)->row_array();
				if ($setting['qr_status'] == 1 && $setting['qr_kelurahan'] != '' && $dataSurat['surat']['info_tambahan']['ttd_srikandi'] != 'on') {
					$kode_unik = $dataSurat['surat']['kode_unik'];

					$setting = "" . $this->auth['cl_kelurahan_desa_id'] . "";

					$randomized_string = randomize_letters($setting);
					// URL untuk QR code
					$url_verifikasi = base_url("cek-dokumen/") . $randomized_string . "?kode=" . urlencode($kode_unik);

					// 🧩 Path temporary file (bisa disesuaikan)
					$temp_qr = tempnam(sys_get_temp_dir(), 'qr_') . '.png';

					// Include library QR Code
					require_once APPPATH . 'third_party/phpqrcode/qrlib.php';

					// Buat QR Code ke file sementara
					QRcode::png($url_verifikasi, $temp_qr, QR_ECLEVEL_H, 4, 1);

					// Ubah QR jadi base64 supaya bisa disisipkan langsung di HTML/PDF
					$qr_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($temp_qr));

					// 🧾 Footer PDF dengan QR
					$cFoot = "
							<table style=\"font-size:8;\">
								<tr>
									<td>
										<img src=\"" . $qr_base64 . "\" width=\"55px\">
									</td>
									<td>
										Catatan :<br>
										• Sesuai Undang-Undang Nomor 24 Tahun 2013 tentang Administrasi Kependudukan, pemalsuan dokumen kependudukan merupakan tindak pidana.<br>
										• Keaslian dokumen ini dapat diverifikasi melalui pemindaian QR Code di samping.<br>
										• Masukkan kode unik surat : <b>" . $kode_unik . "</b>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td style=\"text-align: right; font-size: 8px;\">
										<b>.::PRINTED BY SIMLURAH::.</b>
									</td>
								</tr>
								<tr>
									<td colspan=\"4\">&nbsp;</td>
								</tr>
							</table>";
				} else {
					if ($dataSurat['surat']['info_tambahan']['ttd_srikandi'] == 'on') {
						$cFoot = "<table style=\"font-size:8;\">
							<tr>
								<td style=\"text-align: center; font-size: 8px;\">
										support by SIMLURAH
								</td>
								</tr>
								<tr>
									<td colspan=\"4\">&nbsp;</td>
								</tr>
						</table>";
					} else {
						$cFoot = "
						<table style=\"font-size:8;\">
							<tr>
								<td style=\"text-align: right; font-size: 8px;\">
										<b>.::PRINTED BY SIMLURAH::.</b>
								</td>
								</tr>
								<tr>
									<td colspan=\"4\">&nbsp;</td>
								</tr>
						</table>";
					}
				}

				$spdf->SetHTMLFooter($cFoot);



				//$file_name = date('YmdHis');

				// $spdf->SetProtection(array('print'));
				// if ($draft) {
				// 	$spdf->SetWatermarkText('DRAFT');
				// 	$spdf->showWatermarkText = true;
				// }

				$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF

				//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can

				//$spdf->Output('repositories/Billing/'.$filename.'.pdf', 'F');
				if ($output == 'F') {
					$spdf->Output($filename, $output);
				} else {
					$spdf->Output($filename . '.pdf', $output); // view file
				}

				break;

			case 'excel':
				$this->nsmarty->assign('data', $data);

				$this->nsmarty->assign('mod', $mod);


				if ($data['data_surat'] <> '') {
					$htmlcontent = $data['data_surat'];
				} else {
					$htmlcontent = $this->nsmarty->fetch($temp);
				}


				if (isset($_GET['view'])) {
					// code...
					echo $htmlcontent;
					exit;
				}

				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename.xls");
				echo ($htmlcontent);
				break;
		}
	}

	function hasil_output2($p1, $mod, $data, $filename, $temp, array $config = [], $output = 'I', $draft = false)
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', -1);
		switch ($p1) {
			case "pdf":

				$this->nsmarty->assign('data', $data);

				$this->nsmarty->assign('mod', $mod);


				if (isset($data['data_surat']) && $data['data_surat'] <> '') {
					$htmlcontent = $data['data_surat'];
				} else {
					$htmlcontent = $this->nsmarty->fetch($temp);
				}


				if (isset($_GET['view'])) {
					echo $htmlcontent;
					exit;
				}

				// ini_set('pcre.backtrack_limit',2000000);
				ini_set('pcre.backtrack_limit', 2000000);

				$spdf = new \Mpdf\Mpdf($config, null);

				$spdf->showImageErrors = false;

				$spdf->ignore_invalid_utf8 = true;

				ob_clean();
				ini_set('memory_limit', -1);
				ini_set('max_execution_time', -1);

				// bukan sulap bukan sihir sim salabim jadi apa prok prok prok

				$spdf->allow_charset_conversion = true;     // which is already true by default

				$spdf->charset_in = 'iso-8859-1';  // set content encoding to iso

				$spdf->SetDisplayMode('fullpage');

				//$spdf->SetHTMLHeader($htmlheader);

				//$spdf->keep_table_proportions = true;

				$spdf->useSubstitutions = false;

				// $spdf->simpleTables = true;

				$spdf->keep_table_proportions = true;

				$spdf->SetHTMLFooter('

					<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">

						<table width="100%" style="font-family:arial; font-size:8px;">

							<tr>

								<td style="padding-top: -20px">
									<b>.::PRINTED BY SIMLURAH::.</b>
								</td>

							</tr>

						</table>

					</div>

				');



				//$file_name = date('YmdHis');

				// $spdf->SetProtection(array('print'));
				if ($draft) {
					$spdf->SetWatermarkText('DRAFT');
					$spdf->showWatermarkText = true;
				}

				$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF

				//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can

				//$spdf->Output('repositories/Billing/'.$filename.'.pdf', 'F');
				if ($output == 'F') {
					$spdf->Output($filename, $output);
				} else {
					$spdf->Output($filename . '.pdf', $output); // view file
				}

				break;

			case 'excel':
				$this->nsmarty->assign('data', $data);

				$this->nsmarty->assign('mod', $mod);


				if ($data['data_surat'] <> '') {
					$htmlcontent = $data['data_surat'];
				} else {
					$htmlcontent = $this->nsmarty->fetch($temp);
				}


				if (isset($_GET['view'])) {
					// code...
					echo $htmlcontent;
					exit;
				}

				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename.xls");
				echo ($htmlcontent);
				break;
		}
	}

	function hasil_output3($htmlcontent, $filename, $path, $dataSurat)
	{
		$spdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8',
			'format' => 'A4',
			'margin_left' => 5,
			'margin_right' => 5,
			'margin_top' => 5,
			'margin_bottom' => 5,
			'margin_header' => 0,
			'margin_footer' => 0,
			'default_font_size' => '12pt',
			'default_font' => '',
		], null);

		$spdf->showImageErrors = false;

		$spdf->ignore_invalid_utf8 = true;

		ob_clean();
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', -1);

		$spdf->allow_charset_conversion = true;

		$spdf->charset_in = 'iso-8859-1';

		$spdf->SetDisplayMode('fullpage');

		$spdf->useSubstitutions = false;

		$spdf->keep_table_proportions = true;

		$arraynya = array(

			'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

			'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

			'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

			'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],


		);
		$setting = $this->db->get_where('tbl_setting_apps', $arraynya)->row_array();
		if ($setting['qr_status'] == 1 && $setting['qr_kelurahan'] != '' && $dataSurat['surat']['info_tambahan']['ttd_srikandi'] != 'on') {
			$cFoot = "
				<table style=\"font-size:8;\">
					<tr>
						<td>
						<img src=\"" . FCPATH . "" . $setting['qr_kelurahan'] . "\" width=\"35px\">
						</td>
						<td>
						Catatan :<br>
						• Sesuai Undang-Undang Nomor 24 Tahun 2013 tentang Administrasi Kependudukan, pemalsuan dokumen kependudukan merupakan tindak pidana.<br>
						• Keaslian dokumen ini dapat diverifikasi melalui pemindaian QR Code di samping.<br>
						• Masukkan kode unik surat : <b>" . $dataSurat['surat']['kode_unik'] . "</b>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style=\"text-align: right; font-size: 8px;\">
								<b>.::PRINTED BY SIMLURAH::.</b>
						</td>
						</tr>
						<tr>
							<td colspan=\"4\">&nbsp;</td>
						</tr>
				</table>";
		} else {
			if ($dataSurat['surat']['info_tambahan']['ttd_srikandi'] == 'on') {
				$cFoot = "<table style=\"font-size:8;\">
					<tr>
						<td style=\"text-align: center; font-size: 8px;\">
								support by SIMLURAH
						</td>
						</tr>
						<tr>
							<td colspan=\"4\">&nbsp;</td>
						</tr>
				</table>";
			} else {
				$cFoot = "
				<table style=\"font-size:8;\">
					<tr>
						<td style=\"text-align: right; font-size: 8px;\">
								<b>.::PRINTED BY SIMLURAH::.</b>
						</td>
						</tr>
						<tr>
							<td colspan=\"4\">&nbsp;</td>
						</tr>
				</table>";
			}
		}

		$spdf->SetHTMLFooter($cFoot);

		$spdf->WriteHTML($htmlcontent);

		$spdf->Output($path . "/" . $filename . '.pdf', 'F');
	}

	function zipFolder($sourceFolder, $zipFilePath)
	{
		if (!extension_loaded('zip') || !file_exists($sourceFolder)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
			return false;
		}

		$sourceFolder = realpath($sourceFolder);

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($sourceFolder),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $file) {
			if (!$file->isDir()) {
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($sourceFolder) + 1);
				$zip->addFile($filePath, $relativePath);
			}
		}

		$zip->close();
		$sourceFolder = rtrim($sourceFolder, '/\\');
		$files = glob($sourceFolder . '/*.pdf');
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
		return true;
	}

	function generate_esign($nomor_register, $cl_kelurahan_desa_id)
	{
		$key = encrypt_url($nomor_register . '_' . $cl_kelurahan_desa_id);
		$logo = "__assets/images/logo_qr.png";
		$forecolor = '0,0,0';
		$backcolor = '255,255,255';
		$text = base_url('qscan?k=' . $key);
		$dir  = date('Ymd');
		if (!is_dir('./__data/' . $dir)) {
			mkdir('./__data/' . $dir, 0755);
		}
		$path = '__data/' . $dir . '/' . $nomor_register . '_' . $cl_kelurahan_desa_id . '_' . date('YmdHis') . '.png';
		// $path = false;
		$this->load->library('Ciqrcode');
		QRcode::png($text, $path, "H", 5, 2, 0, $forecolor, $backcolor, $logo);
		if (!file_exists($path)) {
			return false;
		} else {
			return $path;
		}
	}

	public function generate_pdf_esign($stamp_esign, $file_src_esign, $nik, $passphrase)
	{
		$this->load->helper('file');
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => '103.151.191.67/api/sign/pdf',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array(
				'file' => new CURLFILE(FCPATH . $file_src_esign, 'application/pdf', 'file'),
				'imageTTD' => new CURLFILE(FCPATH . $stamp_esign, 'image/png', 'file'),
				'nik' => $nik,
				'passphrase' => $passphrase,
				'tampilan' => 'visible',
				'image' => 'true',
				'width' => '70',
				'height' => '70',
				'tag_koordinat' => '~'
			),
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic c21hcnRvZmZpY2V2MzpiaXNtaWxsYWg='
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		if (count(json_decode($response)) == 0) {
			$newfilename = str_replace('.pdf', '_approved_' . date('YmdHis') . '.pdf', $file_src_esign);
			if (write_file($newfilename, $response)) {
				return $newfilename;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function downloadfile()
	{

		$this->load->helper('download');

		$filenya = $this->input->post("filenya");

		$log['aktivitas'] = "Download File Name <b>" . $this->input->post("namafile") . "</b> oleh " . $this->auth['nama_user'];

		$log['data_id'] = $this->input->post("id");

		$log['flag_tbl'] = "tbl_upload_file";

		$log['create_date'] = date('Y-m-d H:i:s');

		$log['create_by'] = $this->auth['nama_user'];

		$this->db->insert('tbl_log', $log);



		force_download($filenya, NULL);
	}



	function getauth()
	{

		$data = $this->db->get('tbl_user')->result_array();

		$html = "";

		foreach ($data as $k => $v) {

			$html .= $v['username'] . " - " . $this->encrypt->decode($v['password']) . "<br/>";
		}



		echo $html;
	}



	function mappingrole()
	{

		$sql = "

			SELECT id

			FROM tbl_user_menu

		";

		$data = $this->db->query($sql)->result_array();

		foreach ($data as $k => $v) {

			$array = array(

				'cl_user_group_id' => '1',

				'tbl_menu_id' => $v['id'],

				'buat' => '1',

				'baca' => '1',

				'ubah' => '1',

				'hapus' => '1',

			);

			$this->db->insert('tbl_user_prev_group', $array);
		}
	}

	// untuk menyimpan data ke database
	function simpan_ttd()
	{
		$ttd 	= $this->input->get('nip');
		$qttd	= $this->db->query("select * from tbl_data_penandatanganan where nip='$ttd'")->row();

		//$data itu untuk menyimpan data di tbl_data_penandatanganan
		$data = array(
			'nip_kepala_desa' 		=> $qttd->nip,
			'nama_kepala_desa' 		=> $qttd->nama
		);


		if ($this->mbackend->save('tbl_setting_apps', $data, '2') == true) {
			$return = array(
				'status' => true,
				'message'  => 'Data berhasil disimpan',
			);
		} else {
			$return = array(
				'status' => false,
				'message'  => 'Terjadi kesalahan!',
			);
		}
		echo json_encode($return);
	}

	function notif_broadcast()
	{
		$notif	= $this->input->get('id');
		$notifx	= $this->db->query("select COUNT(id) as jumlah from tbl_data_broadcast")->row('jumlah');

		echo json_encode($notifx);
	}

	public function get_data_riwayat_esign($id)
	{
		$res = $this->db->where('tbl_data_surat_id', $id)->order_by('id', 'asc')->get('tbl_riwayat_esign')->result();
		$data = [];
		foreach ($res as $row) {
			$row->created_at = date('d/m/Y H:i', strtotime($row->created_at));
			$data[] = $row;
		}
		echo json_encode($data);
	}

	public function get_petugas_rt_rw()
	{
		$data = $this->db->get('tbl_data_rt_rw')->result_array();
		echo json_encode($data);
	}

	public function kirim_ke_sekcam()
	{
		$ids = $this->input->post('data_terpilih');

		if (is_array($ids) && count($ids) > 0) {
			$setting = $this->db->get('tbl_setting_apps')->row();
			$periode_bulan = !empty($setting->periode_bulan) ? $setting->periode_bulan : date('Y-m');
			$tgl_usulan = !empty($setting->tgl_usulan) ? $setting->tgl_usulan : date('Y-m-d');

			foreach ($ids as $id) {
				// Cek apakah data sudah ada sebelumnya
				$cek = $this->db->where('id_petugas', $id)->get('tbl_usulan_ke_sekcam')->num_rows();
				if ($cek > 0) continue;

				// Ambil detail petugas dari tbl_data_rt_rw
				$petugas = $this->db->get_where('tbl_data_rt_rw', ['id' => $id])->row();
				if (!$petugas) continue;

				$this->db->insert('tbl_usulan_ke_sekcam', [
					'id_petugas'        => $id,
					'nama_lengkap'      => $petugas->nama_lengkap,
					'jab_rt_rw'           => $petugas->jab_rt_rw,
					'no_npwp'           => $petugas->no_npwp,
					'no_rekening'       => $petugas->no_rekening, // pastikan ini memang ada di tabel
					'periode_bulan'     => $periode_bulan,
					'tgl_usulan'        => $tgl_usulan,
					'status'            => 'terkirim',
					'dikirim_oleh'      => $this->session->userdata('auth')['username'],
					'waktu_kirim'       => date('Y-m-d H:i:s'),
					'user_group_tujuan' => 4
				]);
			}

			echo json_encode(['status' => true, 'pesan' => 'Data berhasil dikirim ke Sekcam.']);
		} else {
			echo json_encode(['status' => false, 'pesan' => 'Tidak ada data yang dipilih.']);
		}
	}



	function get_nomor_surat($cl_kecamatan_id = '', $cl_kelurahan_desa_id = '', $cl_jenis_surat_id = '', $tanggal_surat = '')
	{
		if ($this->auth['cl_user_group_id'] == 2 || $this->auth['cl_user_group_id'] == 3) {
			echo json_encode(format_nomor_surat($cl_kecamatan_id, $cl_kelurahan_desa_id, $cl_jenis_surat_id, $tanggal_surat));
		} else {
			echo json_encode([]);
		}
	}

	// function get_opsi_ttd($kel)
	// {
	// 	$sql = "

	// 					SELECT nip as id, nama as txt

	// 					FROM tbl_data_penandatanganan 

	// 					where cl_kelurahan_desa_id = '$kel'


	// 				";
	// 	$res = $this->db->query($sql)->result();
	// 	foreach ($res as $row) {
	// 		$result[] = array(
	// 			'id'    => $row->id,
	// 			'txt'    => $row->txt
	// 		);
	// 	}
	// 	echo json_encode($result);
	// }

	function get_opsi_ttd($kel = '')
	{
		$result = [];

		if (!empty($kel)) {
			$sql = "
				SELECT nip as id, nama as txt
				FROM tbl_data_penandatanganan 
				WHERE cl_kelurahan_desa_id = '$kel'
			";
			$res = $this->db->query($sql)->result();

			foreach ($res as $row) {
				$result[] = array(
					'id'  => $row->id,
					'txt' => $row->txt
				);
			}
		}

		echo json_encode($result);
	}


	public function cek_nik($nik)
	{
		$cek = $this->db->where('nik', $nik)->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id'])->get('tbl_data_penduduk')->num_rows();
		if ($cek > 0) {
			$data = 1;
		} else {
			$data = 0;
		}
		echo $data;
	}

	public function cek_nopol($nopol)
	{

		$crep = 'replace(nopol," ","")=';

		$cek = $this->db->get_where('tbl_data_kendaraan', array($crep => $nopol, 'cl_kecamatan_id' => $this->auth['cl_kecamatan_id']));
		$cek_nop = $cek->num_rows();
		// echo $cek_nop;die;
		if ($cek_nop > 0) {
			$data = 1;
		} else {
			$data = 0;
		}
		echo $data;
	}

	public function cek_no_passport($no_passport)
	{

		$crep = 'replace(no_passport," ","")=';

		$cek = $this->db->get_where('tbl_data_penduduk_asing', array($crep => $no_passport, 'cl_kecamatan_id' => $this->auth['cl_kecamatan_id']));
		$cek_pass = $cek->num_rows();
		// echo $cek_nop;die;
		if ($cek_pass > 0) {
			$data = 1;
		} else {
			$data = 0;
		}
		echo $data;
	}

	public function dash_cetak_usia($mod, $p1 = "", $p2 = "", $p3 = "", $p4 = "")
	{
		switch ($mod) {

			case "laporan_cetak_usia":

				$data = $this->mbackend->getdata('laporan_cetak_usia', 'result_array');

				$filename = "laporan_cetak_usia-" . date('YmdHis');

				$temp = "backend/cetak/laporan_cetak_usia.html";

				$this->hasil_output('pdf', $mod, $data, $filename, $temp, "LEGAL-L");

				break;
		}
	}

	function simpan_format_nomor_surat()
	{
		$data = $this->input->post();
		$data['id'] = $data['cl_nomor_surat_id'];
		unset($data['cl_nomor_surat_id']);
		$data['bulan'] = romawi(intval(date('m')));
		$data['nomor'] = '001';
		$data['tahun'] = date('Y');
		$format_nomor = $data['format_nomor'];
		$data['format_nomor'] = '';
		foreach ($format_nomor as $key => $value) {
			if (in_array($value, $data['format_nomor_aktif'])) {
				$data['format_nomor'][] = $value;
			}
		}
		$param_nomor = $data['param_nomor'];
		$data['param_nomor'] = '';
		foreach ($param_nomor as $key => $value) {
			if (in_array($value, $data['param_nomor_aktif'])) {
				$data['param_nomor'][] = $value;
			}
		}

		$data['kode_surat'] = '';
		foreach ($data['format_nomor'] as $key => $value) {
			$data['kode_surat'] .= $data[$value];
			if ($value != 'tahun') {
				$data['kode_surat'] .= '/';
			}
		}

		$data['format_nomor'] = json_encode($data['format_nomor']);
		$data['param_nomor'] = json_encode($data['param_nomor']);
		unset($data['format_nomor_aktif']);
		unset($data['param_nomor_aktif']);
		$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
		$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
		if ($data['id'] != '') {
			$sql = $this->db->where('id', $data['id'])->update('cl_nomor_surat', $data);
		} else {
			$sql = $this->db->insert('cl_nomor_surat', $data);
		}

		if ($sql) {
			echo json_encode([
				'stat' => true,
				'message' => 'Data tersimpan',
			]);
		} else {
			echo json_encode([
				'stat' => false,
				'message' => $this->db->error()['message'],
			]);
		}
	}

	function reset_format_nomor_surat()
	{
		$data = $this->input->post();

		$sql = $this->db->where('id', $data['cl_nomor_surat_id'])->delete('cl_nomor_surat');

		if ($sql) {
			echo json_encode([
				'stat' => true,
				'message' => 'Data direset',
			]);
		} else {
			echo json_encode([
				'stat' => false,
				'message' => $this->db->error()['message'],
			]);
		}
	}

	function get_format_nomor_surat()
	{
		$id = $this->input->post('cl_nomor_surat_id');
		$data = $this->db->where('id', $id)->get('cl_nomor_surat')->row_array();
		$data['format_nomor'] = json_decode($data['format_nomor']);
		$data['param_nomor'] = json_decode($data['param_nomor']);
		if ($data) {
			echo json_encode([
				'stat' => true,
				'data' => $data,
			]);
		} else {
			echo json_encode([
				'stat' => false,
				'message' => $this->db->error()['message'],
			]);
		}
	}

	function simpan_favorit_surat()
	{
		$data = $this->input->post();

		if ($data['status'] == 0) {
			$sql = $this->db->where([
				'id' => $data['id'],
				'user_id' => $this->auth['id']
			])->delete('cl_jenis_surat_favorit');
		} else {
			$res = $this->db->where([
				'id' => $data['id'],
			])->get('cl_jenis_surat')->row_array();
			$res['user_id'] = $this->auth['id'];
			$sql = $this->db->insert('cl_jenis_surat_favorit', $res);
		}

		if ($sql) {
			echo json_encode([
				'stat' => true,
				'message' => 'Data tersimpan',
			]);
		} else {
			echo json_encode([
				'stat' => false,
				'message' => $this->db->error()['message'],
			]);
		}
	}

	function hapus_foto_usaha($id, $index)
	{
		$file_name = $this->input->post('file_name');
		$data = $this->db->where('id', $id)->get('tbl_data_surat')->row_array();
		$info = json_decode($data['info_tambahan']);
		$files = [];
		$i = 0;
		$file = '';
		foreach ($info->foto_usaha as $row) {
			if ($file_name == $row->file_name) {
				$file = $row;
			} else {
				$files[] = $row;
			}
			$i++;
		}
		$info->foto_usaha = $files;
		$sql = $this->db->where('id', $id)->update('tbl_data_surat', ['info_tambahan' => json_encode($info)]);
		if ($sql) {
			if (file_exists('./' . $file->file_name)) {
				unlink('./' . $file->file_name);
			}
			echo 1;
		} else {
			echo 'Terjadi kesalahan, coba lagi!';
		}
	}

	function hapus_foto_pegawai_kel_kec($id, $index = 0)
	{
		$file_name = $this->input->post('files');
		$data = $this->db->where('id', $id)->get('tbl_data_pegawai_kel_kec')->row_array();
		$info = json_decode($data['file']);
		$files = [];
		$i = 0;
		$file = '';
		foreach ($info as $row) {
			if ($file_name == $row->files) {
				$file = $row;
			} else {
				$files[] = $row;
			}
			$i++;
		}
		$sql = $this->db->where('id', $id)->update('tbl_data_pegawai_kel_kec', ['file' => json_encode($files)]);
		if ($sql) {
			if (file_exists('./' . $file->files)) {
				unlink('./' . $file->files);
			}
			echo 1;
		} else {
			echo 'Terjadi kesalahan, coba lagi!';
		}
	}

	function hapus_foto_kendaraan($id, $index = 0)
	{
		$file_name = $this->input->post('files');
		$data = $this->db->where('id', $id)->get('tbl_data_kendaraan')->row_array();
		$info = json_decode($data['file']);
		$files = [];
		$i = 0;
		$file = '';
		foreach ($info as $row) {
			if ($file_name == $row->files) {
				$file = $row;
			} else {
				$files[] = $row;
			}
			$i++;
		}
		$sql = $this->db->where('id', $id)->update('tbl_data_kendaraan', ['file' => json_encode($files)]);
		if ($sql) {
			if (file_exists('./' . $file->files)) {
				unlink('./' . $file->files);
			}
			echo 1;
		} else {
			echo 'Terjadi kesalahan, coba lagi!';
		}
	}

	function survei_kepuasan($cl_user_group_id, $cl_kecamatan_id, $cl_kelurahan_desa_id = '')
	{
		switch ($cl_user_group_id) {
			case '2':
				$nama_kelurahan_desa = $this->db->where([
					'id' => $cl_kelurahan_desa_id,
					'kecamatan_id' => $cl_kecamatan_id,
				])->get('cl_kelurahan_desa')->row('nama');
				$this->load->view('survey_kepuasan_kelurahan', [
					'cl_user_group_id' => $cl_user_group_id,
					'cl_kecamatan_id' => $cl_kecamatan_id,
					'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id,
					'nama_kelurahan_desa' => $nama_kelurahan_desa,
				]);
				break;
			case '3':
				$nama_kelurahan_desa = $this->db->where([
					'id' => $cl_kecamatan_id,
				])->get('cl_kecamatan')->row('nama');
				$this->load->view('survey_kepuasan_kelurahan', [
					'cl_user_group_id' => $cl_user_group_id,
					'cl_kecamatan_id' => $cl_kecamatan_id,
					'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id,
					'nama_kelurahan_desa' => $nama_kelurahan_desa,
				]);
				break;

			default:
				# code...
				break;
		}
	}

	function simpan_survei()
	{
		$data = $this->input->post();
		$data['sesi_id'] = $this->db->select("COALESCE(MAX(sesi_id),0)+1 as sesi_id")->get('tbl_penilaian_skm')->row('sesi_id');
		$res_skala = $data['skala'];
		$res_indikator_skm = $data['indikator_skm_id'];
		$nama_kelurahan_desa = $data['nama_kelurahan_desa'];
		unset($data['nama_kelurahan_desa']);
		unset($data['skala']);
		unset($data['indikator_skm_id']);
		$data_batch = [];
		$total = 0;
		$num = count($res_indikator_skm);
		for ($i = 1; $i <= count($res_indikator_skm); $i++) {
			$data['indikator_skm_id'] = $res_indikator_skm[$i];
			$data['penilaian'] = $res_skala[$i];
			$total += $res_skala[$i];
			$data_batch[] = $data;
		}
		if (count($data_batch) > 0) {
			$sql = $this->db->insert_batch('tbl_penilaian_skm', $data_batch);
			if ($sql) {
				$rata = floor($total / $num);
				if ($rata >= 3) {
					$icon = 'fa-regular fa-face-laugh-beam fa-5x text-success';
					$msg = 'Terima kasih atas survei luar biasa yang Anda berikan! Kami senang mengetahui bahwa Anda puas dengan layanan kami.';
				} elseif ($rata == 2) {
					$icon = 'fa-regular fa-face-laugh-beam fa-5x text-warning';
					$msg = 'Terima kasih atas survei Anda! Kami senang bahwa Anda cukup puas dengan layanan kami.';
				} else {
					$icon = 'fa-regular fa-face-sad-cry fa-5x text-danger';
					$msg = 'Kami mohon maaf atas ketidaknyamanan yang Anda alami. Kami akan berusaha memberi pelayanan terbaik.';
				}
			} else {
				$icon = 'fa-solid fa-triangle-exclamation fa-5x text-warning';
				$msg = 'Terjadi kesalahan, coba lagi!';
			}
		} else {
			$icon = 'fa-solid fa-triangle-exclamation fa-5x text-warning';
			$msg = 'Terjadi kesalahan, coba lagi!';
		}

		$this->load->view('survey_feedback', [
			'cl_user_group_id' => $data['cl_user_group_id'],
			'cl_kecamatan_id' => $data['cl_kecamatan_id'],
			'cl_kelurahan_desa_id' => $data['cl_kelurahan_desa_id'],
			'nama_kelurahan_desa' => $nama_kelurahan_desa,
			'icon' => $icon,
			'msg' => $msg,
		]);
	}

	function get_data_penilaian_rt_rw_id()
	{
		try {

			$data = $this->input->post();
			$bulan = $data['bulan_lalu'] - 1;
			if ($data['bulan_lalu'] == 1) {
				$tahun = $this->auth['tahun'] - 1;
				$bulan = 12;
				$this->db->where("year(tgl_surat)=$tahun");
			} else {
				$this->db->where("year(tgl_surat)=" . $this->auth['tahun']);
			}
			$cek = $this->db->where([
				'tbl_data_rt_rw_id' => $data['rt_rw_id'],
				'bulan' => $bulan,
			])->order_by('id')->get('tbl_penilaian_rt_rw');
			if ($cek->num_rows() > 0) {
				echo json_encode([
					'status' => true,
					'message' => 'Data tersedia!',
					'data' => $cek->result_array()
				]);
				return;
			}
			throw new Exception("Data tidak tersedia!");
		} catch (\Exception  $th) {
			echo json_encode([
				'status' => false,
				'message' => $th->getMessage(),
				'data' => []
			]);
		}
	}

	function salin_data_penilaian_rt_rw()
	{
		try {

			$post = $this->input->post();
			$post['tgl_penilaian'] = date('Y-m-d', strtotime($post['tgl_penilaian']));
			$where = '';
			if (!isset($post['replace_penilaian'])) {
				$where .= "
					AND b.id NOT IN(
						SELECT a.tbl_data_rt_rw_id
						FROM tbl_penilaian_rt_rw a
						INNER JOIN tbl_data_rt_rw b ON a.tbl_data_rt_rw_id = b.id AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id
						WHERE a.bulan = $post[bulan_tujuan]
						AND b.cl_kecamatan_id='" . $this->auth['cl_kecamatan_id'] . "'
						AND b.cl_kelurahan_desa_id='" . $this->auth['cl_kelurahan_desa_id'] . "'
						AND YEAR(a.tgl_surat)=" . $this->auth['tahun'] . "
					)
				";
			}
			$res = $this->db->query("
				WITH base AS (
					SELECT
						a.cl_provinsi_id,
						a.cl_kab_kota_id,
						a.cl_kecamatan_id,
						a.cl_kelurahan_desa_id,
						b.id AS tbl_data_rt_rw_id,
						b.nik,
						b.nama_lengkap,
						'$post[tgl_penilaian]' AS tgl_surat,
						'$post[bulan_tujuan]' AS bulan,
						a.kategori_penilaian_rt_rw_id,
						a.kategori,
						a.uraian,
						a.satuan,
						a.target,
						a.capaian,
						a.nilai,
						a.id AS penilaian_internal_id,
						ROW_NUMBER() OVER (PARTITION BY b.id ORDER BY a.id) AS row_num_within_b_id,
						DENSE_RANK() OVER (ORDER BY b.id) AS unique_b_id_rank
					FROM tbl_penilaian_rt_rw a
					INNER JOIN tbl_data_rt_rw b ON a.tbl_data_rt_rw_id = b.id AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id
					WHERE b.status = 'Aktif'
					AND a.bulan = $post[bulan_asal]
					AND b.cl_kecamatan_id='" . $this->auth['cl_kecamatan_id'] . "'
					AND b.cl_kelurahan_desa_id='" . $this->auth['cl_kelurahan_desa_id'] . "'
					AND YEAR(a.tgl_surat)=" . $this->auth['tahun'] . "
					$where
					),
					max_id AS (
					SELECT COALESCE(MAX(penilaian_id), 0) AS max_penilaian_id FROM tbl_penilaian_rt_rw
					)
					SELECT
						m.max_penilaian_id + b.unique_b_id_rank AS penilaian_id,
						b.cl_provinsi_id,
						b.cl_kab_kota_id,
						b.cl_kecamatan_id,
						b.cl_kelurahan_desa_id,
						b.tbl_data_rt_rw_id,
						b.nik,
						b.nama_lengkap,
						b.tgl_surat,
						b.bulan,
						b.kategori_penilaian_rt_rw_id,
						b.kategori,
						b.uraian,
						b.satuan,
						b.target,
						b.capaian,
						b.nilai,
						CURRENT_DATE AS create_date
					FROM base b, max_id m
					ORDER BY (m.max_penilaian_id + b.unique_b_id_rank);
			");
			$data = [];
			$penilaian_id_temp = '';
			$tbl_data_rt_rw_id_temp = '';
			$total = 0;
			$tbl_data_rt_rw_id = [];
			foreach ($res->result_array() as $row) {
				$row['create_by'] = $this->auth['nama_lengkap'];
				if ($penilaian_id_temp != $row['penilaian_id']) {
					$total++;
					$penilaian_id_temp = $row['penilaian_id'];
				}
				if ($tbl_data_rt_rw_id_temp != $row['tbl_data_rt_rw_id']) {
					$tbl_data_rt_rw_id[] = $row['tbl_data_rt_rw_id'];
					$tbl_data_rt_rw_id_temp = $row['tbl_data_rt_rw_id'];
				}
				$data[] = $row;
			}
			if (count($data) > 0) {
				$this->db->trans_begin();
				if (isset($post['replace_penilaian'])) {
					$this->db->where([
						'bulan' => $post['bulan_tujuan'],
						'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
						'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
						'YEAR(tgl_surat)' => $this->auth['tahun']
					])->where_in('tbl_data_rt_rw_id', $tbl_data_rt_rw_id)->delete('tbl_penilaian_rt_rw');
				}
				$sql = $this->db->insert_batch('tbl_penilaian_rt_rw', $data);

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					throw new Exception($this->db->error()['message']);
				} else {
					$this->db->trans_commit();
				}
			} else {
				throw new Exception("Data tidak tersedia!");
			}

			echo json_encode([
				'status' => true,
				'message' => "Data tersalin ($total)",
			]);
		} catch (\Exception  $th) {
			echo json_encode([
				'status' => false,
				'message' => $th->getMessage(),
			]);
		}
	}

	function get_data_penduduk()
	{
		$nik = $this->input->post('nik', true);
		$cek = $this->db->where([
			'nik' => $nik,
			'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
			'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
		])->get('tbl_data_penduduk');
		if ($cek->num_rows() > 0) {
			echo json_encode([
				'status' => true,
				'message' => '',
				'data' => $cek->row_array()
			]);
		} else {
			echo json_encode([
				'status' => false,
				'message' => 'Data penduduk tidak tersedia di data master, silahkan lengkapi informasi penduduk dibawah!',
				'data' => []
			]);
		}
	}

	// public function salin_data_rekap_bulanan()
	// {
	// 	$bulan_asal = $this->input->post('bulan_asal');
	// 	$bulan_tujuan = $this->input->post('bulan_tujuan');
	// 	$tgl_cetak = $this->input->post('tgl_cetak');
	// 	$ganti = $this->input->post('ganti'); // checkbox

	// 	// Ambil data dari bulan asal
	// 	$data_asal = $this->db->get_where('tbl_data_rekap_bulanan', [
	// 		'bulan' => $bulan_asal,
	// 		'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
	// 	])->result_array();

	// 	if (empty($data_asal)) {
	// 		echo json_encode(['status' => false, 'message' => 'Data bulan asal tidak ditemukan!']);
	// 		return;
	// 	}

	// 	// Jika data tujuan sudah ada
	// 	$data_tujuan = $this->db->get_where('tbl_data_rekap_bulanan', [
	// 		'bulan' => $bulan_tujuan,
	// 		'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
	// 	])->result_array();

	// 	if (!empty($data_tujuan)) {
	// 		if ($ganti == '1') {
	// 			// hapus dulu data bulan tujuan
	// 			$this->db->where([
	// 				'bulan' => $bulan_tujuan,
	// 				'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
	// 			])->delete('tbl_data_rekap_bulanan');
	// 		} else {
	// 			echo json_encode(['status' => false, 'message' => 'Data bulan tujuan sudah ada!']);
	// 			return;
	// 		}
	// 	}

	// 	// duplikasi data asal -> bulan tujuan
	// 	foreach ($data_asal as $row) {
	// 		unset($row['id']); // hilangkan ID agar auto increment baru
	// 		$row['bulan'] = $bulan_tujuan;
	// 		$row['create_date'] = date('Y-m-d H:i:s');
	// 		$this->db->insert('tbl_data_rekap_bulanan', $row);
	// 	}

	// 	echo json_encode(['status' => true, 'message' => 'Data berhasil disalin ke bulan ' . $bulan_tujuan]);
	// }

	//fungsi punya penilaian rt rw ya
	// public function salin_data_rekap_bulanan()
	// {
	// 	$bulan_asal = $this->input->post('bulan_asal');
	// 	$bulan_tujuan = $this->input->post('bulan_tujuan');
	// 	$tgl_cetak = $this->input->post('tgl_cetak');
	// 	$ganti = $this->input->post('ganti'); // checkbox (1 = gantikan)
	// 	$cl_kelurahan_desa_id = $this->auth['cl_kelurahan_desa_id'];

	// 	if (empty($bulan_asal) || empty($bulan_tujuan)) {
	// 		echo json_encode(['status' => false, 'message' => 'Bulan asal atau bulan tujuan tidak boleh kosong!']);
	// 		return;
	// 	}

	// 	// Ambil data dari bulan asal
	// 	$data_asal = $this->db->get_where('tbl_penilaian_rt_rw', [
	// 		'bulan' => $bulan_asal,
	// 		'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
	// 	])->result_array();

	// 	if (empty($data_asal)) {
	// 		echo json_encode(['status' => false, 'message' => 'Data bulan asal tidak ditemukan!']);
	// 		return;
	// 	}

	// 	// Cek apakah data tujuan sudah ada
	// 	$data_tujuan = $this->db->get_where('tbl_penilaian_rt_rw', [
	// 		'bulan' => $bulan_tujuan,
	// 		'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
	// 	])->result_array();

	// 	if (!empty($data_tujuan) && $ganti != '1') {
	// 		echo json_encode(['status' => false, 'message' => 'Data bulan tujuan sudah ada!']);
	// 		return;
	// 	}

	// 	$this->db->trans_start();

	// 	// Jika "gantikan" dicentang, hapus data bulan tujuan
	// 	if ($ganti == '1') {
	// 		$this->db->where([
	// 			'bulan' => $bulan_tujuan,
	// 			'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
	// 		])->delete('tbl_penilaian_rt_rw');
	// 	}

	// 	// Duplikasi data asal ke bulan tujuan
	// 	foreach ($data_asal as $row) {
	// 		unset($row['id']); // hilangkan ID agar auto increment
	// 		$row['bulan'] = $bulan_tujuan;
	// 		$row['create_date'] = date('Y-m-d H:i:s');
	// 		$this->db->insert('tbl_penilaian_rt_rw', $row);
	// 	}

	// 	$this->db->trans_complete();

	// 	if ($this->db->trans_status() === false) {
	// 		echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan saat menyalin data!']);
	// 	} else {
	// 		echo json_encode(['status' => true, 'message' => 'Data berhasil disalin dari bulan ' . $bulan_asal . ' ke bulan ' . $bulan_tujuan]);
	// 	}
	// }

	//fungsi punya Rekap Bulanan Penduduk ya
	public function salin_data_rekap_bulanan()
	{
		$bulan_asal = $this->input->post('bulan_asal');
		$bulan_tujuan = $this->input->post('bulan_tujuan');
		$tgl_cetak = $this->input->post('tgl_cetak');
		$ganti = $this->input->post('ganti');
		$cl_kelurahan_desa_id = $this->auth['cl_kelurahan_desa_id'];

		if (empty($bulan_asal) || empty($bulan_tujuan)) {
			echo json_encode(['status' => false, 'message' => 'Bulan asal dan bulan tujuan wajib diisi!']);
			return;
		}

		// Ambil data dari bulan asal
		$data_asal = $this->db->get_where('tbl_data_rekap_bulanan', [
			'bulan' => $bulan_asal,
			'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
		])->result_array();

		if (empty($data_asal)) {
			echo json_encode(['status' => false, 'message' => 'Data bulan asal tidak ditemukan!']);
			return;
		}

		// Cek apakah data bulan tujuan sudah ada
		$data_tujuan = $this->db->get_where('tbl_data_rekap_bulanan', [
			'bulan' => $bulan_tujuan,
			'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
		])->result_array();

		if (!empty($data_tujuan) && $ganti != '1') {
			echo json_encode(['status' => false, 'message' => 'Data bulan tujuan sudah ada!']);
			return;
		}

		// Debug log
		log_message('error', 'DEBUG SALIN DATA = ' . json_encode([
			'asal' => $data_asal,
			'tujuan' => $data_tujuan,
			'post' => $_POST
		]));

		$this->db->trans_start();

		// Jika dicentang gantikan, hapus bulan tujuan
		if ($ganti == '1') {
			$this->db->where([
				'bulan' => $bulan_tujuan,
				'cl_kelurahan_desa_id' => $cl_kelurahan_desa_id
			])->delete('tbl_data_rekap_bulanan');
		}

		// Salin data
		foreach ($data_asal as $row) {
			unset($row['id']); // auto increment
			$row['bulan'] = $bulan_tujuan;
			$row['tgl_cetak'] = $tgl_cetak ? date('Y-m-d', strtotime($tgl_cetak)) : date('Y-m-d');
			$row['create_date'] = date('Y-m-d H:i:s');
			$row['create_by'] = $this->auth['id'];
			$row['update_date'] = null;
			$row['update_by'] = null;

			$this->db->insert('tbl_data_rekap_bulanan', $row);
		}

		$this->db->trans_complete();

		if (!$this->db->trans_status()) {
			echo json_encode(['status' => false, 'message' => 'Gagal menyalin data!']);
		} else {
			echo json_encode(['status' => true, 'message' => 'Data berhasil disalin!']);
		}
	}

	//fungsi PBB dan NOP
	public function cek_nop_penduduk($id)
	{
		$cek = $this->db
			->select('nop')
			->from('tbl_data_penduduk')
			->where('id', $id)
			->get()
			->row();

		if (!$cek || trim($cek->nop) == "") {
			echo json_encode(["status" => "no_nop"]);
		} else {
			echo json_encode(["status" => "ada", "nop" => $cek->nop]);
		}
	}

	public function get_nop_by_nik()
	{
		$nik = $this->input->post('nik');

		$cek = $this->db->get_where('tbl_data_penduduk', ['nik' => $nik])->row();

		if ($cek && $cek->nop != '' && $cek->nop != null) {
			echo json_encode([
				'status' => true,
				'nop'    => $cek->nop
			]);
		} else {
			echo json_encode([
				'status' => false
			]);
		}
	}

	private function normalize_dashboard($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return [];
		}

		$result = [];

		foreach ($rows as $row) {

			// ambil label/nama apapun yang tersedia
			$nama = '';
			if (isset($row['nama'])) {
				$nama = $row['nama'];
			} elseif (isset($row['label'])) {
				$nama = $row['label'];
			} elseif (isset($row['kategori'])) {
				$nama = $row['kategori'];
			}

			// ambil jumlah / total
			$jumlah = 0;
			if (isset($row['jumlah'])) {
				$jumlah = (int)$row['jumlah'];
			} elseif (isset($row['total'])) {
				$jumlah = (int)$row['total'];
			}

			$result[] = [
				'nama'   => $nama,
				'jumlah' => $jumlah
			];
		}

		return $result;
	}

}


