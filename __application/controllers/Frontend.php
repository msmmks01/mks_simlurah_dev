<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("If-Modified-Since: Mon, 22 Jan 2008 00:00:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: private");
		header("Pragma: no-cache");
		
		$this->nsmarty->assign('acak', md5(date('H:i:s')) );
		$this->temp = "frontend/";
		$this->load->model('mfrontend');
		$this->load->library(array('encrypt','lib'));

		$array_setting = array(
			'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
			'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
			'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
			'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
		);
		$this->setting = $this->db->get("tbl_setting_apps")->row_array();
		
		if($this->setting){
			$this->nsmarty->assign("isi_setting", 'ada');
		}
		
		$this->nsmarty->assign("main_css", $this->lib->assetsmanager('css','main') );
		$this->nsmarty->assign("main_js", $this->lib->assetsmanager('js','main') );
		$this->nsmarty->assign("setting", $this->setting );
	}
	
	function index(){
		//if($this->auth){
			//$menu = $this->mfrontend->getdata('menu', 'variable');						$jenis_surat = $this->db->get('cl_jenis_surat')->result_array();			$this->nsmarty->assign('jenis_surat', $jenis_surat);
			//$this->nsmarty->assign('menu', $menu);
			$this->nsmarty->display( 'frontend/main-frontend.html');
		/*}else{
			$this->nsmarty->assign("main_css", $this->lib->assetsmanager('css','login') );
			$this->nsmarty->assign("main_js", $this->lib->assetsmanager('js','login') );
			$this->nsmarty->display( 'frontend/main-login.html');
		}*/
	}
	
	function modul($p1="",$p2=""){
		$temp = 'frontend/modul/'.$p1.'/'.$p2.'.html';
		if($this->auth){
			switch($p1){
				case "register":
					
				break;
			}
			
			$this->nsmarty->assign("main", $p1);
			$this->nsmarty->assign("mod", $p2);
			
			if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			else{$this->nsmarty->display($temp);}	
		}
	}	
	
	function get_grid($mod){
		$temp = 'frontend/grid_config.html';
		$filter = $this->combo_option($mod);
		$cekmenu = $this->db->get_where('tbl_user_menu', array('ref_tbl' => $mod) )->row_array();
		if($cekmenu){
			$id_modul = $cekmenu['id'];
			$judul = $cekmenu['nama_menu'];
		}else{
			$id_modul = 0;
			$judul = "Data Development";
		}

		switch($mod){
			case "laporan_penduduk":
				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "") ));
			break;
			case "laporan_persuratan":
				$this->nsmarty->assign("kelurahan", $this->lib->fillcombo("kelurahan_report", "return", ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : ""), ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0" ? $this->auth["cl_kelurahan_desa_id"] : "") ));
			break;
		}

		$prev = $this->mfrontend->getdata("previliges_menu", "row_array", $id_modul, $this->auth["cl_user_group_id"]);
		
		$this->nsmarty->assign('data_select', $filter);
		$this->nsmarty->assign('mod',$mod);
		$this->nsmarty->assign('judul',$judul);
		$this->nsmarty->assign('prev',$prev);
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	
	function get_grid_report($mod){
		$temp = 'frontend/__modul/grid_report_config.html';
		
		$cekmenu = $this->db->get_where('tbl_user_menu', array('ref_tbl' => $mod) )->row_array();
		if($cekmenu){
			$id_modul = $cekmenu['id'];
			$judul = $cekmenu['nama_menu'];
		}else{
			$id_modul = 0;
			$judul = "Data Development";
		}
		
		switch($mod){
			case "list_work_order":
				$filter = $this->combo_option($mod);
				$prev = $this->mfrontend->getdata("previliges_menu", "row_array", $id_modul, $this->auth["cl_user_group_id"]);
				
				$this->nsmarty->assign('upd', $this->lib->fillcombo('upd', 'return') );
				$this->nsmarty->assign('bo', $this->lib->fillcombo('staff_bo', 'return') );
				$this->nsmarty->assign('request_type', $this->lib->fillcombo('cl_request_type', 'return') );
				$this->nsmarty->assign('request_status', $this->lib->fillcombo('request_status_filter', 'return') );
				$this->nsmarty->assign('data_select', $filter);
				$this->nsmarty->assign('prev',$prev);
			break;
		}
		
		$this->nsmarty->assign('mod',$mod);
		$this->nsmarty->assign('judul',$judul);
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	
	function get_form($mod){
		$temp='frontend/form/'.$mod.".html";
		$sts = $this->input->post('editstatus');
		
		switch($mod){
			case "beranda_admin_filter":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				$array_penduduk = array(
					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'cl_kelurahan_desa_id' => $desa_id,
					'status_data'=>'AKTIF'
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
				
				$summary_persuratan = $this->mfrontend->getdata('summary_persuratan', 'result_array');
				
				$jenis_kelamin = $this->mfrontend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');
				$agama = $this->mfrontend->getdata_laporan('dashboard_agama', 'result_array');
				$range_umur = $this->mfrontend->getdata_laporan('dashboard_range_umur', 'result_array');
				
				$status_data = $this->mfrontend->getdata_laporan('dashboard_status_data', 'result_array');
				$status_kawin = $this->mfrontend->getdata_laporan('dashboard_status_kawin', 'result_array');
				$pendidikan = $this->mfrontend->getdata_laporan('dashboard_pendidikan', 'result_array');
			
				$data = array(
					'jumlah_penduduk' => $jumlah_penduduk,
					'jumlah_kk' => $jumlah_kk,
					'jumlah_surat' => $jumlah_surat,
					
					'summary_persuratan' => $summary_persuratan,
					
					'jenis_kelamin' => $jenis_kelamin,
					'agama' => $agama,
					'range_umur' => $range_umur,
					
					'status_data' => $status_data,
					'status_kawin' => $status_kawin,
					'pendidikan' => $pendidikan,
				);
				
				$this->nsmarty->assign('data',$data);
			break;
			case "beranda_admin":
				$jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', array('status_data'=>'AKTIF'))->num_rows();
				$jumlah_kk = $this->db->get('tbl_kartu_keluarga')->num_rows();
				$jumlah_surat = $this->db->get('tbl_data_surat')->num_rows();

				$summary_persuratan = $this->mfrontend->getdata('summary_persuratan', 'result_array');				
				$summary_penduduk = $this->mfrontend->getdata_laporan('dashboard_summary_penduduk', 'result_array');

				$jenis_kelamin = $this->mfrontend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');
				$agama = $this->mfrontend->getdata_laporan('dashboard_agama', 'result_array');
				$range_umur = $this->mfrontend->getdata_laporan('dashboard_range_umur', 'result_array');
				
				$status_data = $this->mfrontend->getdata_laporan('dashboard_status_data', 'result_array');
				$status_kawin = $this->mfrontend->getdata_laporan('dashboard_status_kawin', 'result_array');
				$pendidikan = $this->mfrontend->getdata_laporan('dashboard_pendidikan', 'result_array');
			
				$data = array(
					'jumlah_penduduk' => $jumlah_penduduk,
					'jumlah_kk' => $jumlah_kk,
					'jumlah_surat' => $jumlah_surat,
					
					'summary_persuratan' => $summary_persuratan,
					'summary_penduduk' => $summary_penduduk,
					
					'jenis_kelamin' => $jenis_kelamin,
					'agama' => $agama,
					'range_umur' => $range_umur,
					
					'status_data' => $status_data,
					'status_kawin' => $status_kawin,
					'pendidikan' => $pendidikan,
				);
				
				$this->nsmarty->assign('data',$data);
				$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return" ));
			break;
			case "beranda":
				$array_penduduk = array(
					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					'status_data'=>'AKTIF'
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
				);
				
				$jumlah_penduduk = $this->db->get_where('tbl_data_penduduk', $array_penduduk)->num_rows();
				$jumlah_kk = $this->db->get_where('tbl_kartu_keluarga', $array_kk)->num_rows();
				$jumlah_surat = $this->db->get_where('tbl_data_surat', $array_surat)->num_rows();
				
				$summary_persuratan = $this->mfrontend->getdata('summary_persuratan', 'result_array');
				
				$jenis_kelamin = $this->mfrontend->getdata_laporan('dashboard_jenis_kelamin', 'result_array');
				$agama = $this->mfrontend->getdata_laporan('dashboard_agama', 'result_array');
				$range_umur = $this->mfrontend->getdata_laporan('dashboard_range_umur', 'result_array');
				
				$status_data = $this->mfrontend->getdata_laporan('dashboard_status_data', 'result_array');
				$status_kawin = $this->mfrontend->getdata_laporan('dashboard_status_kawin', 'result_array');
				$pendidikan = $this->mfrontend->getdata_laporan('dashboard_pendidikan', 'result_array');
			
				$data = array(
					'jumlah_penduduk' => $jumlah_penduduk,
					'jumlah_kk' => $jumlah_kk,
					'jumlah_surat' => $jumlah_surat,
					
					'summary_persuratan' => $summary_persuratan,
					
					'jenis_kelamin' => $jenis_kelamin,
					'agama' => $agama,
					'range_umur' => $range_umur,
					
					'status_data' => $status_data,
					'status_kawin' => $status_kawin,
					'pendidikan' => $pendidikan,
				);
				
				$this->nsmarty->assign('data',$data);
			break;
			
			case "identitas_desa":
				$arraynya = array(
					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
				);

				$cekdata = $this->db->get_where('tbl_setting_apps', $arraynya)->row_array();
				if($cekdata){
					$sts = "edit";
					$this->nsmarty->assign('data',$cekdata);

					$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", ($sts == "edit" ? $cekdata["cl_provinsi_id"] : "") ));
					$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", ($sts == "edit" ? $cekdata["cl_kab_kota_id"] : "") ));
					$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", ($sts == "edit" ? $cekdata["cl_kecamatan_id"] : "") ));
					$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $cekdata["cl_kelurahan_desa_id"] : "") ));
				}else{

					$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", $this->auth["cl_provinsi_id"] ));
					$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", $this->auth["cl_kab_kota_id"] ));
					$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", $this->auth["cl_kecamatan_id"] ));
					$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", $this->auth["cl_kelurahan_desa_id"] ));
				}
				
				
			break;
			case "form_surat":
				$idx = $this->input->post('idx');
				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id'=>$idx) )->row_array();
				$this->nsmarty->assign("desa", $this->lib->fillcombo("cl_kelurahan_desa","return"));
				//echo "<pre>";print_r($this->setting);exit;
				switch($idx){
					case "22":
						$this->nsmarty->assign("bunyi_bunyian", $this->lib->fillcombo("ya_atau_tidak","return"));
						$this->nsmarty->assign("jalan_lorong", $this->lib->fillcombo("ya_atau_tidak","return"));
						$this->nsmarty->assign("tutup_sementara", $this->lib->fillcombo("ya_atau_tidak","return"));

						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));	
					break;
					case "11":
					case "10":
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_belum_menikah", "return"));	
					break;
					case "7":
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_anak", "return"));	
					break;
					case "6":
						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin","return"));
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));		
					break;
					case "43":
						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin","return"));
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));		
					break;
					case "1":
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk_belum_menikah", "return"));	
					break;
					default:
						$this->nsmarty->assign("nik_id", $this->lib->fillcombo("data_penduduk", "return"));				
					break;
				}
				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan","return",($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "") ));				$this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin","return",($sts == "edit" ? $data["status_kawin"] : "") ));				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama","return",($sts == "edit" ? $data["agama"] : "") ));				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin","return",($sts == "edit" ? $data["jenis_kelamin"] : "") ));				$this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan","return",($sts == "edit" ? $data["pendidikan"] : "") ));				$this->nsmarty->assign("status_data", $this->lib->fillcombo("status","return",($sts == "edit" ? $data["status_data"] : "") ));
				$this->nsmarty->assign('idx',$idx);
				$this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']) );
			break;
			case "buat_surat":
				$jenis_surat = $this->db->get('cl_jenis_surat')->result_array();
				$this->nsmarty->assign('jenis_surat', $jenis_surat);
			break;
			case "data_surat":
				if($sts=='edit'){
					$data = $this->db->get_where('tbl_data_surat', array('id'=>$this->input->post('id')) )->row_array();
					if($data['info_tambahan'] != ""){
						$data_info = json_decode($data['info_tambahan'], true);
						$this->nsmarty->assign('data_info',$data_info);
					}

					$this->nsmarty->assign('data',$data);
				}
				
				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id'=>$data['cl_jenis_surat_id']) )->row_array();
				
				switch($data['cl_jenis_surat_id']){
					case "11":
					case "10":
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_belum_menikah", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
					case "7":
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_anak", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
					case "6":
						$this->nsmarty->assign("jenis_kelamin_bayi", $this->lib->fillcombo("jenis_kelamin","return", ($sts == "edit" ? $data_info["jenis_kelamin_bayi"] : "") ));
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
					case "43":
						$this->nsmarty->assign("jenis_kelamin_anak", $this->lib->fillcombo("jenis_kelamin","return", ($sts == "edit" ? $data_info["jenis_kelamin_anak"] : "") ));
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
					case "1":
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk_belum_menikah", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
					default:
						$this->nsmarty->assign("nik", $this->lib->fillcombo("data_penduduk", "return", ($sts == "edit" ? $data["tbl_data_penduduk_id"] : "") ));
					break;
				}
				
				$this->nsmarty->assign("cl_jenis_surat_id", $this->lib->fillcombo("cl_jenis_surat", "return", ($sts == "edit" ? $data["cl_jenis_surat_id"] : "") ));
				$this->nsmarty->assign('idx',$data['cl_jenis_surat_id']);
				$this->nsmarty->assign('judul_surat', strtoupper($jenis_surat['jenis_surat']) );
			break;
			case 'surat_masuk':
				$jenis_surat = $this->db->get('cl_jenis_surat_masuk')->result_array();
				$this->nsmarty->assign('jenis_surat', $jenis_surat);
				break;
			case "data_keluarga":
				if($sts=='edit'){
					$data = $this->db->get_where('tbl_kartu_keluarga', array('id'=>$this->input->post('id')) )->row_array();
					if($data){
						$data_detail = $this->db->get_where('tbl_data_penduduk', array('no_kk'=>$data['no_kk']) )->result_array();
						$this->nsmarty->assign('detail', $data_detail);
					}
					$this->nsmarty->assign('data',$data);
				}
				$array_penduduk = array(
					'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
					'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
					'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
					'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
				//	'status_data'=>'AKTIF'
				);

				$data_hubungan = $this->db->get_where('cl_hubungan_keluarga')->result_array();
				$data_penduduk = $this->db->get_where('tbl_data_penduduk', $array_penduduk)->result_array();
				
				$this->nsmarty->assign("data_penduduk", $data_penduduk);
				$this->nsmarty->assign("combo_penduduk", $this->lib->fillcombo("data_penduduk", "return"));
				$this->nsmarty->assign("hubungan_keluarga", $data_hubungan);
				$this->nsmarty->assign("combo_hubungan_keluarga", $this->lib->fillcombo("hubungan_keluarga", "return") );
			break;
			case "data_penduduk":
				if($sts=='edit'){
					$data = $this->db->get_where('tbl_data_penduduk', array('id'=>$this->input->post('id')) )->row_array();
					$this->nsmarty->assign('data',$data);
				}
				
				$this->nsmarty->assign("cl_jenis_pekerjaan_id", $this->lib->fillcombo("cl_jenis_pekerjaan","return",($sts == "edit" ? $data["cl_jenis_pekerjaan_id"] : "") ));
				$this->nsmarty->assign("status_kawin", $this->lib->fillcombo("cl_status_kawin","return",($sts == "edit" ? $data["status_kawin"] : "") ));
				$this->nsmarty->assign("agama", $this->lib->fillcombo("cl_agama","return",($sts == "edit" ? $data["agama"] : "") ));
				$this->nsmarty->assign("jenis_kelamin", $this->lib->fillcombo("jenis_kelamin","return",($sts == "edit" ? $data["jenis_kelamin"] : "") ));
				$this->nsmarty->assign("pendidikan", $this->lib->fillcombo("cl_pendidikan","return",($sts == "edit" ? $data["pendidikan"] : "") ));
				$this->nsmarty->assign("status_data", $this->lib->fillcombo("status","return",($sts == "edit" ? $data["status_data"] : "") ));
			break;
			
			case "form_user_role":
				$id_role = $this->input->post('id');
				$array = array();
				$dataParent = $this->mfrontend->getdata('menu_parent', 'result_array');
				foreach($dataParent as $k=>$v){
					$dataChild = $this->mfrontend->getdata('menu_child', 'result_array', $v['id']);
					$dataPrev = $this->mfrontend->getdata('previliges_menu', 'row_array', $v['id'], $id_role);
					
					$array[$k]['id'] = $v['id'];
					$array[$k]['nama_menu'] = $v['nama_menu'];
					$array[$k]['type_menu'] = $v['type_menu'];
					$array[$k]['id_prev'] = (isset($dataPrev['id']) ? $dataPrev['id'] : 0) ;
					$array[$k]['buat'] = (isset($dataPrev['buat']) ? $dataPrev['buat'] : 0) ;
					$array[$k]['baca'] = (isset($dataPrev['baca']) ? $dataPrev['baca'] : 0);
					$array[$k]['ubah'] = (isset($dataPrev['ubah']) ? $dataPrev['ubah'] : 0);
					$array[$k]['hapus'] = (isset($dataPrev['hapus']) ? $dataPrev['hapus'] : 0);
					$array[$k]['child_menu'] = array();
					$jml = 0;
					foreach($dataChild as $y => $t){
						$dataPrevChild = $this->mfrontend->getdata('previliges_menu', 'row_array', $t['id'], $id_role);
						$array[$k]['child_menu'][$y]['id_child'] = $t['id'];
						$array[$k]['child_menu'][$y]['nama_menu_child'] = $t['nama_menu'];
						$array[$k]['child_menu'][$y]['type_menu'] = $t['type_menu'];
						$array[$k]['child_menu'][$y]['id_prev'] = (isset($dataPrevChild['id']) ? $dataPrevChild['id'] : 0) ;
						$array[$k]['child_menu'][$y]['buat'] = (isset($dataPrevChild['buat']) ? $dataPrevChild['buat'] : 0) ;
						$array[$k]['child_menu'][$y]['baca'] = (isset($dataPrevChild['baca']) ? $dataPrevChild['baca'] : 0) ;
						$array[$k]['child_menu'][$y]['ubah'] = (isset($dataPrevChild['ubah']) ? $dataPrevChild['ubah'] : 0) ;
						$array[$k]['child_menu'][$y]['hapus'] = (isset($dataPrevChild['hapus']) ? $dataPrevChild['hapus'] : 0) ;
						$jml++;
						
						if($t['type_menu'] == 'CHC'){
							$array[$k]['child_menu'][$y]['sub_child_menu'] = array();
							$dataSubChild = $this->mfrontend->getdata('menu_child_2', 'result_array', $t['id']);
							$jml_sub_child = 0;
							foreach($dataSubChild as $x => $z){
								$dataPrevSubChild = $this->mfrontend->getdata('previliges_menu', 'row_array', $z['id'], $id_role);
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['id_sub_child'] = $z['id'];
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['nama_menu_sub_child'] = $z['nama_menu'];
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['id_prev'] = (isset($dataPrevSubChild['id']) ? $dataPrevSubChild['id'] : 0) ;
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['buat'] = (isset($dataPrevSubChild['buat']) ? $dataPrevSubChild['buat'] : 0) ;
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['baca'] = (isset($dataPrevSubChild['baca']) ? $dataPrevSubChild['baca'] : 0) ;
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['ubah'] = (isset($dataPrevSubChild['ubah']) ? $dataPrevSubChild['ubah'] : 0) ;
								$array[$k]['child_menu'][$y]['sub_child_menu'][$x]['hapus'] = (isset($dataPrevSubChild['hapus']) ? $dataPrevSubChild['hapus'] : 0) ;
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
				if($sts=='edit'){
					$data = $this->db->get_where('cl_user_group', array('id'=>$this->input->post('id')) )->row_array();
					$this->nsmarty->assign('data',$data);
				}
			break;
			case "user_mng":
				if($sts=='edit'){
					$data = $this->db->get_where('tbl_user', array('id'=>$this->input->post('id')) )->row_array();
					$data["password"] = $this->encrypt->decode($data["password"]);
					$this->nsmarty->assign('data',$data);
				}

				$this->nsmarty->assign("cl_user_group_id", $this->lib->fillcombo("cl_user_group","return",($sts == "edit" ? $data["cl_user_group_id"] : "") ));
				$this->nsmarty->assign("cl_provinsi_id", $this->lib->fillcombo("cl_provinsi", "return", ($sts == "edit" ? $data["cl_provinsi_id"] : "") ));
				$this->nsmarty->assign("cl_kab_kota_id", $this->lib->fillcombo("cl_kab_kota", "return", ($sts == "edit" ? $data["cl_kab_kota_id"] : "") ));
				$this->nsmarty->assign("cl_kecamatan_id", $this->lib->fillcombo("cl_kecamatan", "return", ($sts == "edit" ? $data["cl_kecamatan_id"] : "") ));
				$this->nsmarty->assign("cl_kelurahan_desa_id", $this->lib->fillcombo("cl_kelurahan_desa", "return", ($sts == "edit" ? $data["cl_kelurahan_desa_id"] : "") ));
			break;
			default:
				if($sts=='edit'){
					$table = $this->input->post("ts");
					$data = $this->db->get_where("tbl_".$table, array('id'=>$this->input->post('id')) )->row_array();
					$this->nsmarty->assign('data',$data);
				}
			break;
		}
		
		if(isset($sts)){
			$this->nsmarty->assign('sts',$sts);
		}else{
			$sts = "";
		}
		
		$this->nsmarty->assign('mod',$mod);
		$this->nsmarty->assign('temp',$temp);
		
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
		
	}	
	
	function getdisplay($type){
		switch($type){
			case "combo_penduduk":
				echo $this->lib->fillcombo("data_penduduk", "return");
			break;
			case "hapusfoto_galeri":
				$id = htmlspecialchars($this->input->post('id'), ENT_QUOTES);
				$filename = htmlspecialchars($this->input->post('filename'), ENT_QUOTES);
				$upload_path = "./__repository/gallery/";
				
				if(file_exists($upload_path.$filename)){
					$this->db->delete('tbl_gallery_detail', array('tbl_gallery_id'=>$id) );
					unlink($upload_path.$filename);
					echo 1;
				}
			break;
			case "hapusfoto_lelang":
				$id = htmlspecialchars($this->input->post('id'), ENT_QUOTES);
				$filename = htmlspecialchars($this->input->post('filename'), ENT_QUOTES);
				$upload_path = "./__repository/lelang/";
				
				if(file_exists($upload_path.$filename)){
					$this->db->delete('tbl_lelang_foto', array('tbl_lelang_id'=>$id) );
					unlink($upload_path.$filename);
					echo 1;
				}
			break;
			case "ceksession":
				if($this->auth){
					echo 1;
				}else{
					echo 2;
				}
			break;
		}
	}
	
	function getdata($p1,$p2="",$p3=""){
		echo $this->mfrontend->getdata($p1,'json',$p3);
	}
	
	function simpandata($p1="",$p2=""){
		//print_r($_POST);exit;
		
		if($this->input->post('mod'))$p1=$this->input->post('mod');
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				$post[$k] = $this->input->post($k);
			}else{
				$post[$k] = null;
			}

			//$post[$k] = str_replace("'", "’", $post[$k]);
			$post[$k] = str_replace("'", "&#96;", $post[$k]);
			$post[$k] = str_replace("’", "&#96;", $post[$k]);
		}
		
		if(isset($post['editstatus'])){$editstatus = $post['editstatus'];unset($post['editstatus']);}
		else $editstatus = $p2;
		
		echo $this->mfrontend->simpandata($p1, $post, $editstatus);
	}
	
	function test(){
		echo $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME']);exit;
	}
	
	function combo_option($mod){
		$opt="";
		switch($mod){
			case "data_surat":
				$opt .="<option value='A.nama_pemohon'>Nama Pemohon</option>";
				$opt .="<option value='B.nama_lengkap'>Nama Dalam Surat</option>";
				$opt .="<option value='B.nik'>NIK Surat</option>";
			break;
			case "data_keluarga":
				$opt .="<option value='A.no_kk'>No. KK</option>";
				$opt .="<option value='A.B.nama_lengkap'>Nama Kepala Keluarga</option>";
			break;
			case "data_penduduk":
				$opt .="<option value='A.nik'>NIK</option>";
				$opt .="<option value='A.nama_lengkap'>Nama Lengkap</option>";
				$opt .="<option value='A.no_kk'>No. KK</option>";
			break;
			
			case "user_group":
				$opt .="<option value='A.user_group'>User Group Name</option>";
			break;
			case "user_mng":
				$opt .="<option value='A.username'>Username</option>";
				$opt .="<option value='A.nama_lengkap'>Real Name</option>";
			break;
			default:
				$txt = str_replace("_", " ", $mod);
				$opt .="<option value='A.".$mod."'>".strtoupper($txt)."</option>";
			break;
		}
		
		return $opt;
	}
	
	function cetak($mod, $p1="", $p2="", $p3=""){
		switch($mod){
			case "laporan_penduduk":
				$data = $this->mfrontend->getdata('laporan_penduduk', 'result_array');
				$filename = "laporan-penduduk-".date('YmdHis');
				$temp = "frontend/cetak/laporan_penduduk.html";
				$this->hasil_output('pdf',$mod,$data,$filename,$temp,"A4-L");

				//echo "<pre>";
				//print_r($data);exit;
			break;
			case "laporan_persuratan":
				$data = $this->mfrontend->getdata('laporan_persuratan', 'result_array');
				$filename = "laporan-persuratan-".date('YmdHis');
				$temp = "frontend/cetak/laporan_persuratan.html";
				$this->hasil_output('pdf',$mod,$data,$filename,$temp,"A4");
			break;
			case "cetak_surat":
				$data = $this->mfrontend->getdata('cetak_surat', 'variable', $p1, $p2, $p3);
				$jenis_surat = $this->db->get_where('cl_jenis_surat', array('id'=>$p1) )->row_array();
				$filename = str_replace(" ", "_", $jenis_surat['jenis_surat'])."_".$p2;
				$temp = "frontend/cetak/surat_".$p1.".html";
				$this->hasil_output('pdf',$mod,$data,$filename,$temp,"A4");
				
				//echo "<pre>";
				//print_r($data);exit;
			break;
			case "qrcode":
				$this->load->library("encrypt");
				$p1 = $this->lib->base64url_decode($p1);
				if($p1){
					$data = $this->mfrontend->getdata('tbl_metering', 'row_array', $p1);
					$filename = $data["no_serial"];
					$this->hasil_output('pdf',$mod,$data,$filename,"A7-L");
				}else{
					echo "Invalid ID : Tutup Tab ini pada Browser Dan Generate Kembali";
				}
			break;
		}
	}
	
	function hasil_output($p1,$mod,$data,$filename,$temp,$ukuran="A4"){
		switch($p1){
			case "pdf":
				$this->load->library('mlpdf');	
				$pdf = $this->mlpdf->load();
				
				$this->nsmarty->assign('data', $data);
				$this->nsmarty->assign('mod', $mod);
				
				$htmlcontent = $this->nsmarty->fetch($temp);
				
				//echo $htmlcontent;exit;
				
				$spdf = new mPDF('', $ukuran, 0, '', 10, 10, 10, 15, 0, 0, 'P');
				$spdf->ignore_invalid_utf8 = true;
				// bukan sulap bukan sihir sim salabim jadi apa prok prok prok
				$spdf->allow_charset_conversion = true;     // which is already true by default
				$spdf->charset_in = 'iso-8859-1';  // set content encoding to iso
				$spdf->SetDisplayMode('fullpage');		
				//$spdf->SetHTMLHeader($htmlheader);
				//$spdf->keep_table_proportions = true;
				$spdf->useSubstitutions=false;
				$spdf->simpleTables=true;
				
				
				$spdf->SetHTMLFooter('
					<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
						<table width="100%" style="font-family:arial; font-size:8px;">
							<tr>
								<td width="30%" align="left">
									<img src="'.$this->host.'__repository/qrcode/qrcodepantesonline.png" width="50px" />
								</td>
								<td width="40%" align="center">
									<i>Dicetak menggunakan aplikasi <br/> Sistem Informasi Kelurahan (SIL - ONLINE)</i>
								</td>
								<td width="30%" align="right">
									Hal. {PAGENO}
								</td>
							</tr>
						</table>
					</div>
				');				
				
				//$file_name = date('YmdHis');
				$spdf->SetProtection(array('print'));				
				$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
				//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
				//$spdf->Output('repositories/Billing/'.$filename.'.pdf', 'F');
				$spdf->Output($filename.'.pdf', 'I'); // view file	
			break;
		}
	}
	
	function downloadfile(){
		$this->load->helper('download');
		$filenya = $this->input->post("filenya");
		$log['aktivitas']="Download File Name <b>".$this->input->post("namafile")."</b> oleh ".$this->auth['nama_user'];
		$log['data_id'] = $this->input->post("id");
		$log['flag_tbl']="tbl_upload_file";
		$log['create_date'] = date('Y-m-d H:i:s');
		$log['create_by'] = $this->auth['nama_user'];
		$this->db->insert('tbl_log', $log);
		
		force_download($filenya, NULL);
	}
	
	function getauth(){
		$data = $this->db->get('tbl_user')->result_array();
		$html = "";
		foreach($data as $k => $v){
			$html .= $v['username']." - ".$this->encrypt->decode($v['password'])."<br/>";
		}

		echo $html;
	}
	
	function mappingrole(){
		$sql = "
			SELECT id
			FROM tbl_user_menu
		";
		$data = $this->db->query($sql)->result_array();
		foreach($data as $k => $v){
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
	
	
}
