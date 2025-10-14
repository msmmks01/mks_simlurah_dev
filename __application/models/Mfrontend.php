<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}
//
class Mfrontend extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('s3ntr4lb0')));
		$this->setting = $this->db->get("tbl_setting_apps")->row_array();
	}
	
	function getdata($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where  = " WHERE 1=1 ";
		$where2 = "";
		
		$dbdriver = $this->db->dbdriver;
		if($dbdriver == "postgre"){
			$select = " ROW_NUMBER() OVER (ORDER BY A.id DESC) as rowID, ";
		}else{
			$select = "";
		}
		
		if($this->input->post('key')){
			$key = $this->input->post('key');
			$kat = $this->input->post('kat');
			
			$where .= " AND LOWER(".$kat.") like '%".strtolower(trim($key))."%' ";				
		}
		
		if($this->auth["cl_user_group_id"] == '4'){
			//$where .= " AND A.create_by = '".$this->auth["nama_lengkap"]."' ";
		}

		switch($type){
			case "data_login":
				$sql = "
					SELECT A.*,B.user_group,
						D.nama as nama_kab_kota, E.nama as nama_kecamatan,
						F.nama as nama_desa 
					FROM tbl_user A 
					LEFT JOIN cl_user_group B ON A.cl_user_group_id = B.id 
					LEFT JOIN cl_provinsi C ON C.id = A.cl_provinsi_id
					LEFT JOIN cl_kab_kota D ON D.id = A.cl_kab_kota_id
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					WHERE A.username = '".$p1."' 
					ORDER BY A.id DESC
				";
			break;
			
			// Modul Laporan
			case "laporan_penduduk":
				$desa_id = $this->input->post('kelurahan_id');
				$rt = $this->input->post('rt');
				$rw = $this->input->post('rw');
				$rt_get = $this->input->get('rt');
				$rw_get = $this->input->get('rw');

				if($rt_get){
					$where .= "and A.rt like '%".$rt_get."%'";
				}
				if($rw_get){
					$where .= "and A.rw like '%".$rw_get."%'";
				}
				
				if($rt){
					$where .= "and A.rt like '%".$rt."%'";
				}
				if($rw){
					$where .= "and A.rw like '%".$rw."%'";
				}

				if($desa_id){
					$where .= "and A.cl_kelurahan_desa_id = '".$desa_id."'";
				}else{
					if($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0"){
						$where .= "and A.cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
					}

					if($this->input->get('kelurahan_id')){
						$where .= "and A.cl_kelurahan_desa_id = '".$this->input->get('kelurahan_id')."'";
					}
				}

				$sql = "
					SELECT A.*, 
						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,
						E.nama as kecamatan, F.nama as kelurahan,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat 
					FROM tbl_data_penduduk A
					LEFT JOIN cl_agama B ON B.id = A.agama
					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					$where 
					and A.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
					and A.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
					and A.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					ORDER BY A.id DESC
				";

				//echo $sql;exit;
			break;
			case "laporan_persuratan":
				if($this->input->get('tgl_mulai')){
					$date_start = $this->input->get('tgl_mulai');
					$date_end = $this->input->get('tgl_selesai');

					if(isset($date_start) && isset($date_end)){
						if($date_start != "" && $date_end != ""){
							$where .= " AND a.tgl_surat BETWEEN '".$date_start."' AND '".$date_end."' ";
						}
					}
				}
				if($this->input->post('tgl_mulai')){
					$date_start = $this->input->post('tgl_mulai');
					$date_end = $this->input->post('tgl_selesai');
					
					if(isset($date_start) && isset($date_end)){
						if($date_start != "" && $date_end != ""){
							$where .= " AND a.tgl_surat BETWEEN '".$date_start."' AND '".$date_end."' ";
						}
					}
				}

				$desa_id = $this->input->post('kelurahan_id');
				if($desa_id){
					$where .= "and a.cl_kelurahan_desa_id = '".$desa_id."'";
				}else{
					if($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0"){
						$where .= "and a.cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
					}

					if($this->input->get('kelurahan_id')){
						$where .= "and a.cl_kelurahan_desa_id = '".$this->input->get('kelurahan_id')."'";
					}
				}

				$sql = "
					select a.*, b.jenis_surat, c.nama as nama_kelurahan_desa,
						d.nama_lengkap,
						CONCAT(
							CASE DAYOFWEEK(a.tgl_surat)
								WHEN 1 THEN 'Minggu'
								WHEN 2 THEN 'Senin'
								WHEN 3 THEN 'Selasa'
								WHEN 4 THEN 'Rabu'
								WHEN 5 THEN 'Kamis'
								WHEN 6 THEN 'Jumat'
								WHEN 7 THEN 'Sabtu'
							END, ', ',
							DATE_FORMAT(a.tgl_surat, '%d-%m-%Y')
						) as tanggal_layanan
					from tbl_data_surat a
					left join cl_jenis_surat b on b.id = a.cl_jenis_surat_id
					left join cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
					left join tbl_data_penduduk d on d.id = a.tbl_data_penduduk_id
					$where 
					and a.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
					and a.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
					and a.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					ORDER BY a.tgl_surat ASC
				";

				//echo $sql;exit;
			break;
			// End Modul Laporan
			
			//Dashboard
			case "summary_persuratan":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}

				$sql = "
					SELECT A.jenis_surat, B.total 
					FROM cl_jenis_surat A
					LEFT JOIN (
						SELECT COUNT(id) as total, cl_jenis_surat_id
						FROM tbl_data_surat
						$where
						GROUP BY cl_jenis_surat_id
					) B ON B.cl_jenis_surat_id = A.id
				";
			break;
			//End Dashboard
			
			case "cetak_surat":
				$dbapak = array();
				$dibu = array();
				
				$sql = "
					SELECT A.*, B.nama_pekerjaan, C.nama_agama as agama,
						D.nama_status_kawin as status_kawin
					FROM tbl_data_penduduk A
					LEFT JOIN cl_jenis_pekerjaan B ON B.id = A.cl_jenis_pekerjaan_id
					LEFT JOIN cl_agama C ON C.id = A.agama
					LEFT JOIN cl_status_kawin D ON D.id = A.status_kawin
					WHERE A.id = '".$p2."'
				";
				$data = $this->db->query($sql)->row_array();
				if($data){
					$sbapak = "
						SELECT A.*, B.nama_pekerjaan, C.nama_agama as agama,
							D.nama_status_kawin as status_kawin
						FROM tbl_data_penduduk A
						LEFT JOIN cl_jenis_pekerjaan B ON B.id = A.cl_jenis_pekerjaan_id
						LEFT JOIN cl_agama C ON C.id = A.agama
						LEFT JOIN cl_status_kawin D ON D.id = A.status_kawin
						WHERE A.no_kk = '".$data['no_kk']."' AND A.cl_status_hubungan_keluarga_id = '1'
					";
					$dbapak = $this->db->query($sbapak)->row_array();
					
					$sibu = "
						SELECT A.*, B.nama_pekerjaan, C.nama_agama as agama,
							D.nama_status_kawin as status_kawin
						FROM tbl_data_penduduk A
						LEFT JOIN cl_jenis_pekerjaan B ON B.id = A.cl_jenis_pekerjaan_id
						LEFT JOIN cl_agama C ON C.id = A.agama
						LEFT JOIN cl_status_kawin D ON D.id = A.status_kawin
						WHERE A.no_kk = '".$data['no_kk']."' AND A.cl_status_hubungan_keluarga_id = '2'
					";
					$dibu = $this->db->query($sibu)->row_array();
				}
				
				$ssurat = "
					SELECT A.*, 
						DATE_FORMAT(A.tgl_surat, '%d-%m-%Y') as tanggal_surat 
					FROM tbl_data_surat A
					WHERE A.id = '".$p3."'
				";
				// A.cl_jenis_surat_id = '".$p1."' AND A.nik = '".$p2."'
				$dsurat = $this->db->query($ssurat)->row_array();
				if($dsurat['info_tambahan'] != ""){
					$dsurat['info_tambahan'] = json_decode($dsurat['info_tambahan'], true);
				}
				
				$array['pemohon'] = $data;
				$array['bapak'] = $dbapak;
				$array['ibu'] = $dibu;
				$array['surat'] = $dsurat;
			break;
			case "data_surat":
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and A.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and A.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and A.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and A.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and A.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and A.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and A.cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT A.*, B.nama_lengkap, B.nik as nik_id, C.jenis_surat,
						E.nama as kecamatan, F.nama as kelurahan,
						DATE_FORMAT(A.tgl_surat, '%d-%m-%Y') as tanggal_surat, 
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat 
					FROM tbl_data_surat A
					LEFT JOIN tbl_data_penduduk B ON B.id = A.tbl_data_penduduk_id
					LEFT JOIN cl_jenis_surat C ON C.id = A.cl_jenis_surat_id
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					$where
					ORDER BY A.id DESC
				";
			break;
			case "data_keluarga":
				if($this->auth['cl_user_group_id'] == 3){ // User Kecamatan
					$where .= "
						and A.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and A.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and A.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){ // User kelurahan
					$where .= "
						and A.cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and A.cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and A.cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and A.cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT A.*, B.nama_lengkap as nama_kepala_keluarga,
						C.total as jumlah_anggota_keluarga,
						E.nama as kecamatan, F.nama as kelurahan,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat 
					FROM tbl_kartu_keluarga A
					LEFT JOIN (
						SELECT no_kk, nama_lengkap
						FROM tbl_data_penduduk
						WHERE cl_status_hubungan_keluarga_id = '1'
					) B ON B.no_kk = A.no_kk
					LEFT JOIN (
						SELECT no_kk, COUNT(id) as total
						FROM tbl_data_penduduk
						GROUP BY no_kk
					) C ON C.no_kk = A.no_kk
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					$where 
					ORDER BY A.id DESC
				";
			break;
			case "data_penduduk":
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				$sql = "
					SELECT A.*, 
						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,
						E.nama as kecamatan, F.nama as kelurahan,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat 
					FROM tbl_data_penduduk A
					LEFT JOIN cl_agama B ON B.id = A.agama
					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					$where 
					ORDER BY A.id DESC
				";
			break;
			
			// Modul User Management
			case "tbl_user":
				$sql = "
					SELECT A.*, B.user_group
					FROM tbl_user A
					LEFT JOIN cl_user_group B ON B.id = A.cl_user_group_id
					$where
				";
			break;
			case "menu":
				$sql = "
					SELECT a.tbl_menu_id, b.nama_menu, b.type_menu, b.icon_menu, b.url, b.ref_tbl
						FROM tbl_user_prev_group a
					LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id 
					WHERE a.cl_user_group_id=".$this->auth['cl_user_group_id']." 
					AND (b.type_menu='P' OR b.type_menu='PC') AND b.status='1'
					ORDER BY b.urutan ASC
				";
				
				$parent = $this->db->query($sql)->result_array();
				$menu = array();
				foreach($parent as $v){
					$menu[$v['tbl_menu_id']]=array();
					$menu[$v['tbl_menu_id']]['parent']=$v['nama_menu'];
					$menu[$v['tbl_menu_id']]['icon_menu']=$v['icon_menu'];
					$menu[$v['tbl_menu_id']]['url']=$v['url'];
					$menu[$v['tbl_menu_id']]['type_menu']=$v['type_menu'];
					$menu[$v['tbl_menu_id']]['judul_kecil']=$v['ref_tbl'];
					$menu[$v['tbl_menu_id']]['child']=array();
					$sql="
						SELECT a.tbl_menu_id, b.nama_menu, b.url, b.icon_menu , b.type_menu, b.ref_tbl
							FROM tbl_user_prev_group a
						LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id 
						WHERE a.cl_user_group_id=".$this->auth['cl_user_group_id']." 
						AND (b.type_menu = 'C' OR b.type_menu = 'CHC') 
						AND b.status='1' AND b.parent_id=".$v['tbl_menu_id']." 
						ORDER BY b.urutan ASC
						";
					$child = $this->db->query($sql)->result_array();
					foreach($child as $x){
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]=array();
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['menu']=$x['nama_menu'];
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['type_menu']=$x['type_menu'];
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['url']=$x['url'];
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['icon_menu']=$x['icon_menu'];
						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['judul_kecil']=$x['ref_tbl'];
						
						if($x['type_menu'] == 'CHC'){
							$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'] = array();
							$sqlSubChild="
								SELECT a.tbl_menu_id, b.nama_menu, b.url, b.icon_menu , b.type_menu, b.ref_tbl
									FROM tbl_user_prev_group a
								LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id 
								WHERE a.cl_user_group_id=".$this->auth['cl_user_group_id']." 
								AND b.type_menu = 'CC'
								AND b.parent_id_2 = ".$x['tbl_menu_id']."
								AND b.status='1' 
								ORDER BY b.urutan ASC
							";
							$SubChild = $this->db->query($sqlSubChild)->result_array();
							foreach($SubChild as $z){
								$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'][$z['tbl_menu_id']]['sub_menu'] = $z['nama_menu'];
								$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'][$z['tbl_menu_id']]['type_menu'] = $z['type_menu'];
								$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'][$z['tbl_menu_id']]['url'] = $z['url'];
								$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'][$z['tbl_menu_id']]['icon_menu'] = $z['icon_menu'];
							}
						}
						
					}
				}
				
				/*
				echo "<pre>";
				print_r($menu);exit;
				//*/
				
				$array = $menu;
			break;		

			case "menu_parent":
				$sql = "
					SELECT A.*
					FROM tbl_user_menu A
					WHERE (A.type_menu = 'P' OR A.type_menu = 'PC') AND A.status = '1'
					ORDER BY A.urutan ASC
				";
			break;
			case "menu_child":
				$sql = "
					SELECT A.*
					FROM tbl_user_menu A
					WHERE (A.type_menu = 'C') AND A.parent_id = '".$p1."' AND A.status = '1'
					ORDER BY A.urutan ASC
				";
			break;
			case "menu_child_2":
				$sql = "
					SELECT A.*
					FROM tbl_user_menu A
					WHERE A.type_menu = 'CC' AND A.parent_id_2 = '".$p1."' AND A.status = '1'
					ORDER BY A.urutan ASC
				";
			break;
			case "previliges_menu":
				$sql = "
					SELECT A.*
					FROM tbl_user_prev_group A
					WHERE A.tbl_menu_id = '".$p1."' AND A.cl_user_group_id = '".$p2."'
				";
			break;		
			// End Modul User Management
			
			
			default:
				if($balikan=='get'){$where .=" AND A.id=".$this->input->post('id');}
				$sql="
					SELECT A.*
					FROM ".$type." A ".$where."
				";
				if($balikan=='get')return $this->db->query($sql)->row_array();
			break;
		}
		
		if($balikan == 'json'){
			return $this->lib->json_grid($sql,$type);
		}elseif($balikan == 'row_array'){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == 'result'){
			return $this->db->query($sql)->result();
		}elseif($balikan == 'result_array'){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == 'json_encode'){
			$data = $this->db->query($sql)->result_array(); 
			return json_encode($data);
		}elseif($balikan == 'variable'){
			return $array;
		}
		
	}
	
	function getdata_laporan($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where = "WHERE 1=1 ";

		switch($type){
			case "dashboard_summary_penduduk":
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				
				$sql = "
					SELECT A.nama, COALESCE(B.total, 0 ) as total
					FROM cl_kelurahan_desa A
					LEFT JOIN (
						SELECT COUNT(id) as total, cl_kelurahan_desa_id
						FROM tbl_data_penduduk
						$where and status_data = 'AKTIF'
						GROUP BY cl_kelurahan_desa_id
					) B ON B.cl_kelurahan_desa_id = A.id
					where A.kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
				";

				// /echo $sql;exit;
			break;
			case "dashboard_pendidikan":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}

				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}

				$sql = "
					SELECT A.nama_pendidikan as keterangan, A.color,
						COALESCE(B.total, 0 ) as total
					FROM cl_pendidikan A
					LEFT JOIN (
						SELECT COUNT(id) as total, pendidikan
						FROM tbl_data_penduduk
						$where and status_data = 'AKTIF'
						GROUP BY pendidikan
					) B ON B.pendidikan = A.id
				";
			break;
			case "dashboard_status_kawin":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT A.nama_status_kawin as keterangan, A.color,
						COALESCE(B.total, 0 ) as total
					FROM cl_status_kawin A
					LEFT JOIN (
						SELECT COUNT(id) as total, status_kawin
						FROM tbl_data_penduduk
						$where and status_data = 'AKTIF'
						GROUP BY status_kawin
					) B ON B.status_kawin = A.id
				";
			break;
			case "dashboard_status_data":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT COUNT(id) as total, 'AKTIF' as keterangan,
						'#FF3200' as color
					FROM tbl_data_penduduk
					$where and status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, 'MENINGGAL DUNIA' as keterangan,
						'#01FF25' as color
					FROM tbl_data_penduduk
					$where and status_data = 'MENINGGAL DUNIA'
					UNION ALL
					SELECT COUNT(id) as total, 'PINDAH DOMISILI' as keterangan,
						'#EFFF00' as color
					FROM tbl_data_penduduk
					$where and status_data = 'PINDAH DOMISILI'
				";
			break;
			case "dashboard_range_umur":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT COUNT(id) as total, '0 - 5' as keterangan,
						'#D30102' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 0 AND 5
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '6 - 10' as keterangan,
						'#3EDE37' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 6 AND 10
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '11 - 15' as keterangan,
						'#FEF200' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 11 AND 15
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '16 - 20' as keterangan,
						'#376FDE' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 16 AND 20
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '21 - 30' as keterangan,
						'#FFAA00' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 21 AND 30
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '31 - 40' as keterangan,
						'#CDCDCD' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 31 AND 40
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '41 - 50' as keterangan,
						'#9ACD32' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 41 AND 50
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '51 - 60' as keterangan,
						'#FF6347' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 51 AND 60
					AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, '> 60' as keterangan,
						'#EE82EE' as color
					FROM tbl_data_penduduk
					$where and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) > 60
					AND status_data = 'AKTIF'
				";
			break;
			case "dashboard_agama":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT A.nama_agama as keterangan, A.color,
						COALESCE(B.total, 0 ) as total
					FROM cl_agama A
					LEFT JOIN (
						SELECT COUNT(id) as total, agama
						FROM tbl_data_penduduk
						$where and status_data = 'AKTIF'
						GROUP BY agama
					) B ON B.agama = A.id
				";
			break;
			case "dashboard_jenis_kelamin":
				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');
				if($desa_id){
					$where .= "and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'";
				}
				
				if($this->auth['cl_user_group_id'] == 3){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					";
				}
				if($this->auth['cl_user_group_id'] == 2){
					$where .= "
						and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
						and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
						and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
						and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
					";
				}
				
				$sql = "
					SELECT COUNT(id) as total, 'LAKI-LAKI' as keterangan,
						'#FFAA00' as color
					FROM tbl_data_penduduk
					$where and jenis_kelamin = 'LAKI-LAKI' AND status_data = 'AKTIF'
					UNION ALL
					SELECT COUNT(id) as total, 'PEREMPUAN' as keterangan,
						'#376FDE' as color
					FROM tbl_data_penduduk
					$where and jenis_kelamin = 'PEREMPUAN' AND status_data = 'AKTIF'
				";
			break;
		}
		
		if($balikan == 'json'){
			return $this->lib->json_grid($sql,$type);
		}elseif($balikan == 'row_array'){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == 'result'){
			return $this->db->query($sql)->result();
		}elseif($balikan == 'result_array'){
			return $this->db->query($sql)->result_array();
		}elseif($balikan == 'json_encode'){
			$data = $this->db->query($sql)->result_array(); 
			return json_encode($data);
		}elseif($balikan == 'variable'){
			return $array;
		}
	}
	
	function get_combo($type="", $p1="", $p2="", $p3="", $p4=""){
		$where = "where 1=1 ";
		switch($type){
			case "kelurahan_report":
			case "cl_kelurahan_desa":
				if($p2 != ""){
					$where .= "
						and id = '".$p2."'
					";
				}
				
				$sql = "
					SELECT id, nama as txt
					FROM cl_kelurahan_desa
					$where
				";
			break;
			case "cl_kecamatan":
				$sql = "
					SELECT id, nama as txt
					FROM cl_kecamatan
				";
			break;
			case "cl_kab_kota":
				$sql = "
					SELECT id, nama as txt
					FROM cl_kab_kota
				";
			break;
			case "cl_provinsi":
				$sql = "
					SELECT id, nama as txt
					FROM cl_provinsi
				";
			break;
			
			case "cl_pendidikan":
				$sql = "
					SELECT id, nama_pendidikan as txt
					FROM cl_pendidikan
				";
			break;
			case "cl_agama":
				$sql = "
					SELECT id, nama_agama as txt
					FROM cl_agama
				";
			break;
			case "cl_status_kawin":
				$sql = "
					SELECT id, nama_status_kawin as txt
					FROM cl_status_kawin
				";
			break;
			case "cl_jenis_surat":
				$sql = "
					SELECT id, jenis_surat as txt
					FROM cl_jenis_surat
				";
			break;
			case "hubungan_keluarga":
				$sql = "
					SELECT id, nama as txt
					FROM cl_hubungan_keluarga
				";
			break;
			case "data_penduduk_anak":
				$sql = "
					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt
					FROM tbl_data_penduduk
					$where
					and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
					and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
					and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."' 
				";
				
				// cl_status_hubungan_keluarga_id = '3'
				// AND status_data = 'AKTIF'
			break;
			case "data_penduduk_belum_menikah":
				$sql = "
					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt
					FROM tbl_data_penduduk
					$where
					and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
					and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
					and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
				";
				
				//WHERE ( status_kawin = '2' OR status_kawin = '3' )
				//AND status_data = 'AKTIF'
			break;
			case "data_penduduk":
				$sql = "
					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt
					FROM tbl_data_penduduk
					$where
					and cl_provinsi_id = '".$this->auth['cl_provinsi_id']."'
					and cl_kab_kota_id = '".$this->auth['cl_kab_kota_id']."'
					and cl_kecamatan_id = '".$this->auth['cl_kecamatan_id']."'
					and cl_kelurahan_desa_id = '".$this->auth['cl_kelurahan_desa_id']."'
				";
				
				//WHERE status_data = 'AKTIF'
			break;
			case "cl_jenis_pekerjaan":
				$sql = "
					SELECT id, nama_pekerjaan as txt
					FROM cl_jenis_pekerjaan
				";
			break;
			
			default:
				$txt = str_replace("cl_","",$type);
				$sql = "
					SELECT id, $txt as txt
					FROM $type
				";
			break;
		}
		
		return $this->db->query($sql)->result_array();
	}
	
	function simpandata($table,$data,$sts_crud){ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		if(isset($data['id'])){
			$id = $data['id'];
			unset($data['id']);
		}
		
		if($sts_crud == "add"){
			$data['create_date'] = date('Y-m-d H:i:s');
			$data['create_by'] = $this->auth['nama_lengkap'];
				
			unset($data['id']);
		}
		
		if($sts_crud == "edit"){
			$data['update_date'] = date('Y-m-d H:i:s');
			$data['update_by'] = $this->auth['nama_lengkap'];
		}
		
		switch($table){
			
			case "import_data_penduduk_sulsel":
				$this->load->library('PHPExcel'); 
				if(!empty($_FILES['filename']['name'])){
					$ext = explode('.',$_FILES['filename']['name']);
					$exttemp = sizeof($ext) - 1;
					$extension = $ext[$exttemp];
					$upload_path = "./__repository/tmp_upload/";
					$filen = "IMPORTEXCELSULSEL-".date('Ymd_His');
					$filename =  $this->lib->uploadnong($upload_path, 'filename', $filen);
					
					$folder_aplod = $upload_path.$filename;
					$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
					$cacheSettings = array('memoryCacheSize' => '1600MB');
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
					if($extension=='xls'){
						$lib="Excel5";
					}else{
						$lib="Excel2007";
					}
					$objReader =  PHPExcel_IOFactory::createReader($lib); //excel2007
					ini_set('max_execution_time', 123456);
					$objPHPExcel = $objReader->load($folder_aplod); 
					$objReader->setReadDataOnly(true);
					$nama_sheet=$objPHPExcel->getSheetNames();
					$worksheet = $objPHPExcel->setActiveSheetIndex(0);
					$array_benar = array();
					for($i=7; $i <= $worksheet->getHighestRow(); $i++){
						//$cek = $this->db->get_where('tbl_data_penduduk', array('nik'=>$worksheet->getCell("C".$i)->getCalculatedValue()))->row_array();
						//if(!$cek){
							if($worksheet->getCell("F".$i)->getCalculatedValue()){
								$ultah = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("F".$i)->getCalculatedValue()));
							}else{
								$ultah = null;
							}
							
							if($worksheet->getCell("H".$i)->getCalculatedValue() == 'L'){
								$jenis_kelamin = "LAKI-LAKI";
							}elseif($worksheet->getCell("H".$i)->getCalculatedValue() == 'P'){
								$jenis_kelamin = "PEREMPUAN";
							}else{
								$jenis_kelamin = null;
							}

							if($worksheet->getCell("G".$i)->getCalculatedValue()){
								$sts_kawin = "
									SELECT id
									FROM cl_status_kawin 
									WHERE akronim = '".$worksheet->getCell("G".$i)->getCalculatedValue()."'
								";
								$status_kawin = $this->db->query($sts_kawin)->row_array();
							}else{
								$jenis_kelamin = null;
							}
							
							$cek_kk = $this->db->get_where('tbl_kartu_keluarga', array('no_kk'=>$worksheet->getCell("B".$i)->getCalculatedValue()) )->row_array();
							if(!$cek_kk){
								$array_kk = array(
									'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
									'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
									'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
									'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
									'no_kk' => $worksheet->getCell("B".$i)->getCalculatedValue(),
									'create_by' => $this->auth['nama_lengkap']." - Import Excel SIAK",
									'create_date' => date('Y-m-d H:i:s'),
								);
								$this->db->insert('tbl_kartu_keluarga', $array_kk);
							}
							
							$arrayinsertbenar = array(
								'no_kk' => $worksheet->getCell("B".$i)->getCalculatedValue(),
								'nik' => $worksheet->getCell("C".$i)->getCalculatedValue(),
								'nama_lengkap' => $worksheet->getCell("D".$i)->getCalculatedValue(),
								'tempat_lahir' => $worksheet->getCell("E".$i)->getCalculatedValue(),
								'tgl_lahir' => $ultah,
								'jenis_kelamin' => $jenis_kelamin,
								'status_kawin' => $status_kawin['id'],
								'alamat' => $worksheet->getCell("I".$i)->getCalculatedValue(),
								'rt' => (string)$worksheet->getCell("J".$i)->getCalculatedValue(),
								'rw' => (string)$worksheet->getCell("K".$i)->getCalculatedValue(),
								
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
								
								'status_data' => "AKTIF",
								'create_by' => $this->auth['nama_lengkap'],
								'create_date' => date('Y-m-d H:i:s'),
							);
							
							array_push($array_benar, $arrayinsertbenar);
						//}
					}	

					if(!empty($array_benar)){
						$this->db->insert_batch('tbl_data_penduduk', $array_benar);
						unlink($folder_aplod);
					}
				}
			break;
			case "import_data_penduduk_siak":
				$this->load->library('PHPExcel'); 
				if(!empty($_FILES['filename']['name'])){
					$ext = explode('.',$_FILES['filename']['name']);
					$exttemp = sizeof($ext) - 1;
					$extension = $ext[$exttemp];
					$upload_path = "./__repository/tmp_upload/";
					$filen = "IMPORTEXCEL-".date('Ymd_His');
					$filename =  $this->lib->uploadnong($upload_path, 'filename', $filen);
					
					$folder_aplod = $upload_path.$filename;
					$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
					$cacheSettings = array('memoryCacheSize' => '1600MB');
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
					if($extension=='xls'){
						$lib="Excel5";
					}else{
						$lib="Excel2007";
					}
					$objReader =  PHPExcel_IOFactory::createReader($lib); //excel2007
					ini_set('max_execution_time', 123456);
					$objPHPExcel = $objReader->load($folder_aplod); 
					$objReader->setReadDataOnly(true);
					$nama_sheet=$objPHPExcel->getSheetNames();
					$worksheet = $objPHPExcel->setActiveSheetIndex(0);
					$array_benar = array();
					for($i=2; $i <= $worksheet->getHighestRow(); $i++){
						//echo date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("E".$i)->getCalculatedValue()));
						//echo $worksheet->getCell("E".$i)->getCalculatedValue();
						//exit;
						
						if($worksheet->getCell("E".$i)->getCalculatedValue()){
							$ultah = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("E".$i)->getCalculatedValue()));
						}else{
							$ultah = null;
						}
						
						if($worksheet->getCell("C".$i)->getCalculatedValue() == 'L'){
							$jenis_kelamin = "LAKI-LAKI";
						}elseif($worksheet->getCell("C".$i)->getCalculatedValue() == 'P'){
							$jenis_kelamin = "PEREMPUAN";
						}
						
						$sts_kawin = "
							SELECT id
							FROM cl_status_kawin 
							WHERE nama_status_kawin LIKE '%".$worksheet->getCell("I".$i)->getCalculatedValue()."%'
						";
						$status_kawin = $this->db->query($sts_kawin)->row_array();
						
						$spendidikan = "
							SELECT id
							FROM cl_pendidikan
							WHERE nama_pendidikan LIKE '%".$worksheet->getCell("N".$i)->getCalculatedValue()."%'
						";
						$pendidikan = $this->db->query($spendidikan)->row_array();
						
						$spekerjaan = "
							SELECT id
							FROM cl_jenis_pekerjaan
							WHERE nama_pekerjaan LIKE '%".$worksheet->getCell("M".$i)->getCalculatedValue()."%'
						";
						$pekerjaan = $this->db->query($spekerjaan)->row_array();
						
						$cek_kk = $this->db->get_where('tbl_kartu_keluarga', array('no_kk'=>$worksheet->getCell("O".$i)->getCalculatedValue()) )->row_array();
						if(!$cek_kk){
							$array_kk = array(
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
								'no_kk' => $worksheet->getCell("O".$i)->getCalculatedValue(),
								'create_by' => $this->auth['nama_lengkap']." - Import Excel SIAK",
								'create_date' => date('Y-m-d H:i:s'),
							);
							$this->db->insert('tbl_kartu_keluarga', $array_kk);
						}
						
						$shubkeluarga = "
							SELECT id
							FROM cl_hubungan_keluarga
							WHERE nama LIKE '%".$worksheet->getCell("J".$i)->getCalculatedValue()."%'
						";
						$hubkeluarga = $this->db->query($shubkeluarga)->row_array();
						
						if($worksheet->getCell("L".$i)->getCalculatedValue() == "TDK_TH"){
							$gol_drh = null;
						}else{
							$gol_drh = $worksheet->getCell("L".$i)->getCalculatedValue();
						}
						
						$arrayinsertbenar = array(
							'no_kk' => $worksheet->getCell("O".$i)->getCalculatedValue(),
							'cl_status_hubungan_keluarga_id' => $hubkeluarga['id'],
							'nik' => $worksheet->getCell("A".$i)->getCalculatedValue(),
							'nama_lengkap' => $worksheet->getCell("B".$i)->getCalculatedValue(),
							'jenis_kelamin' => $jenis_kelamin,
							'agama' => $worksheet->getCell("F".$i)->getCalculatedValue(),
							'tempat_lahir' => $worksheet->getCell("D".$i)->getCalculatedValue(),
							'tgl_lahir' => $ultah,
							'status_kawin' => $status_kawin['id'],
							'pendidikan' => $pendidikan['id'],
							'cl_jenis_pekerjaan_id' => $pekerjaan['id'],
							'golongan_darah' => $gol_drh,
							'alamat' => $worksheet->getCell("U".$i)->getCalculatedValue(),
							
							'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
							'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
							'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
							'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							
							'status_data' => "AKTIF",
							'create_by' => $this->auth['nama_lengkap'],
							'create_date' => date('Y-m-d H:i:s'),
						);
						
						array_push($array_benar, $arrayinsertbenar);
						
					}
					
					if(!empty($array_benar)){
						$this->db->insert_batch('tbl_data_penduduk', $array_benar);
					}
				}
			break;
			
			case "identitas_desa":
				$table = "tbl_setting_apps";

				$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];
				$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];
				$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
				$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				
				$nama_provinsi = $this->db->get_where('cl_provinsi', array('id'=>$data['cl_provinsi_id']) )->row_array();
				$nama_kab_kota = $this->db->get_where('cl_kab_kota', array('id'=>$data['cl_kab_kota_id']) )->row_array();
				$nama_kecamatan = $this->db->get_where('cl_kecamatan', array('id'=>$data['cl_kecamatan_id']) )->row_array();
				$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id'=>$data['cl_kelurahan_desa_id']) )->row_array();
				
				$data['nama_provinsi'] = $nama_provinsi['nama'];
				$data['nama_kab_kota'] = $nama_kab_kota['nama'];
				$data['nama_kecamatan'] = $nama_kecamatan['nama'];
				$data['nama_desa'] = $nama_kelurahan['nama'];
			break;
			case "data_surat":
				$table = "tbl_data_surat";
				$array = array();
				
				$dt_pemohon=array('cl_kelurahan_desa_id'=>$data["cl_kelurahan_desa_id"],
								  'cl_provinsi_id'=>$this->setting['cl_provinsi_id'],
								  'cl_kab_kota_id'=>$this->setting['cl_kab_kota_id'],
								  'cl_kecamatan_id'=>$this->setting["cl_kecamatan_id"],
								  'nik'=>$data["nik"],
								  'nama_lengkap'=>$data["nama_lengkap"],
								  'tempat_lahir'=>$data["tempat_lahir"],
								  'agama'=>$data["agama"],
								  'status_kawin'=>$data["status_kawin"],
								  'tgl_lahir'=>$data["tgl_lahir"],
								  'jenis_kelamin'=>$data["jenis_kelamin"],
								  'pendidikan'=>$data["pendidikan"],
								  'cl_jenis_pekerjaan_id'=>$data["cl_jenis_pekerjaan_id"],
								  'rt'=>$data["rt"],
								  'rw'=>$data["rw"],
								  'kode_pos'=>$data["kode_pos"],
								  'alamat'=>$data["alamat"],
								  'cl_jenis_surat_id'=>$data["cl_jenis_surat_id"],
								  'create_date'=>date('Y-m-d H:i:s'),
				);
				
				unset($data["nama_lengkap"]);
				unset($data["tempat_lahir"]);
				unset($data["agama"]);
				unset($data["status_kawin"]);
				unset($data["tgl_lahir"]);
				unset($data["jenis_kelamin"]);
				unset($data["pendidikan"]);
				unset($data["cl_jenis_pekerjaan_id"]);
				unset($data["rt"]);
				unset($data["rw"]);
				unset($data["kode_pos"]);
				unset($data["alamat"]);
				$this->db->insert('tbl_registrasi_surat',$dt_pemohon);
				$data["tbl_registrasi_id"]=$this->db->insert_id();
				$data["flag_reg"]='Y';
				if($sts_crud == "add"){
					$data['cl_provinsi_id'] =$this->setting['cl_provinsi_id'];
					$data['cl_kab_kota_id'] = $this->setting['cl_kab_kota_id'];
					$data['cl_kecamatan_id'] = $this->setting['cl_kecamatan_id'];
					//$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
					
					if($data['cl_jenis_surat_id'] == '16'){
						$penduduk = array(
							'status_kawin' => '3',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Cerai Nikah",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
					}
					if($data['cl_jenis_surat_id'] == '12'){
						$penduduk = array(
							'status_data' => 'PINDAH DOMISILI',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Pindah Penduduk",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
					}
					
					if($data['cl_jenis_surat_id'] == '9'){
						$penduduk = array(
							'status_data' => 'MENINGGAL DUNIA',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Kematian",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
					}
				}
				
				if($sts_crud == "edit"){
					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];
					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];
					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
					
					if($data['cl_jenis_surat_id'] == '16'){
						$penduduk = array(
							'status_kawin' => '3',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Cerai Nikah",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
						
						if($data['nik'] != $data['nik_lama']){
							$aktifkan = array(
								'status_kawin' => '1',
								'update_date' => date('Y-m-d H:i:s'),
								'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Cerai Nikah",
							);
							//$this->db->update('tbl_data_penduduk', $aktifkan, array('id'=>$data['tbl_data_penduduk_id_lama']) );
						}
					}

					if($data['cl_jenis_surat_id'] == '12'){
						$penduduk = array(
							'status_data' => 'PINDAH DOMISILI',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Pindah Penduduk",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
						
						if($data['nik'] != $data['nik_lama']){
							$aktifkan = array(
								'status_data' => 'AKTIF',
								'update_date' => date('Y-m-d H:i:s'),
								'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Keterangan Pindah Penduduk",
							);
							//$this->db->update('tbl_data_penduduk', $aktifkan, array('id'=>$data['tbl_data_penduduk_id_lama']) );
						}
					}
					
					if($data['cl_jenis_surat_id'] == '9'){
						$penduduk = array(
							'status_data' => 'MENINGGAL DUNIA',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Kematian",
						);
						//$this->db->update('tbl_data_penduduk', $penduduk, array('id'=>$data['tbl_data_penduduk_id']) );
						
						if($data['nik'] != $data['nik_lama']){
							$aktifkan = array(
								'status_data' => 'AKTIF',
								'update_date' => date('Y-m-d H:i:s'),
								'update_by' => $this->auth['nama_lengkap']." - Via Data Surat Kematian",
							);
							//$this->db->update('tbl_data_penduduk', $aktifkan, array('id'=>$data['tbl_data_penduduk_id_lama']) );
						}
					}
				}
				
				if($sts_crud == "delete"){
					$datax = $this->db->get_where('tbl_data_surat', array('id'=>$id) )->row_array();
					if($datax){
						$aktifkan = array(
							'status_data' => 'AKTIF',
							'update_date' => date('Y-m-d H:i:s'),
							'update_by' => $this->auth['nama_lengkap']." - Via Data Surat",
						);
						//$this->db->update('tbl_data_penduduk', $aktifkan, array('id'=>$datax['tbl_data_penduduk_id']) );
					}
				}
				
				if($sts_crud == "add" || $sts_crud == "edit"){
					switch($data['cl_jenis_surat_id']){
						case "43":

							$this->load->helper('terbilang');

							$array['jumlah_penghasilan']  = $data['jumlah_penghasilan'];
							$array['jumlah_penghasilan_terbilang'] = number_to_words((int)$data['jumlah_penghasilan']);

							$array['nama_anak']           = $data['nama_anak'];
							$array['tempat_lahir_anak']   = $data['tempat_lahir_anak'];
							$array['tgl_lahir_anak']      = $data['tgl_lahir_anak'];
							$array['nik_anak']            = $data['nik_anak'];
							$array['agama_anak']          = $data['agama_anak'];
							$array['jenis_kelamin_anak']  = $data['jenis_kelamin_anak'];
							$array['alamat_anak']         = $data['alamat_anak'];
							$array['no_pengantar']        = $data['no_pengantar'];
							$array['tgl_pengantar']       = $data['tgl_pengantar'];
							$array['keperluan_surat']     = $data['keperluan_surat'];
							$data['info_tambahan']        = json_encode($array);
							break;
						case "31":
							$array['nama_anak'] = $data['nama_anak'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "30":
							$array['keperluan'] = $data['keperluan'];
							$array['nama_dalam_sertifikat'] = $data['nama_dalam_sertifikat'];
							$array['no_sertifikat'] = $data['no_sertifikat'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "29":
							$array['nama_ibu'] = $data['nama_ibu'];
							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "28":
							$this->load->helper('terbilang');
							$array['penghasilan'] = $data['penghasilan'];
							$array['istri'] = $data['istri'];
							$array['istri_terbilang'] = number_to_words((int)$data['istri']);
							$array['anak'] = $data['anak'];
							$array['anak_terbilang'] = number_to_words((int)$data['anak']);
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "27":
							$array['nama_suami'] = $data['nama_suami'];
							$array['tgl_meninggal'] = $data['tgl_meninggal'];
							$array['hari_meninggal'] = $data['hari_meninggal'];
							$array['nama_pemakaman'] = $data['nama_pemakaman'];
							$array['no_surat_keterangan'] = $data['no_surat_keterangan'];
							$array['rumah_sakit'] = $data['rumah_sakit'];
							$array['surat_pengantar'] = $data['surat_pengantar'];
							$array['tgl_pengantar'] = $data['tgl_pengantar'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "26":
							$array['tgl_kebakaran'] = $data['tgl_kebakaran'];
							$array['rincian_kerugian'] = $data['rincian_kerugian'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "25":
							$array['peruntukan_surat'] = $data['peruntukan_surat'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "24":
							$array['nama_istri'] = $data['nama_istri'];
							$array['tgl_surat_pernyataan'] = $data['tgl_surat_pernyataan'];
							$array['no_surat_pengantar'] = $data['no_surat_pengantar'];
							$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];
							$array['tgl_pergi'] = $data['tgl_pergi'];
							$array['peruntukan_surat'] = $data['peruntukan_surat'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "22":
							$array['nama_kegiatan'] = $data['nama_kegiatan'];
							$array['hari'] = $data['hari'];
							$array['tgl_acara'] = $data['tgl_acara'];
							$array['tempat'] = $data['tempat'];

							$array['bunyi_bunyian'] = $data['bunyi_bunyian'];
							$array['jalan_lorong'] = $data['jalan_lorong'];
							$array['tutup_sementara'] = $data['tutup_sementara'];

							$array['no_surat_pengantar'] = $data['no_surat_pengantar'];
							$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];
							$array['satker_polisi'] = $data['satker_polisi'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "19":
							$array['nama_usaha'] = $data['nama_usaha'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "17":
						case "16":
							$array['nama_pasangan'] = $data['nama_pasangan'];
							$array['nik_pasangan'] = $data['nik_pasangan'];
							$array['alamat_pasangan'] = $data['alamat_pasangan'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "15":
							$array['nama_usaha'] = $data['nama_usaha'];
							$array['alamat_usaha'] = $data['alamat_usaha'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "14":
							$array['tgl_mulai_berlaku'] = $data['tgl_mulai_berlaku'];
							$array['tgl_selesai_berlaku'] = $data['tgl_selesai_berlaku'];
							$array['keperluan'] = $data['keperluan'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "12":
							$array['tgl_pindah'] = $data['tgl_pindah'];
							$array['alasan_pindah'] = $data['alasan_pindah'];
							$array['alamat_pindah'] = $data['alamat_pindah'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "11":
							$array['nama_pasangan'] = $data['nama_pasangan'];
							$array['nama_ayah_pasangan'] = $data['nama_ayah_pasangan'];
							$array['alamat_pasangan'] = $data['alamat_pasangan'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "9":
							$array['hari_kematian'] = $data['hari_kematian'];
							$array['tgl_kematian'] = $data['tgl_kematian'];
							$array['jam_kematian'] = $data['jam_kematian'];
							$array['tempat_kematian'] = $data['tempat_kematian'];
							$array['penyebab_kematian'] = $data['penyebab_kematian'];
							$array['nik_pelapor'] = $data['nik_pelapor'];
							$array['hubungan_pelapor'] = $data['hubungan_pelapor'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "8":
							$array['usia_kandungan_kematian'] = $data['usia_kandungan_kematian'];
							$array['hari_kematian'] = $data['hari_kematian'];
							$array['tgl_kematian'] = $data['tgl_kematian'];
							$array['jam_kematian'] = $data['jam_kematian'];
							$array['tempat_kematian'] = $data['tempat_kematian'];
							$array['nama_pelapor'] = $data['nama_pelapor'];
							$array['hubungan_pelapor'] = $data['hubungan_pelapor'];
							
							$data['info_tambahan'] = json_encode($array);
						break;
						case "6":
							$array['nama_bayi'] = $data['nama_bayi'];
							$array['jenis_kelamin_bayi'] = $data['jenis_kelamin_bayi'];
							$array['jam_lahir'] = $data['jam_lahir'];
							$array['hari_lahir'] = $data['hari_lahir'];
							$array['tgl_lahir'] = $data['tgl_lahirr'];
							$array['tempat_lahir'] = $data['tempat_lahirr'];
							unset($data['tgl_lahirr']);
							unset($data['tempat_lahirr']);
							$data['info_tambahan'] = json_encode($array);
						break;
					}
				}
				
				if(!empty($array)){
					foreach($array as $k => $v){
						unset($data[$k]);
					}
				}
				
				unset($data['tbl_data_penduduk_id_lama']);
				
				//echo "<pre>";
				//print_r($data);exit;
			break;
			case "data_keluarga":
				$table = "tbl_kartu_keluarga";
				
				if($sts_crud == "add" || $sts_crud == "edit"){
					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];
					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];
					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
					
					$nik = $data['nik'];	
					$cl_status_hubungan_keluarga_id = $data['cl_status_hubungan_keluarga_id'];	
					
					unset($data['nik']);
					unset($data['cl_status_hubungan_keluarga_id']);
				}
				
				if($sts_crud == "delete"){
					$cekdata = $this->db->get_where('tbl_kartu_keluarga', array('id'=>$id) )->row_array();
					if($cekdata){
						$this->db->update('tbl_data_penduduk', array('no_kk'=>null,'cl_status_hubungan_keluarga_id'=>null), array('no_kk'=>$cekdata['no_kk']) );
					}
				}
			break;
			case "data_penduduk":
				$table = "tbl_data_penduduk";
				
				if($sts_crud == "add" || $sts_crud == "edit"){
					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];
					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];
					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}
			break;
			
			case "user_role_group":
				$id_group = $id;
				$this->db->delete('tbl_user_prev_group', array('cl_user_group_id'=>$id_group) );
				if(isset($data['data'])){
					$postdata = $data['data'];
					$row=array();
					foreach($postdata as $v){
						$pecah = explode("_",$v);
						$row["buat"]=0;
						$row["baca"]=0;
						$row["ubah"]=0;
						$row["hapus"]=0;
						
						switch($pecah[0]){
							case "C":
								$row["buat"]=1;
							break;
							case "R":
								$row["baca"]=1;
							break;
							case "U":
								$row["ubah"]=1;
							break;
							case "D":
								$row["hapus"]=1;
							break;
						}
						
						$row["tbl_menu_id"] = $pecah[1];
						$row["cl_user_group_id"] = $id_group;
						
						$cek_data = $this->db->get_where('tbl_user_prev_group', array('tbl_menu_id'=>$pecah[1], 'cl_user_group_id'=>$id_group) )->row_array();
						if(!$cek_data){
							$row['create_date'] = date('Y-m-d H:i:s');
							$row['create_by'] = $this->auth['nama_lengkap'];
							
							$this->db->insert('tbl_user_prev_group', $row);
						}else{
							if($row["buat"]==0)unset($row["buat"]);
							if($row["baca"]==0)unset($row["baca"]);
							if($row["ubah"]==0)unset($row["ubah"]);
							if($row["hapus"]==0)unset($row["hapus"]);

							$row['update_date'] = date('Y-m-d H:i:s');
							$row['update_by'] = $this->auth['nama_lengkap'];
							
							$this->db->update('tbl_user_prev_group', $row, array('tbl_menu_id'=>$pecah[1], 'cl_user_group_id'=>$id_group) );
						}
					}	
				}
			break;
			case "user_mng":
				$table = "tbl_user";
				if($sts_crud=='add' || $sts_crud == 'edit'){
					$data['password']=$this->encrypt->encode($data['password']);
				}
			break;
			case "user_group":
				$table = "cl_user_group";
			break;
			case "ubah_password":
				$this->load->library("encrypt");

				$table = "tbl_user";
				$password_lama = $this->encrypt->decode($this->auth["password"]);
				if($data["pwd_lama"] != $password_lama){
					echo 2;
					exit;
				}

				$data["password"] = $this->encrypt->encode($data["pwd_baru"]);

				unset($data["pwd_lama"]);
				unset($data["pwd_baru"]);
			break;
		}
		
		switch ($sts_crud){
			case "add":
				$insert = $this->db->insert($table,$data);
				$id = $this->db->insert_id();
				
				if($insert){
					if($table == "tbl_kartu_keluarga"){
						if(isset($nik)){
							foreach($nik as $k => $v){
								if(trim($v) != ""){
									$array_update = array(
										'no_kk' => $data['no_kk'],
										'cl_status_hubungan_keluarga_id' => $cl_status_hubungan_keluarga_id[$k],
									);
									$this->db->update( 'tbl_data_penduduk', $array_update, array('id'=>$v) );
								}
							}
						}
					}
				}
			break;
			case "edit":
				$update = $this->db->update($table, $data, array('id' => $id) );	
				
				if($update){
					if($table == "tbl_kartu_keluarga"){
						if(isset($nik)){
							foreach($nik as $k => $v){
								if(trim($v) != ""){
									$array_update = array(
										'no_kk' => $data['no_kk'],
										'cl_status_hubungan_keluarga_id' => $cl_status_hubungan_keluarga_id[$k],
									);
									$this->db->update( 'tbl_data_penduduk', $array_update, array('id'=>$v) );
								}
							}
						}
					}
				}
			break;
			case "delete":
				$this->db->delete($table, array('id' => $id));	
			break;
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 'gagal';
		}else{
			 return $this->db->trans_commit();
		}
	}
	
}