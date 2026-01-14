<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}


class Mbackend extends CI_Model
{

	function __construct()
	{

		parent::__construct();

		$this->auth = unserialize(base64_decode($this->session->userdata('s3ntr4lb0')));
		// print_r($this->auth);exit;
		$this->setting = $this->db->get("tbl_setting_apps")->row_array();
	}


	function getdata($type = "", $balikan = "", $p1 = "", $p2 = "", $p3 = "", $p4 = "")
	{

		$where  = " WHERE 1=1 ";

		$where2 = "";

		$dbdriver = $this->db->dbdriver;

		if ($dbdriver == "postgre") {

			$select = " ROW_NUMBER() OVER (ORDER BY A.id DESC) as rowID, ";
		} else {

			$select = "";
		}


		if ($this->input->post('key')) {

			$key = $this->input->post('key');

			$kat = $this->input->post('kat');

			$where .= " AND LOWER(" . $kat . ") like '%" . strtolower(trim($key)) . "%' ";
		}


		if ($this->auth["cl_user_group_id"] == '4') {

			//$where .= " AND A.create_by = '".$this->auth["nama_lengkap"]."' ";

		}


		switch ($type) {

			// Modul Laporan

			case "laporan_penduduk":

				$where = "WHERE 1=1";

				// Jika user kecamatan/kelurahan
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= " AND A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'";
					$where .= " AND A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'";
					$where .= " AND A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'";

					// kalau user kelurahan/rt/rw → tambah filter kelurahan
					if (!empty($this->auth['cl_kelurahan_desa_id'])) {
						$where .= " AND A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}
				}

				// Ambil data filter dari GET saja (karena cetak pakai GET)
				$kelurahan = $this->input->get('kelurahan');
				$rt        = $this->input->get('rt');
				$rw        = $this->input->get('rw');
				$nik       = $this->input->get('nik');
				$nama      = $this->input->get('nama_lengkap');
				$no_kk     = $this->input->get('no_kk');
				$status    = $this->input->get('status_data');

				// Filter berdasarkan isi, hanya jika tidak kosong
				if (!empty($kelurahan)) {
					$where .= " AND A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				if (!empty($rt)) {
					$where .= " AND A.rt = '" . $rt . "'";
				}

				if (!empty($rw)) {
					$where .= " AND A.rw = '" . $rw . "'";
				}

				if (!empty($nik)) {
					$where .= " AND A.nik = '" . $nik . "'";
				}

				if (!empty($nama)) {
					$where .= " AND A.nama_lengkap = '" . $nama . "'";
				}

				if (!empty($no_kk)) {
					$where .= " AND A.no_kk = '" . $no_kk . "'";
				}

				if (!empty($status)) {
					$where .= " AND A.status_data LIKE '%" . $status . "%'";
				}

				$sql = "
					SELECT 
						A.*, 
						CONCAT(LEFT(nik, 13), 'xxx') AS nik2,
						CONCAT(LEFT(no_kk, 13), 'xxx') AS no_kk2,
						B.nama_agama,
						C.nama_status_kawin,
						D.nama_pendidikan,
						G.nama_pekerjaan,
						E.nama AS kecamatan,
						F.nama AS kelurahan,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat
					FROM tbl_data_penduduk A
					LEFT JOIN cl_agama B ON B.id = A.agama
					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					LEFT JOIN cl_jenis_pekerjaan G ON G.id = A.cl_jenis_pekerjaan_id
					$where
					ORDER BY A.rw ASC, A.rt ASC
				";

				// echo $sql; exit;

				break;

			case "laporan_penduduk_asing":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$kelurahan = $this->input->get('kelurahan');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}


				if ($kelurahan != '') {

					$where .= "and F.id = '" . $kelurahan . "'";
				}



				$no_passport = $this->input->get('no_passport');

				if ($no_passport != '') {
					$where .= "and A.no_passport='$no_passport'";
				}

				$nama_lengkap = $this->input->get('nama_lengkap');
				if ($nama_lengkap != '') {
					$where .= "and A.nama_lengkap='$nama_lengkap'";
				}

				$no_pengenalan = $this->input->get('no_pengenalan');
				if ($no_pengenalan != '') {
					$where .= "and A.no_pengenalan='$no_pengenalan'";
				}


				$sql = "

					SELECT A.*,

					B.nama_agama, E.nama_pekerjaan, C.nama as kecamatan, D.nama as kelurahan,

					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_penduduk_asing A

					LEFT JOIN cl_agama B ON B.id = A.agama 

					LEFT JOIN cl_kecamatan C ON C.id = A.cl_kecamatan_id 

					LEFT JOIN cl_kelurahan_desa D ON D.id = A.cl_kelurahan_desa_id 
					
					LEFT JOIN cl_jenis_pekerjaan E ON E.id = A.cl_jenis_pekerjaan_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.rw ASC,A.rt ASC

				";



				//echo $sql;exit;
				break;



			case "laporan_cetak_usia":

				$type = $this->input->get('type');

				if ($this->session->userdata('cl_user_gorup_id') == '3') {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'";
				} else {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				}

				if ($type == '1') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 0 AND 5";
				} elseif ($type == '2') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 6 AND 10";
				} elseif ($type == '3') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 11 AND 15";
				} elseif ($type == '4') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 16 AND 20";
				} elseif ($type == '5') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 21 AND 30";
				} elseif ($type == '6') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 31 AND 40";
				} elseif ($type == '7') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 41 AND 50";
				} elseif ($type == '8') {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) BETWEEN 51 AND 60";
				} else {
					$where .= "and TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) > 60";
				}


				$sql = "

					SELECT A.*,

						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,G.nama_pekerjaan,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_penduduk A

					LEFT JOIN cl_agama B ON B.id = A.agama

					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin

					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					LEFT JOIN cl_jenis_pekerjaan G ON G.id = A.cl_jenis_pekerjaan_id

					$where

					AND status_data='AKTIF'

					

					ORDER BY A.rw ASC,A.rt ASC

				";

				//echo $sql;exit;

				break;

			case "laporan_cetak_kelamin":

				$type = $this->input->get('type');

				if ($this->session->userdata('cl_user_gorup_id') == '3') {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'";
				} else {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				}

				if ($type == 'PEREMPUAN') {
					$where .= "and jenis_kelamin = 'Perempuan'";
				} else {
					$where .= "and jenis_kelamin = 'Laki-Laki'";
				}


				$sql = "
	
						SELECT A.*,
	
							B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,G.nama_pekerjaan,
	
							E.nama as kecamatan, F.nama as kelurahan,
	
							DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
	
						FROM tbl_data_penduduk A
	
						LEFT JOIN cl_agama B ON B.id = A.agama
	
						LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
	
						LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
	
						LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
	
						LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
	
						LEFT JOIN cl_jenis_pekerjaan G ON G.id = A.cl_jenis_pekerjaan_id
	
						$where
	
						AND status_data='AKTIF'
	
						
	
						ORDER BY A.rw ASC,A.rt ASC
	
					";

				//echo $sql;exit;

				break;

			case "laporan_cetak_kawin":

				$type = $this->input->get('type');

				if ($this->session->userdata('cl_user_gorup_id') == '3') {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
		
							and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
		
							and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'";
				} else {
					$where = "WHERE A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
		
							and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
		
							and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
		
							and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				}

				if ($type == 'Belum Kawin') {
					$where .= "and status_kawin = '1'";
				} else if ($type == 'Kawin') {
					$where .= "and status_kawin = '2'";
				} else if ($type == 'Cerai Mati') {
					$where .= "and status_kawin = '3'";
				} else {
					$where .= "and status_kawin = '4'";
				}


				$sql = "
		
							SELECT A.*,
		
								B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,G.nama_pekerjaan,
		
								E.nama as kecamatan, F.nama as kelurahan,
		
								DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
		
							FROM tbl_data_penduduk A
		
							LEFT JOIN cl_agama B ON B.id = A.agama
		
							LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
		
							LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
		
							LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
		
							LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
		
							LEFT JOIN cl_jenis_pekerjaan G ON G.id = A.cl_jenis_pekerjaan_id
		
							$where
		
							AND status_data='AKTIF'
		
							
		
							ORDER BY A.rw ASC,A.rt ASC
		
						";

				//echo $sql;exit;

				break;

			case "laporan_ktp":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "
	
						SELECT A.*,
	
							B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,
	
							E.nama as kecamatan, F.nama as kelurahan,
	
							DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
	
						FROM tbl_data_rekam_ktp A
	
						LEFT JOIN cl_agama B ON B.id = A.agama
	
						LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
	
						LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
	
						LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
	
						LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
	
						$where
	
						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						ORDER BY A.id DESC
	
					";



				//echo $sql;exit;

				break;

			case 'laporan_hasil_skm':
				$tahun_post = $this->input->post('tahun');
				$tahun = (int)($tahun_post ? $tahun_post : date('Y'));

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$xkec  = $this->auth['cl_kecamatan_id'];
					$xkel  = $this->auth['cl_kelurahan_desa_id'];
				} else {
					$xkec  = $this->auth['cl_kecamatan_id'];
					$xkel  = (int)$this->input->post('kelurahan_id', true);
				}
				if ($this->input->get('kelurahan_id', true) != '') {
					$xkel = $this->input->get('kelurahan_id', true);
				}
				$query = $this->db->query("CALL sp_lap_penilaian_skm(2025,$xkec,$xkel);");

				$ret = $query->result_array();

				// Penting: bebaskan result dan reset koneksi karena pakai stored procedure
				$query->free_result();
				$this->db->conn_id->next_result();

				return $ret;

			case 'laporan_hasil_skm2':
				$tahun_post = $this->input->post('tahun');
				$tahun = (int)($tahun_post ? $tahun_post : date('Y'));

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$xkec  = $this->auth['cl_kecamatan_id'];
					$xkel  = $this->auth['cl_kelurahan_desa_id'];
				} else {
					$xkec  = $this->auth['cl_kecamatan_id'];
					$xkel  = (int)$this->input->post('kelurahan_id', true);
				}
				if ($this->input->get('kelurahan_id', true) != '') {
					$xkel = $this->input->get('kelurahan_id', true);
				}

				// $sql = " SELECT 
				// 			'Jenis Kelamin' AS kategori,
				// 			CASE
				// 				WHEN a.jenis_kelamin = 'L' THEN 'Laki-laki'
				// 				WHEN a.jenis_kelamin = 'P' THEN 'Perempuan'
				// 				ELSE 'Tidak Diketahui'
				// 			END AS nama_kategori,
				// 			COUNT(*) AS jumlah,
				// 			ROUND(COUNT(*) * 100.0 / total.total_responden, 2) AS persentase,
				// 			1 AS urutan_kategori,
				// 			CASE a.jenis_kelamin
				// 				WHEN 'Laki-laki' THEN 1
				// 				WHEN 'Perempuan' THEN 2
				// 				ELSE 99
				// 			END AS urutan_sub
				// 			FROM tbl_penilaian_skm a
				// 			CROSS JOIN (
				// 			SELECT COUNT(*) AS total_responden 
				// 			FROM tbl_penilaian_skm 
				// 			WHERE cl_kecamatan_id = '$xkec' AND cl_kelurahan_desa_id = '$xkel'
				// 			) AS total
				// 			WHERE a.cl_kecamatan_id = '$xkec' AND a.cl_kelurahan_desa_id = '$xkel'
				// 			GROUP BY a.jenis_kelamin

				// 			UNION ALL

				// 			SELECT 
				// 			'Pendidikan' AS kategori,
				// 			nama_pendidikan AS nama_kategori,
				// 			0 AS jumlah,
				// 			0 AS persentase,
				// 			2 AS urutan_kategori,
				// 			CASE nama_pendidikan
				// 				WHEN 'SD/SEDERAJAT' THEN 1
				// 				WHEN 'TAMAT SD/SEDERAJAT' THEN 2
				// 				WHEN 'SLTP/SEDERAJAT' THEN 3
				// 				WHEN 'SLTA/SEDERAJAT' THEN 4
				// 				WHEN 'DIPLOMA I/II' THEN 5
				// 				WHEN 'AKADEMI/DIPLOMA III/S.MUDA' THEN 6
				// 				WHEN 'DIPLOMA IV/STRATA I' THEN 7
				// 				WHEN 'STRATA II' THEN 8
				// 				WHEN 'STRATA III' THEN 9
				// 				WHEN 'TIDAK TAMAT/BELUM TAMAT SD/SEDERAJAT' THEN 10
				// 				WHEN 'TIDAK/BELUM SEKOLAH' THEN 11
				// 				ELSE 99
				// 			END AS urutan_sub
				// 			FROM cl_pendidikan

				// 			UNION ALL

				// 			SELECT 
				// 			'Pekerjaan' AS kategori,
				// 			c.nama_pekerjaan AS nama_kategori,
				// 			COUNT(*) AS jumlah,
				// 			ROUND(COUNT(*) * 100.0 / total.total_responden, 2) AS persentase,
				// 			3 AS urutan_kategori,
				// 			1 AS urutan_sub
				// 			FROM tbl_penilaian_skm a
				// 			LEFT JOIN cl_jenis_pekerjaan c ON c.id = a.cl_jenis_pekerjaan_id
				// 			CROSS JOIN (
				// 			SELECT COUNT(*) AS total_responden 
				// 			FROM tbl_penilaian_skm 
				// 			WHERE cl_kecamatan_id = '$xkec' AND cl_kelurahan_desa_id = '$xkel'
				// 			) AS total
				// 			WHERE a.cl_kecamatan_id = '$xkec' AND a.cl_kelurahan_desa_id = '$xkel'
				// 			GROUP BY c.nama_pekerjaan

				// 			UNION ALL

				// 			SELECT 
				// 			'Jenis Layanan' AS kategori,
				// 			e.jenis_surat AS nama_kategori,
				// 			COUNT(*) AS jumlah,
				// 			ROUND(COUNT(*) * 100.0 / total.total_responden, 2) AS persentase,
				// 			4 AS urutan_kategori,
				// 			1 AS urutan_sub
				// 			FROM tbl_penilaian_skm a
				// 			LEFT JOIN cl_jenis_surat e ON e.id = a.cl_jenis_surat_id
				// 			CROSS JOIN (
				// 			SELECT COUNT(*) AS total_responden 
				// 			FROM tbl_penilaian_skm 
				// 			WHERE cl_kecamatan_id = '$xkec' AND cl_kelurahan_desa_id = '$xkel'
				// 			) AS total
				// 			WHERE a.cl_kecamatan_id = '$xkec' AND a.cl_kelurahan_desa_id = '$xkel'
				// 			GROUP BY e.jenis_surat

				// 			ORDER BY urutan_kategori, urutan_sub, jumlah DESC
				// ";		
				$sql = "SELECT a.*,b.nama_pendidikan AS pendidikan,c.nama_pekerjaan AS pekerjaan,d.jenis_surat AS layanan,e.nama AS kelurahan, f.nama AS kecamatan,
						CASE
							WHEN a.jenis_kelamin = 'L' THEN 'Laki-laki'
							WHEN a.jenis_kelamin = 'P' THEN 'Perempuan'
							ELSE 'Tidak Diketahui'
						END AS kategori
						FROM tbl_penilaian_skm a
						LEFT JOIN cl_pendidikan b ON a.cl_pendidikan_id = b.id
						LEFT JOIN cl_jenis_pekerjaan c ON a.cl_jenis_pekerjaan_id = c.id
						LEFT JOIN cl_jenis_surat d ON a.cl_jenis_surat_id=d.id
						LEFT JOIN cl_kelurahan_desa e ON a.cl_kelurahan_desa_id=e.id
						LEFT JOIN cl_kecamatan f ON a.cl_kecamatan_id=f.id
						WHERE a.cl_kecamatan_id =  '$xkec'
						AND a.cl_kelurahan_desa_id = '$xkel'
						GROUP BY a.sesi_id
						ORDER BY a.id;
				";
				break;

			case "laporan_wamis":

				$where = "WHERE 1=1";

				$desa_id	= $this->input->get('kelurahan_id');
				$rt			= $this->input->get('rt');
				$rw			= $this->input->get('rw');

				if ($rt !== '' && $rt !== null) {
					$where .= " AND g.rt = '" . $rt . "'";
				}

				if ($rw !== '' && $rw !== null) {
					$where .= " AND g.rw = '" . $rw . "'";
				}

				if ($desa_id !== '' && $desa_id !== null) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $desa_id . "'";
				}


				$sql = "SELECT a.*,g.alamat,g.rw,g.rt,e.nama as kecamatan, f.nama as kelurahan,
	
						DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
	
						FROM tbl_data_wamis a

						LEFT JOIN cl_jenis_wamis b ON b.id = a.jenis_wamis
	
						LEFT JOIN cl_kecamatan e ON e.id = a.cl_kecamatan_id
	
						LEFT JOIN cl_kelurahan_desa f ON f.id = a.cl_kelurahan_desa_id

						left join tbl_data_penduduk g on a.nik=g.nik
	
						$where
	
						and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						ORDER BY g.rw ASC, g.rt ASC
	
					";



				//echo $sql;exit;

				break;

			case "laporan_umkm":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM cl_master_umkm A

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where	

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_dasawisma":

				$kelurahan = $this->input->get('kelurahan');
				$rt = $this->input->get('rt');
				$rw = $this->input->get('rw');

				$where = "";

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
					and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
					and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
					and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
				";
				}

				if ($kelurahan != '') {
					$where .= " and F.id = '" . $kelurahan . "'";
				}

				if ($rt) {
					$where .= " and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {
					$where .= " and A.rw like '%" . $rw . "%'";
				}

				// Query utama
				$sql = "SELECT A.*,
						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,
						E.nama as kecamatan, F.nama as kelurahan,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
					FROM tbl_data_dasawisma A
					LEFT JOIN cl_agama B ON B.id = A.agama
					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin
					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan
					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
					WHERE 1=1
					$where
					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					ORDER BY A.rw ASC, A.rt ASC
				";
				//echo $sql;exit;

				break;

			case "laporan_faskes":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');


				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}


				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

				SELECT A.* FROM tbl_data_rs A

				$where

				and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

				and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

				and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_rt_rw":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');


				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}


				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = " SELECT a.*,
				CASE 
				WHEN a.jab_rt_rw = 'Ketua RW' THEN CONCAT('Ketua RW ', LPAD(a.rw, 3, '0'))
				WHEN a.jab_rt_rw = 'Ketua RT' THEN CONCAT('Ketua RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
				ELSE a.jab_rt_rw
				END AS jab_rt_rw,
				DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,a.agama nama_agama,a.alamat from tbl_data_rt_rw a 
				
				left join cl_agama b on a.agama=b.id

				where a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' 
				
				ORDER BY CONCAT(a.rw,'.',if(a.rt='' OR a.rt IS NULL,'000',a.rt))

				";



				//echo $sql;exit;

				break;

			case "laporan_penilaian_rt_rw":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');

				$rt_rw_id = $this->input->get('rt_rw_id');

				$nik = $this->input->get('nik');

				$bulan = $this->input->get('bulan');


				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}


				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$desa_id = $this->auth['cl_kelurahan_desa_id'];

				// $sql = "SELECT  (CASE WHEN nor = 1 THEN @nh := @nh + 1 ELSE NULL END) AS urutHeader,a.*
				// 		FROM (
				// 		SELECT 
				// 			a.nor,
				// 			a.xid,
				// 			a.singkatan,
				// 			a.kategori,
				// 			a.uraian,
				// 			(CASE WHEN a.nor = 3 THEN a.satuan ELSE b.satuan END) AS satuan,
				// 			b.target,
				// 			b.capaian,
				// 			b.nilai
				// 		FROM (

				// 			SELECT 
				// 			MIN(id) AS id,
				// 			NULL AS xid,
				// 			1 AS nor,
				// 			'' AS singkatan,
				// 			kategori,
				// 			'' AS uraian,
				// 			0 AS satuan
				// 			FROM tbl_kategori_penilaian_rt_rw
				// 			GROUP BY kategori

				// 			UNION ALL


				// 			SELECT 
				// 			id,
				// 			id AS xid,
				// 			2 AS nor,
				// 			'' AS singkatan,
				// 			kategori,
				// 			uraian,
				// 			0 AS satuan
				// 			FROM tbl_kategori_penilaian_rt_rw

				// 			UNION ALL


				// 			SELECT 
				// 			MAX(id) AS id,
				// 			NULL AS xid,
				// 			3 AS nor,
				// 			'' AS singkatan,
				// 			kategori,
				// 			'Maksimal Skor' AS uraian,
				// 			COUNT(id) * 100 AS satuan
				// 			FROM tbl_kategori_penilaian_rt_rw
				// 			GROUP BY kategori
				// 		) a
				// 		LEFT JOIN (
				// 			SELECT 
				// 			kategori_penilaian_rt_rw_id,
				// 			nik,
				// 			satuan,
				// 			target,
				// 			capaian,
				// 			nilai
				// 			FROM tbl_penilaian_rt_rw
				// 			WHERE tbl_data_rt_rw_id = '$rt_rw_id' AND bulan = '$bulan'
				// 		) b ON b.kategori_penilaian_rt_rw_id = a.xid,
				// 		(SELECT @nh := 0) urut
				// 		ORDER BY a.id, a.kategori, nor
				// 		) a
				// ";

				$sql = "SELECT (CASE WHEN nor = 1 THEN @nh := @nh + 1 ELSE NULL END) AS urutHeader,
						a.*
						FROM (
						SELECT 
							a.nor, 
							a.id,
							a.xid, 
							a.singkatan, 
							a.kategori, 
							a.uraian,
							(CASE WHEN a.nor = 3 THEN a.satuan ELSE b.satuan END) AS satuan, 
							b.target, 
							b.capaian, 
						
							
							-- Tambahkan kolom total nilai per kategori di baris nor = 3
							(CASE WHEN a.nor = 3 THEN total.total_nilai ELSE b.nilai END) AS nilai

						FROM (
							-- Struktur 3 baris per kategori (nor 1=header, 2=indikator, 3=total)
							SELECT MIN(id) AS id, NULL AS xid, 1 AS nor, '' AS singkatan, kategori, '' AS uraian, 0 AS satuan
							FROM tbl_kategori_penilaian_rt_rw 
							GROUP BY kategori

							UNION ALL

							SELECT id, id AS xid, 2 AS nor, '' AS singkatan, kategori, uraian, 0 AS satuan 
							FROM tbl_kategori_penilaian_rt_rw 

							UNION ALL

							SELECT MAX(id) AS id, NULL AS xid, 3 AS nor, '' AS singkatan, kategori, 'Maksimal Skor' AS uraian, COUNT(id) * 100 AS satuan 
							FROM tbl_kategori_penilaian_rt_rw 
							GROUP BY kategori
						) a 

						-- Join detail nilai indikator
						LEFT JOIN (
							SELECT kategori_penilaian_rt_rw_id, nik, satuan, target, capaian, nilai 
							FROM tbl_penilaian_rt_rw 
							WHERE tbl_data_rt_rw_id = '$rt_rw_id' AND bulan = '$bulan'
						) b ON b.kategori_penilaian_rt_rw_id = a.xid

						-- Join total nilai per kategori, berdasarkan nama kategori
						LEFT JOIN (
							SELECT 
							k.kategori,
							SUM(p.nilai) AS total_nilai
							FROM tbl_penilaian_rt_rw p
							JOIN tbl_kategori_penilaian_rt_rw k ON p.kategori_penilaian_rt_rw_id = k.id
							WHERE p.tbl_data_rt_rw_id = '$rt_rw_id' AND p.bulan = '$bulan'
							GROUP BY k.kategori
						) total ON total.kategori = a.kategori

						) a, (SELECT @nh := 0) urut

						ORDER BY a.id, a.kategori, nor;

				";

				//echo $sql;exit;

				break;

			case "laporan_rekap_penilaian_rt_rw":
				$wheres = '';
				$whereb = '';
				if (in_array($this->auth['cl_user_group_id'], [2, 3, 4, 5])) {

					$where .= "

						and b.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and b.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and b.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";

					if (!empty($this->auth['cl_kelurahan_desa_id'])) {

						$where .= " and b.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}

					$whereb .= "

						and b.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and b.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and b.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and b.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
					$wheres .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');

				$bulan = $this->input->get('bulan');

				$wherebln = '';
				if ($rt_get) {

					$where .= " and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= " and CAST(b.rw as INT)= $rw_get";
				}


				if ($rt) {

					$where .= " and A.rt like '%" . $rt . "%'";
				}

				if (!empty($rw)) {
					$where .= " and b.rw like '%$rw%'";
				}

				if ($bulan) {
					$wherebln = " and a.bulan like '%" . $bulan . "%'";
				}

				if ($desa_id) {

					$where .= " and b.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= " and b.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}


					if ($this->input->get('kelurahan_id')) {

						$where .= " and b.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$desa_id = $this->auth['cl_kelurahan_desa_id'];


				$sql = "SELECT nor,
					a.nik,
					b.nama_lengkap,
					b.no_npwp,
					b.no_rekening,
					CONCAT( b.jab_rt_rw, ' ', 
						CASE 
							WHEN b.rt='' AND b.rw!=''
							THEN LPAD(b.rw, 3, '0')
							WHEN b.rt!='' AND b.rw!=''
							THEN CONCAT(LPAD(b.rt, 3, '0'), '/', LPAD(b.rw, 3, '0'))
							ELSE ''
						END
						) AS jabatan_lengkap,
					b.rw,
					b.rt,
					a.bulan,
					a.kategori,
					a.nil_indikator,
					a.nil_max,
					a.rekap,
					ceil((rekap / jml_indikator)) AS rata_rata,
					CASE
						WHEN ceil((rekap / jml_indikator)) < 60
						THEN '--'
						WHEN ceil((rekap / jml_indikator)) BETWEEN  60 AND 70
						THEN 'Cukup'
						WHEN ceil((rekap / jml_indikator)) BETWEEN  71 AND 80
						THEN 'Cukup Baik'
						WHEN ceil((rekap / jml_indikator)) BETWEEN  81 AND 90
						THEN 'Baik'
						WHEN ceil((rekap / jml_indikator)) >= 91
						THEN 'Sangat Baik'
					END AS standar,
					CASE
						WHEN ceil((rekap / jml_indikator)) < 60
						THEN 0
						WHEN ceil((rekap / jml_indikator)) BETWEEN  60 AND 70
						THEN 300000
						WHEN ceil((rekap / jml_indikator)) BETWEEN  71 AND 80
						THEN 600000
						WHEN ceil((rekap / jml_indikator)) BETWEEN  81 AND 90
						THEN 900000
						WHEN ceil((rekap / jml_indikator)) >= 91
						THEN 1200000
					END AS insentif
					FROM(
						SELECT
						nor,
						id,
						nik,
						nama_lengkap,
						jab_rt_rw,
						bulan,
						GROUP_CONCAT(singkatan
						ORDER BY urut) AS kategori,
						GROUP_CONCAT(nilai
						ORDER BY urut) AS nil_indikator,
						GROUP_CONCAT( (jml_sub*100) ORDER BY urut) AS nil_max,
						SUM(nilai) AS rekap,
						SUM(jml_sub_indikator) AS jml_indikator
						FROM(
							SELECT 
							1 AS nor,
							b.id,
							a.nik,
							b.nama_lengkap,
							b.jab_rt_rw,
							a.bulan,
							singkatan,
							c.id AS urut,
							COUNT(a.kategori) AS jml_sub,
							COUNT(a.uraian) AS jml_sub_indikator,
							SUM(a.nilai) AS nilai
							FROM tbl_penilaian_rt_rw a
							INNER JOIN tbl_data_rt_rw b ON a.tbl_data_rt_rw_id = b.id AND a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id
							LEFT JOIN tbl_kategori_penilaian_rt_rw c ON a.kategori_penilaian_rt_rw_id = c.id
							$where AND a.bulan='$bulan'
							GROUP BY a.kategori, a.tbl_data_rt_rw_id, b.nama_lengkap, a.bulan
							UNION ALL
							SELECT 
							2 AS nor,
							b.id,
							b.nik,
							b.nama_lengkap,
							b.jab_rt_rw,
							'' AS bulan,
							'' AS singkatan,
							99 AS urut,
							0 AS jml_sub,
							0 AS jml_sub_indikator,
							0 AS nilai
							FROM tbl_data_rt_rw b
							$where
							AND b.id NOT IN
							(SELECT tbl_data_rt_rw_id
							FROM tbl_penilaian_rt_rw
							WHERE bulan = '$bulan')
							GROUP BY b.id,b.nama_lengkap
						) sub
						GROUP BY id,nama_lengkap,jab_rt_rw
					) a
					LEFT JOIN tbl_data_rt_rw b ON a.id = b.id
					$where
					AND (b.status='Aktif' OR a.bulan!='')
					ORDER BY CONCAT(b.rw,'.',if(b.rt='' OR b.rt is null,'000',b.rt))";

				break;

			case "laporan_staff":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');

				$kat = $this->input->get('kat');

				$key = $this->input->get('key');

				$pns_nonpns = $this->input->get('pns_nonpns');


				if ($pns_nonpns == 'pns') {
					$where .= " and A.nip!=''";
				}

				if ($pns_nonpns == 'nonpns') {
					$where .= " and (A.nip='' OR A.nip is null)";
				}

				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}

				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}

				if ($key != '') {
					$where .= " AND $kat like '%$key%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "
					SELECT * FROM(
					SELECT A.*, B.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,CAST(if(no='-','99',no) AS UNSIGNED) as nor

					FROM tbl_data_pegawai_kel_kec A

					LEFT JOIN cl_kelurahan_desa B ON B.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					)z order by nor asc,z.id asc
				";

				//echo $sql;exit;

				break;

			case "laporan_bpjs":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');

				$kat = $this->input->get('kat');

				$key = $this->input->get('key');

				$pns_nonpns = $this->input->get('pns_nonpns');


				if ($pns_nonpns == 'pns') {
					$where .= " and A.nip!=''";
				}

				if ($pns_nonpns == 'nonpns') {
					$where .= " and (A.nip='' OR A.nip is null)";
				}

				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}

				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}

				if ($key != '') {
					$where .= " AND $kat like '%$key%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "
					SELECT * FROM(
					SELECT A.*, B.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,CAST(if(no='-','99',no) AS UNSIGNED) as nor

					FROM tbl_data_pegawai_kel_kec A

					LEFT JOIN cl_kelurahan_desa B ON B.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					)z order by nor asc,z.id asc
				";

				//echo $sql;exit;

				break;

			case "laporan_retribusi":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "
		
							SELECT A.*,
		
								E.nama as kecamatan, F.nama as kelurahan,
		
								DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat
		
							FROM tbl_data_retribusi_sampah A
						
							LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
		
							LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
		
							$where
		
							and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
		
							and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
		
							and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
		
							ORDER BY A.id DESC
		
						";



				//echo $sql;exit;

				break;

			case "laporan_keluarga":

				// Ambil param dari controller, bukan GET
				$desa_id = isset($p1['kelurahan']) ? $p1['kelurahan'] : "";
				$rt      = isset($p1['rt']) ? $p1['rt'] : "";
				$rw      = isset($p1['rw']) ? $p1['rw'] : "";

				$where = " WHERE 1=1 ";


				if (!empty($desa_id)) {

					$where .= " AND A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= " AND A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}


					if ($this->input->get('kelurahan_id')) {

						$where .= " AND A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				// // Filter kelurahan
				// if (!empty($desa_id)) {
				// 	$where .= " AND A.cl_kelurahan_desa_id = '$desa_id' ";
				// }

				// Filter RT dari tabel B
				if (!empty($rt)) {
					$where .= " AND B.rt = '$rt' ";
				}

				// Filter RW dari tabel B
				if (!empty($rw)) {
					$where .= " AND B.rw = '$rw' ";
				}


				$sql = "SELECT A.*,CONCAT(LEFT(A.no_kk,13),'xxx') AS no_kk2, B.nama_lengkap as nama_kepala_keluarga,B.alamat AS alamat_kepala_keluarga,B.jenis_kelamin AS jns_kel_keluarga,

					C.total as jumlah_anggota_keluarga,

					B.rw, B.rt,

					E.nama as kecamatan, F.nama as kelurahan,

					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

				FROM tbl_kartu_keluarga A

				LEFT JOIN (

					SELECT no_kk, rw, rt,nama_lengkap,alamat,jenis_kelamin

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

				ORDER BY
				LPAD(B.rw, 3, '0') ASC,
				LPAD(B.rt, 3, '0') ASC,
				A.id DESC";

				//echo $sql;exit;

				break;

			case "laporan_kebersihan":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,
						
						b.nama_status, E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_petugas_kebersihan A

					LEFT JOIN cl_status_pegawai b ON b.id = A.status_pegawai

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_sekolah":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM cl_master_pendidikan A

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_ibadah":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "SELECT A.*,


				DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

				FROM tbl_data_tempat_ibadah A 

				$where

				and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

				and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

				and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				ORDER BY A.id DESC";

				//echo $sql;exit;

				break;

			case "laporan_daftar_agenda":

				$where = " WHERE 1=1 ";

				$desa_id     = $this->input->get('kelurahan_id');
				$tgl_mulai   = $this->input->get('tgl_mulai');
				$tgl_selesai = $this->input->get('tgl_selesai');

				if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

					$where .= " AND DATE(a.tgl_kegiatan) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ";
				}

				if ($desa_id) {
					$where .= " AND a.cl_kelurahan_desa_id = '$desa_id' ";
				} else {
					if (!empty($this->auth['cl_kelurahan_desa_id'])) {
						$where .= " AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
					}
				}

				$sql = "SELECT a.*, 
						DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat
						FROM tbl_data_daftar_agenda a
						$where
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						ORDER BY a.id DESC";
				break;

			case "laporan_hasil_agenda":

				$where = " WHERE 1=1 ";

				$tgl_mulai   = $this->input->get('tgl_mulai');
				$tgl_selesai = $this->input->get('tgl_selesai');
				$desa_id     = $this->input->get('kelurahan_id');

				if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

					$where .= " AND DATE(a.tgl_hasil_agenda) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ";
				}

				if ($desa_id) {
					$where .= " AND a.cl_kelurahan_desa_id = '$desa_id' ";
				} else if (!empty($this->auth['cl_kelurahan_desa_id'])) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}

				$sql = "SELECT 
							a.*, 
							DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat,
							b.perihal_kegiatan AS agenda
						FROM tbl_data_hasil_agenda a
						LEFT JOIN tbl_data_daftar_agenda b 
							ON b.id = a.perihal_hasil_agenda
						$where
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						ORDER BY a.id DESC";
				break;

			case "laporan_pkl":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_pkl A


					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_penandatanganan":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_penandatanganan A


					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					ORDER BY A.id DESC

				";

				//echo $sql;exit;

				break;

			case "laporan_lorong":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_lorong A


					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";



				//echo $sql;exit;

				break;

			case "laporan_rekap_bulan":

				$desa_id = $this->input->post('kelurahan_id');
				$rt = $this->input->post('rt');
				$rw = $this->input->post('rw');
				$rt_get = $this->input->get('rt');
				$rw_get = $this->input->get('rw');
				$id = $this->input->post('id') ?: $this->input->get('id');

				$where = " WHERE 1=1 ";

				if ($rt_get) {
					$where .= "and a.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {
					$where .= "and a.rw like '%" . $rw_get . "%'";
				}


				if ($rt) {
					$where .= "and a.rt like '%" . $rt . "%'";
				}

				if ($rw) {
					$where .= "and a.rw like '%" . $rw . "%'";
				}


				if ($desa_id) {
					$where .= "and a.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {
					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {
						$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}


					if ($this->input->get('kelurahan_id')) {
						$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$bulan = $this->input->post('bulan') ?: $this->input->get('bulan');
				if ($bulan) {
					$where .= " AND a.bulan = '" . (int)$bulan . "'";
				}

				$sql = "SELECT a.*,
							a.cl_kelurahan_desa_id,
							DATE_FORMAT(MAX(a.create_date), '%d-%m-%Y %H:%i') AS tanggal_buat,
							SUM(a.jml_lk_wni + a.jml_lk_wna) AS jml_lk,
							SUM(a.jml_pr_wni + a.jml_pr_wna) AS jml_pr,
							SUM(a.lahir_lk_wni + a.lahir_lk_wna) AS jml_lahir_lk,
							SUM(a.lahir_pr_wni + a.lahir_pr_wna) AS jml_lahir_pr,
							SUM(a.mati_lk_wni + a.mati_lk_wna) AS jml_mati_lk,
							SUM(a.mati_pr_wni + a.mati_pr_wna) AS jml_mati_pr,
							SUM(a.datang_lk_wni + a.datang_lk_wna) AS jml_datang_lk,
							SUM(a.datang_pr_wni + a.datang_pr_wna) AS jml_datang_pr,
							SUM(a.pindah_lk_wni + a.pindah_lk_wna) AS jml_pindah_lk,
							SUM(a.pindah_pr_wni + a.pindah_pr_wna) AS jml_pindah_pr,
							SUM(a.non_permanen_lk_wni + a.non_permanen_lk_wna) AS jml_non_permanen_lk,
							SUM(a.non_permanen_pr_wni + a.non_permanen_pr_wna) AS jml_non_permanen_pr,

							SUM(a.jml_lk_wni + a.lahir_lk_wni - a.mati_lk_wni + a.datang_lk_wni - a.pindah_lk_wni + a.non_permanen_lk_wni) AS jml_akhir_lk_wni,
							SUM(a.jml_pr_wni + a.lahir_pr_wni - a.mati_pr_wni + a.datang_pr_wni - a.pindah_pr_wni + a.non_permanen_pr_wni) AS jml_akhir_pr_wni,
							SUM(a.jml_lk_wna + a.lahir_lk_wna - a.mati_lk_wna + a.datang_lk_wna - a.pindah_lk_wna + a.non_permanen_lk_wna) AS jml_akhir_lk_wna,
							SUM(a.jml_pr_wna + a.lahir_pr_wna - a.mati_pr_wna + a.datang_pr_wna - a.pindah_pr_wna + a.non_permanen_pr_wna) AS jml_akhir_pr_wna,

							SUM(a.lahir_lk_wni + a.lahir_pr_wni + a.lahir_lk_wna + a.lahir_pr_wna) AS jml_lahir,
							SUM(a.mati_lk_wni + a.mati_pr_wni + a.mati_lk_wna + a.mati_pr_wna) AS jml_mati,
							SUM(a.datang_lk_wni + a.datang_pr_wni + a.datang_lk_wna + a.datang_pr_wna) AS jml_datang,
							SUM(a.pindah_lk_wni + a.pindah_pr_wni + a.pindah_lk_wna + a.pindah_pr_wna) AS jml_datang,
							SUM(a.non_permanen_lk_wni + a.non_permanen_pr_wni + a.non_permanen_lk_wna + a.non_permanen_pr_wna) AS jml_non_permanen,
							
							SUM(
								a.jml_lk_wni + a.jml_lk_wna + a.lahir_lk_wni + a.lahir_lk_wna - a.mati_lk_wni - a.mati_lk_wna + a.datang_lk_wni + a.datang_lk_wna - a.pindah_lk_wni - a.pindah_lk_wna + a.non_permanen_lk_wni + a.non_permanen_lk_wna
							) AS jml_lk2,
							SUM(
								a.jml_pr_wni + a.jml_pr_wna + a.lahir_pr_wni + a.lahir_pr_wna - a.mati_pr_wni - a.mati_pr_wna + a.datang_pr_wni + a.datang_pr_wna - a.pindah_pr_wni - a.pindah_pr_wna + a.non_permanen_pr_wni + a.non_permanen_pr_wna
							) AS jml_pr2,

							SUM(
								a.kk_bln_ini + a.kk_lahir - a.kk_kematian + a.kk_pendatang - a.kk_pindah + a.kk_non_permanen
							) AS jml_kk2

						FROM tbl_data_rekap_bulanan a
						LEFT JOIN cl_kelurahan_desa b ON b.id = a.cl_kelurahan_desa_id

						$where

						and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and a.id = '" . $id . "'

						ORDER BY a.id DESC

					";

				//echo $sql;exit;

				break;

			case "laporan_ekspedisi":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_ekspedisi A

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";


				//echo $sql;exit;

				break;

			case "laporan_rekap_imb":

				$desa_id = $this->input->post('kelurahan_id');

				$rt = $this->input->post('rt');

				$rw = $this->input->post('rw');

				$rt_get = $this->input->get('rt');

				$rw_get = $this->input->get('rw');



				if ($rt_get) {

					$where .= "and A.rt like '%" . $rt_get . "%'";
				}

				if ($rw_get) {

					$where .= "and A.rw like '%" . $rw_get . "%'";
				}



				if ($rt) {

					$where .= "and A.rt like '%" . $rt . "%'";
				}

				if ($rw) {

					$where .= "and A.rw like '%" . $rw . "%'";
				}



				if ($desa_id) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and A.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "

					SELECT A.*,

					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_rekap_imb A

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					ORDER BY A.id DESC

				";


				//echo $sql;exit;
				break;


			//Data Penandatanganan
			case "data_penandatanganan":

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}
				$sql = "SELECT A.*, 
				B.nama AS kecamatan, C.nama AS kelurahan,
				DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
				FROM tbl_data_penandatanganan A
				LEFT JOIN cl_kecamatan B ON B.id = A.cl_kecamatan_id
				LEFT JOIN cl_kelurahan_desa C ON C.id = A.cl_kelurahan_desa_id
				$where
				ORDER BY A.id DESC
				";
				break;
			//end Data Penandatanganan

			//Daftar Agenda Kegiatan
			// case "daftar_agenda_kegiatan":
			// 	$where = " WHERE 1=1 ";

			// 	$tgl_mulai   = $this->input->post('tgl_mulai');
			// 	$tgl_selesai = $this->input->post('tgl_selesai');

			// 	if (!empty($tgl_mulai) && !empty($tgl_selesai)) {

			// 		// ubah format dari dd-mm-yyyy ke yyyy-mm-dd
			// 		$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
			// 		$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

			// 		$where .= " AND DATE(a.tgl_kegiatan) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ";
			// 	}


			// 	if ($this->auth['cl_user_group_id'] == 3) {
			// 		$where .= "
			// 			and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
			// 			and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
			// 			and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 			and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		";
			// 	}
			// 	if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
			// 		$where .= "
			// 			and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
			// 			and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
			// 			and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 			and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		";
			// 	}

			// 	$sql = "SELECT a.*, 
			// 	b.nama AS kecamatan, c.nama AS kelurahan,
			// 	DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
			// 	FROM tbl_data_daftar_agenda a
			// 	LEFT JOIN cl_kecamatan b ON b.id = a.cl_kecamatan_id
			// 	LEFT JOIN cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
			// 	$where
			// 	ORDER BY a.id DESC
			// 	";
			// break;
			case "daftar_agenda_kegiatan":

				$where = " WHERE 1=1 ";

				if (!empty($this->auth['tahun'])) {
					$where .= "
						AND YEAR(a.tgl_kegiatan) = '" . $this->auth['tahun'] . "'
					";
				}

				$tgl_mulai   = $this->input->post('tgl_mulai');
				$tgl_selesai = $this->input->post('tgl_selesai');

				if (!empty($tgl_mulai) && !empty($tgl_selesai)) {

					// dd-mm-yyyy → yyyy-mm-dd
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

					$where .= "
						AND DATE(a.tgl_kegiatan) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
					";
				}

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}

				$sql = "SELECT a.*,
						b.nama AS kecamatan,
						c.nama AS kelurahan,
						DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat
					FROM tbl_data_daftar_agenda a
					LEFT JOIN cl_kecamatan b ON b.id = a.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
					$where
					ORDER BY a.id DESC
				";
				break;
			//end Daftar Agenda Kegiatan

			//Laporan Hasil Agenda Kegiatan
			// case "laporan_hasil_kegiatan":

			// 	$where = " WHERE 1=1 ";

			// 	$tgl_mulai   = $this->input->post('tgl_mulai');
			// 	$tgl_selesai = $this->input->post('tgl_selesai');

			// 	if (!empty($tgl_mulai) && !empty($tgl_selesai)) {

			// 		// ubah format dari dd-mm-yyyy ke yyyy-mm-dd
			// 		$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
			// 		$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

			// 		$where .= " AND DATE(a.tgl_hasil_agenda) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ";
			// 	}

			// 	if ($this->auth['cl_user_group_id'] == 3) {
			// 		$where .= "
			// 			and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
			// 			and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
			// 			and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 			and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		";
			// 	}
			// 	if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
			// 		$where .= "
			// 			and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
			// 			and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
			// 			and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 			and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		";
			// 	}

			// 	$sql = "SELECT a.*, 
			// 	b.nama AS kecamatan, c.nama AS kelurahan,d.perihal_kegiatan as agenda,
			// 	DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
			// 	FROM tbl_data_hasil_agenda a
			// 	LEFT JOIN cl_kecamatan b ON b.id = a.cl_kecamatan_id
			// 	LEFT JOIN cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
			// 	LEFT JOIN tbl_data_daftar_agenda d ON d.id = a.perihal_hasil_agenda
			// 	$where
			// 	ORDER BY a.id DESC
			// 	";
			// break;

			case "laporan_hasil_kegiatan":

				$where = " WHERE 1=1 ";

				if (!empty($this->auth['tahun'])) {
					$where .= "
						AND YEAR(a.tgl_hasil_agenda) = '" . $this->auth['tahun'] . "'
					";
				}

				$tgl_mulai   = $this->input->post('tgl_mulai');
				$tgl_selesai = $this->input->post('tgl_selesai');

				if (!empty($tgl_mulai) && !empty($tgl_selesai)) {

					// dd-mm-yyyy → yyyy-mm-dd
					$tgl_mulai   = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_mulai)));
					$tgl_selesai = date('Y-m-d', strtotime(str_replace('-', '/', $tgl_selesai)));

					$where .= "
						AND DATE(a.tgl_hasil_agenda) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
					";
				}

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						AND a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						AND a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}

				$sql = "SELECT 
						a.*,
						b.nama AS kecamatan,
						c.nama AS kelurahan,
						d.perihal_kegiatan AS agenda,
						DATE_FORMAT(a.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat
					FROM tbl_data_hasil_agenda a
					LEFT JOIN cl_kecamatan b ON b.id = a.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
					LEFT JOIN tbl_data_daftar_agenda d ON d.id = a.perihal_hasil_agenda
					$where
					ORDER BY a.id DESC
				";
				break;
			//end Laporan Hasil Agenda Kegiatan

			//Data Pemohon
			case "data_permohonan":

				$sql = "SELECT A.*,B.jenis_surat,X.nama as prov,Z.nama as kab_kota,C.nama as kec,D.nama as desa,

						E.nama_pendidikan,F.nama_status_kawin,G.nama_agama,H.nama_pekerjaan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

						FROM tbl_registrasi_surat A

						LEFT JOIN cl_jenis_surat B ON A.cl_jenis_surat_id=B.id

						LEFT JOIN cl_provinsi X ON A.cl_provinsi_id=X.id

						LEFT JOIN cl_kab_kota Z ON A.cl_kab_kota_id=Z.id

						LEFT JOIN cl_kecamatan C ON A.cl_kecamatan_id=C.id

						LEFT JOIN cl_kelurahan_desa D ON A.cl_kelurahan_desa_id=D.id

						LEFT JOIN cl_pendidikan E ON A.pendidikan=E.id

						LEFT JOIN cl_status_kawin F ON A.status_kawin=F.id

						LEFT JOIN cl_agama G ON A.agama=G.id

						LEFT JOIN cl_jenis_pekerjaan H ON A.cl_jenis_pekerjaan_id=H.id

						WHERE A.status_data <> 'F'";

				if ($balikan == 'row_array') {

					$sql .= " AND A.id=" . $this->input->post('id');

					return $this->db->query($sql)->row_array();
				}

				break;
			//end Data Pemohon

			//Data Login
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

					WHERE A.username = '" . $p1 . "'

					ORDER BY A.id DESC

				";

				//echo $sql;

				break;
			//end Data Login

			//Data Penduduk Asing
			case "data_penduduk_asing":

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
							and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
							and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
							and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						";
				}
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
							and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
							and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
							and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						";
				}
				$sql = "
					SELECT A.*, 
					B.nama AS kecamatan, C.nama AS kelurahan,
					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
					FROM tbl_data_penduduk_asing A
					LEFT JOIN cl_kecamatan B ON B.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa C ON C.id = A.cl_kelurahan_desa_id
					$where
					ORDER BY A.id DESC
					";
				break;
			//end Data Penduduk Asing

			//Data Kendaraan
			case "data_kendaraan":

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}
				$sql = "SELECT A.*, 
				B.nama AS kecamatan, C.nama AS kelurahan,
				DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
				FROM tbl_data_kendaraan A
				LEFT JOIN cl_kecamatan B ON B.id = A.cl_kecamatan_id
				LEFT JOIN cl_kelurahan_desa C ON C.id = A.cl_kelurahan_desa_id
				$where
				ORDER BY A.id DESC
				";
				break;
			//end Data Kendaraan

			//Data Indikator SKM
			case "data_indikator_skm":

				// $where .= "
				// 	and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
				// ";

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				/* if ($this->auth['cl_user_group_id'] == 3) {
				}
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				} */
				$sql = "SELECT a.*,b.nama AS kecamatan FROM tbl_indikator_skm a
				LEFT JOIN cl_kecamatan b ON a.cl_kecamatan_id=b.id
				$where
				AND a.tahun<='" . $this->auth['tahun'] . "'
				ORDER BY a.id ASC";
				break;
			//end Data Indikator SKM

			//Data Indikator Sub Indikator RT RW
			case "form_sub_indikator_rt_rw":

				$sql = "
					SELECT * FROM tbl_kategori_penilaian_rt_rw a
					
					$where

					AND a.tahun<='" . $this->auth['tahun'] . "'

					ORDER BY id ASC";

				break;
			//end Data Indikator SKM

			//Data Indikator Sub Indikator RT RW
			case "data_sub_indikator_rt_rw":

				$sql = "
					SELECT * FROM tbl_kategori_penilaian_rt_rw a
					
					$where

					AND a.tahun<='" . $this->auth['tahun'] . "'

					ORDER BY id ASC";

				break;
			//end Data Indikator SKM

			//Data Penilaian SKM
			// case "data_penilaian_skm":
			// 	// print_r('xxxxxxx');exit;

			// 	if ($this->auth['cl_user_group_id'] == 3) {
			// 		$where .= "
			// 			and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 		";
			// 		$kelurahan_id = $this->input->post('kelurahan');
			// 		if ($kelurahan_id != '') {
			// 			$where .= "
			// 				and a.cl_kelurahan_desa_id = '" . $kelurahan_id . "'
			// 			";
			// 		}
			// 	}
			// 	if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
			// 		$where .= "
			// 			and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
			// 			and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		";
			// 	}
			// 	$sql = " SELECT c.*,ROUND((c.nilai / IFNULL(c.jumlah_indikator, 0)),2) AS rata_rata FROM(
			// 		SELECT a.id,a.jenis_kelamin,a.umur,a.deskripsi_jenis_surat,SUM(a.penilaian)AS nilai,COUNT(a.indikator_skm_id) AS jumlah_indikator,c.nama_pendidikan AS pendidikan,d.jenis_surat as jenis_surat,e.nama_pekerjaan AS pekerjaan,a.create_date,a.update_date,f.nama AS kelurahan,g.nama AS kecamatan
			// 		FROM tbl_penilaian_skm a
			// 		LEFT JOIN cl_pendidikan c ON a.cl_pendidikan_id=c.id
			// 		LEFT JOIN cl_jenis_surat d ON a.cl_jenis_surat_id=d.id 
			// 		LEFT JOIN cl_jenis_pekerjaan e ON a.cl_jenis_pekerjaan_id=e.id
			// 		LEFT JOIN cl_kelurahan_desa f ON a.cl_kelurahan_desa_id=f.id
			// 		INNER JOIN cl_kecamatan g ON a.cl_kecamatan_id=g.id
			// 		$where 
			// 		GROUP BY a.sesi_id
			// 		ORDER BY a.sesi_id ASC
			// 	)c";
			// break;

			case "data_penilaian_skm":

				if (!empty($this->auth['tahun'])) {
					$where .= "
						AND YEAR(a.create_date) = '" . $this->auth['tahun'] . "'
					";
				}

				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					";

					$kelurahan_id = $this->input->post('kelurahan');
					if (!empty($kelurahan_id)) {
						$where .= "
							AND a.cl_kelurahan_desa_id = '" . $kelurahan_id . "'
						";
					}
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						AND a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}

	
				$sql = "SELECT 
						c.*,
						ROUND((c.nilai / NULLIF(c.jumlah_indikator,0)),2) AS rata_rata
					FROM (
						SELECT 
							a.id,
							a.sesi_id,
							a.jenis_kelamin,
							a.umur,
							a.deskripsi_jenis_surat,
							SUM(a.penilaian) AS nilai,
							COUNT(a.indikator_skm_id) AS jumlah_indikator,
							c.nama_pendidikan AS pendidikan,
							d.jenis_surat,
							e.nama_pekerjaan AS pekerjaan,
							a.create_date,
							a.update_date,
							f.nama AS kelurahan,
							g.nama AS kecamatan
						FROM tbl_penilaian_skm a
						LEFT JOIN cl_pendidikan c ON a.cl_pendidikan_id = c.id
						LEFT JOIN cl_jenis_surat d ON a.cl_jenis_surat_id = d.id
						LEFT JOIN cl_jenis_pekerjaan e ON a.cl_jenis_pekerjaan_id = e.id
						LEFT JOIN cl_kelurahan_desa f ON a.cl_kelurahan_desa_id = f.id
						INNER JOIN cl_kecamatan g ON a.cl_kecamatan_id = g.id
						$where
						GROUP BY a.sesi_id
						ORDER BY a.sesi_id ASC
					) c
				";
				break;
			//end Data Penilaian SKM

			//Data Persuratan
			case "data_jenis_persuratan":
				if ($this->auth['cl_user_group_id'] == 3) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
					";
				}
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= "
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					";
				}
				$sql = "
					SELECT A.*, 
					B.nama AS kecamatan, C.nama AS kelurahan,
					DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat 
					FROM cl_jenis_surat A
					LEFT JOIN cl_kecamatan B ON B.id = A.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa C ON C.id = A.cl_kelurahan_desa_id
				
					ORDER BY A.id DESC
				";
				break;
			//endData Persuratan

			case "laporan_persuratan":

				// LAPORAN
				if ($this->input->get('tgl_mulai')) {

					$date_start = $this->input->get('tgl_mulai');

					$date_end = $this->input->get('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				// GET DATA
				if ($this->input->post('tgl_mulai')) {

					$date_start = $this->input->post('tgl_mulai');

					$date_end = $this->input->post('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {
					$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				}
				// else if ($this->auth['cl_kelurahan_desa_id'] == "0" && $this->auth['cl_kecamatan_id'] != "") {
				// 	$where .= " AND a.cl_kelurahan_desa_id = '0' AND a.cl_kecamatan_id = ".$this->auth['cl_kecamatan_id']."";
				// }


				if ($this->input->post('kelurahan_id') !== null) {
					if ($this->input->post('kelurahan_id') == '0') {

						if ($this->auth['cl_kelurahan_desa_id'] == "0" && $this->auth['cl_kecamatan_id'] != "") {
							$where .= " AND a.cl_kelurahan_desa_id = '0' AND a.cl_kecamatan_id = " . $this->auth['cl_kecamatan_id'] . "";
						}
					} else {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->post('kelurahan_id') . "'";
					}
				}


				// if ($this->input->get('kelurahan_id')!== null) {
				// 	if ($this->input->get('kelurahan_id') =='0'){

				// 		if ($this->auth['cl_kelurahan_desa_id'] == "0" && $this->auth['cl_kecamatan_id'] != "") {
				// 			$where .= " AND a.cl_kelurahan_desa_id = '0' AND a.cl_kecamatan_id = ".$this->auth['cl_kecamatan_id']."";
				// 		}
				// 	}else{

				// 		$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
				// 	}
				// }

				// $jenis_surat = $this->input->post('jenis_surat');
				// if ($jenis_surat) {
				// 	$where .= "and a.cl_jenis_surat_id='$jenis_surat'";
				// }

				// $jenis_ctk = $this->input->get('jenis_surat');
				// if ($jenis_ctk) {
				// 	$where .= "and a.cl_jenis_surat_id='$jenis_ctk'";
				// }

				if ($this->input->get('kelurahan_id') !== null && $this->input->get('kelurahan_id') !== '') {
					if ($this->input->get('kelurahan_id') == '0') {
						if ($this->auth['cl_kelurahan_desa_id'] == "0" && $this->auth['cl_kecamatan_id'] != "") {
							$where .= " AND a.cl_kelurahan_desa_id = '0' AND a.cl_kecamatan_id = " . $this->auth['cl_kecamatan_id'] . "";
						}
					} else {
						$where .= " AND a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$jenis_surat = $this->input->get_post('jenis_surat');
				if ($jenis_surat != '') {
					$where .= " AND a.cl_jenis_surat_id = '$jenis_surat'";
				}


				$nik = $this->input->post('nik');
				if ($nik != '') {
					$where .= "and d.nik='$nik'";
				}

				$nik = $this->input->get('nik');
				if ($nik != '') {
					$where .= "and d.nik='$nik'";
				}

				$nip = $this->input->post('nip');
				if ($nip != '') {
					$where .= "and e.nip='$nip'";
				}

				$nip = $this->input->get('nip');
				if ($nip != '') {
					$where .= "and e.nip='$nip'";
				}
				echo $nip;


				$sql = "SELECT a.*, b.jenis_surat, c.nama as nama_kelurahan_desa,e.nama as nama_penandatanganan,

						d.nama_lengkap,d.alamat,

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

						) as tanggal_layanan,'$nik' as filter_nik

					from tbl_data_surat a

					left join cl_jenis_surat b on b.id = a.cl_jenis_surat_id

					left join cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id

					left join tbl_data_penduduk d on d.id = a.tbl_data_penduduk_id
					
					left join tbl_data_penandatanganan e on a.nip=e.nip AND a.cl_kelurahan_desa_id=e.cl_kelurahan_desa_id

					$where

					and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					ORDER BY a.tgl_surat DESC

				";
				break;

			case "laporan_rekap_usaha":

				if ($this->input->get('tgl_mulai')) {

					$date_start = $this->input->get('tgl_mulai');

					$date_end = $this->input->get('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				if ($this->input->post('tgl_mulai')) {

					$date_start = $this->input->post('tgl_mulai');

					$date_end = $this->input->post('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}



				$desa_id = $this->input->post('kelurahan_id');

				if ($desa_id) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$jenis_surat = $this->input->post('jenis_surat');
				if ($jenis_surat) {
					$where .= "and a.cl_jenis_surat_id='$jenis_surat'";
				}

				$jenis_ctk = $this->input->get('jenis_surat');
				if ($jenis_ctk) {
					$where .= "and a.cl_jenis_surat_id='$jenis_ctk'";
				}



				$sql = "SELECT a.*, b.jenis_surat, c.nama as nama_kelurahan_desa,e.nama as nama_penandatanganan,

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
					
					left join tbl_data_penandatanganan e on a.nip=e.nip AND a.cl_kelurahan_desa_id=e.cl_kelurahan_desa_id

					$where

					and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and a.cl_jenis_surat_id = '19'

					and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					ORDER BY c.nama,a.tgl_surat ASC

				";


				break;

			case "laporan_rt_rw_excel":

				$where = " WHERE 1=1 ";

				$kolom      = $this->input->post('kat'); 
				$keyword    = $this->input->post('key');
				$status_tab = $this->input->post('status_tab');

				// ================== PENCARIAN ==================
				if (!empty($kolom) && !empty($keyword)) {
					$where .= " 
						AND {$kolom} LIKE '%" . $this->db->escape_like_str($keyword) . "%'
					";
				}

				// ================== HAK AKSES ==================
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= " 
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' 
					";
				}

				// ================== FILTER KELURAHAN ==================
				$kelurahan = $this->input->post('kelurahan');
				if (!empty($kelurahan)) {
					$where .= " 
						AND a.cl_kelurahan_desa_id = '" . $kelurahan . "' 
					";
				}

				// ================== STATUS TAB ==================
				if (empty($status_tab) || $status_tab == 'aktif') {

					$where .= "
						AND a.status = 'Aktif'
						AND a.pilih_tahun = '2026'
						AND a.id IN (
							SELECT MAX(id)
							FROM tbl_data_rt_rw
							WHERE status = 'Aktif'
							AND pilih_tahun = '2026'
							GROUP BY nik
						)
					";

				} elseif ($status_tab == 'tidak_aktif') {

					$where .= "
						AND a.status = 'Tidak Aktif'
						AND a.pilih_tahun <> '2026'
						AND a.id IN (
							SELECT MAX(id)
							FROM tbl_data_rt_rw
							WHERE status = 'Tidak Aktif'
							AND pilih_tahun <> '2026'
							GROUP BY nik
						)
					";
				}

				// ================== QUERY ==================
				$sql = "
					SELECT 
						a.*,
						CASE 
							WHEN a.jab_rt_rw = 'Ketua RW' 
								THEN CONCAT('Ketua RW ', LPAD(a.rw, 3, '0'))
							WHEN a.jab_rt_rw = 'Ketua RT' 
								THEN CONCAT('Ketua RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
							ELSE a.jab_rt_rw
						END AS jabatan_rt_rw,
						c.nama_agama,
						d.nama AS nama_kelurahan,
						a.alamat
					FROM tbl_data_rt_rw a
					LEFT JOIN cl_agama c ON a.agama = c.id
					LEFT JOIN cl_kelurahan_desa d 
						ON a.cl_kelurahan_desa_id = d.id
						AND a.cl_kecamatan_id = d.kecamatan_id
					$where
					ORDER BY a.rw, a.rt, a.id DESC
				";

				return $this->db->query($sql)->result_array();

			case "laporan_rekap_pengantar_kendaraan":

				if ($this->input->get('tgl_mulai')) {

					$date_start = $this->input->get('tgl_mulai');

					$date_end = $this->input->get('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				if ($this->input->post('tgl_mulai')) {

					$date_start = $this->input->post('tgl_mulai');

					$date_end = $this->input->post('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}



				$desa_id = $this->input->get_post('kelurahan_id');
				/* $sql_kel = "SELECT nama as nama_kelurahan from cl_kelurahan_desa where id='$desa_id'";
				$datakel = $this->db->query($sql_kel)->row();
				$nama_kelurahan = $datakel->nama_kelurahan; */

				if ($desa_id) {
					$where .= " AND f.cl_kelurahan_desa_id LIKE '%" . $desa_id . "%'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and f.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and f.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}

				$jenis_surat = $this->input->post('jenis_surat');
				if ($jenis_surat) {
					$where .= "and a.cl_jenis_surat_id='$jenis_surat'";
				}

				$jenis_ctk = $this->input->get('jenis_surat');
				if ($jenis_ctk) {
					$where .= "and a.cl_jenis_surat_id='$jenis_ctk'";
				}



				$sql = "SELECT a.*,a.info_tambahan as info_tambahan1,a.info_tambahan as info_tambahan2,a.info_tambahan as info_tambahan3,b.jenis_surat, c.nama as nama_kelurahan_desa,e.nama as nama_penandatanganan,f.asal_kelurahan,

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

						) as tanggal_layanan,
						c.nama as nama_kelurahan_desa

					from (
						SELECT a.*,JSON_UNQUOTE(JSON_EXTRACT(info_tambahan, '$.nopol')) AS nopol FROM tbl_data_surat a
						WHERE a.cl_jenis_surat_id = '55'
					) a

					left join cl_jenis_surat b on b.id = a.cl_jenis_surat_id

					left join tbl_data_kendaraan f on a.nopol =f.nopol
					left join cl_kelurahan_desa c ON c.id = f.cl_kelurahan_desa_id

					left join tbl_data_penduduk d on d.id = a.tbl_data_penduduk_id
					
					left join tbl_data_penandatanganan e on a.nip=e.nip AND a.cl_kelurahan_desa_id=e.cl_kelurahan_desa_id


					$where

					and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					ORDER BY a.tgl_surat ASC,c.nama

				";


				break;

			case "laporan_persuratan_rt_rw":

				if ($this->input->get('tgl_mulai')) {

					$date_start = $this->input->get('tgl_mulai');

					$date_end = $this->input->get('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				if ($this->input->post('tgl_mulai')) {

					$date_start = $this->input->post('tgl_mulai');

					$date_end = $this->input->post('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}



				$desa_id = $this->input->post('kelurahan_id');

				if ($desa_id) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
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
	
						and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						ORDER BY a.tgl_surat ASC
	
					";



				//echo $sql;exit;

				break;



			case "laporan_persuratan_masuk":

				if ($this->input->get('tgl_mulai')) {

					$date_start = $this->input->get('tgl_mulai');

					$date_end = $this->input->get('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}

				if ($this->input->post('tgl_mulai')) {

					$date_start = $this->input->post('tgl_mulai');

					$date_end = $this->input->post('tgl_selesai');



					if (isset($date_start) && isset($date_end)) {

						if ($date_start != "" && $date_end != "") {

							$where .= " AND a.tgl_surat BETWEEN '" . $date_start . "' AND '" . $date_end . "' ";
						}
					}
				}



				$desa_id = $this->input->post('kelurahan_id');

				if ($desa_id) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $desa_id . "'";
				} else {

					if ($this->auth['cl_kelurahan_desa_id'] != "" && $this->auth['cl_kelurahan_desa_id'] != "0") {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
					}



					if ($this->input->get('kelurahan_id')) {

						$where .= "and a.cl_kelurahan_desa_id = '" . $this->input->get('kelurahan_id') . "'";
					}
				}



				$sql = "
	
						select a.*, b.jenis_surat,d.sifat_surat, c.nama as nama_kelurahan_desa,
	
	
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
	
							) as tanggal_layanan,
							CONCAT(
	
								CASE DAYOFWEEK(a.tgl_diterima)
	
									WHEN 1 THEN 'Minggu'
	
									WHEN 2 THEN 'Senin'
	
									WHEN 3 THEN 'Selasa'
	
									WHEN 4 THEN 'Rabu'
	
									WHEN 5 THEN 'Kamis'
	
									WHEN 6 THEN 'Jumat'
	
									WHEN 7 THEN 'Sabtu'
	
								END, ', ',
	
								DATE_FORMAT(a.tgl_diterima, '%d-%m-%Y')
	
							) as tanggal_terima
	
						from tbl_data_surat_masuk a
	
						left join cl_jenis_surat_masuk b on b.id = a.cl_jenis_surat_masuk_id

						left join cl_sifat_surat d on d.id = a.cl_sifat_surat_masuk_id
	
						left join cl_kelurahan_desa c ON c.id = a.cl_kelurahan_desa_id
	
	
						$where
	
						and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'
	
						ORDER BY a.tgl_diterima DESC
	
					";



				//echo $sql;exit;

				break;

			// End laporan surat masuk umum

			//Dashboard
			case "summary_persuratan":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						
						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}



				$sql = "SELECT b.jenis_surat,SUM(total) as total,
					SUM(januari)januari,
					SUM(februari)februari,
					SUM(maret)maret,
					SUM(april)april,
					SUM(mei)mei,
					SUM(juni)juni,
					SUM(juli)juli,
					SUM(agustus)agustus,
					SUM(september)september,
					SUM(oktober)oktober,
					SUM(november)november,
					SUM(desember)desember 
				FROM( 
				SELECT cl_jenis_surat_id,COUNT(id)total,
					(CASE WHEN MONTH(tgl_surat)='1' THEN COUNT(id) ELSE 0 END)januari,
					(CASE WHEN MONTH(tgl_surat)='2' THEN COUNT(id) ELSE 0 END)februari,
					(CASE WHEN MONTH(tgl_surat)='3' THEN COUNT(id) ELSE 0 END)maret,
					(CASE WHEN MONTH(tgl_surat)='4' THEN COUNT(id) ELSE 0 END)april,
					(CASE WHEN MONTH(tgl_surat)='5' THEN COUNT(id) ELSE 0 END)mei,
					(CASE WHEN MONTH(tgl_surat)='6' THEN COUNT(id) ELSE 0 END)juni,
					(CASE WHEN MONTH(tgl_surat)='7' THEN COUNT(id) ELSE 0 END)juli,
					(CASE WHEN MONTH(tgl_surat)='8' THEN COUNT(id) ELSE 0 END)agustus,
					(CASE WHEN MONTH(tgl_surat)='9' THEN COUNT(id) ELSE 0 END)september,
					(CASE WHEN MONTH(tgl_surat)='10' THEN COUNT(id) ELSE 0 END)oktober,
					(CASE WHEN MONTH(tgl_surat)='11' THEN COUNT(id) ELSE 0 END)november,
					(CASE WHEN MONTH(tgl_surat)='12' THEN COUNT(id) ELSE 0 END)desember
				FROM tbl_data_surat $where
				
				GROUP BY cl_jenis_surat_id,MONTH(tgl_surat)

				)a RIGHT JOIN cl_jenis_surat b ON a.cl_jenis_surat_id=b.id
				WHERE b.jenis_surat!=''
				GROUP BY b.id
				ORDER BY total DESC LIMIT 10;
					

				";

				break;

			//End Dashboard

			// Surat Surat
			case "cetak_surat":

				$dbapak = array();

				$dibu = array();

				// $sql = "SELECT
				// 		b.*,
				// 		f.nama_pekerjaan,
				// 		g.nama_agama AS agama,
				// 		a.nip,
				// 		a.masa_berlaku,
				// 		a.status_usaha,
				// 		i.nama,
				// 		i.jabatan,
				// 		i.pangkat,
				// 		h.nama_status_kawin AS status_kawin,
				// 		e.nama AS nama_kelurahan,
				// 		d.nama AS nama_kecamatan,
				// 		c.nama AS nama_kota,
				// 		CONCAT(
				// 		DAY ( b.tgl_lahir ),
				// 		' ',
				// 		CASE
				// 			MONTH ( b.tgl_lahir ) 
				// 			WHEN 1 THEN
				// 			'Januari' 
				// 			WHEN 2 THEN
				// 			'Februari' 
				// 			WHEN 3 THEN
				// 			'Maret' 
				// 			WHEN 4 THEN
				// 			'April' 
				// 			WHEN 5 THEN
				// 			'Mei' 
				// 			WHEN 6 THEN
				// 			'Juni' 
				// 			WHEN 7 THEN
				// 			'Juli' 
				// 			WHEN 8 THEN
				// 			'Agustus' 
				// 			WHEN 9 THEN
				// 			'September' 
				// 			WHEN 10 THEN
				// 			'Oktober' 
				// 			WHEN 11 THEN
				// 			'November' 
				// 			WHEN 12 THEN
				// 			'Desember' 
				// 		END,
				// 		' ',
				// 		YEAR ( b.tgl_lahir ) 
				// 		) AS tanggal_lahir,
				// 		a.data_surat,
				// 		j.nm_golongan,
				// 		a.tgl_surat 
				// 		FROM tbl_data_surat a
				// 		LEFT JOIN (
				// 			SELECT
				// 			*,(
				// 			'0' 
				// 			) wn,
				// 			'' AS nama_wn,
				// 			'' AS keperluan_passport,
				// 			'' AS tgl_kel_passport,
				// 			'' AS tgl_akhir_passport,
				// 			'' AS alamat_asal,
				// 			'' AS jenis_passport 
				// 			FROM
				// 			tbl_data_penduduk UNION
				// 			SELECT
				// 			id,
				// 			no_pengenalan,
				// 			'' AS cl_status_hubingan_keluarga_id,
				// 			no_passport,
				// 			nama_lengkap,
				// 			tempat_lahir,
				// 			tgl_lahir,
				// 			jenis_kelamin,
				// 			agama,
				// 			'' AS status_kawin,
				// 			'' AS pendidikan,
				// 			'' AS gol_darah,
				// 			cl_jenis_pekerjaan_id,
				// 			'' AS golongan_darah,
				// 			cl_provinsi_id,
				// 			cl_kab_kota_id,
				// 			cl_kecamatan_id,
				// 			cl_kelurahan_desa_id,
				// 			rt,
				// 			rw,
				// 			alamat,
				// 			kode_pos,
				// 			'' AS status_data,
				// 			create_date,
				// 			create_by,
				// 			update_date,
				// 			update_by,
				// 			FILE,(
				// 			'1' 
				// 			) wn,
				// 			kewarganegaraan AS nama_wn,
				// 			keperluan AS keperluan_passport,
				// 			tgl_kel_passport AS tgl_kel_passport,
				// 			tgl_akhir_passport AS tgl_akhir_passport,
				// 			alamat_asal AS alamat_asal,
				// 			jenis_passport AS jenis_passport 
				// 			FROM
				// 			tbl_data_penduduk_asing
				// 		)b ON a.tbl_data_penduduk_id=b.id
				// 		LEFT JOIN cl_kab_kota c ON c.id = a.cl_kab_kota_id
				// 		LEFT JOIN cl_kecamatan d ON d.id = a.cl_kecamatan_id
				// 		LEFT JOIN cl_kelurahan_desa e ON e.id = a.cl_kelurahan_desa_id
				// 		LEFT JOIN cl_jenis_pekerjaan f ON f.id = b.cl_jenis_pekerjaan_id
				// 		LEFT JOIN cl_agama g ON g.id = b.agama
				// 		LEFT JOIN cl_status_kawin h ON h.id = b.status_kawin
				// 		LEFT JOIN tbl_data_penandatanganan i ON i.id = a.id_penandatanganan
				// 		LEFT JOIN cl_golongan j ON i.pangkat = j.pangkat
				// 		WHERE a.id = '" . $p3 . "'
				// ";

				$sql = "SELECT 
						a.id,
						b.*,
						f.nama_pekerjaan,
						g.nama_agama AS agama,
						a.nip,
						a.masa_berlaku,
						a.status_usaha,
						i.nama,
						i.jabatan,
						i.pangkat,
						h.nama_status_kawin AS status_kawin,
						e.nama AS nama_kelurahan,
						d.nama AS nama_kecamatan,
						c.nama AS nama_kota,
						CONCAT(
							DAY(b.tgl_lahir), ' ',
							CASE MONTH(b.tgl_lahir)
								WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
								WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
								WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
								WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
							END, ' ', YEAR(b.tgl_lahir)
						) AS tanggal_lahir,
						a.data_surat,
						j.nm_golongan,
						a.tgl_surat
					FROM tbl_data_surat a
					LEFT JOIN (
						SELECT
							id,
							nik,
							no_kk,
							cl_status_hubungan_keluarga_id,
							'' AS no_passport,            
							nama_lengkap,
							tempat_lahir,
							tgl_lahir,
							jenis_kelamin,
							agama,
							status_kawin,
							pendidikan,
							gol_darah,
							cl_jenis_pekerjaan_id,
							cl_provinsi_id,
							cl_kab_kota_id,
							cl_kecamatan_id,
							cl_kelurahan_desa_id,
							rt,
							rw,
							alamat,
							kode_pos,
							status_data,
							create_date,
							create_by,
							update_date,
							update_by,
							file,
							'0' AS wn,
							'' AS nama_wn,
							'' AS keperluan_passport,
							'' AS tgl_kel_passport,
							'' AS tgl_akhir_passport,
							'' AS alamat_asal,
							'' AS jenis_passport
						FROM tbl_data_penduduk

						UNION ALL

						SELECT
							id,
							'' AS nik,
							no_pengenalan AS no_kk,
							'' AS cl_status_hubungan_keluarga_id,
							no_passport,
							nama_lengkap,
							tempat_lahir,
							tgl_lahir,
							jenis_kelamin,
							agama,
							'' AS status_kawin,
							'' AS pendidikan,
							'' AS gol_darah,
							cl_jenis_pekerjaan_id,
							cl_provinsi_id,
							cl_kab_kota_id,
							cl_kecamatan_id,
							cl_kelurahan_desa_id,
							rt,
							rw,
							alamat,
							kode_pos,
							'' AS status_data,
							create_date,
							create_by,
							update_date,
							update_by,
							file,
							'1' AS wn,
							kewarganegaraan AS nama_wn,
							keperluan AS keperluan_passport,
							tgl_kel_passport,
							tgl_akhir_passport,
							alamat_asal,
							jenis_passport
						FROM tbl_data_penduduk_asing
					) b ON a.tbl_data_penduduk_id = b.id
					LEFT JOIN cl_kab_kota c ON c.id = a.cl_kab_kota_id
					LEFT JOIN cl_kecamatan d ON d.id = a.cl_kecamatan_id
					LEFT JOIN cl_kelurahan_desa e ON e.id = a.cl_kelurahan_desa_id
					LEFT JOIN cl_jenis_pekerjaan f ON f.id = b.cl_jenis_pekerjaan_id
					LEFT JOIN cl_agama g ON g.id = b.agama
					LEFT JOIN cl_status_kawin h ON h.id = b.status_kawin
					LEFT JOIN tbl_data_penandatanganan i ON i.id = a.id_penandatanganan
					LEFT JOIN cl_golongan j ON i.pangkat = j.pangkat
					WHERE a.id = '" . $p3 . "'
				";

				$data = $this->db->query($sql)->row_array();


				if ($data) {

					$sbapak = "SELECT A.*, B.nama_pekerjaan, C.nama_agama as agama,

							D.nama_status_kawin as status_kawin,
							CONCAT(
								DAY(A.tgl_lahir),' ',
								CASE MONTH(A.tgl_lahir) 
								  WHEN 1 THEN 'Januari' 
								  WHEN 2 THEN 'Februari' 
								  WHEN 3 THEN 'Maret' 
								  WHEN 4 THEN 'April' 
								  WHEN 5 THEN 'Mei' 
								  WHEN 6 THEN 'Juni' 
								  WHEN 7 THEN 'Juli' 
								  WHEN 8 THEN 'Agustus' 
								  WHEN 9 THEN 'September'
								  WHEN 10 THEN 'Oktober' 
								  WHEN 11 THEN 'November' 
								  WHEN 12 THEN 'Desember' 
								END,' ',
								YEAR(A.tgl_lahir)
							  ) AS tanggal_lahir

						FROM tbl_data_penduduk A

						LEFT JOIN cl_jenis_pekerjaan B ON B.id = A.cl_jenis_pekerjaan_id

						LEFT JOIN cl_agama C ON C.id = A.agama

						LEFT JOIN cl_status_kawin D ON D.id = A.status_kawin

						WHERE A.no_kk = '" . $data['no_kk'] . "' AND (A.cl_status_hubungan_keluarga_id = '1' 
						AND A.jenis_kelamin = 'LAKI-LAKI')

					";

					$dbapak = $this->db->query($sbapak)->row_array();


					$sibu = "

						SELECT A.*, B.nama_pekerjaan, C.nama_agama as agama,

							D.nama_status_kawin as status_kawin,
							CONCAT(
								DAY(A.tgl_lahir),' ',
								CASE MONTH(A.tgl_lahir) 
								  WHEN 1 THEN 'Januari' 
								  WHEN 2 THEN 'Februari' 
								  WHEN 3 THEN 'Maret' 
								  WHEN 4 THEN 'April' 
								  WHEN 5 THEN 'Mei' 
								  WHEN 6 THEN 'Juni' 
								  WHEN 7 THEN 'Juli' 
								  WHEN 8 THEN 'Agustus' 
								  WHEN 9 THEN 'September'
								  WHEN 10 THEN 'Oktober' 
								  WHEN 11 THEN 'November' 
								  WHEN 12 THEN 'Desember' 
								END,' ',
								YEAR(A.tgl_lahir)
							  ) AS tanggal_lahir

						FROM tbl_data_penduduk A

						LEFT JOIN cl_jenis_pekerjaan B ON B.id = A.cl_jenis_pekerjaan_id

						LEFT JOIN cl_agama C ON C.id = A.agama

						LEFT JOIN cl_status_kawin D ON D.id = A.status_kawin

						WHERE A.no_kk = '" . $data['no_kk'] . "' AND 
						(A.cl_status_hubungan_keluarga_id = '2' OR A.cl_status_hubungan_keluarga_id = '1') 
						AND A.jenis_kelamin = 'PEREMPUAN'

					";

					$dibu = $this->db->query($sibu)->row_array();
				}



				$ssurat = "SELECT A.*,

					CONCAT(
						DAY(tgl_surat),' ',
						CASE MONTH(tgl_surat) 
						  WHEN 1 THEN 'Januari' 
						  WHEN 2 THEN 'Februari' 
						  WHEN 3 THEN 'Maret' 
						  WHEN 4 THEN 'April' 
						  WHEN 5 THEN 'Mei' 
						  WHEN 6 THEN 'Juni' 
						  WHEN 7 THEN 'Juli' 
						  WHEN 8 THEN 'Agustus' 
						  WHEN 9 THEN 'September'
						  WHEN 10 THEN 'Oktober' 
						  WHEN 11 THEN 'November' 
						  WHEN 12 THEN 'Desember' 
						END,' ',
						YEAR(tgl_surat)
					  ) AS tanggal_surat,
					  data_surat

					FROM tbl_data_surat A

					WHERE A.id = '" . $p3 . "'

				";

				// A.cl_jenis_surat_id = '".$p1."' AND A.nik = '".$p2."'

				$dsurat = $this->db->query($ssurat)->row_array();
				$array['data_surat'] = $dsurat['data_surat'];



				if ($dsurat['info_tambahan'] != "") {

					$dsurat['info_tambahan'] = json_decode($dsurat['info_tambahan'], true);
				}


				$info = $dsurat['info_tambahan'];


				// var_dump($info['data_dokumen'][0]['tgl_lahir_anak']);
				// exit;
				@$dsurat['info_tambahan']['hari_pengecekan'] = tgl_indo_hari($data['tgl_surat']);
				@$dsurat['info_tambahan']['tgl_pengecekan'] = terbilang_hari(tgl_aja($data['tgl_surat']));
				@$dsurat['info_tambahan']['bln_pengecekan'] = bln_aja($data['tgl_surat']);
				@$dsurat['info_tambahan']['thn_pengecekan'] = terbilang_hari(thn_aja($data['tgl_surat']));
				@$dsurat['info_tambahan']['thn_pengecekanx'] = thn_aja($info['tgl_pengecekan']);
				@$dsurat['info_tambahan']['tgl_pengecekanx'] = ($info['tgl_pengecekan']);
				@$dsurat['info_tambahan']['tgl_lahir'] = tgl_indo($info['tgl_lahir']);
				@$dsurat['info_tambahan']['tgl_kematian'] = tgl_indo($info['tgl_kematian']);
				@$dsurat['info_tambahan']['tgl_pernyataan'] = tgl_indo($info['tgl_pernyataan']);
				@$dsurat['info_tambahan']['tgl_meninggal'] = tgl_indo($info['tgl_meninggal']);
				@$dsurat['info_tambahan']['hari_meninggal'] = tgl_indo_hari($info['tgl_meninggal']);
				@$dsurat['info_tambahan']['tgl_penguburan'] = tgl_indo($info['tgl_penguburan']);
				@$dsurat['info_tambahan']['hari_penguburan'] = tgl_indo_hari($info['tgl_penguburan']);
				@$dsurat['info_tambahan']['hari_kematian'] = tgl_indo_hari($info['tgl_kematian']);
				@$dsurat['info_tambahan']['hari_kelahiran_anak'] = tgl_indo_hari($info['data_dokumen'][0]['tgl_lahir_anak']);
				@$dsurat['info_tambahan']['hari_panggilan'] = tgl_indo_hari($info['tgl_panggilan']);
				@$dsurat['info_tambahan']['hari_lahir'] = tgl_indo_hari($info['tgl_lahir']);
				@$dsurat['info_tambahan']['tgl_kebakaran'] = tgl_indo($info['tgl_kebakaran']);
				@$dsurat['info_tambahan']['tgl_surat_pernyataan'] = tgl_indo($info['tgl_surat_pernyataan']);
				@$dsurat['info_tambahan']['tgl_surat_pengantar'] = tgl_indo($info['tgl_surat_pengantar']);
				@$dsurat['info_tambahan']['tgl_pergi'] = tgl_indo($info['tgl_pergi']);
				@$dsurat['info_tambahan']['tgl_acara'] = tgl_indo($info['tgl_acara']);
				@$dsurat['info_tambahan']['tgl_mulai_berlaku'] = tgl_indo($info['tgl_mulai_berlaku']);
				@$dsurat['info_tambahan']['tgl_selesai_berlaku'] = tgl_indo($info['tgl_selesai_berlaku']);
				@$dsurat['info_tambahan']['tgl_pindah'] = tgl_indo($info['tgl_pindah']);
				@$dsurat['info_tambahan']['tgl_awal_penugasan'] = tgl_indo($info['tgl_awal_penugasan']);
				@$dsurat['info_tambahan']['tgl_akhir_penugasan'] = tgl_indo($info['tgl_akhir_penugasan']);
				@$dsurat['info_tambahan']['hari_awal_tugas'] = tgl_indo_hari($info['tgl_awal_penugasan']);
				@$dsurat['info_tambahan']['hari_akhir_tugas'] = tgl_indo_hari($info['tgl_akhir_penugasan']);
				@$dsurat['info_tambahan']['tgl_awal_kegiatan'] = tgl_indo($info['tgl_awal_kegiatan']);
				@$dsurat['info_tambahan']['tgl_akhir_kegiatan'] = tgl_indo($info['tgl_akhir_kegiatan']);
				@$dsurat['info_tambahan']['hari_awal_kegiatan'] = tgl_indo_hari($info['tgl_awal_kegiatan']);
				@$dsurat['info_tambahan']['hari_akhir_kegiatan'] = tgl_indo_hari($info['tgl_akhir_kegiatan']);
				@$dsurat['info_tambahan']['tgl_undangan'] = tgl_indo($info['tgl_undangan']);
				@$dsurat['info_tambahan']['tgl_undangan_akhir'] = tgl_indo($info['tgl_undangan_akhir']);
				@$dsurat['info_tambahan']['hari_awal_undangan'] = tgl_indo_hari($info['tgl_undangan']);
				@$dsurat['info_tambahan']['hari_akhir_undangan'] = tgl_indo_hari($info['tgl_undangan_akhir']);
				@$dsurat['info_tambahan']['tgl_pernyataan_keg'] = tgl_indo($info['tgl_pernyataan_keg']);
				@$dsurat['info_tambahan']['hari_pernyataan_keg'] = tgl_indo_hari($info['tgl_pernyataan_keg']);
				@$dsurat['info_tambahan']['hari_kejadian_izin'] = tgl_indo_hari($info['tgl_kejadian_izin']);


				$array['pemohon'] = $data;
				$array['pemohon']['umur'] = hitungUmur($data['tgl_lahir']);
				if ($this->auth['cl_user_group_id'] == 3) {
					$nama_kecamatan = $this->db->where('id', $dsurat['cl_kecamatan_id'])->get('cl_kecamatan')->row('nama');
					$an = [
						'start' => 'an.&nbsp;&nbsp;',
						'center' => 'Camat ' . ucwords(strtolower($nama_kecamatan)),
						'end' => '',
					];
					$comm = ',';
					$array['pemohon']['pangkat'] = $array['pemohon']['pangkat'] . ', ' . $array['pemohon']['nm_golongan'];
				} else {
					$an = [
						'start' => '',
						'center' => 'a.n Lurah',
						'end' => '',
					];
					$comm = '';
				}

				$jab = strtolower($data['jabatan']);

				if (
					(strpos($jab, 'lurah') !== false || strpos($jab, 'camat') !== false)
					&& strpos($jab, 'sekretaris') === false
				) {
					$array['ttd'] = [
						0 => [
							'start' => '',
							'center' => $data['jabatan'] . $comm,
							'end' => '',
						]
					];
				} else {
					$array['ttd'] = [
						0 => $an,
						1 => [
							'start' => '',
							'center' => $data['jabatan'] . $comm,
							'end' => '',
						]
					];
				}

				$array['bapak'] = $dbapak;

				$array['ibu'] = $dibu;

				$array['surat'] = $dsurat;

				break;

			//End Cetak Surat

			//Surat Himbauan
			case "cetak_himbauan":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}


				$no_surat = str_replace("xxx", "/", "$p1");
				$sql = "

					SELECT A.*, B.nama_lengkap, B.nik as nik_id, C.sifat_surat,

						E.nama as kecamatan, F.nama as kelurahan,

						CONCAT(
							DAY(A.tgl_surat),' ',
							CASE MONTH(A.tgl_surat) 
							  WHEN 1 THEN 'Januari' 
							  WHEN 2 THEN 'Februari' 
							  WHEN 3 THEN 'Maret' 
							  WHEN 4 THEN 'April' 
							  WHEN 5 THEN 'Mei' 
							  WHEN 6 THEN 'Juni' 
							  WHEN 7 THEN 'Juli' 
							  WHEN 8 THEN 'Agustus' 
							  WHEN 9 THEN 'September'
							  WHEN 10 THEN 'Oktober' 
							  WHEN 11 THEN 'November' 
							  WHEN 12 THEN 'Desember' 
							END,' ',
							YEAR(A.tgl_surat)
						  ) AS tanggal_surat,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,A.file

					FROM tbl_data_surat_himbauan A

					LEFT JOIN tbl_data_penduduk B ON B.id = A.tbl_data_penduduk_id

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					LEFT JOIN cl_sifat_surat C on A.cl_sifat_surat = C.id

					$where AND A.no_surat='$no_surat'

					ORDER BY A.id DESC

				";
				$data = $this->db->query($sql)->row_array();


				$ttd = " select * from tbl_data_penandatanganan A $where AND LEFT(A.jabatan,5)='Lurah'";
				$xttd = $this->db->query($ttd)->row_array();

				$array['surat'] = $data;
				$array['ttd'] = $xttd;

				break;
			//End Cetak Surat Himbauan

			//Data Surat
			case "data_surat":

				if ($this->auth['cl_user_group_id'] == 3) {
					if ($this->auth['id'] == '42') {
						$ur = "";
					} else {
						$ur = "and user_id = '" . $this->auth['id'] . "'";
					}

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = 0

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'
						$ur
						

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}

				// $sql = "SELECT A.*, B.nama_lengkap, B.nik as nik_id, C.jenis_surat,

				// 		E.nama as kecamatan, F.nama as kelurahan,

				// 		CONCAT(
				// 			DAY(A.tgl_surat),' ',
				// 			CASE MONTH(A.tgl_surat) 
				// 			  WHEN 1 THEN 'Januari' 
				// 			  WHEN 2 THEN 'Februari' 
				// 			  WHEN 3 THEN 'Maret' 
				// 			  WHEN 4 THEN 'April' 
				// 			  WHEN 5 THEN 'Mei' 
				// 			  WHEN 6 THEN 'Juni' 
				// 			  WHEN 7 THEN 'Juli' 
				// 			  WHEN 8 THEN 'Agustus' 
				// 			  WHEN 9 THEN 'September'
				// 			  WHEN 10 THEN 'Oktober' 
				// 			  WHEN 11 THEN 'November' 
				// 			  WHEN 12 THEN 'Desember' 
				// 			END,' ',
				// 			YEAR(A.tgl_surat)
				// 		) AS tanggal_surat,

				// 		DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,A.file,G.nama as nama_ttd,G.jabatan as jabatan_ttd

				// 	FROM tbl_data_surat A

				// 	LEFT JOIN (
				// 		SELECT *,('0')wn,'' AS nama_wn,'' AS keperluan_passport,'' AS tgl_kel_passport,'' AS tgl_akhir_passport,'' AS alamat_asal,'' AS jenis_passport 
				// 		FROM tbl_data_penduduk 
				// 		WHERE cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
				// 		UNION
				// 		SELECT id,no_pengenalan,'' AS cl_status_hubingan_keluarga_id,no_passport,nama_lengkap,tempat_lahir,tgl_lahir,jenis_kelamin,agama,'' AS status_kawin,'' AS pendidikan,'' AS gol_darah,cl_jenis_pekerjaan_id,'' AS golongan_darah,cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,cl_kelurahan_desa_id,rt,rw,alamat,kode_pos,'' AS status_data,create_date,create_by,update_date,update_by,FILE,('1')wn ,kewarganegaraan AS nama_wn,keperluan AS keperluan_passport,tgl_kel_passport AS tgl_kel_passport,tgl_akhir_passport AS tgl_akhir_passport,alamat_asal AS alamat_asal,jenis_passport AS jenis_passport
				// 		FROM tbl_data_penduduk_asing 
				// 		WHERE cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
				// 	) B  ON B.id = A.tbl_data_penduduk_id AND A.cl_kelurahan_desa_id=B.cl_kelurahan_desa_id

				// 	LEFT JOIN cl_jenis_surat C ON C.id = A.cl_jenis_surat_id

				// 	LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

				// 	LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

				// 	LEFT JOIN tbl_data_penandatanganan G ON A.nip=G.nip AND A.cl_kelurahan_desa_id=G.cl_kelurahan_desa_id

				// 	$where AND A.status_esign=0

				// 	ORDER BY A.id DESC

				// ";

				$sql = "SELECT  A.*,
						B.nama_lengkap,
						B.nik AS nik_id,        -- nik dari penduduk lokal (atau '' jika baris dari penduduk_asing)
						B.no_passport AS no_passport, -- passport dari penduduk asing (atau '' jika baris dari penduduk lokal)
						C.jenis_surat,
						E.nama AS kecamatan,
						F.nama AS kelurahan,
						CONCAT(
							DAY(A.tgl_surat),' ',
							CASE MONTH(A.tgl_surat)
							WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
							WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
							WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
							WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
							END,' ',
							YEAR(A.tgl_surat)
						) AS tanggal_surat,
						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') AS tanggal_buat,
						A.file,
						G.nama AS nama_ttd,
						G.jabatan AS jabatan_ttd
						FROM tbl_data_surat A

						LEFT JOIN (
							SELECT
								id,
								nik,
								'' AS no_pengenalan,
								'' AS no_passport,
								nama_lengkap,
								tempat_lahir,
								tgl_lahir,
								jenis_kelamin,
								agama,
								status_kawin,
								pendidikan,
								gol_darah,
								cl_jenis_pekerjaan_id,
								golongan_darah,
								cl_provinsi_id,
								cl_kab_kota_id,
								cl_kecamatan_id,
								cl_kelurahan_desa_id,
								rt,
								rw,
								alamat,
								kode_pos,
								status_data,
								nop,                 -- 🔥 ditambahkan di sini
								create_date,
								create_by,
								update_date,
								update_by,
								file,
								'0' AS wn,
								'' AS nama_wn,
								'' AS keperluan_passport,
								'' AS tgl_kel_passport,
								'' AS tgl_akhir_passport,
								'' AS alamat_asal,
								'' AS jenis_passport
							FROM tbl_data_penduduk
							WHERE cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

							UNION ALL

							SELECT
								id,
								'' AS nik,
								no_pengenalan,
								no_passport,
								nama_lengkap,
								tempat_lahir,
								tgl_lahir,
								jenis_kelamin,
								agama,
								'' AS status_kawin,
								'' AS pendidikan,
								'' AS gol_darah,
								cl_jenis_pekerjaan_id,
								'' AS golongan_darah,
								cl_provinsi_id,
								cl_kab_kota_id,
								cl_kecamatan_id,
								cl_kelurahan_desa_id,
								rt,
								rw,
								alamat,
								kode_pos,
								'' AS status_data,
								nop,                 -- 🔥 ditambahkan di sini
								create_date,
								create_by,
								update_date,
								update_by,
								file,
								'1' AS wn,
								kewarganegaraan AS nama_wn,
								keperluan AS keperluan_passport,
								tgl_kel_passport,
								tgl_akhir_passport,
								alamat_asal,
								jenis_passport
							FROM tbl_data_penduduk_asing
							WHERE cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						) B
						ON B.id = A.tbl_data_penduduk_id
						AND A.cl_kelurahan_desa_id = B.cl_kelurahan_desa_id

						LEFT JOIN cl_jenis_surat C ON C.id = A.cl_jenis_surat_id
						LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
						LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id
						LEFT JOIN tbl_data_penandatanganan G ON A.nip = G.nip AND A.cl_kelurahan_desa_id = G.cl_kelurahan_desa_id

						$where
						ORDER BY A.id DESC
				";
				// ini di where kondisinya (disisi where)
				// $where AND A.status_esign = 0
				break;
			//End Data Surat

			//Data Esign
			case "data_esign":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "
	
							and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
							and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
							and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "
	
							and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
							and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
							and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
							and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
	
						";
				}



				$sql = "
	
						SELECT A.*, B.nama_lengkap, B.nik as nik_id, C.jenis_surat,
	
							E.nama as kecamatan, F.nama as kelurahan,
	
							CONCAT(
								DAY(A.tgl_surat),' ',
								CASE MONTH(A.tgl_surat) 
								  WHEN 1 THEN 'Januari' 
								  WHEN 2 THEN 'Februari' 
								  WHEN 3 THEN 'Maret' 
								  WHEN 4 THEN 'April' 
								  WHEN 5 THEN 'Mei' 
								  WHEN 6 THEN 'Juni' 
								  WHEN 7 THEN 'Juli' 
								  WHEN 8 THEN 'Agustus' 
								  WHEN 9 THEN 'September'
								  WHEN 10 THEN 'Oktober' 
								  WHEN 11 THEN 'November' 
								  WHEN 12 THEN 'Desember' 
								END,' ',
								YEAR(A.tgl_surat)
							) AS tanggal_surat,
	
							DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,A.file,G.nama as nama_ttd,G.jabatan as jabatan_ttd
	
						FROM tbl_data_surat A
	
						LEFT JOIN tbl_data_penduduk B ON B.id = A.tbl_data_penduduk_id
	
						LEFT JOIN cl_jenis_surat C ON C.id = A.cl_jenis_surat_id
	
						LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id
	
						LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

						LEFT JOIN tbl_data_penandatanganan G ON A.nip=G.nip
	
						$where AND A.status_esign>0
	
						ORDER BY A.id DESC
	
					";
				break;
			//End Data Esign

			//Data Keluarga
			case "data_keluarga":

				if ($this->auth['cl_user_group_id'] == 3) { // User Kecamatan

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) { // User kelurahan

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "SELECT A.*, B.nama_lengkap as nama_kepala_keluarga, B.rw AS rw_penduduk, B.rt AS rt_penduduk,

						C.total as jumlah_anggota_keluarga,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_kartu_keluarga A

					LEFT JOIN (

						SELECT no_kk, nama_lengkap, rw, rt

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

					ORDER BY CASE 
							WHEN (B.rw IS NULL OR B.rw = '' OR B.rt IS NULL OR B.rt = '') THEN 1
							ELSE 0
						END ASC,
						LPAD(B.rw, 3, '0') ASC,
						LPAD(B.rt, 3, '0') ASC,
						A.id DESC

				";
				break;
			//End Data Keluarga

			//Data Penduduk
			case "data_penduduk":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and F.id = '" . $kelurahan . "'";
				}

				$sql = "SELECT A.*,

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
			//End Data Penduduk

			//Data Penduduk Asing
			case "data_penduduk_asing":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and F.nama = '" . $kelurahan . "'";
				}

				$sql = "

					SELECT A.*,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_penduduk_asing A

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					ORDER BY A.id DESC

				";
				break;
			//End Data Penduduk Asing

			//Data KTP
			case "data_ktp":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$sql = "

					SELECT A.*,

						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_rekam_ktp A

					LEFT JOIN cl_agama B ON B.id = A.agama

					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin

					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					ORDER BY A.id DESC

				";
				break;
			//end Data KTP

			//Data Pegawai Kel Kec
			case "data_pegawai_kel_kec":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and B.id = '" . $kelurahan . "'";
				}

				$sql = "
					SELECT * FROM(
					SELECT A.*, B.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,CAST(if(no='-','99',no) AS UNSIGNED) as nor

					FROM tbl_data_pegawai_kel_kec A

					LEFT JOIN cl_kelurahan_desa B ON B.id = A.cl_kelurahan_desa_id

					$where

					and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					)z order by z.cl_kelurahan_desa_id ASC,z.nor asc,z.id asc

				";

				break;
			//end Data Pegawai Kel Kec

			//Data Dasawisma
			case "data_dasawisma":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "

					SELECT A.*,

						B.nama_agama, C.nama_status_kawin, D.nama_pendidikan,

						E.nama as kecamatan, F.nama as kelurahan,

						DATE_FORMAT(A.create_date, '%d-%m-%Y %H:%i') as tanggal_buat

					FROM tbl_data_dasawisma A

					LEFT JOIN cl_agama B ON B.id = A.agama

					LEFT JOIN cl_status_kawin C ON C.id = A.status_kawin

					LEFT JOIN cl_pendidikan D ON D.id = A.pendidikan

					LEFT JOIN cl_kecamatan E ON E.id = A.cl_kecamatan_id

					LEFT JOIN cl_kelurahan_desa F ON F.id = A.cl_kelurahan_desa_id

					$where

					ORDER BY A.id DESC

				";

				break;
			//end Data Dasawisma

			//Tbl Data User
			case "tbl_user":

				$sql = "

					SELECT A.*, B.user_group

					FROM tbl_user A

					LEFT JOIN cl_user_group B ON B.id = A.cl_user_group_id

					$where

					ORDER BY id DESC
				";

				break;
			//end Tbl Data User

			//Data Lorong
			case "data_lorong":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}
				$kelurahan = $this->input->post('kelurahan');
				if ($kelurahan) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}


				$sql = "

					SELECT *

					FROM tbl_data_lorong A

					$where

				";

				break;
			//end Data Lorong

			//Data Rekap Bulanan
			case "data_rekap_bulan":

				if (!empty($this->auth['tahun'])) {
					$where .= "
						AND YEAR(a.create_date) = '" . $this->auth['tahun'] . "'
					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}

				$kelurahan = $this->input->post('kelurahan');
				if ($kelurahan) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				// Filter berdasarkan kolom 'bulan' (jika ada input)
				$bulan = $this->input->post('bulan');
				if ($bulan) {
					$where .= " AND a.bulan = '" . (int)$bulan . "'";
				}

				// Query tetap menggunakan tgl_cetak untuk menampilkan nama bulan
				$sql = "SELECT a.*, 
							(CASE MONTH(a.tgl_cetak)
								WHEN 1 THEN 'Januari'
								WHEN 2 THEN 'Februari'
								WHEN 3 THEN 'Maret'
								WHEN 4 THEN 'April'
								WHEN 5 THEN 'Mei'
								WHEN 6 THEN 'Juni'
								WHEN 7 THEN 'Juli'
								WHEN 8 THEN 'Agustus'
								WHEN 9 THEN 'September'
								WHEN 10 THEN 'Oktober'
								WHEN 11 THEN 'November'
								WHEN 12 THEN 'Desember'
							END) AS bulan_indo
						FROM tbl_data_rekap_bulanan a
						$where
						ORDER BY id DESC";

				break;
			//end Data Rekap Bulanan

			//Data Ekspedisi
			case "data_ekspedisi":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where = "where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}


				$sql = "

				SELECT *

				FROM tbl_data_ekspedisi 

				$where

				ORDER BY id DESC

				";

				break;
			//Data Ekspedisi

			//end Data Rekap IMB
			case "data_rekap_imb":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where = "where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}


				$sql = "

				SELECT *

				FROM tbl_data_rekap_imb 

				$where

				ORDER BY id DESC

				";

				break;
			//end Data Rekap IMB

			//Data UMKM
			case "data_umkm":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "SELECT A.* FROM cl_master_umkm A

					$where

					ORDER BY id DESC";

				break;

			// data umkm

			//Surat Masuk
			case "surat_masuk":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and A.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and A.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and A.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and A.cl_kelurahan_desa_id = 0

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

				and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

				and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

				and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				and year(tgl_surat) = '" . $this->auth['tahun'] . "'

				";
				}

				$sql = "

				SELECT A.*, B.jenis_surat, C.sifat_surat

				FROM tbl_data_surat_masuk A 

				LEFT JOIN cl_jenis_surat_masuk B on A.cl_jenis_surat_masuk_id = B.id

				LEFT JOIN cl_sifat_surat C on A.cl_sifat_surat_masuk_id = C.id

				$where

				ORDER BY A.tgl_diterima DESC

				";

				break;
			//end Surat Masuk

			//data surat lain
			case "surat_lain":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

				and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

				and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

				and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				and year(tgl_surat) = '" . $this->auth['tahun'] . "'

			  ";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

				and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

				and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

				and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				and year(tgl_surat) = '" . $this->auth['tahun'] . "'

			  ";
				}

				$sql = " SELECT * FROM tbl_data_surat_lain

				$where

				";

				break;
			//end data surat lain

			//Surat Himbauan
			case "surat_himbauan":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

						and year(tgl_surat) = '" . $this->auth['tahun'] . "'

					";
				}

				$sql = "

					SELECT A.*, C.sifat_surat

					FROM tbl_data_surat_himbauan A 

					LEFT JOIN cl_sifat_surat C on A.cl_sifat_surat = C.id


					$where

				";

				break;
			// end surat himbauan

			//Broadcast
			case "broadcast":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%'

					";
				}

				$sql = "
				
				SELECT * FROM tbl_data_broadcast
				$where
				";

				$sql = $this->db->query($sql);
				$rows = [];
				foreach ($sql->result_array() as $row) {
					$nama_kelurahan = '';
					for ($i = 0; $i < count(json_decode($row['tujuan'])); $i++) {
						$nama_kelurahan .= $this->db->where('id', json_decode($row['tujuan'])[$i])->get('cl_kelurahan_desa')->row('nama') . ', ';
					}
					$row['nama_kelurahan'] = $nama_kelurahan;
					$rows[] = $row;
				}

				return json_encode($rows);

				break;
			//end Broadcast

			//Notif Broadcast
			case "notif_broadcast":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "
	
							and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
							and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$this->db->like('tujuan', $this->auth['cl_kelurahan_desa_id'])->update('tbl_data_broadcast', ['flag_reg' => 'Y']);

					$where .= "
	
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
							and tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%'
	
						";
				}

				$sql = "
					
					SELECT * FROM tbl_data_broadcast
					$where
					";

				$sql = $this->db->query($sql);
				$rows = [];
				foreach ($sql->result_array() as $row) {
					$nama_kelurahan = '';
					for ($i = 0; $i < count(json_decode($row['tujuan'])); $i++) {
						$nama_kelurahan .= $this->db->where('id', json_decode($row['tujuan'])[$i])->get('cl_kelurahan_desa')->row('nama') . ', ';
					}
					$row['nama_kelurahan'] = $nama_kelurahan;
					$rows[] = $row;
				}

				return json_encode($rows);

				break;
			//end Notif Broadcast

			//Data PKL
			case "data_pkl":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}


				$sql = "

					SELECT *

					FROM tbl_data_pkl A

					$where

				";

				break;
			//end Data PKL

			//Data Petugas Kebersihan
			case "data_petugas_kebersihan":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = " SELECT a.*,b.nama_status FROM tbl_data_petugas_kebersihan a

						 LEFT JOIN cl_status_pegawai b on a.status_pegawai=b.id

						$where
				";

				break;
			//end Data Petugas Kebersihan

			//data_retribusi_sampah
			case "data_retribusi_sampah":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}
				$sql = "

				SELECT a.*,b.nama_bulan FROM tbl_data_retribusi_sampah a
				left join cl_pilih_bulan b on a.bulan = b.id

				$where

				";

				break;
			//end data_retribusi_sampah

			//data RT RW
			// case "data_rt_rw":

			// 	if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

			// 		$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
			// 	} else {
			// 		$where .= "";
			// 	}
			// 	$kelurahan = $this->input->post('kelurahan');

			// 	if ($kelurahan) {

			// 		$where .= "and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
			// 	}

			// 	$sql = "SELECT a.*, 
			// 	CASE 
			// 		WHEN a.jab_rt_rw = 'Ketua RW' THEN CONCAT('Ketua RW ', LPAD(a.rw, 3, '0'))
			// 		WHEN a.jab_rt_rw = 'Ketua RT' THEN CONCAT('Ketua RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
			// 		ELSE a.jab_rt_rw
			// 	END AS jabatan_rt_rw,

			// 	c.nama_agama,d.nama,a.alamat as nama_keluahan_desa from tbl_data_rt_rw a

				
			// 	left join cl_agama c on a.agama=c.id

			// 	left join cl_kelurahan_desa d on a.cl_kelurahan_desa_id=d.id and a.cl_kecamatan_id=d.kecamatan_id

			// 	$where

			// 	ORDER BY CASE a.status
			// 				WHEN 'Aktif' THEN 0
			// 				WHEN 'Tidak Aktif' THEN 1
			// 				ELSE 2
			// 			END,
			// 	a.rw,a.rt,a.id DESC

			// 	";
			// break;

			case "data_rt_rw":

				$where = " WHERE 1=1 ";

				$kolom      = $this->input->post('kat'); 
				$keyword    = $this->input->post('key');
				$status_tab = $this->input->post('status_tab');

				// ================== MAPPING KOLOM (AMAN) ==================
				$map_kolom = [
					'a.nama_lengkap' => 'nama_lengkap',
					'a.nik'          => 'nik',
					'a.jab_rt_rw'    => 'jab_rt_rw'
				];

				$kolom_sub = isset($map_kolom[$kolom]) ? $map_kolom[$kolom] : '';

				// ================== PENCARIAN ==================
				
				if(!empty($kolom) && !empty($keyword)) {

					$where .= " 
						AND {$kolom} LIKE '%" . $this->db->escape_like_str($keyword) . "%'
					";
				}

				// ================== HAK AKSES USER ==================
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= " 
						AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' 
					";
				}

				// ================== FILTER KELURAHAN ==================
				$kelurahan = $this->input->post('kelurahan');
				if (!empty($kelurahan)) {
					$where .= " 
						AND a.cl_kelurahan_desa_id = '" . $kelurahan . "' 
					";
				}

				// ================== TAB STATUS ==================
				if (empty($status_tab) || $status_tab == 'aktif') {

					// ===== AKTIF (TIDAK DIUBAH) =====
					$where .= "
						AND a.status = 'Aktif'
						AND a.pilih_tahun = '2026'
						AND a.id IN (
							SELECT MAX(id)
							FROM tbl_data_rt_rw
							WHERE status = 'Aktif'
							AND pilih_tahun = '2026'
							GROUP BY nik
						)
					";

				} elseif ($status_tab == 'tidak_aktif') {

					// ===== TIDAK AKTIF (FINAL FIX) =====
					$where .= "
						AND a.status = 'Tidak Aktif'
						AND a.pilih_tahun <> '2026'
						AND a.id IN (
							SELECT MAX(id)
							FROM tbl_data_rt_rw
							WHERE status = 'Tidak Aktif'
							AND pilih_tahun <> '2026' GROUP BY nik)
					";

					// 🔴 LIKE KHUSUS DI SUBQUERY (TANPA alias a.)
				
				
				}

				// ================== QUERY FINAL ==================
				$sql = "SELECT a.*,
						CASE 
							WHEN a.jab_rt_rw = 'Ketua RW' 
								THEN CONCAT('Ketua RW ', LPAD(a.rw, 3, '0'))
							WHEN a.jab_rt_rw = 'Ketua RT' 
								THEN CONCAT('Ketua RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
							ELSE a.jab_rt_rw
						END AS jabatan_rt_rw,
						c.nama_agama,
						d.nama,
						a.alamat AS nama_keluahan_desa
					FROM tbl_data_rt_rw a
					LEFT JOIN cl_agama c ON a.agama = c.id
					LEFT JOIN cl_kelurahan_desa d 
						ON a.cl_kelurahan_desa_id = d.id
						AND a.cl_kecamatan_id = d.kecamatan_id
					$where
					ORDER BY a.rw, a.rt, a.id DESC
				";

				break;

			//end RT RW

			//data RT RW
			// case "usulan_penilaian_rt_rw":

			// 	if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

			// 		$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
			// 	} else {
			// 		$where .= "";
			// 	}
			// 	$kelurahan = $this->input->post('kelurahan');

			// 	if ($kelurahan) {

			// 		$where .= "and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
			// 	}

			// 	// $sql = " SELECT a.*,c.nama_agama,d.nama as nama_keluahan_desa from tbl_data_rt_rw a

			// 	// LEFT JOIN tbl_data_penduduk b on a.nik=b.nik and b.status_data=a.nik

			// 	// LEFT JOIN cl_agama c on a.agama=c.id

			// 	// LEFT JOIN cl_kelurahan_desa d on a.cl_kelurahan_desa_id=d.id and a.cl_kecamatan_id=d.kecamatan_id

			// 	// $where

			// 	// ORDER BY a.id DESC

			// 	$sql = " SELECT * FROM tbl_usulan_ke_sekcam ";

			// break;
			//end RT RW

			//Penilaian RT RW
			case "penilaian_rt_rw":

				$where = " WHERE 1=1 ";
				$tahun_login = $this->auth['tahun'];

				$where .= " AND a.pilih_tahun = '$tahun_login' 
							AND a.status = 'aktif' ";

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}

				$kelurahan = $this->input->post('kelurahan');
				if ($kelurahan) {
					$where .= " AND a.cl_kelurahan_desa_id = '" . $kelurahan . "' ";
				}


				$on_bulan = '';
				if ($this->input->post('bulan') != '') {
					$on_bulan = " AND '" . $this->input->post('bulan') . "'=c.bulan";
				}

				$sql = "SELECT a.id as rt_rw_id,c.penilaian_id as id,c.tgl_surat,c.kategori_penilaian_rt_rw_id,c.kategori,c.uraian,c.satuan,c.target,c.capaian,
					CEIL(SUM(c.nilai)/COUNT(c.id)) AS nilai,

					(CASE
						WHEN e.id is not null then 1 else 0 end) as lpj,  

					CASE 
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) < 60 THEN '--'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 60 AND 70 THEN 'Cukup'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 71 AND 80 THEN 'Cukup Baik'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 81 AND 90 THEN 'Baik'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) >= 91 THEN 'Sangat Baik'
						ELSE 'Belum Dinilai'
					END AS standar_nilai,
					CASE 
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) < 60 THEN 'Rp. 0,00'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 60 AND 70 THEN 'Rp. 300.000,00'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 71 AND 80 THEN 'Rp. 600.000,00'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) BETWEEN 81 AND 90 THEN 'Rp. 900.000,00'
						WHEN CEIL(SUM(c.nilai)/COUNT(c.id)) >=91 THEN 'Rp. 1.200.000,00'
						ELSE 0
					END AS usulan_insentif,
					c.create_date,c.create_by,a.nik,a.nama_lengkap,c.bulan,b.nama_bulan,d.nama AS nama_keluahan_desa,

					CONCAT(
							DAY(c.tgl_surat),' ',
							CASE MONTH(c.tgl_surat) 
							  WHEN 1 THEN 'Januari' 
							  WHEN 2 THEN 'Februari' 
							  WHEN 3 THEN 'Maret' 
							  WHEN 4 THEN 'April' 
							  WHEN 5 THEN 'Mei' 
							  WHEN 6 THEN 'Juni' 
							  WHEN 7 THEN 'Juli' 
							  WHEN 8 THEN 'Agustus' 
							  WHEN 9 THEN 'September'
							  WHEN 10 THEN 'Oktober' 
							  WHEN 11 THEN 'November' 
							  WHEN 12 THEN 'Desember' 
							END,' ',
							YEAR(c.tgl_surat)
						) AS tanggal_surat, 

						 DATE_FORMAT(c.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,
						 CASE 
					WHEN a.jab_rt_rw = 'Ketua RW' THEN CONCAT('Ketua RW ', LPAD(a.rw, 3, '0'))
					WHEN a.jab_rt_rw = 'PJ RW' THEN CONCAT('PJ RW ', LPAD(a.rw, 3, '0'))
					WHEN a.jab_rt_rw = 'Ketua RT' THEN CONCAT('Ketua RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
					WHEN a.jab_rt_rw = 'PJ RT' THEN CONCAT('PJ RT ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
					ELSE a.jab_rt_rw
					END AS jabatan_rt_rw
					
					FROM tbl_data_rt_rw a

					LEFT JOIN tbl_penilaian_rt_rw c ON c.tbl_data_rt_rw_id = a.id $on_bulan
					
					LEFT JOIN cl_pilih_bulan b ON c.bulan = b.id
					
					LEFT JOIN cl_kelurahan_desa d on a.cl_kelurahan_desa_id=d.id and a.cl_kecamatan_id=d.kecamatan_id

					LEFT JOIN tbl_lpj_rtrw e on a.nik=e.nik
					$where

					GROUP BY a.id, MONTH(tgl_surat), YEAR(tgl_surat)
					ORDER BY CONCAT(a.rw, '.', IF(a.rt = '' OR a.rt is null, '000', a.rt))
					         

				";

				break;
			//end Penilaian RT RW

			//Rekap RT RW
			case "rekap_penilaian_kelrtrw":

				$where = " WHERE 1=1 ";
				$rw = $this->input->post('rw');
				$bulan = $this->input->post('bulan');
				$tahun_login = $this->auth['tahun'] ;

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				} else {
					$where .= "";
				}

				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= " and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				if ($rw != '') {
					$where .= " AND CAST(a.rw as INT)='$rw'";
				}
				if ($bulan != '') {
					$where .= " AND c.bulan=$bulan";
				}

				/* filter WAJIB tahun surat */
				$where .= " AND pilih_tahun = '$tahun_login' ";

				$sql = " SELECT c.id,a.nama_lengkap,c.tgl_surat,c.kategori_penilaian_rt_rw_id,c.kategori,c.uraian,c.satuan,c.target,c.capaian,
					ceil(SUM(c.nilai)/COUNT(c.id)) AS nilai,
					c.create_date,c.create_by,a.nik,a.nama_lengkap,b.nama_bulan,a.rw,d.nama AS nama_keluahan_desa,

					CONCAT(
							DAY(c.tgl_surat),' ',
							CASE MONTH(c.tgl_surat) 
							  WHEN 1 THEN 'Januari' 
							  WHEN 2 THEN 'Februari' 
							  WHEN 3 THEN 'Maret' 
							  WHEN 4 THEN 'April' 
							  WHEN 5 THEN 'Mei' 
							  WHEN 6 THEN 'Juni' 
							  WHEN 7 THEN 'Juli' 
							  WHEN 8 THEN 'Agustus' 
							  WHEN 9 THEN 'September'
							  WHEN 10 THEN 'Oktober' 
							  WHEN 11 THEN 'November' 
							  WHEN 12 THEN 'Desember' 
							END,' ',
							YEAR(c.tgl_surat)
						) AS tanggal_surat, 

						 DATE_FORMAT(c.create_date, '%d-%m-%Y %H:%i') as tanggal_buat,
						 CASE 
							WHEN a.rw!='' AND a.rt='' THEN CONCAT(a.jab_rt_rw,' ', LPAD(a.rw, 3, '0'))
							WHEN a.rw!='' AND a.rt!='' THEN CONCAT(a.jab_rt_rw,' ', LPAD(a.rt, 3, '0'), '/', LPAD(a.rw, 3, '0'))
							ELSE a.jab_rt_rw
							END AS jabatan_rt_rw
					
					from tbl_data_rt_rw a

					left join tbl_penilaian_rt_rw c ON c.tbl_data_rt_rw_id = a.id

					left join cl_pilih_bulan b ON c.bulan = b.id

					
					left join cl_kelurahan_desa d on a.cl_kelurahan_desa_id=d.id and a.cl_kecamatan_id=d.kecamatan_id

					$where

					GROUP BY a.nama_lengkap, MONTH(tgl_surat), YEAR(tgl_surat)

					ORDER BY c.bulan,CONCAT(a.rw,'.',if(a.rt='' OR a.rt is null,'000',a.rt))

				";


			break;
			//end Rekap RT RW

			//data sekolah
			case "data_sekolah":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "

					SELECT A.*

					FROM cl_master_pendidikan A

					$where

					ORDER BY id DESC

				";

				break;
			//end data sekolah

			//Data Detail Sekolah
			case "data_detail_sekolah":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "
	
							and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "
	
							and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
							and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
	
						";
				}

				$sql = "
	

						SELECT b.id, a.npsn,nama_sekolah,bp,status,alamat,pd AS jumlah_siswa,rombel AS jumlah_rombel,
						guru AS jumlah_guru,pegawai AS jumlah_pegawai,r_kelas AS ruang_kelas,
						r_lab AS jumlah_ruang_lab,r_perpus AS ruang_perpus, thn_ajar
						FROM cl_master_pendidikan a INNER JOIN cl_master_dapodik b ON a.npsn=b.npsn
	
						$where
	
						ORDER BY id DESC
	
					";

				break;
			//end Data Detail Sekolah

			//Data Tempat Ibadah
			case "data_tempat_ibadah":

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' ";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "

					SELECT *

					FROM tbl_data_tempat_ibadah A

					$where

				";

				break;
			//end Data Tempat Ibadah

			//Data Faskes
			case "data_faskes":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}
				$kelurahan = $this->input->post('kelurahan');
				if ($kelurahan) {

					$where .= "and a.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = " SELECT A.*

					FROM tbl_data_rs A

					$where

					ORDER BY id DESC

				";

				break;
			//Data Faskes

			//Data Wamis
			case "data_wamis":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "
	
							and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
							and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "
	
							and a.cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
							and a.cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
							and a.cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
							and a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
	
						";
				}
				$kelurahan = $this->input->post('kelurahan');

				if ($kelurahan) {

					$where .= "and A.cl_kelurahan_desa_id = '" . $kelurahan . "'";
				}

				$sql = "SELECT a.*,b.alamat,b.rw,b.rt
	
						FROM tbl_data_wamis a

						left join tbl_data_penduduk b on a.nik=b.nik
	
						$where
	
					";
				break;
			//Data Wamis

			//Data Kunjungan Rumah
			case "data_kunjungan_rumah":

				$sql = "

					SELECT A.*

					FROM tbl_data_kunjungan_rumah A

					$where

				";

				break;
			//end Data Kunjungan Rumah

			//Data Kerja Bakti
			case "data_kerja_bakti":

				$sql = "

					SELECT A.*

					FROM tbl_data_kerja_bakti A

					$where

				";

				break;
			//Data Kerja Bakti

			//Data Notulen Rapat
			case "data_notulen_rapat":

				$sql = "

					SELECT A.*

					FROM tbl_data_notulen_rapat A

					$where

				";

				break;
			//end Data Notulen Rapat

			//Menu
			case "menu":

				$sql = "

					SELECT a.tbl_menu_id, b.nama_menu, b.type_menu, b.icon_menu, b.url, b.ref_tbl

					FROM tbl_user_prev_group a

					LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id

					WHERE a.cl_user_group_id=" . $this->auth['cl_user_group_id'] . "

					AND (b.type_menu='P' OR b.type_menu='PC' OR b.type_menu='R') AND b.status='1'

					ORDER BY b.urutan ASC

				";

				$parent = $this->db->query($sql)->result_array();

				$menu = array();

				foreach ($parent as $v) {

					$menu[$v['tbl_menu_id']] = array();

					$menu[$v['tbl_menu_id']]['parent'] = $v['nama_menu'];

					$menu[$v['tbl_menu_id']]['icon_menu'] = $v['icon_menu'];

					$menu[$v['tbl_menu_id']]['url'] = $v['url'];

					$menu[$v['tbl_menu_id']]['type_menu'] = $v['type_menu'];

					$menu[$v['tbl_menu_id']]['judul_kecil'] = $v['ref_tbl'];

					$menu[$v['tbl_menu_id']]['child'] = array();

					$sql = "

						SELECT a.tbl_menu_id, b.nama_menu, b.url, b.icon_menu , b.type_menu, b.ref_tbl

							FROM tbl_user_prev_group a

						LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id

						WHERE a.cl_user_group_id=" . $this->auth['cl_user_group_id'] . "

						AND (b.type_menu = 'C' OR b.type_menu = 'CHC')

						AND b.status='1' AND b.parent_id=" . $v['tbl_menu_id'] . "

						ORDER BY b.urutan ASC

						";

					$child = $this->db->query($sql)->result_array();

					foreach ($child as $x) {

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']] = array();

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['menu'] = $x['nama_menu'];

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['type_menu'] = $x['type_menu'];

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['url'] = $x['url'];

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['icon_menu'] = $x['icon_menu'];

						$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['judul_kecil'] = $x['ref_tbl'];



						if ($x['type_menu'] == 'CHC') {

							$menu[$v['tbl_menu_id']]['child'][$x['tbl_menu_id']]['sub_child'] = array();

							$sqlSubChild = "

								SELECT a.tbl_menu_id, b.nama_menu, b.url, b.icon_menu , b.type_menu, b.ref_tbl

									FROM tbl_user_prev_group a

								LEFT JOIN tbl_user_menu b ON a.tbl_menu_id = b.id

								WHERE a.cl_user_group_id=" . $this->auth['cl_user_group_id'] . "

								AND b.type_menu = 'CC'

								AND b.parent_id_2 = " . $x['tbl_menu_id'] . "

								AND b.status='1'

								ORDER BY b.urutan ASC

							";

							$SubChild = $this->db->query($sqlSubChild)->result_array();

							foreach ($SubChild as $z) {

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
			//end Menu

			//Data Menu Parent
			case "menu_parent":

				$sql = "

					SELECT A.*

					FROM tbl_user_menu A

					WHERE (A.type_menu = 'P' OR A.type_menu = 'PC') AND A.status = '1'

					ORDER BY A.urutan ASC

				";

				break;
			//end Data Menu Parent

			case "menu_child":

				$sql = "

					SELECT A.*

					FROM tbl_user_menu A

					WHERE (A.type_menu = 'C') AND A.parent_id = '" . $p1 . "' AND A.status = '1'

					ORDER BY A.urutan ASC

				";

				break;

			case "menu_child_2":

				$sql = "

					SELECT A.*

					FROM tbl_user_menu A

					WHERE A.type_menu = 'CC' AND A.parent_id_2 = '" . $p1 . "' AND A.status = '1'

					ORDER BY A.urutan ASC

				";

				break;

			case "previliges_menu":

				$sql = "

					SELECT A.*

					FROM tbl_user_prev_group A

					WHERE A.tbl_menu_id = '" . $p1 . "' AND A.cl_user_group_id = '" . $p2 . "'

				";

				break;

			// End Modul User Management

			default:

				if ($balikan == 'get') {
					$where .= " AND A.id=" . $this->input->post('id');
				}

				$sql = "

					SELECT A.*

					FROM " . $type . " A " . $where . "

				";

				if ($balikan == 'get') return $this->db->query($sql)->row_array();

				break;
		}

		if ($balikan == 'json') {

			return $this->lib->json_grid($sql, $type);
		} elseif ($balikan == 'row_array') {

			return $this->db->query($sql)->row_array();
		} elseif ($balikan == 'result') {

			return $this->db->query($sql)->result();
		} elseif ($balikan == 'result_array') {

			return $this->db->query($sql)->result_array();
		} elseif ($balikan == 'json_encode') {

			$data = $this->db->query($sql)->result_array();

			return json_encode($data);
		} elseif ($balikan == 'variable') {

			return $array;
		}
	}

	function get_notif()
	{

		$tgl_hari_ini = date('Y-m-d');

		$sql = "

				SELECT count(id) as jml FROM tbl_data_broadcast a

				WHERE a.tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%' 

				and a.flag_reg='N'
								
			";

		$parent = $this->db->query($sql)->result_array();

		$array = '';

		foreach ($parent as $v) {

			$array = $v['jml'];
		}

		return $array;
	}

	function get_subjek()
	{

		$tgl_hari_ini = date('Y-m-d');

		$sql = "

				SELECT subjek FROM tbl_data_broadcast a

				WHERE a.tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%' 

				and a.flag_reg='N'
				
			";

		$parent = $this->db->query($sql)->result_array();

		$array = '';

		foreach ($parent as $v) {

			$array = $v['subjek'];
		}

		return $array;
	}

	function get_data_bc()
	{



		$sql = "

				SELECT flag_reg,subjek,tgl_broadcast FROM tbl_data_broadcast a

				WHERE a.tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%'
								
			";

		$array = $this->db->query($sql)->result_array();

		return $array;
	}

	function save($table, $data, $status)
	{
		if ($status == '2') {
			$this->db->update($table, $data);
			return true;
		} else {
			$this->db->insert($table, $data);
			return true;
		}
	}

	function getdata_laporan($type = "", $balikan = "", $p1 = "", $p2 = "", $p3 = "", $p4 = "")
	{

		$where = "WHERE 1=1 ";



		switch ($type) {

			case "dashboard_summary_penduduk":

				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

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

					where A.kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

				";



				// /echo $sql;exit;

				break;

			case "dashboard_pendidikan":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

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

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

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

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

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

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

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

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}


				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				$sql = "SELECT A.nama_agama as keterangan,

						CASE A.id
							WHEN '1' THEN '#6EC1E4'  -- Islam = biru soft
							WHEN '2' THEN '#F28B82'  -- Kristen = merah soft
							WHEN '3' THEN '#F7D36B'  -- Katolik = kuning soft
							WHEN '4' THEN '#81C995'  -- Hindu = hijau soft
							WHEN '5' THEN '#B39DDB'  -- Budha = ungu soft
							WHEN '6' THEN '#FFCC80'  -- Khonghucu = orange soft
							ELSE '#6EC1E4'
						END as color,

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

			case "dashboard_sekolah":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "


						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "


						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT bp,COUNT(bp) AS jumlah FROM cl_master_pendidikan
				
				GROUP BY bp

				";

				break;

			case "dashboard_umkm":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = "SELECT jenis,COUNT(nik) AS jumlah FROM cl_master_umkm GROUP BY jenis ";

				break;

			case "dashboard_kebersihan":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT unit_kerja,COUNT(nik_petugas) AS jumlah FROM tbl_data_petugas_kebersihan GROUP BY nik_petugas ";

				break;

			case "dashboard_ibadah":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT jns_tempat_ibadah,COUNT(jns_tempat_ibadah) AS jumlah FROM tbl_data_tempat_ibadah GROUP BY jns_tempat_ibadah ";

				break;

			case "dashboard_pkl":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT jenis_usaha,COUNT(nik_pkl) AS jumlah FROM tbl_data_pkl GROUP BY jenis_usaha ";

				break;

			case "dashboard_wamis":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT kelurahan,COUNT(nik) AS jumlah FROM tbl_data_wamis GROUP BY nik ";

				break;

			case "dashboard_longwis":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT kelurahan,COUNT(create_by) AS jumlah FROM tbl_data_lorong GROUP BY create_by ";

				break;

			case "dashboard_ekspedisi":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT kelurahan,COUNT(create_by) AS jumlah FROM tbl_data_ekspedisi GROUP BY create_by ";

				break;

			case "dashboard_rekap_imb":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT kelurahan,COUNT(create_by) AS jumlah FROM tbl_data_rekap_imb GROUP BY create_by ";

				break;

			case "dashboard_ekspedisi":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT kelurahan,COUNT(create_by) AS jumlah FROM tbl_data_ekspedisi GROUP BY create_by ";

				break;


			case "dashboard_dasawisma":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT a.nama,COUNT(b.nik) AS jumlah FROM cl_kelurahan_desa a
				LEFT JOIN tbl_data_dasawisma b ON a.id=b.cl_kelurahan_desa_id ";

				break;

			case "dashboard_rt_rw":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT a.nama,COUNT(b.nik) AS jumlah FROM cl_kelurahan_desa a 
				LEFT JOIN tbl_data_rt_rw b ON a.id=b.cl_kelurahan_desa_id ";

				break;

			case "dashboard_notif":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}



				$sql = " SELECT COUNT(id) AS jumlah FROM tbl_data_broadcast ";

				break;

			//Tertulis tanggal 29Mei 2023
			case "dashboard_broadcast":

				$where .= "
	
							and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
							and tujuan like'%" . $this->auth['cl_kelurahan_desa_id'] . "%'
	
						";



				$sql = " SELECT subjek,tgl_broadcast FROM tbl_data_broadcast $where";

				break;

			case "dashboard_retribusi_sampah":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}


				$sql = " SELECT kelurahan,SUM(total2) AS total FROM tbl_data_retribusi_sampah 
				GROUP BY jumlah_wajub_retribusi ";

				break;

			case "dashboard_pegawai_kel_kec":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}


				$sql = " SELECT a.nama,COUNT(b.nama) AS jumlah FROM cl_kelurahan_desa a
				LEFT JOIN tbl_data_pegawai_kel_kec b ON a.id=b.cl_kelurahan_desa_id GROUP BY b.create_by
				";

				break;

			case "dashboard_ktp_tercetak":

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
				}



				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}


				$sql = " SELECT a.nama,COUNT(b.id) AS jumlah FROM cl_kelurahan_desa a
				LEFT JOIN tbl_data_rekam_ktp b ON a.id=b.cl_kelurahan_desa_id
				 ";

				break;


			case "dashboard_jenis_kelamin":

				$session_data = unserialize(base64_decode($this->session->userdata('s3ntr4lb0')));
				$tahun_login = isset($session_data['tahun']) ? $session_data['tahun'] : date('Y');

				$desa_id = $this->input->post('cl_kelurahan_desa_id_filter');

				if ($desa_id) {

					$where .= "and cl_kelurahan_desa_id = $desa_id";
					// $where .= "and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				}


				if ($this->auth['cl_user_group_id'] == 3) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					";
				}

				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {

					$where .= "

						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

					";
				}

				// 🔥 FILTER TAHUN LOGIN (WAJIB)
				$where .= " AND YEAR(create_date) <= '$tahun_login' ";

				$sql = "SELECT COUNT(id) as total, 'LAKI-LAKI' as keterangan,

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

			case "data_nama_kelurahan_broadcast":


				$sql = "

					SELECT id,nama

					FROM cl_kelurahan_desa

				";

				break;

			case "laporan_hasil_skm":

				$tahun_post = $this->input->post('tahun');
				$tahun = (int)($tahun_post ? $tahun_post : date('Y'));

				// Otomatis jika user lurah/staff kelurahan
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$xkec = $this->auth['cl_kecamatan_id'];
					$xkel = $this->auth['cl_kelurahan_desa_id'];
				} else {
					$xkec = $this->auth['cl_kecamatan_id'];
					$xkel = (int)$this->input->post('kelurahan_id', true);
				}

				// Jika lewat GET (misal laporan/cetak)
				if ($this->input->get('kelurahan_id', true) != '') {
					$xkel = $this->input->get('kelurahan_id', true);
				}

				// EXECUTE STORED PROCEDURE
				$query = $this->db->query("CALL sp_lap_penilaian_skm(2025,$xkec,$xkel)");
				$raw = $query->result_array();
				$query->free_result();
				$this->db->conn_id->next_result();

				// UNSUR TETAP
				$unsur = [
					['kode' => 'U1', 'nama' => 'Persyaratan'],
					['kode' => 'U2', 'nama' => 'Prosedure'],
					['kode' => 'U3', 'nama' => 'Waktu Pelayanan'],
					['kode' => 'U4', 'nama' => 'Biaya/Tarif'],
					['kode' => 'U5', 'nama' => 'Produk Layanan'],
					['kode' => 'U6', 'nama' => 'Kompetensi Pelaksana'],
					['kode' => 'U7', 'nama' => 'Perilaku Pelaksana'],
					['kode' => 'U8', 'nama' => 'Sarana dan Prasarana'],
					['kode' => 'U9', 'nama' => 'Penanganan Pengaduan'],
				];

				// GABUNGKAN UNSUR TETAP + NILAI SP
				foreach ($unsur as &$u) {
					$u['nilai'] = 0;

					foreach ($raw as $r) {

						// Auto-detect key unsur dari SP
						$key_unsur = null;

						if (isset($r['unsur'])) $key_unsur = 'unsur';
						else if (isset($r['kode'])) $key_unsur = 'kode';
						else if (isset($r['kode_unsur'])) $key_unsur = 'kode_unsur';
						else if (isset($r['u'])) $key_unsur = 'u';

						// Jika tidak ada field unsur, skip raw ini
						if (!$key_unsur) continue;

						// Cocokkan U1–U9
						if ($r[$key_unsur] == $u['kode']) {

							// nilai bisa beda nama: nilai, total, nrr_unsur, skor, dll.
							$nilaiKey = null;

							if (isset($r['nilai'])) $nilaiKey = 'nilai';
							else if (isset($r['nrr'])) $nilaiKey = 'nrr';
							else if (isset($r['total'])) $nilaiKey = 'total';
							else if (isset($r['nilai_unsur'])) $nilaiKey = 'nilai_unsur';

							if ($nilaiKey)
								$u['nilai'] = floatval($r[$nilaiKey]);

							break;
						}
					}
				}
				return $unsur;
			case "beranda_hasil_skm":

				$tahun_post = $this->input->post('tahun');
				$tahun = (int)($tahun_post ? $tahun_post : date('Y'));

				// Otomatis jika user lurah/staff kelurahan
				if (in_array($this->auth['cl_user_group_id'], [2, 4, 5])) {
					$xkec = $this->auth['cl_kecamatan_id'];
					$xkel = $this->auth['cl_kelurahan_desa_id'];
				} else {
					$xkec = $this->auth['cl_kecamatan_id'];
					$xkel = (int)$this->input->post('kelurahan_id', true);
				}

				// Jika lewat GET (misal laporan/cetak)
				if ($this->input->get('kelurahan_id', true) != '') {
					$xkel = $this->input->get('kelurahan_id', true);
				}

				// EXECUTE STORED PROCEDURE
				$query = $this->db->query("CALL sp_lap_penilaian_skm(2025,$xkec,$xkel)");
				$raw = $query->result_array();
				$query->free_result(); 
				$this->db->conn_id->next_result();

				// UNSUR TETAP
				$unsur = [
					['kode' => 'U1', 'nama' => 'Persyaratan'],
					['kode' => 'U2', 'nama' => 'Prosedure'],
					['kode' => 'U3', 'nama' => 'Waktu Pelayanan'],
					['kode' => 'U4', 'nama' => 'Biaya/Tarif'],
					['kode' => 'U5', 'nama' => 'Produk Layanan'],
					['kode' => 'U6', 'nama' => 'Kompetensi Pelaksana'],
					['kode' => 'U7', 'nama' => 'Perilaku Pelaksana'],
					['kode' => 'U8', 'nama' => 'Sarana dan Prasarana'],
					['kode' => 'U9', 'nama' => 'Penanganan Pengaduan'],
				];

				// GABUNGKAN UNSUR TETAP + NILAI SP
				foreach ($unsur as &$u) {

					$u['nilai'] = 0;

					foreach ($raw as $r) {
						// Baris NRR Unsur
						if ($r['nor'] == '3') {

						// kolom di DB adalah u1,u2,u3,...u9
							$key = strtolower($u['kode']); // U1 -> u1

							if (isset($r[$key])) {
								$nilai = round($r[$key] * 25, 2);

								$u['nilai'] = $nilai;                 // untuk chart
								$u['mutu']  = $this->mutu_skm($nilai); // A/B/C/D
							}

							break;
						}
					}
				}

				return $unsur;

			break;
		}



		if ($balikan == 'json') {

			return $this->lib->json_grid($sql, $type);
		} elseif ($balikan == 'row_array') {

			return $this->db->query($sql)->row_array();
		} elseif ($balikan == 'result') {

			return $this->db->query($sql)->result();
		} elseif ($balikan == 'result_array') {

			return $this->db->query($sql)->result_array();
		} elseif ($balikan == 'json_encode') {

			$data = $this->db->query($sql)->result_array();

			return json_encode($data);
		} elseif ($balikan == 'variable') {

			return [];
		}
	}



	function get_combo($type = "", $p1 = "", $p2 = "", $p3 = "", $p4 = "")
	{

		$where = "where 1=1 ";
		$where_id = " where no_kk NOT IN (SELECT no_kk FROM tbl_kartu_keluarga WHERE no_kk=a.no_kk) and cl_kelurahan_desa_id ='" . $this->auth['cl_kelurahan_desa_id'] . "'";

		switch ($type) {

			case "kelurahan_report":
				// $where = '';
				// if ($this->auth['cl_user_group_id'] == 3) {
				// 	$where = 'WHERE id!=0';
				// }

				$sql = "

					SELECT id, nama as txt

					FROM cl_kelurahan_desa

					$where 

					order by nama asc

				";

				break;

			case "jenis_surat_report":

				$sql = "

					SELECT id, jenis_surat as txt

					FROM cl_jenis_surat

					order by nama asc

				";

				break;

			case "cl_kelurahan_desa":

				if ($p2 != "") {

					$where .= "

						and id = '" . $p2 . "'

					";
				}

				$sql = "

					SELECT  id, nama as txt

					FROM cl_kelurahan_desa

					where id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "cl_kecamatan":

				$sql = "

					SELECT id, nama as txt

					FROM cl_kecamatan

				";

				break;

			case "cl_jenis_pekerjaan":

				$sql = "SELECT id, nama_pekerjaan as txt

					FROM cl_jenis_pekerjaan
				";

				break;

			case "hubungan_keluarga":

				$sql = "SELECT id, nama as txt

					FROM cl_hubungan_keluarga
				";

				break;

			case "status_penduduk":

				$sql = "

					SELECT nama_status as id, nama_status as txt

					FROM cl_status_penduduk 

				";

				break;

			case "jenis_passport":

				$sql = "

					SELECT jenis_passport as id, jenis_passport as txt

					FROM cl_jenis_passport 

				";

				break;

			case "keperluan_passport":

				$sql = "

					SELECT keperluan as id, keperluan as txt

					FROM cl_keperluan_passport 

				";

				break;

			case "status_kawin":

				$sql = "

					SELECT nama_status_kawin as id, nama_status_kawin as txt

					FROM cl_status_kawin 

				";

				break;

			case "pilih_ttd":
				$where = '';
				if ($this->auth['cl_user_group_id'] == 2) {
					$where = 'WHERE LEFT(tingkat_jabatan,1)=2';
				}
				if ($this->auth['cl_user_group_id'] == 3) {
					$where = 'WHERE LEFT(tingkat_jabatan,1)=1';
				}
				$sql = "

					SELECT nip as id, nama as txt

					FROM tbl_data_penandatanganan
					$where

				";

				break;

			// case "pilih_ttd_lainnya":
			// 	$where = "WHERE tingkat_jabatan='3.1'";

			// 	$sql = " SELECT nip as id, nama as txt

			// 		FROM tbl_data_penandatanganan
			// 		$where and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

			// 	";

			// 	break;

			case "pilih_ttd_lainnya":
				if ($this->auth['cl_user_group_id'] == 3) {
					// group 3 → ambil semua ketua LPM (tingkat_jabatan 3.1) di kecamatan user
					$where = "WHERE tingkat_jabatan='3.1' 
							AND cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'";
				} elseif ($this->auth['cl_user_group_id'] == 2) {
					// group 2 → ambil ketua LPM hanya di kelurahan login user
					$where = "WHERE tingkat_jabatan='3.1' 
							AND cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'";
				} else {
					// default fallback kalau butuh
					$where = "WHERE tingkat_jabatan='3.1'";
				}

				$sql = "
					SELECT nip as id, nama as txt
					FROM tbl_data_penandatanganan
					$where
				";
				break;

			case "pilih_ttd_lain_pembuat":
				$where = "WHERE tingkat_jabatan='2.4'";

				$sql = "SELECT nip as id, nama as txt

					FROM tbl_data_penandatanganan
					$where and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "pilih_tingkat_jabatan":
				$where = '';
				if ($this->auth['cl_user_group_id'] == 3) {
					$where = 'WHERE LEFT(id,1)=1 OR LEFT(id,1)=3';
				}
				if ($this->auth['cl_user_group_id'] == 2) {
					$where = 'WHERE LEFT(id,1)=2 OR LEFT(id,1)=3';
				}
				$sql = "

					SELECT id, nama as txt

					FROM cl_tingkat_jabatan
					$where

				";

				break;

			case "data_penduduk_id":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "nik_laki_laki":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "nik_perempuan":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "nik_beri_pernyataan":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "nik_pemilik_rumah":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "data_kategori_id":

				$sql = "SELECT id,kategori as txt

					FROM tbl_kategori_penilaian_rt_rw
				";

				break;

			case "perihal_hasil_agenda":

				$sql = "SELECT id,perihal_kegiatan as txt

					FROM tbl_data_daftar_agenda
				";

				break;

			case "data_rt_rw":

				$sql = "

					SELECT nik as id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_rt_rw

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;
			case "data_rt_rw_id":

				$sql = "

					SELECT id,concat(nik, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_rt_rw

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "data_penduduk_asing_id":

				$sql = "

					SELECT no_passport as id,concat(no_passport, ' - ' , nama_lengkap) as txt 

					FROM tbl_data_penduduk_asing

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				break;

			case "data_pindah_penduduk_id":

				$sql = "

					SELECT nik as id, nama_lengkap as txt

					FROM tbl_data_penduduk

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					AND status_data = 'AKTIF'

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

			case "cl_jenis_wamis":

				$sql = "

					SELECT jenis_wamis as id, jenis_wamis as txt

					FROM cl_jenis_wamis

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

			case "cl_jenis_surat_masuk":

				$sql = "
	
						SELECT id, jenis_surat as txt
	
						FROM cl_jenis_surat_masuk
	
					";

				break;

			case "cl_sifat_surat":

				$sql = "
	
						SELECT id, sifat_surat as txt
	
						FROM cl_sifat_surat
	
					";

				break;

			case "cl_sifat_surat2":

				$sql = "
	
						SELECT sifat_surat id, sifat_surat as txt
	
						FROM cl_sifat_surat
	
					";

				break;

			case "hubungan_keluarga":

				$sql = "

					SELECT id, nama as txt

					FROM cl_hubungan_keluarga

				";

				break;
			case "status_pegawai":

				$sql = "

					SELECT nama_status as id, nama_status as txt

					FROM cl_status_pegawai

				";

				break;
			case "rw":

				$sql = "SELECT rw as id, rw AS txt
						FROM tbl_data_rt_rw
						WHERE 1=1 and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						GROUP BY rw
						ORDER BY rw ASC;
				";

				break;
			case "jenjang_sekolah":

				$sql = "

					SELECT jenjang_sekolah as id, jenjang_sekolah as txt

					FROM cl_jenjang_sekolah

				";

				break;

			case "list_npsn":

				$sql = "
	
						SELECT npsn as id, nama_sekolah as txt
	
						FROM cl_master_pendidikan

						where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
	
					";

				break;

			case "jenis_pekerjaan":

				$sql = "

					SELECT nama_pekerjaan as id, nama_pekerjaan as txt

					FROM cl_jenis_pekerjaan

				";

				break;

			case "jenis_pendidikan":

				$sql = "

					SELECT nama_pendidikan as id, nama_pendidikan as txt

					FROM cl_pendidikan

				";

				break;

			case "asal_kelurahan":

				$sql = "

					SELECT id, nama as txt

					FROM cl_kelurahan_desa

				";

				break;

			case "pilih_tahun":

				$sql = "

					SELECT pilih_tahun as id, pilih_tahun as txt
					FROM cl_pilih_tahun
					ORDER BY pilih_tahun

				";

				break;

			case "pilih_bulan":

				$sql = "

					SELECT nama_bulan as id, nama_bulan as txt

					FROM cl_pilih_bulan

				";

				break;

			case "pilih_tahun_perolehan":

				$sql = "

					SELECT pilih_tahun_perolehan as id, pilih_tahun_perolehan as txt

					FROM cl_tahun_perolehan

					ORDER BY pilih_tahun_perolehan

				";

				break;

			case "pilih_jabatan_id":

				$sql = "

					SELECT id,nm_pangkat as txt

					FROM cl_jabatan

				";

				break;
			case "pilih_jabatan":

				$sql = "
	
						SELECT nm_pangkat as id,nm_pangkat as txt
	
						FROM cl_jabatan
	
					";

				break;

			case "pilih_golonganx":

				$sql = "
	
				SELECT nm_golongan as id, CONCAT(pangkat,', ',nm_golongan) as txt

				FROM cl_golongan
				";

				break;

			case "pilih_pangkatx":

				$sql = "
	
				SELECT pangkat as id, CONCAT(pangkat,', ',nm_golongan) as txt

				FROM cl_golongan
				";

				break;

			case "pilih_pangkat":

				$sql = "
	
				SELECT pangkat as id,pangkat as txt

				FROM cl_golongan
				";

				break;

			case "pilih_golongan":

				$sql = "
	
				SELECT nm_golongan as id,nm_golongan as txt

				FROM cl_golongan
				";

				break;
			case "pilih_golongan_id":

				$sql = "
	
				SELECT id,CONCAT(nm_golongan,' - ',pangkat) as txt

				FROM cl_golongan
				order by nm_golongan
				";

				break;

			case "pilih_jsurat":

				$sql = "

				SELECT id,jenis_surat AS txt
				FROM cl_jenis_surat

				";

				break;

			case "data_penandatangananx":

				$sql = "SELECT nip as id, nama as txt

					FROM tbl_data_penandatanganan 

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					
				";

			case "data_penandatanganan_5":
				$sql = "SELECT nip AS id, nama as txt
				FROM tbl_data_penandatanganan a
				WHERE a.cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
				AND a.tingkat_jabatan IN ('2.1','2.2','2.3.1','2.3.2','2.3.3')
				AND a.STATUS = 'Aktif'
			";
				break;


			case "data_penandatanganan_2":

				$sql = "SELECT nip as id, nama as txt

					FROM tbl_data_penandatanganan 

					where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
					and tingkat_jabatan = '2.1' and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
				";

				break;

			// case "data_penandatanganan_3":

			// 	$sql = "SELECT nip as id, nama as txt

			// 		FROM tbl_data_penandatanganan 

			// 		where cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 		and tingkat_jabatan = '1.1' and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
			// 	";

			// 	break;
			case "data_penandatanganan_3":

				$where_tingkat = "";

				if (!empty($this->auth['cl_kelurahan_desa_id'])) {
					// Login di level kelurahan
					$where_tingkat = "AND tingkat_jabatan = '2.1'";
				} else {
					// Login di level kecamatan
					$where_tingkat = "AND tingkat_jabatan = '1.1'";
				}

				$sql = "
				SELECT nip AS id, nama AS txt
				FROM tbl_data_penandatanganan
				WHERE 
				cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
				AND cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
				AND cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
				" . (!empty($this->auth['cl_kelurahan_desa_id']) ? "AND cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'" : "") . "
				$where_tingkat
				";

				break;
			case "data_penandatanganan_4":

				$sql = "SELECT nip as id, nama as txt

					FROM tbl_data_penandatanganan 

					where  tingkat_jabatan = '1.1'
				";

				break;
			case "data_penandatanganan_all":

				$sql = "
	
						SELECT nip as id, nama as txt
	
						FROM tbl_data_penandatanganan 
	
	
					";

				break;

			case "nama_sopir":

				$sql = "

				SELECT nopol as id,concat(nopol, ' - ' , nama_sopir) as txt 

				FROM tbl_data_kendaraan

				";
				break;

			case "kd_brg":

				$sql = "

				SELECT kd_brg as id,uraian as title,concat(kd_brg, ' - ' , uraian) as txt 

				FROM mbarang_kendaraan

				";
				break;

			case "data_penduduk_anak":

				$sql = "

					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt

					FROM tbl_data_penduduk

					$where

					and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";



				// cl_status_hubungan_keluarga_id = '3'

				// AND status_data = 'AKTIF'

				break;

			case "data_penduduk_belum_menikah":

				$sql = "

					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt

					FROM tbl_data_penduduk

					$where

					and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";



				//WHERE ( status_kawin = '2' OR status_kawin = '3' )

				//AND status_data = 'AKTIF'

				break;

			case "data_penandatanganan":

				$sql = "
	
						SELECT nip as id, CONCAT(nip,' - ',nama) as txt
	
						FROM tbl_data_penandatanganan
	
						$where
	
						and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'
	
						and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'
	
						and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'
	
						and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

						AND status = 'Aktif'
	
					";

				break;

			case "data_penduduk":

				$sql = "

					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt

					FROM tbl_data_penduduk

					$where

					and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";

				//WHERE status_data = 'AKTIF'

				break;

			case "data_penduduk_asing":

				$sql = "

					SELECT id, CONCAT(no_passport,' - ',nama_lengkap) as txt

					FROM tbl_data_penduduk_asing

					$where

					and cl_provinsi_id = '" . $this->auth['cl_provinsi_id'] . "'

					and cl_kab_kota_id = '" . $this->auth['cl_kab_kota_id'] . "'

					and cl_kecamatan_id = '" . $this->auth['cl_kecamatan_id'] . "'

					and cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

				";


				//WHERE status_data = 'AKTIF'

				break;

			case "data_keluarga_id":

				$sql = "

					SELECT id, CONCAT(nik,' - ',nama_lengkap) as txt

					FROM tbl_data_penduduk a

					$where_id 

				";



				//WHERE status_data = 'AKTIF'

				break;

			// case "data_ahli_waris":

			// 	$sql = "

			// 			SELECT nik as id, CONCAT(nik,' - ',nama_lengkap) as txt

			// 			FROM tbl_data_penduduk

			// 			where

			// 			cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'

			// 		";

			// 	break;

			case "waris":

				$sql = "

						SELECT nik as id, CONCAT(nik,' - ',nama_lengkap) as txt

						FROM tbl_data_penduduk

						where

						cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "'
						and status_data = 'meninggal dunia'

					";

				break;

			default:

				$txt = str_replace("cl_", "", $type);

				$sql = "

					SELECT id, $txt as txt

					FROM $type

				";

				break;
		}



		return $this->db->query($sql)->result_array();
	}



	function simpandata($table, $data, $sts_crud, $table2, $data2)
	{ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE

		$this->db->trans_begin();

		if (isset($data['id'])) {

			$id = $data['id'];

			unset($data['id']);
		}



		if ($sts_crud == "add") {

			$data['create_date'] = date('Y-m-d H:i:s');

			$data['create_by'] = $this->auth['nama_lengkap'];



			unset($data['id']);
		}



		if ($sts_crud == "edit") {

			$data['update_date'] = date('Y-m-d H:i:s');

			$data['update_by'] = $this->auth['nama_lengkap'];
		}



		switch ($table) {

			case "data_permohonan":

				//print_r($data);exit;
				// if(!$data["no_passport"]){
				// 	$sql = "SELECT * FROM tbl_data_penduduk WHERE no_passport='" . $data["passport"] . "'";
				// }
				// else{
				// 	$sql = "SELECT * FROM tbl_data_penduduk WHERE nik='" . $data["nik"] . "'";
				// }	

				$sql = "SELECT * FROM tbl_data_penduduk WHERE nik='" . $data["nik"] . "'";

				$ex = $this->db->query($sql)->row_array();

				$id_penduduk = 0;

				if (!isset($ex["nik"])) {

					//TRANSFER DATA PENDUDUK;

					$sql = "INSERT INTO tbl_data_penduduk

							(nik,nama_lengkap,tempat_lahir,tgl_lahir,jenis_kelamin,agama,status_kawin,

							pendidikan,cl_jenis_pekerjaan_id,

							cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,

							cl_kelurahan_desa_id,rt,rw,alamat,kode_pos,status_data,

							create_date,create_by)

							SELECT nik,nama_lengkap,tempat_lahir,tgl_lahir,jenis_kelamin,agama,status_kawin,

							pendidikan,cl_jenis_pekerjaan_id,

							cl_provinsi_id,cl_kab_kota_id,cl_kecamatan_id,

							cl_kelurahan_desa_id,rt,rw,alamat,kode_pos,'AKTIF','" . date('Y-m-d H:i:s') . "','" . $this->auth['nama_lengkap'] . "'

							FROM tbl_registrasi_surat WHERE id= " . $id;

					$this->db->query($sql);

					//END TRANSFER

					$id_penduduk = $this->db->insert_id();
				} else {

					$id_penduduk = $ex["id"];
				}

				//UPDATE TBL SURAT

				$sql = "UPDATE tbl_data_surat set no_surat='" . $data["no_surat"] . "',

				tgl_surat='" . $data["tgl_surat"] . "',

				tbl_data_penduduk_id=" . $id_penduduk . "

				WHERE tbl_registrasi_id=" . $id;

				$this->db->query($sql);

				//END UPDATE



				//UPDATE TBL REGISTRASI

				$sql = "UPDATE tbl_registrasi_surat set status_data='F' WHERE id=" . $id;

				$this->db->query($sql);

				//END UPDATE





				if ($this->db->trans_status() == false) {

					$this->db->trans_rollback();

					return 0;
				} else {

					//$this->db->trans_commit();

					return $this->db->trans_commit();
				}



				break;

			case "import_data_penduduk_sulsel":

				$this->load->library('PHPExcel');

				if (!empty($_FILES['filename']['name'])) {

					$ext = explode('.', $_FILES['filename']['name']);

					$exttemp = sizeof($ext) - 1;

					$extension = $ext[$exttemp];

					$upload_path = "./__repository/tmp_upload/";

					$filen = "IMPORTEXCELSULSEL-" . date('Ymd_His');

					$filename =  $this->lib->uploadnong($upload_path, 'filename', $filen);



					$folder_aplod = $upload_path . $filename;

					$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;

					$cacheSettings = array('memoryCacheSize' => '1600MB');

					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

					if ($extension == 'xls') {

						$lib = "Excel5";
					} else {

						$lib = "Excel2007";
					}

					$objReader =  PHPExcel_IOFactory::createReader($lib); //excel2007

					ini_set('max_execution_time', 123456);

					$objPHPExcel = $objReader->load($folder_aplod);

					$objReader->setReadDataOnly(true);

					$nama_sheet = $objPHPExcel->getSheetNames();

					$worksheet = $objPHPExcel->setActiveSheetIndex(0);

					$array_benar = array();

					for ($i = 7; $i <= $worksheet->getHighestRow(); $i++) {

						//$cek = $this->db->get_where('tbl_data_penduduk', array('nik'=>$worksheet->getCell("C".$i)->getCalculatedValue()))->row_array();

						//if(!$cek){

						if ($worksheet->getCell("F" . $i)->getCalculatedValue()) {

							$ultah = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("F" . $i)->getCalculatedValue()));
						} else {

							$ultah = null;
						}



						if ($worksheet->getCell("H" . $i)->getCalculatedValue() == 'L') {

							$jenis_kelamin = "LAKI-LAKI";
						} elseif ($worksheet->getCell("H" . $i)->getCalculatedValue() == 'P') {

							$jenis_kelamin = "PEREMPUAN";
						} else {

							$jenis_kelamin = null;
						}



						if ($worksheet->getCell("G" . $i)->getCalculatedValue()) {

							$sts_kawin = "

									SELECT id

									FROM cl_status_kawin

									WHERE akronim = '" . $worksheet->getCell("G" . $i)->getCalculatedValue() . "'

								";

							$status_kawin = $this->db->query($sts_kawin)->row_array();
						} else {

							$jenis_kelamin = null;
						}



						$cek_kk = $this->db->get_where('tbl_kartu_keluarga', array('no_kk' => $worksheet->getCell("B" . $i)->getCalculatedValue()))->row_array();

						if (!$cek_kk) {

							$array_kk = array(

								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],

								'no_kk' => $worksheet->getCell("B" . $i)->getCalculatedValue(),

								'create_by' => $this->auth['nama_lengkap'] . " - Import Excel SIAK",

								'create_date' => date('Y-m-d H:i:s'),

							);

							$this->db->insert('tbl_kartu_keluarga', $array_kk);
						}



						$arrayinsertbenar = array(

							'no_kk' => $worksheet->getCell("B" . $i)->getCalculatedValue(),

							'nik' => $worksheet->getCell("C" . $i)->getCalculatedValue(),

							'nama_lengkap' => $worksheet->getCell("D" . $i)->getCalculatedValue(),

							'tempat_lahir' => $worksheet->getCell("E" . $i)->getCalculatedValue(),

							'tgl_lahir' => $ultah,

							'jenis_kelamin' => $jenis_kelamin,

							'status_kawin' => $status_kawin['id'],

							'alamat' => $worksheet->getCell("I" . $i)->getCalculatedValue(),

							'rt' => (string)$worksheet->getCell("J" . $i)->getCalculatedValue(),

							'rw' => (string)$worksheet->getCell("K" . $i)->getCalculatedValue(),



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



					if (!empty($array_benar)) {

						$this->db->insert_batch('tbl_data_penduduk', $array_benar);

						unlink($folder_aplod);
					}
				}

				break;

			case "import_data_penduduk_siak":

				$this->load->library('PHPExcel');

				if (!empty($_FILES['filename']['name'])) {

					$ext = explode('.', $_FILES['filename']['name']);

					$exttemp = sizeof($ext) - 1;

					$extension = $ext[$exttemp];

					$upload_path = "./__repository/tmp_upload/";

					$filen = "IMPORTEXCEL-" . date('Ymd_His');

					$filename =  $this->lib->uploadnong($upload_path, 'filename', $filen);



					$folder_aplod = $upload_path . $filename;

					$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;

					$cacheSettings = array('memoryCacheSize' => '1600MB');

					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

					if ($extension == 'xls') {

						$lib = "Excel5";
					} else {

						$lib = "Excel2007";
					}

					$objReader =  PHPExcel_IOFactory::createReader($lib); //excel2007

					ini_set('max_execution_time', 123456);

					$objPHPExcel = $objReader->load($folder_aplod);

					$objReader->setReadDataOnly(true);

					$nama_sheet = $objPHPExcel->getSheetNames();

					$worksheet = $objPHPExcel->setActiveSheetIndex(0);

					$array_benar = array();

					for ($i = 2; $i <= $worksheet->getHighestRow(); $i++) {

						//echo date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("E".$i)->getCalculatedValue()));

						//echo $worksheet->getCell("E".$i)->getCalculatedValue();

						//exit;



						if ($worksheet->getCell("E" . $i)->getCalculatedValue()) {

							$ultah = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell("E" . $i)->getCalculatedValue()));
						} else {

							$ultah = null;
						}



						if ($worksheet->getCell("C" . $i)->getCalculatedValue() == 'L') {

							$jenis_kelamin = "LAKI-LAKI";
						} elseif ($worksheet->getCell("C" . $i)->getCalculatedValue() == 'P') {

							$jenis_kelamin = "PEREMPUAN";
						}



						$sts_kawin = "

							SELECT id

							FROM cl_status_kawin

							WHERE nama_status_kawin LIKE '%" . $worksheet->getCell("I" . $i)->getCalculatedValue() . "%'

						";

						$status_kawin = $this->db->query($sts_kawin)->row_array();



						$spendidikan = "

							SELECT id

							FROM cl_pendidikan

							WHERE nama_pendidikan LIKE '%" . $worksheet->getCell("N" . $i)->getCalculatedValue() . "%'

						";

						$pendidikan = $this->db->query($spendidikan)->row_array();



						$spekerjaan = "

							SELECT id

							FROM cl_jenis_pekerjaan

							WHERE nama_pekerjaan LIKE '%" . $worksheet->getCell("M" . $i)->getCalculatedValue() . "%'

						";

						$pekerjaan = $this->db->query($spekerjaan)->row_array();



						$cek_kk = $this->db->get_where('tbl_kartu_keluarga', array('no_kk' => $worksheet->getCell("O" . $i)->getCalculatedValue()))->row_array();

						if (!$cek_kk) {

							$array_kk = array(

								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],

								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],

								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],

								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],

								'no_kk' => $worksheet->getCell("O" . $i)->getCalculatedValue(),

								'create_by' => $this->auth['nama_lengkap'] . " - Import Excel SIAK",

								'create_date' => date('Y-m-d H:i:s'),

							);

							$this->db->insert('tbl_kartu_keluarga', $array_kk);
						}



						$shubkeluarga = "

							SELECT id

							FROM cl_hubungan_keluarga

							WHERE nama LIKE '%" . $worksheet->getCell("J" . $i)->getCalculatedValue() . "%'

						";

						$hubkeluarga = $this->db->query($shubkeluarga)->row_array();



						if ($worksheet->getCell("L" . $i)->getCalculatedValue() == "TDK_TH") {

							$gol_drh = null;
						} else {

							$gol_drh = $worksheet->getCell("L" . $i)->getCalculatedValue();
						}



						$arrayinsertbenar = array(

							'no_kk' => $worksheet->getCell("O" . $i)->getCalculatedValue(),

							'cl_status_hubungan_keluarga_id' => $hubkeluarga['id'],

							'nik' => $worksheet->getCell("A" . $i)->getCalculatedValue(),

							'nama_lengkap' => $worksheet->getCell("B" . $i)->getCalculatedValue(),

							'jenis_kelamin' => $jenis_kelamin,

							'agama' => $worksheet->getCell("F" . $i)->getCalculatedValue(),

							'tempat_lahir' => $worksheet->getCell("D" . $i)->getCalculatedValue(),

							'tgl_lahir' => $ultah,

							'status_kawin' => $status_kawin['id'],

							'pendidikan' => $pendidikan['id'],

							'cl_jenis_pekerjaan_id' => $pekerjaan['id'],

							'golongan_darah' => $gol_drh,

							'alamat' => $worksheet->getCell("U" . $i)->getCalculatedValue(),



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



					if (!empty($array_benar)) {

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



				$nama_provinsi = $this->db->get_where('cl_provinsi', array('id' => $data['cl_provinsi_id']))->row_array();

				$nama_kab_kota = $this->db->get_where('cl_kab_kota', array('id' => $data['cl_kab_kota_id']))->row_array();

				$nama_kecamatan = $this->db->get_where('cl_kecamatan', array('id' => $data['cl_kecamatan_id']))->row_array();

				$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

				if (!empty($_FILES['file']['name']) || !empty($_FILES['logo_kab_kota']['name']) || !empty($_FILES['logo_desa']['name'])) {
					$dir                     = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 2048;
					$config['encrypt_name']			= true;
					$this->load->library('upload', $config);
				}

				if (!empty($_FILES['file']['name'])) {
					$this->upload->initialize($config);

					if (!$this->upload->do_upload('file')) {
						return $this->upload->display_errors();
					} else {
						$data['file'] = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
					}
				}

				if (!empty($_FILES['logo_kab_kota']['name'])) {
					$this->upload->initialize($config);

					if (!$this->upload->do_upload('logo_kab_kota')) {
						return $this->upload->display_errors();
					} else {
						$data['logo_kab_kota'] = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
					}
				}

				if (!empty($_FILES['logo_desa']['name'])) {
					$this->upload->initialize($config);

					if (!$this->upload->do_upload('logo_desa')) {
						return $this->upload->display_errors();
					} else {
						$data['logo_desa'] = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
					}
				}


				$data['nama_provinsi'] = $nama_provinsi['nama'];

				$data['nama_kab_kota'] = $nama_kab_kota['nama'];

				$data['nama_kecamatan'] = $nama_kecamatan['nama'];

				$data['nama_desa'] = $nama_kelurahan['nama'];

				$data['status_logo_lainnya'] = (isset($data['status_logo_lainnya']) ? $data['status_logo_lainnya'] : 0);

				$data['qr_status'] = (isset($data['qr_status']) ? $data['qr_status'] : 0);

				break;

			case "surat_himbauan":

				$table = "tbl_data_surat_himbauan";


				$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

				$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

				$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

				$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

				$data['nip'] = $this->input->post('nip');

				$ttd = $this->db->get_where('tbl_data_penandatanganan', array('nip' => $data['nip']))->row_array();

				$data['nama'] = $ttd['nama'];

				$data['jabatan'] = $ttd['jabatan'];

				$data['pangkat'] = $ttd['pangkat'];

				break;

			case "broadcast":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|png';
				$config['max_size']             = 5120;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;
				$table = "tbl_data_broadcast";

				$array = array();


				$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

				$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

				$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

				$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];


				$data['tujuan'] = json_encode($data['tujuan']);

				$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa');

				break;

			case "data_surat":

				$data['user_id'] = $this->auth['id'];
				$data['data_surat'] = null;

				if (isset($data['tgl_surat']) && $data['tgl_surat'] != '') {
					$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
				}

				if (isset($data['tgl_surat']) && $data['tgl_surat'] != '') {
					$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
				}

				if (isset($data['tgl_pernyataan']) && $data['tgl_pernyataan'] != '') {
					$data['tgl_pernyataan'] = date('Y-m-d', strtotime($data['tgl_pernyataan']));
				}
				if (isset($data['tgl_pengantar']) && $data['tgl_pengantar'] != '') {
					$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
				}


				// $arsip = '';
				// $xdir  = date('Ymd');
				// if (!is_dir('./__data/' . $xdir)) {
				// 	mkdir('./__data/' . $xdir, 0755);
				// }


				$arsip = '';
				$xdir = date('Ymd');
				$path = './__data/' . $xdir;

				if (!is_dir($path)) {
					mkdir($path, 0755, true);
				}


				$xconfig['upload_path']          = './__data/' . $xdir;
				$xconfig['allowed_types']        = 'pdf|jpg|png|jpeg';
				$xconfig['max_size']             = 5120;
				$xconfig['encrypt_name']			= true;


				$this->load->library('upload', $xconfig);
				$this->upload->initialize($xconfig);

				if (!$this->upload->do_upload('arsip')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$arsip = '__data/' . $xdir . '/' . $this->upload->data()['file_name'];
				}

				$data['arsip'] = $arsip;

				$table = "tbl_data_surat";

				$array = array();

				if (isset($data['ttd_srikandi'])) {
					$array['ttd_srikandi'] = $data['ttd_srikandi'];
				}

				if (isset($data['nip'])) {
					$nip = $data['nip'];
					$ceknip = $this->db->query("SELECT id from tbl_data_penandatanganan where nip='$nip' and status='Aktif'")->row('id');
					$data['id_penandatanganan'] = $ceknip;
				}

				// 🔹 Jika belum ada id_penandatanganan, ambil otomatis penandatangan aktif di kelurahan user
				if (empty($data['id_penandatanganan'])) {
					$penanda = $this->db->query("
						SELECT id 
						FROM tbl_data_penandatanganan 
						WHERE status='Aktif' 
						AND cl_kelurahan_desa_id = '" . $this->auth['cl_kelurahan_desa_id'] . "' 
						LIMIT 1
					")->row_array();

					$data['id_penandatanganan'] = $penanda ? $penanda['id'] : null;
				}


				// if (isset($data['id'])) {
				// 	$nik = $data['nik'];
				// 	$ceknik = $this->db->query("SELECT id from tbl_data_penduduk where nik='$nik' and tbl_data_penduduk_id=")->row('id');
				// 	$data['tbl_data_penduduk_id'] = $ceknik;
				// }

				if ($sts_crud == "add") {


					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];


					$cek_res_nomor = $this->db
						->where('cl_jenis_surat_id', $data['cl_jenis_surat_id'])
						->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id'])
						->get('cl_nomor_surat');

					if (isset($data['no_surat']) &&  is_numeric($data['no_surat']) && $cek_res_nomor->num_rows() > 0) {

						$data_ns = format_nomor_surat($this->auth['cl_kecamatan_id'], $this->auth['cl_kelurahan_desa_id'], $data['cl_jenis_surat_id'], $data['tgl_surat'], $data['no_surat']);
						$data['no_surat'] = $data_ns['nomor_surat'];
						$data['nomor'] = $data_ns['nomor'];
						$data['bulan'] = $data_ns['bulan'];
						$data['tahun'] = $data_ns['tahun'];
						$data['p1'] = $data_ns['p1'];
						$data['p2'] = $data_ns['p2'];
						$data['p3'] = $data_ns['p3'];
						$data['p4'] = $data_ns['p4'];
						$data['format_nomor'] = $data_ns['format_nomor'];
						$data['param_nomor'] = $data_ns['param_nomor'];
						$param = json_decode($data_ns['param_nomor']);
						for ($i = count($param) - 1; $i >= 0; $i--) {
							if ($param[$i] == 'cl_jenis_surat_id') {
								$data[$param[$i]] = $data[$param[$i]];
							} else {
								$data["param_" . $param[$i]] = $data[$param[$i]];
							}
						}
					}


					if ($data['cl_jenis_surat_id'] == '16') {

						$penduduk = array(

							'status_kawin' => '3',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Cerai Nikah",

						);

						$this->db->update('tbl_data_penduduk', $penduduk, array('id' => $data['tbl_data_penduduk_id']));
					}

					// if ($data['cl_jenis_surat_id'] == '12') {

					// 	$sts = 'PINDAH DOMISILI';
					// 	$date = date('Y-m-d H:i:s');
					// 	$up = $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Pindah Penduduk";
					// 	for ($i=0; $i < count($data['nama_dalam_surat']); $i++) { 
					// 		$penduduk[] = array(

					// 			'nik' => $data['nama_dalam_surat'][$i],

					// 			'status_data' => $sts,

					// 			'update_date' => $date,

					// 			'update_by' => $up,

					// 		);
					// 	}
					// 	$this->db->update_batch('tbl_data_penduduk', $penduduk, 'nik');
					// }



					if ($data['cl_jenis_surat_id'] == '9') {

						$penduduk = array(

							'status_data' => 'MENINGGAL DUNIA',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Kematian",

						);

						$this->db->update('tbl_data_penduduk', $penduduk, array('id' => $data['tbl_data_penduduk_id']));
					}
				}



				if ($sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];


					if ($data['cl_jenis_surat_id'] == '16') {

						$penduduk = array(

							'status_kawin' => '3',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Cerai Nikah",

						);

						// $this->db->update('tbl_data_penduduk', $penduduk, array('id' => $data['tbl_data_penduduk_id']));



						if ($data['nik'] != $data['nik_lama']) {

							$aktifkan = array(

								'status_kawin' => '1',

								'update_date' => date('Y-m-d H:i:s'),

								'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Cerai Nikah",

							);

							// $this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $data['tbl_data_penduduk_id_lama']));
						}
					}



					// if ($data['cl_jenis_surat_id'] == '12') {

					// 	$penduduk = array(

					// 		'status_data' => 'PINDAH DOMISILI',

					// 		'update_date' => date('Y-m-d H:i:s'),

					// 		'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Pindah Penduduk",

					// 	);


					// 	if ($data['nik'] != $data['nik_lama']) {

					// 		$aktifkan = array(

					// 			'status_data' => 'AKTIF',

					// 			'update_date' => date('Y-m-d H:i:s'),

					// 			'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Pindah Penduduk",

					// 		);

					// 		// $this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $data['tbl_data_penduduk_id_lama']));
					// 	}
					// }




					if ($data['cl_jenis_surat_id'] == '9') {

						$penduduk = array(

							'status_data' => 'MENINGGAL DUNIA',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Kematian",

						);

						// $this->db->update('tbl_data_penduduk', $penduduk, array('id' => $data['tbl_data_penduduk_id']));



						if ($data['tbl_data_penduduk_id'] != $data['nik_lama']) {

							$aktifkan = array(

								'status_data' => 'AKTIF',

								'update_date' => date('Y-m-d H:i:s'),

								'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat Kematian",

							);

							// $this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $data['tbl_data_penduduk_id_lama']));
						}
					}
				}



				if ($sts_crud == "delete") {

					$datax = $this->db->get_where('tbl_data_surat', array('id' => $id))->row_array();

					if ($datax) {

						$aktifkan = array(

							'status_data' => 'AKTIF',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat",

						);

						$this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $datax['tbl_data_penduduk_id']));
					}
				}

				if ($sts_crud == "add" || $sts_crud == "edit") {

					switch ($data['cl_jenis_surat_id']) {

						case "156":

							$array['kewarganegaraan'] = $data['kewarganegaraan'];
							$array['dok_pembanding'] = $data['dok_pembanding'];
							$array['no_dok_pembanding'] = $data['no_dok_pembanding'];
							$array['tgl_dok_pengantar'] = $data['tgl_dok_pengantar'];

							$array['no_pengantar'] = $data['no_pengantar'];
							$array['tgl_pengantar'] = $data['tgl_pengantar'];
							$array['keperluan_surat'] = $data['keperluan_surat'];
							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "155":

							$array['nama_domisili_tanpa_nik'] = $data['nama_domisili_tanpa_nik'];
							$array['tempat_lahir_domisili'] = $data['tempat_lahir_domisili'];
							$array['tgl_lahir_domisili'] = $data['tgl_lahir_domisili'];
							$array['jenis_kelamin_domisili'] = $data['jenis_kelamin_domisili'];
							$array['agama_domisili'] = $data['agama_domisili'];
							$array['alamat_domisili_tanpa_nik'] = $data['alamat_domisili_tanpa_nik'];

							$array['masa_berlaku'] = $data['masa_berlaku'];
							$array['jenis_domisili'] = $data['jenis_domisili'];
							$array['no_pengantar'] = $data['no_pengantar'];
							$array['tgl_pengantar'] = $data['tgl_pengantar'];
							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_capil'] = $data['no_capil'];

							if ($data['tbl_data_penduduk_id_penjamin'] <> '') {
								$res = $this->db->where('id', $data['tbl_data_penduduk_id_penjamin'])->get('tbl_data_penduduk')->row();
								$array['id_penjamin'] = $data['tbl_data_penduduk_id_penjamin'];
								$array['nama_penjamin']   = $res->nama_lengkap;
								$array['nik_penjamin']    = $res->nik;
								$array['alamat_penjamin'] = $res->alamat;
							}

							$array['alamat_domisili'] = $data['alamat_domisili'];

							unset($data['tbl_data_penduduk_id_penjamin']);


							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "154":
							$array['nama_siswa'] = $data['nama_siswa'];
							$array['nama_sekolah'] = $data['nama_sekolah'];
							$array['id_bank'] = $data['id_bank'];
							$array['tahun_terima_pip'] = $data['tahun_terima_pip'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;


						case "153":

							$array['nama_wali'] = $data['nama_wali'];
							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];
							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];
							$array['agama_wali'] = $data['agama_wali'];
							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];
							$array['status_wali'] = $data['status_wali'];
							$array['alamat_wali'] = $data['alamat_wali'];

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];
							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];
							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];
							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "152":

							$array['nama_wali'] = $data['nama_wali'];
							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];
							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];
							$array['agama_wali'] = $data['agama_wali'];
							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];
							$array['status_wali'] = $data['status_wali'];
							$array['alamat_wali'] = $data['alamat_wali'];

							$array['alamat_domisili_pernyataan'] = $data['alamat_domisili_pernyataan'];
							$array['lama_tinggal'] = $data['lama_tinggal'];
							$array['alamat_asal'] = $data['alamat_asal'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "151":

							$array['nama_anak'] = $data['nama_anak'];
							$array['nik_anak_ubah'] = $data['nik_anak_ubah'];
							$array['tempat_lahir_anak'] = $data['tempat_lahir_anak'];
							$array['tgl_lahir_anak'] = $data['tgl_lahir_anak'];
							$array['agama_anak'] = $data['agama_anak'];
							$array['pekerjaan_anak'] = $data['pekerjaan_anak'];
							$array['alamat_anak'] = $data['alamat_anak'];

							$array['nama_baru'] = $data['nama_baru'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];
							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];
							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "150":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['rubah_alamat'] = $data['rubah_alamat'];
							$array['rubah_agama'] = $data['rubah_agama'];
							$array['rubah_pekerjaan'] = $data['rubah_pekerjaan'];
							$array['rubah_status'] = $data['rubah_status'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "149":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['dasar_surat_pencairan'] = $data['dasar_surat_pencairan'];
							$array['no_dasar_surat_pencairan'] = $data['no_dasar_surat_pencairan'];
							$array['ttg_dasar_surat_pencairan'] = $data['ttg_dasar_surat_pencairan'];
							$array['tgl_penilaian'] = $data['tgl_penilaian'];

							$array['dokumen_lampiran_pencairan'] = [];
							if (isset($data['isi_dokumen']) != '') {
								for ($i = 0; $i < count($data['isi_dokumen']); $i++) {
									$array['dokumen_lampiran_pencairan'][] = htmlspecialchars($data['isi_dokumen'][$i], ENT_QUOTES);
								}
							}
							unset($data['isi_dokumen']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "148":

							$array['dasar_surat_tj'] = $data['dasar_surat_tj'];
							$array['no_dasar_surat_tj'] = $data['no_dasar_surat_tj'];

							$array['pernyataan_tj'] = [];
							if (isset($data['isi_pernyataan']) != '') {
								for ($i = 0; $i < count($data['isi_pernyataan']); $i++) {
									$array['pernyataan_tj'][] = htmlspecialchars($data['isi_pernyataan'][$i], ENT_QUOTES);
								}
							}
							unset($data['isi_pernyataan']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "147":

							$array['nama_pegawai'] = $data['nama_pegawai'];
							$array['nip_pegawai'] = $data['nip_pegawai'];
							$array['jabatan_pegawai'] = $data['jabatan_pegawai'];
							$array['pangkat'] = $data['pangkat'];

							$array['alasan_izin_pegawai'] = $data['alasan_izin_pegawai'];
							$array['uraian_izin_pegawai'] = $data['uraian_izin_pegawai'];
							$array['tgl_kejadian_izin'] = $data['tgl_kejadian_izin'];

							$array['nama_camat'] = $data['nama_camat'];
							$array['nip_camat'] = $data['nip_camat'];
							$array['pangkat_camat'] = $data['pangkat_camat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "146":

							$pemilik_rumah = $this->db->query("SELECT a.*,b.nama_agama,c.nama_pekerjaan FROM tbl_data_penduduk a
													  LEFT JOIN cl_agama b ON a.agama = b.id
													  LEFT JOIN cl_jenis_pekerjaan c ON a.cl_jenis_pekerjaan_id = c.id
													  WHERE a.nik = '" . $data['nik_pemilik_rumah'] . "'")->row_array();

							$array['nama_pemilik_rumah'] = $pemilik_rumah['nama_lengkap'];
							$array['nik_pemilik_rumah'] = $pemilik_rumah['nik'];
							$array['alamat_pemilik_rumah'] = $pemilik_rumah['alamat'];

							$array['pil_jenis_menumpang'] = $data['pil_jenis_menumpang'];
							$array['nama_jalan_tujuan'] = $data['nama_jalan_tujuan'];
							$array['rt_alamat_tujuan'] = $data['rt_alamat_tujuan'];
							$array['rw_alamat_tujuan'] = $data['rw_alamat_tujuan'];
							$array['kelurahan_tujuan'] = $data['kelurahan_tujuan'];
							$array['kecamatan_tujuan'] = $data['kecamatan_tujuan'];
							$array['no_kk_tujuan'] = $data['no_kk_tujuan'];

							$array['status_dengan_pemilik'] = $data['status_dengan_pemilik'];
							$array['kelurahan_numpang_alamat'] = $data['kelurahan_numpang_alamat'];
							$array['kec_numpang_alamat'] = $data['kec_numpang_alamat'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "145":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['redaksi_pernyataan_duda'] = $data['redaksi_pernyataan_duda'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['nama_imam_kel'] = $data['nama_imam_kel'];

							$array['ceklis_ttd_imam'] = isset($data['ceklis_ttd_imam']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "144":

							$array['surat_tindak_lanjut'] = $data['surat_tindak_lanjut'];

							$array['tgl_surat_dasar'] = $data['tgl_surat_dasar'];

							$array['nomor_surat_dasar'] = $data['nomor_surat_dasar'];

							$array['nama_dalam_surat'] = $data['nama_dalam_surat'];

							$array['nip_pemohon'] = $data['nip_pemohon'];

							$array['pendidikan_pemohon'] = $data['pendidikan_pemohon'];

							$array['jabatan_pemohon'] = $data['jabatan_pemohon'];

							$array['unit_kerja'] = $data['unit_kerja'];

							$array['tempat_kerja_tujuan'] = $data['tempat_kerja_tujuan'];

							$array['ceklis_kop_ns'] = $data['ceklis_kop_ns'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "143":
							$pemohon = [];
							$data_penduduk = [];
							for ($i = 0; $i < count($data['nik_sktm']); $i++) {
								$pemohon[] = [
									'no_surat_sktm' => $data['no_surat_sktm'][$i],
									'no_pengantar_sktm' => $data['no_pengantar_sktm'][$i],
									'tgl_pengantar_sktm' => $data['tgl_pengantar_sktm'][$i],
									'nik_sktm' => $data['nik_sktm'][$i],
									'nama_sktm' => $data['nama_sktm'][$i],
									'alamat_domisili_sktm' => $data['alamat_domisili_sktm'][$i],
									'tempat_lahir_sktm' => $data['tempat_lahir_sktm'][$i],
									'tgl_lahir_sktm' => $data['tgl_lahir_sktm'][$i],
									'jns_kelamin_sktm' => $data['jns_kelamin_sktm'][$i],
									'status_sktm' => $data['status_sktm'][$i],
									'agama_sktm' => $data['agama_sktm'][$i],
									'pendidikan_sktm' => $data['pendidikan_sktm'][$i],
									'pekerjaan_sktm' => $data['pekerjaan_sktm'][$i],
									'alamat_sktm' => $data['alamat_sktm'][$i],
									'rt_sktm' => $data['rt_sktm'][$i],
									'rw_sktm' => $data['rw_sktm'][$i],
									'keperluan_surat' => $data['keperluan_surat'],
									'masa_berlaku_surat' => $data['masa_berlaku_surat'],
								];
								$cek = $this->db->where('nik', $data['nik_sktm'][$i])->get('tbl_data_penduduk');
								if ($cek->num_rows() <= 0) {
									$data_penduduk[] = [
										'nik' => $data['nik_sktm'][$i],
										'nama_lengkap' => $data['nama_sktm'][$i],
										'tempat_lahir' => $data['tempat_lahir_sktm'][$i],
										'tgl_lahir' => date('Y-m-d', strtotime($data['tgl_lahir_sktm'][$i])),
										'jenis_kelamin' => $data['jns_kelamin_sktm'][$i],
										'status_kawin' => $data['status_sktm'][$i],
										'agama' => $data['agama_sktm'][$i],
										'pendidikan' => $data['pendidikan_sktm'][$i],
										'cl_jenis_pekerjaan_id' => $data['pekerjaan_sktm'][$i],
										'alamat' => $data['alamat_sktm'][$i],
										'rt' => $data['rt_sktm'][$i],
										'rw' => $data['rw_sktm'][$i],
										'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
										'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
										'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
										'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
										'status_data' => 'AKTIF',
									];
								}
							}
							if (count($data_penduduk) > 0) {
								$this->db->insert_batch('tbl_data_penduduk', $data_penduduk);
							}
							$array['pemohon'] = $pemohon;
							unset($data['no_surat_sktm']);
							unset($data['no_pengantar_sktm']);
							unset($data['tgl_pengantar_sktm']);
							unset($data['nik_sktm']);
							unset($data['nama_sktm']);
							unset($data['alamat_domisili_sktm']);
							unset($data['tempat_lahir_sktm']);
							unset($data['tgl_lahir_sktm']);
							unset($data['jns_kelamin_sktm']);
							unset($data['status_sktm']);
							unset($data['agama_sktm']);
							unset($data['pendidikan_sktm']);
							unset($data['pekerjaan_sktm']);
							unset($data['alamat_sktm']);
							unset($data['rt_sktm']);
							unset($data['rw_sktm']);
							unset($data['keperluan_surat']);
							unset($data['masa_berlaku_surat']);
							$data['info_tambahan'] = json_encode($array);
							break;

						case "142":

							$array['kpd_yth'] = $data['kpd_yth'];

							$array['di_undangan'] = $data['di_undangan'];

							$array['perihal'] = $data['perihal'];

							$array['lampiran_undangan_kec'] = $data['lampiran_undangan_kec'];

							$array['dokumen_pengadaan'] = $data['dokumen_pengadaan'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['kd_rek_pengadaan']); $i++) {
								$array['data_dokumen'][] = array(
									'kd_rek_pengadaan' => $data['kd_rek_pengadaan'][$i],
									'keg_pengadaan'  => $data['keg_pengadaan'][$i],
									'sub_keg_pengadaan'  => $data['sub_keg_pengadaan'][$i],
									'belanja_pengadaan'     => $data['belanja_pengadaan'][$i],
									'nilai_pagu_pengadaan'     => $data['nilai_pagu_pengadaan'][$i],
									'calon_penyedia_pengadaan'     => $data['calon_penyedia_pengadaan'][$i],
								);
							}

							unset($data['kd_rek_pengadaan']);
							unset($data['keg_pengadaan']);
							unset($data['sub_keg_pengadaan']);
							unset($data['belanja_pengadaan']);
							unset($data['nilai_pagu_pengadaan']);
							unset($data['calon_penyedia_pengadaan']);

							$data['info_tambahan'] = json_encode($array);

							break;
						case "141":

							$array['nama_catin_laki'] = $data['nama_catin_laki'];
							$array['wali_catin_laki'] = $data['wali_catin_laki'];
							$array['tmt_lahir_catin_laki'] = $data['tmt_lahir_catin_laki'];
							$array['tgl_lahir_catin_laki'] = $data['tgl_lahir_catin_laki'];
							$array['agama_catin_laki'] = $data['agama_catin_laki'];
							$array['pekerjaan_catin_laki'] = $data['pekerjaan_catin_laki'];
							$array['alamat_catin_laki'] = $data['alamat_catin_laki'];

							$array['nama_catin_wanita'] = $data['nama_catin_wanita'];
							$array['wali_catin_wanita'] = $data['wali_catin_wanita'];
							$array['tmt_lahir_catin_wanita'] = $data['tmt_lahir_catin_wanita'];
							$array['tgl_lahir_catin_wanita'] = $data['tgl_lahir_catin_wanita'];
							$array['agama_catin_wanita'] = $data['agama_catin_wanita'];
							$array['pekerjaan_catin_wanita'] = $data['pekerjaan_catin_wanita'];
							$array['alamat_catin_wanita'] = $data['alamat_catin_wanita'];

							$array['waktu_dispen_catin'] = $data['waktu_dispen_catin'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "140":

							$beri_pernyataan = $this->db->query("SELECT a.*,b.nama_agama,c.nama_pekerjaan FROM tbl_data_penduduk a
													  LEFT JOIN cl_agama b ON a.agama = b.id
													  LEFT JOIN cl_jenis_pekerjaan c ON a.cl_jenis_pekerjaan_id = c.id
													  WHERE a.nik = '" . $data['nik_beri_pernyataan'] . "'")->row_array();

							$array['nama_beri_pernyataan'] = $beri_pernyataan['nama_lengkap'];
							$array['nik_beri_pernyataan'] = $beri_pernyataan['nik'];
							$array['tmt_lahir_beri_pernyataan'] = $beri_pernyataan['tempat_lahir'];
							$array['tgl_lahir_beri_pernyataan'] = $beri_pernyataan['tgl_lahir'];
							$array['agama_beri_pernyataan'] = $beri_pernyataan['nama_agama'];
							$array['pekerjaan_beri_pernyataan'] = $beri_pernyataan['nama_pekerjaan'];
							$array['alamat_beri_pernyataan'] = $beri_pernyataan['alamat'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_register'] = $data['no_register'];

							$array['tgl_register'] = $data['tgl_register'];

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$array['nip_pernyataan'] = $data['nip_pernyataan'];
							$nama = "SELECT nama FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$nama = $this->db->query($nama)->row_array();
							$array['nama'] 		= $nama['nama'];

							$jabatan = "SELECT jabatan FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$jabatan = $this->db->query($jabatan)->row_array();
							$array['jabatan'] 		= $jabatan['jabatan'];

							$pangkat = "SELECT pangkat FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$pangkat = $this->db->query($pangkat)->row_array();
							$array['pangkat'] 		= $pangkat['pangkat'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "139":

							$array['nama_beri_pernyataan'] = $data['nama_beri_pernyataan'];
							$array['nik_beri_pernyataan'] = $data['nik_beri_pernyataan'];
							$array['tempat_lahir_beri_pernyataan'] = $data['tempat_lahir_beri_pernyataan'];
							$array['tgl_lahir_beri_pernyataan'] = $data['tgl_lahir_beri_pernyataan'];
							$array['jenis_kelamin_beri_pernyataan'] = $data['jenis_kelamin_beri_pernyataan'];
							$array['agama_beri_pernyataan'] = $data['agama_beri_pernyataan'];
							$array['alamat_beri_pernyataan'] = $data['alamat_beri_pernyataan'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "138":

							// Ambil nama laki-laki dari NIK
							$laki = $this->db->query("SELECT a.*,b.nama_agama,c.nama_pekerjaan FROM tbl_data_penduduk a
													  LEFT JOIN cl_agama b ON a.agama = b.id
													  LEFT JOIN cl_jenis_pekerjaan c ON a.cl_jenis_pekerjaan_id = c.id
													  WHERE a.nik = '" . $data['nik_laki_laki'] . "'")->row_array();

							$array['laki_nama'] = $laki['nama_lengkap'];
							$array['nik_laki_laki'] = $laki['nik'];
							$array['laki_tempat_lahir'] = $laki['tempat_lahir'];
							$array['laki_tgl_lahir'] = $laki['tgl_lahir'];
							$array['agama_laki_laki'] = $laki['nama_agama'];
							$array['pekerjaan_laki_laki'] = $laki['nama_pekerjaan'];
							$array['alamat_laki_laki'] = $laki['alamat'];

							// Ambil nama perempuan dari NIK
							$perempuan = $this->db->query("SELECT a.*,b.nama_agama,c.nama_pekerjaan FROM tbl_data_penduduk a
													  LEFT JOIN cl_agama b ON a.agama = b.id
													  LEFT JOIN cl_jenis_pekerjaan c ON a.cl_jenis_pekerjaan_id = c.id
													  WHERE a.nik = '" . $data['nik_perempuan'] . "'")->row_array();

							$array['perempuan_nama'] = $perempuan['nama_lengkap'];
							$array['nik_perempuan'] = $perempuan['nik'];
							$array['perempuan_tempat_lahir'] = $perempuan['tempat_lahir'];
							$array['perempuan_tgl_lahir'] = $perempuan['tgl_lahir'];
							$array['agama_perempuan'] = $perempuan['nama_agama'];
							$array['pekerjaan_perempuan'] = $perempuan['nama_pekerjaan'];
							$array['alamat_perempuan'] = $perempuan['alamat'];

							$array['kewarganegaraan_lk'] = $data['kewarganegaraan_lk'];

							$array['kewarganegaraan_pr'] = $data['kewarganegaraan_pr'];

							$array['tgl_menikah'] = $data['tgl_menikah'];

							$array['tempat_menikah'] = $data['tempat_menikah'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "137":

							$array['kewarganegaraan'] = $data['kewarganegaraan'];

							$array['alamat_domisili_lahir'] = $data['alamat_domisili_lahir'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_pengantar_sekda'] = $data['no_pengantar_sekda'];

							$array['tgl_pengantar_sekda'] = $data['tgl_pengantar_sekda'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['no_register'] = $data['no_register'];

							$array['tgl_register'] = $data['tgl_register'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_anak_pernyataan']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_anak_pernyataan' => $data['nama_anak_pernyataan'][$i],
									'tempat_lahir_anak'  => $data['tempat_lahir_anak'][$i],
									'tgl_lahir_anak'  => $data['tgl_lahir_anak'][$i],
									'asal_sk'     => $data['asal_sk'][$i],
								);
							}

							unset($data['nama_anak_pernyataan']);
							unset($data['tempat_lahir_anak']);
							unset($data['tgl_lahir_anak']);
							unset($data['asal_sk']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$array['nip_pernyataan'] = $data['nip_pernyataan'];
							$nama = "SELECT nama FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$nama = $this->db->query($nama)->row_array();
							$array['nama'] 		= $nama['nama'];

							$jabatan = "SELECT jabatan FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$jabatan = $this->db->query($jabatan)->row_array();
							$array['jabatan'] 		= $jabatan['jabatan'];

							$pangkat = "SELECT pangkat FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$pangkat = $this->db->query($pangkat)->row_array();
							$array['pangkat'] 		= $pangkat['pangkat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "136":

							$array['nama_dalam_surat'] = $data['nama_dalam_surat'];

							$array['tempat_lahir_pemohon'] = $data['tempat_lahir_pemohon'];

							$array['tgl_lahir_pemohon'] = $data['tgl_lahir_pemohon'];

							$array['pendidikan_pemohon'] = $data['pendidikan_pemohon'];

							$array['alamat_pegawai'] = $data['alamat_pegawai'];

							$array['unit_kerja'] = $data['unit_kerja'];

							$array['tgl_awal_bekerja'] = $data['tgl_awal_bekerja'];

							$array['tgl_akhir_bekerja'] = $data['tgl_akhir_bekerja'];

							$array['no_sk'] = $data['no_sk'];

							$array['tgl_sk'] = $data['tgl_sk'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "135":

							$array['pangkat'] = $data['pangkat'];

							$array['nama_ket_kec'] = $data['nama_ket_kec'];

							$array['nip_ket_kec'] = $data['nip_ket_kec'];

							$array['jabatan_ket_kec'] = $data['jabatan_ket_kec'];

							$array['agama_ket_kec'] = $data['agama_ket_kec'];

							$array['alamat_ket_kec'] = $data['alamat_ket_kec'];

							$array['unit_kerja_ketum'] = $data['unit_kerja_ketum'];

							$array['judul_surat_ket'] 		= $data['judul_surat_ket'];

							$array['uraian'] 				= htmlspecialchars($data['uraian']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "134":

							$array['nama_pemohon'] 			= $data['nama_pemohon'];
							$array['tempat_lahir_pemohon']  = $data['tempat_lahir_pemohon'];
							$array['tgl_lahir_pemohon'] 	= $data['tgl_lahir_pemohon'];
							$array['jenis_kelamin_pemohon'] = $data['jenis_kelamin_pemohon'];
							$array['agama_pemohon'] 		= $data['agama_pemohon'];
							$array['nik_pemohon'] 			= $data['nik_pemohon'];
							$array['pekerjaan_pemohon'] 	= $data['pekerjaan_pemohon'];
							$array['alamat_pemohon'] 		= $data['alamat_pemohon'];

							$array['tujuan_pergi'] 			= $data['tujuan_pergi'];
							$array['keperluan_pergi'] 		= $data['keperluan_pergi'];
							$array['barang_bawaan_pergi'] 	= $data['barang_bawaan_pergi'];
							$array['lama_pergi'] 			= $data['lama_pergi'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_pengikut']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_pengikut' => $data['nama_pengikut'][$i],
								);
							}

							unset($data['nama_pengikut']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "133":

							$array['pangkat'] = $data['pangkat'];

							$array['nama_ket_kec'] = $data['nama_ket_kec'];

							$array['nip_ket_kec'] = $data['nip_ket_kec'];

							$array['jabatan_ket_kec'] = $data['jabatan_ket_kec'];

							$array['agama_ket_kec'] = $data['agama_ket_kec'];

							$array['alamat_ket_kec'] = $data['alamat_ket_kec'];

							$array['unit_kerja_ketum'] = $data['unit_kerja_ketum'];

							$array['judul_surat_ket'] 		= $data['judul_surat_ket'];

							$array['uraian'] 				= htmlspecialchars($data['uraian']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "132":

							$array['nama_alm_anak'] = $data['nama_alm_anak'];

							$array['tempat_meninggal'] = $data['tempat_meninggal'];

							$array['tgl_meninggal'] = $data['tgl_meninggal'];

							$array['jenis_kelamin_bayi'] = $data['jenis_kelamin_bayi'];

							$array['penyebab_kematian'] = $data['penyebab_kematian'];

							$array['alamat_domisili_menikah'] = $data['alamat_domisili_menikah'];

							$array['nama_ibu_alm'] = $data['nama_ibu_alm'];

							$array['nik_ibu_alm'] = $data['nik_ibu_alm'];

							$array['nama_ayah_alm'] = $data['nama_ayah_alm'];

							$array['nik_ayah_alm'] = $data['nik_ayah_alm'];

							$array['alamat_orangtua'] = $data['alamat_orangtua'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_pengantar_sekda'] = $data['no_pengantar_sekda'];

							$array['tgl_pernyataan_sekda'] = $data['tgl_pernyataan_sekda'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['nm_pelapor_kematian'] = $data['nm_pelapor_kematian'];

							$array['ceklis_ttd_pelapor'] = isset($data['ceklis_ttd_pelapor']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "131":

							$array['redaksi_surat_dukungan_2']       = $data['redaksi_surat_dukungan_2'];

							$array['nama_pegawai']       = $data['nama_pegawai'];
							$array['nip_pegawai']      	 = $data['nip_pegawai'];
							$array['pangkat_pegawai']    = $data['pangkat_pegawai'];
							$array['jabatan_pegawai']    = $data['jabatan_pegawai'];

							$array['alamat_kantor']		 = $data['alamat_kantor'];
							$array['keperluan_surat']		  = $data['keperluan_surat'];
							$array['redaksi_surat_dukungan']  = $data['redaksi_surat_dukungan'];

							$data['info_tambahan']       = json_encode($array);

							break;

						case "130":

							$array['nama_yayasan'] = $data['nama_yayasan'];

							$array['status_bangunan_masjid'] = $data['status_bangunan_masjid'];

							$array['bidang_yayasan'] = $data['bidang_yayasan'];

							$array['alamat_yayasan'] = $data['alamat_yayasan'];

							$array['pj_yayasan'] = $data['pj_yayasan'];

							$array['no_akta_pendiri_yayasan'] = $data['no_akta_pendiri_yayasan'];

							$array['tgl_akta_pendiri_yayasan'] = $data['tgl_akta_pendiri_yayasan'];

							$array['nama_notaris'] = $data['nama_notaris'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "129":

							$array['nama_masjid'] = $data['nama_masjid'];

							$array['status_bangunan_masjid'] = $data['status_bangunan_masjid'];

							$array['bidang_masjid'] = $data['bidang_masjid'];

							$array['alamat_masjid'] = $data['alamat_masjid'];

							$array['pj_masjid'] = $data['pj_masjid'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['sk_pengurus_masjid'] = $data['sk_pengurus_masjid'];

							$array['tgl_sk_pengurus_masjid'] = $data['tgl_sk_pengurus_masjid'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "128":

							$array['nama_pernyataan_keg'] = $data['nama_pernyataan_keg'];
							$array['tempat_lahir_pernyataan_keg'] = $data['tempat_lahir_pernyataan_keg'];
							$array['tgl_lahir_pernyataan_keg'] = $data['tgl_lahir_pernyataan_keg'];

							$array['alamat_lahir_pernyataan_keg'] = $data['alamat_lahir_pernyataan_keg'];
							$array['jabatan_lahir_pernyataan_keg'] = $data['jabatan_lahir_pernyataan_keg'];

							$array['nama_usaha_keg'] = $data['nama_usaha_keg'];
							$array['jenis_usaha_keg'] = $data['jenis_usaha_keg'];

							$array['nama_keg_pernyataan'] = $data['nama_keg_pernyataan'];
							$array['tgl_pernyataan_keg'] = $data['tgl_pernyataan_keg'];
							$array['waktu_keg_pernyataan'] = $data['waktu_keg_pernyataan'];

							$array['tempat_keg_pernyataan'] = $data['tempat_keg_pernyataan'];
							$array['telp_keg_pernyataan'] = $data['telp_keg_pernyataan'];

							$array['perjanjian_damai'] = [];
							if (isset($data['isi_perjanjian']) != '') {
								for ($i = 0; $i < count($data['isi_perjanjian']); $i++) {
									$array['perjanjian_damai'][] = htmlspecialchars($data['isi_perjanjian'][$i], ENT_QUOTES);
								}
							}
							unset($data['isi_perjanjian']);

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "127":

							$array['uraian_renov_rumah'] = $data['uraian_renov_rumah'];

							$array['keperluan_surat_renov'] = $data['keperluan_surat_renov'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "126":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['uraian'] = $data['uraian'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "125":

							$array['no_kk_pindah'] 		   = $data['no_kk_pindah'];

							$array['nama_kepala_keluarga'] = $data['nama_kepala_keluarga'];

							$array['alamat_asal'] 		   = $data['alamat_asal'];

							$array['tgl_pindah']           = $data['tgl_pindah'];

							$array['alamat_pindah']        = $data['alamat_pindah'];

							$array['alasan_pindah']        = $data['alasan_pindah'];

							$array['klasifikasi_pindah']   = $data['klasifikasi_pindah'];

							$array['alasan_pindah']        = $data['alasan_pindah'];

							$array['jenis_permohonan']     = $data['jenis_permohonan'];

							$array['jenis_kepindahan']     = $data['jenis_kepindahan'];

							$array['status_kk_tdk_pindah'] = $data['status_kk_tdk_pindah'];

							$array['status_kk_pindah']     = $data['status_kk_pindah'];

							$array['data_pindah_penduduk'] = [];

							for ($i = 0; $i < count($data['nama_pindah_penduduk2']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_pindah_penduduk2'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$res->keterangan_nama_pindah_penduduk2 = $data['keterangan'][$i];
								$array['data_pindah_penduduk'][] = $res;
							}

							unset($data['nama_pindah_penduduk2']);
							unset($data['keterangan']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "124":

							$array['surat_dari']  = $data['surat_dari'];

							$array['tgl_diterima'] = $data['tgl_diterima'];

							$array['no_agenda'] = $data['no_agenda'];

							$array['cl_sifat_surat'] = $data['cl_sifat_surat'];

							$array['perihal_disposisi'] = $data['perihal_disposisi'];

							$array['isi_disposisi'] = $data['isi_disposisi'];

							$array['diteruskan_disposisi'] = $data['diteruskan_disposisi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "123":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "122":

							$array['nama_pemilik_mikra']    = $data['nama_pemilik_mikra'];
							$array['nik_pemilik_mikra']    = $data['nik_pemilik_mikra'];
							$array['alamat_pemilik_mikra']    = $data['alamat_pemilik_mikra'];
							$array['kelurahan_pemilik_mikro']    = $data['kelurahan_pemilik_mikro'];

							$array['redaksi_surat_mikro']    = $data['redaksi_surat_mikro'];
							$array['no_telp_mikro']      	 = $data['no_telp_mikro'];

							$array['nama_perusahaan'] 	 	 = $data['nama_perusahaan'];
							$array['bentuk_perusahaan'] 	 = $data['bentuk_perusahaan'];
							$array['npwp_mikro'] 	 	 	 = $data['npwp_mikro'];
							$array['kegiatan_usaha_mikro'] 	 = $data['kegiatan_usaha_mikro'];
							$array['alamat_usaha_mikro'] 	 = $data['alamat_usaha_mikro'];
							$array['sarana_usaha_mikro'] 	 = $data['sarana_usaha_mikro'];
							$array['modal_usaha_mikro'] 	 = $data['modal_usaha_mikro'];
							$array['no_pendaftaran_mikro'] 	 = $data['no_pendaftaran_mikro'];
							$array['redaksi_surat_mikro_2']  = $data['redaksi_surat_mikro_2'];

							$data['info_tambahan']       = json_encode($array);

							break;

						case "121":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['redaksi_pernyataan_janda'] = $data['redaksi_pernyataan_janda'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['nama_imam_kel'] = $data['nama_imam_kel'];

							$array['ceklis_ttd_imam'] = isset($data['ceklis_ttd_imam']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "120":

							$array['dasar'] = [];
							if (isset($data['dasar']) != '') {
								for ($i = 0; $i < count($data['dasar']); $i++) {
									$array['dasar'][] = array(
										'dasar_penugasan' => $data['dasar'][$i]
									);
								}
							}
							unset($data['dasar']);

							$array['pil_surat_survey'] = $data['pil_surat_survey'];

							$array['nama_mhs'] = $data['nama_mhs'];
							$array['nim_jurusan_mhs'] = $data['nim_jurusan_mhs'];

							$array['nama_lembaga'] = $data['nama_lembaga'];
							$array['nama_ketim'] = $data['nama_ketim'];
							$array['pekerjaan_survey'] = $data['pekerjaan_survey'];
							$array['alamat_survey'] = $data['alamat_survey'];
							$array['tujuan_survey'] = $data['tujuan_survey'];
							$array['judul_survey'] = $data['judul_survey'];
							$array['lokasi_survey'] = $data['lokasi_survey'];
							$array['tgl_awal_penugasan'] = $data['tgl_awal_penugasan'];
							$array['tgl_akhir_penugasan'] = $data['tgl_akhir_penugasan'];
							$array['peneliti_lapangan'] = $data['peneliti_lapangan'];
							$array['nik_peneliti_lapngan'] = $data['nik_peneliti_lapngan'];
							$array['no_peneliti_lapangan'] = $data['no_peneliti_lapangan'];
							$array['redaksi_survey'] = $data['redaksi_survey'];
							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "119":

							$array['kpd_yth'] 		= $data['kpd_yth'];
							$array['di_undangan'] 	= $data['di_undangan'];
							$array['lampiran'] 		= $data['lampiran'];
							$array['perihal'] 		= $data['perihal'];

							$array['surat_lanjutan'] 		 = $data['surat_lanjutan'];
							$array['nomor_surat_lanjutan'] 	 = $data['nomor_surat_lanjutan'];
							$array['tgl_surat_lanjutan'] 	 = $data['tgl_surat_lanjutan'];
							$array['tentang_surat_lanjutan'] = $data['tentang_surat_lanjutan'];

							$array['pekerjaan_mhs'] 	= $data['pekerjaan_mhs'];
							$array['alamat_mhs'] 		= $data['alamat_mhs'];

							$array['tujuan_pkl'] 			= $data['tujuan_pkl'];
							$array['tgl_awal_kegiatan'] 	= $data['tgl_awal_kegiatan'];
							$array['tgl_akhir_kegiatan'] 	= $data['tgl_akhir_kegiatan'];

							$array['anggota_pkl'] = [];
							for ($i = 0; $i < count($data['anggota_pkl']); $i++) {
								$array['anggota_pkl'][] = array(
									'anggota_pkl' => $data['anggota_pkl'][$i]
								);
							}
							unset($data['anggota_pkl']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "118":

							$array['pil_pernyataan_tugas']       = $data['pil_pernyataan_tugas'];

							$array['nama_pegawai']       = $data['nama_pegawai'];
							$array['nip_pegawai']      	 = $data['nip_pegawai'];
							$array['pangkat']    		 = $data['pangkat'];

							$array['redaksi_undangan']   = $data['redaksi_undangan'];

							$data['info_tambahan']       = json_encode($array);

							break;

						case "117":

							$array['nama_pegawai']       = $data['nama_pegawai'];
							$array['nip_pegawai']      	 = $data['nip_pegawai'];
							$array['pangkat']    		 = $data['pangkat'];
							$array['jabatan_pegawai'] 	 = $data['jabatan_pegawai'];
							$array['unit_kerja_pegawai'] = $data['unit_kerja_pegawai'];

							$array['nama_pegawai2'] 	 	 = $data['nama_pegawai2'];
							$array['nip_pegawai2'] 		 	 = $data['nip_pegawai2'];
							$array['pangkat2'] 			 	 = $data['pangkat2'];
							$array['jabatan_pegawai2'] 	 	 = $data['jabatan_pegawai2'];
							$array['unit_kerja_pegawai2'] 	 = $data['unit_kerja_pegawai2'];

							$array['alasan_pinjam_barang'] 	 = $data['alasan_pinjam_barang'];

							$array['serah_terima_barang'] = [];
							for ($i = 0; $i < count($data['kode_barang']); $i++) {
								$array['serah_terima_barang'][] = array(
									'kode_barang'			=> $data['kode_barang'][$i],
									'nama_barang'  			=> $data['nama_barang'][$i],
									'merek_barang'      	=> $data['merek_barang'][$i],
									'nopol_barang'      	=> $data['nopol_barang'][$i],
									'warna_barang'  		=> $data['warna_barang'][$i],
									'tahun_serah_terima'	=> $data['tahun_serah_terima'][$i],
									'harga_perolehan'   	=> $data['harga_perolehan'][$i],
								);
							}

							unset($data['kode_barang']);
							unset($data['nama_barang']);
							unset($data['merek_barang']);
							unset($data['nopol_barang']);
							unset($data['warna_barang']);
							unset($data['tahun_serah_terima']);
							unset($data['harga_perolehan']);

							$array['wajib_pihak2'] = [];
							if (isset($data['wajib_pihak2']) != '') {
								for ($i = 0; $i < count($data['wajib_pihak2']); $i++) {
									$array['wajib_pihak2'][] = array(
										'wajib_pihak2' => $data['wajib_pihak2'][$i]
									);
								}
							}
							unset($data['wajib_pihak2']);

							$data['info_tambahan']       = json_encode($array);

							break;

						case "116":

							$array['nama_pegawai']       = $data['nama_pegawai'];
							$array['nip_pegawai']      	 = $data['nip_pegawai'];
							$array['pangkat']    		 = $data['pangkat'];
							$array['jabatan_pegawai'] 	 = $data['jabatan_pegawai'];
							$array['unit_kerja_pegawai'] = $data['unit_kerja_pegawai'];

							$array['nama_pegawai2'] 	 	 = $data['nama_pegawai2'];
							$array['nip_pegawai2'] 		 	 = $data['nip_pegawai2'];
							$array['pangkat2'] 			 	 = $data['pangkat2'];
							$array['jabatan_pegawai2'] 	 	 = $data['jabatan_pegawai2'];
							$array['unit_kerja_pegawai2'] 	 = $data['unit_kerja_pegawai2'];

							$array['serah_terima_barang'] = [];
							for ($i = 0; $i < count($data['kode_barang']); $i++) {
								$array['serah_terima_barang'][] = array(
									'kode_barang'			=> $data['kode_barang'][$i],
									'nama_barang'  			=> $data['nama_barang'][$i],
									'merek_barang'      	=> $data['merek_barang'][$i],
									'nopol_barang'      	=> $data['nopol_barang'][$i],
									'warna_barang'  		=> $data['warna_barang'][$i],
									'tahun_serah_terima'	=> $data['tahun_serah_terima'][$i],
									'harga_perolehan'   	=> $data['harga_perolehan'][$i],
								);
							}

							unset($data['kode_barang']);
							unset($data['nama_barang']);
							unset($data['merek_barang']);
							unset($data['nopol_barang']);
							unset($data['warna_barang']);
							unset($data['tahun_serah_terima']);
							unset($data['harga_perolehan']);

							$array['wajib_pihak2'] = [];
							if (isset($data['wajib_pihak2']) != '') {
								for ($i = 0; $i < count($data['wajib_pihak2']); $i++) {
									$array['wajib_pihak2'][] = array(
										'wajib_pihak2' => $data['wajib_pihak2'][$i]
									);
								}
							}
							unset($data['wajib_pihak2']);

							$data['info_tambahan']       = json_encode($array);

							break;

						case "115":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];

							$array['kenaikan_gaji'] = [];
							for ($i = 0; $i < count($data['nama_pegawai']); $i++) {

								$array['kenaikan_gaji'][] = array(
									'nama_pegawai' => $data['nama_pegawai'][$i],
									'nip_pegawai'  => $data['nip_pegawai'][$i],
									'pangkat_akhir'  => $this->template_golongan($data['pangkat_akhir'][$i], $format = "concat(pangkat,', ',nm_golongan)"),
									'jabatan_pegawai'  => $data['jabatan_pegawai'][$i],
									'tmt'  => $data['tmt'][$i],
								);
							}

							unset($data['nama_pegawai']);
							unset($data['nip_pegawai']);
							unset($data['pangkat_akhir']);
							unset($data['jabatan_pegawai']);
							unset($data['tmt']);


							$data['info_tambahan'] = json_encode($array);

							break;

						case "114":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];
							$array['cl_sifat_surat'] = $data['cl_sifat_surat'];

							$array['uraian_dinas_non'] = $data['uraian_dinas_non'];
							$array['nama_pegawai_non'] = $data['nama_pegawai_non'];
							$array['nik_pegawai_non'] = $data['nik_pegawai_non'];
							$array['tempat_lahir_non'] = $data['tempat_lahir_non'];
							$array['tgl_lahir_non'] = $data['tgl_lahir_non'];
							$array['unit_kerja'] = $data['unit_kerja'];
							$array['diperbantukan_pada'] = $data['diperbantukan_pada'];

							$array['pil_surat_teguran'] = $data['pil_surat_teguran'];

							$array['uraian_dinas_asn'] = $data['uraian_dinas_asn'];
							$array['nama_pegawai_asn'] = $data['nama_pegawai_asn'];
							$array['nip_pegawai'] = $data['nip_pegawai'];
							$array['pangkat'] = $data['pangkat'];
							$array['jab_sebelumnya'] = $data['jab_sebelumnya'];
							$array['jab_baru'] = $data['jab_baru'];
							$array['kel_asal'] = $data['kel_asal'];
							$array['kel_tujuan'] = $data['kel_tujuan'];

							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "113":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['redaksi_mohon_data'] = htmlspecialchars($data['redaksi_mohon_data']);

							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "112":

							$sql = "SELECT nm_golongan AS id, CONCAT(pangkat,', ',nm_golongan) AS txt FROM cl_golongan";
							$data_pegawai = $this->db->query($sql)->row_array();

							$array['pangkat'] = $data_pegawai['txt'];

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['nama_pegawai'] = $data['nama_pegawai'];
							$array['nip_pegawai'] = $data['nip_pegawai'];
							$array['jabatan_pegawai'] = $data['jabatan_pegawai'];
							$array['unit_kerja_pegawai'] = $data['unit_kerja_pegawai'];
							$array['no_telp_pegawai'] = $data['no_telp_pegawai'];

							$array['tunjangan_ke'] = $data['tunjangan_ke'];

							$array['lampiran_berkas'] = [];
							if (isset($data['lampiran_berkas']) != '') {
								for ($i = 0; $i < count($data['lampiran_berkas']); $i++) {
									$array['lampiran_berkas'][] = htmlspecialchars($data['lampiran_berkas'][$i], ENT_QUOTES);
								}
							}
							unset($data['lampiran_berkas']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "111":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['redaksi_undangan'] = $data['redaksi_undangan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "110":

							$array['dasar'] = [];
							if (isset($data['dasar']) != '') {
								for ($i = 0; $i < count($data['dasar']); $i++) {
									$array['dasar'][] = array(
										'dasar_penugasan' => $data['dasar'][$i]
									);
								}
							}
							unset($data['dasar']);

							$array['perintah_keg'] = [];
							if (isset($data['perintah_keg']) != '') {
								for ($i = 0; $i < count($data['perintah_keg']); $i++) {
									$array['perintah_keg'][] = htmlspecialchars($data['perintah_keg'][$i], ENT_QUOTES);
								}
							}
							unset($data['perintah_keg']);

							$array['perintah_kpd'] = htmlspecialchars($data['perintah_kpd'], ENT_QUOTES);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "109":

							$array['kpd_yth'] 			= $data['kpd_yth'];
							$array['di_undangan'] 		= $data['di_undangan'];
							$array['perihal'] 			= $data['perihal'];
							$array['cl_sifat_surat'] 	= $data['cl_sifat_surat'];
							$array['lampiran_undangan_kec'] = $data['lampiran_undangan_kec'];

							$array['redaksi_undangan'] 	= $data['redaksi_undangan'];

							$array['tgl_undangan'] 		 = $data['tgl_undangan'];
							$array['tgl_undangan_akhir'] = $data['tgl_undangan_akhir'];
							$array['jam_undangan'] 		 = $data['jam_undangan'];
							$array['tempat_undangan'] 	 = $data['tempat_undangan'];

							$array['redaksi_tambahan_undangan'] 	 = $data['redaksi_tambahan_undangan'];

							$array['catatan_undangan'] 	 = $data['catatan_undangan'];
							$array['tembusan'] 			 = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "108":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['lampiran'] = $data['lampiran'];
							$array['perihal'] = $data['perihal'];

							$array['asal_surat_rekomendasi'] = $data['asal_surat_rekomendasi'];
							$array['kegiatan_dilaksanakan'] = $data['kegiatan_dilaksanakan'];
							$array['tgl_awal_kegiatan'] = $data['tgl_awal_kegiatan'];
							$array['tgl_akhir_kegiatan'] = $data['tgl_akhir_kegiatan'];
							$array['jam_mulai'] = $data['jam_mulai'];
							$array['jam_akhir'] = $data['jam_akhir'];

							$array['tempat_kegiatan'] = $data['tempat_kegiatan'];
							$array['estimasi_pengunjung'] = $data['estimasi_pengunjung'];

							$array['dasar_ketentuan'] = [];
							for ($i = 0; $i < count($data['dasar_ketentuan']); $i++) {
								$array['dasar_ketentuan'][] = array(
									'dasar_ketentuan' => $data['dasar_ketentuan'][$i]
								);
							}
							unset($data['dasar_ketentuan']);

							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "107":

							// $array['no_surat_cuti'] = $data['no_surat_cuti'];

							$array['nama_pegawai'] = $data['nama_pegawai'];
							$array['nip_pegawai'] = $data['nip_pegawai'];
							$array['jabatan_pegawai'] = $data['jabatan_pegawai'];
							$array['unit_kerja_pegawai'] = $data['unit_kerja_pegawai'];
							$array['masa_kerja_pegawai'] = $data['masa_kerja_pegawai'];

							$array['alasan_cuti'] = $data['alasan_cuti'];
							$array['lamanya_cuti'] = $data['lamanya_cuti'];
							$array['tgl_awal_cuti'] = $data['tgl_awal_cuti'];
							$array['tgl_akhir_cuti'] = $data['tgl_akhir_cuti'];

							$array['sisa_cuti_thn'] = $data['sisa_cuti_thn'];
							$array['ket_cuti_thn'] = $data['ket_cuti_thn'];
							$array['alamat_selama_cuti'] = $data['alamat_selama_cuti'];
							$array['no_hp'] = $data['no_hp'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "106":

							$array['kewarganegaraan'] = $data['kewarganegaraan'];

							$array['tgl_meninggal'] = $data['tgl_meninggal'];

							$array['tgl_penguburan'] = $data['tgl_penguburan'];

							$array['waktu_penguburan'] = $data['waktu_penguburan'];

							$array['tempat_penguburan'] = $data['tempat_penguburan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "105":

							$array['nama_sekolah'] = $data['nama_sekolah'];

							$array['npsn_sekolah'] = $data['npsn_sekolah'];

							$array['alamat_sekolah'] = $data['alamat_sekolah'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "104":

							$array['nama_pihak_1'] = $data['nama_pihak_1'];
							$array['umur_pihak_1'] = $data['umur_pihak_1'];
							$array['pekerjaan_pihak_1'] = $data['pekerjaan_pihak_1'];
							$array['alamat_pihak_1'] = $data['alamat_pihak_1'];

							$array['nama_pihak_2'] = $data['nama_pihak_2'];
							$array['umur_pihak_2'] = $data['umur_pihak_2'];
							$array['pekerjaan_pihak_2'] = $data['pekerjaan_pihak_2'];
							$array['alamat_pihak_2'] = $data['alamat_pihak_2'];

							$array['redaksi_damai'] = htmlspecialchars($data['redaksi_damai'], ENT_QUOTES);

							$array['perjanjian_damai'] = [];
							if (isset($data['isi_perjanjian']) != '') {
								for ($i = 0; $i < count($data['isi_perjanjian']); $i++) {
									$array['perjanjian_damai'][] = htmlspecialchars($data['isi_perjanjian'][$i], ENT_QUOTES);
								}
							}
							unset($data['isi_perjanjian']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							// $array['data_mengetahui'] = [];
							// if (isset($data['nama_mengetahui'])) {
							// 	for ($i = 0; $i < count($data['nama_mengetahui']); $i++) {
							// 		$array['data_mengetahui'][] = array(
							// 			'nama' => $data['nama_mengetahui'][$i],
							// 			'pekerjaan' => $data['pekerjaan_mengetahui'][$i],
							// 		);
							// 	}
							// }
							// unset($data['nama_mengetahui']);
							// unset($data['pekerjaan_mengetahui']);

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "103":

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['alamat_usaha'] = $data['alamat_usaha'];

							$array['tgl_penutupan_usaha'] = $data['tgl_penutupan_usaha'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['tgl_register'] = $data['tgl_register'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "102":

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['alamat_usaha'] = $data['alamat_usaha'];

							$array['tgl_berdiri_usaha'] = $data['tgl_berdiri_usaha'];

							$array['tgl_penutupan_usaha'] = $data['tgl_penutupan_usaha'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "101":

							$array['lembar_ke'] = $data['lembar_ke'];
							$array['no_agenda'] = $data['no_agenda'];

							$array['pemberi_komitmen'] = $data['pemberi_komitmen'];
							$array['pelaksana_dinas'] = $data['pelaksana_dinas'];
							$array['nip_pelaksana_dinas'] = $data['nip_pelaksana_dinas'];
							$array['pangkat'] = $data['pangkat'];
							$array['jabatan'] = $data['jabatan'];
							$array['biaya_perjalanan_dinas'] = $data['biaya_perjalanan_dinas'];
							$array['tujuan_perjalanan_dinas'] = $data['tujuan_perjalanan_dinas'];
							$array['transportasi_dinas'] = $data['transportasi_dinas'];
							$array['tempat_berangkat'] = $data['tempat_berangkat'];
							$array['tempat_tujuan'] = $data['tempat_tujuan'];
							$array['waktu_dinas'] = $data['waktu_dinas'];
							$array['tgl_dinas'] = $data['tgl_dinas'];
							$array['tgl_selesai_dinas'] = $data['tgl_selesai_dinas'];

							$array['anggaran_dinas'] = $data['anggaran_dinas'];
							$array['kode_rekening_dinas'] = $data['kode_rekening_dinas'];

							$array['data_perjalanan_dinas'] = [];
							if (isset($data['tgl_lahir_dinas']) != '') {
								for ($i = 0; $i < count($data['nama_perjalanan_dinas']); $i++) {
									$array['data_perjalanan_dinas'][] = array(
										'nama_perjalanan_dinas' => $data['nama_perjalanan_dinas'][$i],
										'tgl_lahir_dinas' => $data['tgl_lahir_dinas'][$i],
										'ket_dinas' => $data['ket_dinas'][$i]
									);
								}
							}
							unset($data['nama_perjalanan_dinas']);
							unset($data['tgl_lahir_dinas']);
							unset($data['ket_dinas']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "100":

							$array['tujuan_nota'] = $data['tujuan_nota'];
							$array['pemberi_nota'] = $data['pemberi_nota'];
							$array['sifat_nota'] = $data['sifat_nota'];
							$array['perihal_nota'] = $data['perihal_nota'];
							$array['lampiran_nota'] = $data['lampiran_nota'];
							$array['tembusan_nota'] = $data['tembusan_nota'];

							$array['data_nota'] = [];
							if (isset($data['unit_kerja_lama']) != '') {
								for ($i = 0; $i < count($data['nama_penerima_nota']); $i++) {
									$array['data_nota'][] = array(
										'nama_penerima_nota' => $data['nama_penerima_nota'][$i],
										'unit_kerja_lama' => $data['unit_kerja_lama'][$i],
										'perbantuan_pada' => $data['perbantuan_pada'][$i]
									);
								}
							}
							unset($data['nama_penerima_nota']);
							unset($data['unit_kerja_lama']);
							unset($data['perbantuan_pada']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "99":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['surat_landasan'] = $data['surat_landasan'];
							$array['no_surat_landasan'] = $data['no_surat_landasan'];
							$array['perihal_surat_landasan'] = $data['perihal_surat_landasan'];
							$array['jenis_pelanggaran'] = $data['jenis_pelanggaran'];

							$array['pil_surat_teguran'] = $data['pil_surat_teguran'];

							$array['redaksi_teguran_asn'] = $data['redaksi_teguran_asn'];
							$array['nama_pegawai'] = $data['nama_pegawai'];
							$array['nip_pegawai'] = $data['nip_pegawai'];
							$array['pangkat'] = $data['pangkat'];
							$array['jabatan_pegawai'] = $data['jabatan_pegawai'];

							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "98":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['surat_landasan'] = $data['surat_landasan'];
							$array['no_surat_landasan'] = $data['no_surat_landasan'];
							$array['tgl_surat_landasan'] = $data['tgl_surat_landasan'];
							$array['perihal_surat_landasan'] = $data['perihal_surat_landasan'];

							$array['tgl_panggilan'] = $data['tgl_panggilan'];
							$array['jam_panggilan'] = $data['jam_panggilan'];
							$array['tempat_panggilan'] = $data['tempat_panggilan'];

							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "97":

							$array['nama_dalam_surat_akta'] = $data['nama_dalam_surat_akta'];

							$array['nik_bayi'] = $data['nik_bayi'];

							$array['tempat_lahir_bayi'] = $data['tempat_lahir_bayi'];

							$array['tgl_lahir_bayi'] = $data['tgl_lahir_bayi'];

							$array['alamat_bayi'] = $data['alamat_bayi'];

							$array['nama_ayah'] = $data['nama_ayah'];

							$array['ktp_ayah'] = $data['ktp_ayah'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['ktp_ibu'] = $data['ktp_ibu'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_register'] = $data['tgl_register'];

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "96":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['cl_sifat_surat'] = $data['cl_sifat_surat'];

							$array['nama_pegawai'] = $data['nama_pegawai'];
							$array['nik_pegawai'] = $data['nik_pegawai'];
							$array['tempat_lahir_pegawai'] = $data['tempat_lahir_pegawai'];
							$array['tgl_lahir_pegawai'] = $data['tgl_lahir_pegawai'];
							$array['ibu_kandung_pegawai'] = $data['ibu_kandung_pegawai'];
							$array['no_kpj_pegawai'] = $data['no_kpj_pegawai'];
							$array['alasan_di_phk'] = $data['alasan_di_phk'];
							$array['pekerjaan_pegawai'] = $data['pekerjaan_pegawai'];
							$array['alamat_pegawai'] = $data['alamat_pegawai'];
							$array['tgl_awal_bekerja'] = $data['tgl_awal_bekerja'];
							$array['tgl_akhir_bekerja'] = $data['tgl_akhir_bekerja'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "95":

							$array['pil_surat_penelitian'] = $data['pil_surat_penelitian'];

							$array['nama_peneliti_mhs'] = $data['nama_peneliti_mhs'];
							$array['nim_peneliti_mhs'] = $data['nim_peneliti_mhs'];
							$array['pekerjaan_peneliti_mhs'] = $data['pekerjaan_peneliti_mhs'];
							$array['alamat_peneliti_mhs'] = $data['alamat_peneliti_mhs'];
							$array['judul_penelitian_mhs'] = $data['judul_penelitian_mhs'];
							$array['tgl_awal_mhs'] = $data['tgl_awal_mhs'];
							$array['tgl_akhir_mhs'] = $data['tgl_akhir_mhs'];

							$array['pj_peneliti_instansi'] = $data['pj_peneliti_instansi'];
							$array['nama_peneliti_instansi'] = $data['nama_peneliti_instansi'];
							$array['nik_peneliti_instansi'] = $data['nik_peneliti_instansi'];
							$array['pekerjaan_peneliti_instansi'] = $data['pekerjaan_peneliti_instansi'];
							$array['alamat_peneliti_instansi'] = $data['alamat_peneliti_instansi'];
							$array['rumah_peneliti_instansi'] = $data['rumah_peneliti_instansi'];
							$array['judul_penelitian_instansi'] = $data['judul_penelitian_instansi'];
							$array['tgl_awal_instansi'] = $data['tgl_awal_instansi'];
							$array['tgl_akhir_instansi'] = $data['tgl_akhir_instansi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "94":

							$array['nama_dalam_surat'] = $data['nama_dalam_surat'];

							$array['unit_kerja'] = $data['unit_kerja'];

							$array['nik_pegawai'] = $data['nik_pegawai'];

							$array['alamat_pegawai'] = $data['alamat_pegawai'];

							$array['redaksi_surat_bekerja'] = $data['redaksi_surat_bekerja'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "93":

							$array['alasan_pengurangan'] = $data['alasan_pengurangan'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['nop_pengurangan'] = $data['nop_pengurangan'];
							$array['nama_pengurangan'] = $data['nama_pengurangan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "92":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['data_pergantian'] = [];
							if (isset($data['petugas_diganti']) != '') {
								for ($i = 0; $i < count($data['petugas_diganti']); $i++) {
									$array['data_pergantian'][] = array(
										'petugas_diganti' => $data['petugas_diganti'][$i],
										'petugas_pengganti' => $data['petugas_pengganti'][$i],
									);
								}
							}
							unset($data['petugas_diganti']);
							unset($data['petugas_pengganti']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "91":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['surat_landasan'] = $data['surat_landasan'];
							$array['no_surat_landasan'] = $data['no_surat_landasan'];
							$array['tgl_surat_landasan'] = $data['tgl_surat_landasan'];
							$array['perihal_surat_landasan'] = $data['perihal_surat_landasan'];

							$array['redaksi_surat_bekerja'] = $data['redaksi_surat_bekerja'];
							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "90":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['dasar_surat'] = $data['dasar_surat'];
							$array['tujuan_surat'] = $data['tujuan_surat'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['kegiatan_nontender']); $i++) {

								$array['data_dokumen'][] = array(
									'kegiatan_nontender' => $data['kegiatan_nontender'][$i],
									'pagu_nontender'  => $data['pagu_nontender'][$i],
									'penyedia_nontender'  => $data['penyedia_nontender'][$i],

								);
							}

							unset($data['kegiatan_nontender']);
							unset($data['pagu_nontender']);
							unset($data['penyedia_nontender']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "89":

							$array['data_petugas'] = [];
							if (isset($data['jabatan']) != '') {
								for ($i = 0; $i < count($data['nama_bertugas']); $i++) {
									$array['data_petugas'][] = array(
										'nama_petugas' => $data['nama_bertugas'][$i],
										'pangkat_petugas' => $data['pangkat_petugas'][$i],
										'nip_petugas' => $data['nip_petugas'][$i],
										'jabatan_petugas' => $data['jabatan'][$i]
									);
								}
							}
							unset($data['nama_bertugas']);
							unset($data['pangkat_petugas']);
							unset($data['nip_petugas']);
							unset($data['jabatan']);

							$array['dasar_tugas'] = $data['dasar_tugas'];
							$array['nomor_tugas'] = $data['nomor_tugas'];
							$array['tgl_tugas'] = $data['tgl_tugas'];
							$array['tentang_tugas'] = $data['tentang_tugas'];

							$array['tema_kegiatan'] = $data['tema_kegiatan'];
							$array['tempat_kegiatan'] = $data['tempat_kegiatan'];
							$array['waktu_tugas'] = $data['waktu_tugas'];
							$array['waktu_tugas_akhir'] = $data['waktu_tugas_akhir'];
							$array['tgl_awal_penugasan'] = $data['tgl_awal_penugasan'];
							$array['tgl_akhir_penugasan'] = $data['tgl_akhir_penugasan'];

							$array['ceklis_cetak_perwali'] = isset($data['ceklis_cetak_perwali']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "88":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['tindak_lanjut_surat'] = $data['tindak_lanjut_surat'];
							$array['tgl_surat_edaran'] = $data['tgl_surat_edaran'];
							$array['no_surat_edaran'] = $data['no_surat_edaran'];

							$array['nama_peneliti'] = $data['nama_peneliti'];
							$array['pekerjan_peneliti'] = $data['pekerjan_peneliti'];
							$array['alamat_kantor'] = $data['alamat_kantor'];
							$array['alamat_rumah_peneliti'] = $data['alamat_rumah_peneliti'];
							$array['judul_penelitian'] = $data['judul_penelitian'];

							$array['pil_surat_penelitian'] = $data['pil_surat_penelitian'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_peneliti_mhs']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_peneliti_mhs' 			=> $data['nama_peneliti_mhs'][$i],
									'nim_peneliti_mhs'  			=> $data['nim_peneliti_mhs'][$i],
									'pekerjaan_peneliti_mhs'        => $data['pekerjaan_peneliti_mhs'][$i],
									'alamat_peneliti_mhs'         	=> $data['alamat_peneliti_mhs'][$i],
								);
							}

							unset($data['nama_peneliti_mhs']);
							unset($data['nim_peneliti_mhs']);
							unset($data['pekerjaan_peneliti_mhs']);
							unset($data['alamat_peneliti_mhs']);

							$array['judul_penelitian_mhs'] = $data['judul_penelitian_mhs'];

							$array['tgl_awal_penelitian'] = $data['tgl_awal_penelitian'];
							$array['tgl_akhir_penelitian'] = $data['tgl_akhir_penelitian'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "87":

							$array['masa_berlaku'] = $data['masa_berlaku'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['catatan_undangan'] = $data['catatan_undangan'];

							if ($data['tbl_data_penduduk_id_penjamin'] <> '') {
								$res = $this->db->where('id', $data['tbl_data_penduduk_id_penjamin'])->get('tbl_data_penduduk')->row();
								$array['id_penjamin'] = $data['tbl_data_penduduk_id_penjamin'];
								$array['nama_penjamin']   = $res->nama_lengkap;
								$array['nik_penjamin']    = $res->nik;
								$array['alamat_penjamin'] = $res->alamat;
							}

							unset($data['tbl_data_penduduk_id_penjamin']);

							$array['alamat_domisili'] = $data['alamat_domisili'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "86":

							$array['surat_dari']  = $data['surat_dari'];

							$array['tgl_diterima'] = $data['tgl_diterima'];

							$array['no_agenda'] = $data['no_agenda'];

							$array['cl_sifat_surat'] = $data['cl_sifat_surat'];

							$array['perihal_disposisi'] = $data['perihal_disposisi'];

							$array['isi_disposisi'] = $data['isi_disposisi'];

							$array['diteruskan_disposisi'] = $data['diteruskan_disposisi'];

							$array['catatan_disposisi'] = $data['catatan_disposisi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "85":

							$array['jumlah_penghasilan']  = $data['jumlah_penghasilan'];
							$array['jumlah_terbilang'] = number_to_words(floatval(str_replace(".", "", $data['jumlah_penghasilan'])));

							$array['nama_anak_pernyataan'] = $data['nama_anak_pernyataan'];

							$array['nik_anak_pernyataan'] = $data['nik_anak_pernyataan'];

							$array['tempat_lahir_anak_pernyataan'] = $data['tempat_lahir_anak_pernyataan'];

							$array['tgl_lahir_anak_pernyataan'] = $data['tgl_lahir_anak_pernyataan'];

							$array['jenis_kelamin_anak_pernyataan'] = $data['jenis_kelamin_anak_pernyataan'];

							$array['pekerjaan_anak_pernyataan'] = $data['pekerjaan_anak_pernyataan'];

							$array['alamat_anak_pernyataan'] = $data['alamat_anak_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_register'] = $data['tgl_register'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "84":

							$array['pilih_judul'] = $data['pilih_judul'];

							$array['nama_wali'] = $data['nama_wali'];

							$array['nik_wali'] = $data['nik_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_dalam_dokumen']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_dalam_dokumen' => $data['nama_dalam_dokumen'][$i],
									'dokumen_pendukung'  => $data['dokumen_pendukung'][$i],
									'no_dokumen'         => $data['no_dokumen'][$i],
									'tempat_lahir'         => $data['tempat_lahir'][$i],
									'tgl_lahir_dokumen'  => $data['tgl_lahir_dokumen'][$i],
									'alamat_dokumen'     => $data['alamat_dokumen'][$i],
									'dikeluarkan_oleh'   => $data['dikeluarkan_oleh'][$i],
									'tgl_dikeluarkan'    => $data['tgl_dikeluarkan'][$i],
								);
							}

							unset($data['nama_dalam_dokumen']);
							unset($data['dokumen_pendukung']);
							unset($data['no_dokumen']);
							unset($data['tempat_lahir']);
							unset($data['tgl_lahir_dokumen']);
							unset($data['alamat_dokumen']);
							unset($data['dikeluarkan_oleh']);
							unset($data['tgl_dikeluarkan']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "83":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['uraian'] = $data['uraian'];

							// $array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							// $array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "82":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "81":

							$array['nama_toko'] = $data['nama_toko'];

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['alamat_usaha'] = $data['alamat_usaha'];

							$array['lama_usaha'] = $data['lama_usaha'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "80":

							$array['nama_almarhum'] = $data['nama_almarhum'];

							$array['jenis_kelamin_alm'] = $data['jenis_kelamin_alm'];

							$array['alamat_terakhir'] = $data['alamat_terakhir'];

							$array['tgl_meninggal'] = $data['tgl_meninggal'];

							$array['dikebumikan_di_pernyataan'] = $data['dikebumikan_di_pernyataan'];

							$array['rt_alm'] = $data['rt_alm'];

							$array['kota_meninggal'] = $data['kota_meninggal'];

							$array['no_pengantar_rt_alm'] = $data['no_pengantar_rt_alm'];

							$array['tgl_pengantar_rt_alm'] = $data['tgl_pengantar_rt_alm'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_register'] = $data['tgl_register'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "79":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_tidak_mampu'] = [];
							for ($i = 0; $i < count($data['nama_tidak_mampu']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_tidak_mampu'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$res->keterangan_nama_tidak_mampu = $data['keterangan'][$i];
								$array['data_tidak_mampu'][] = $res;
							}
							unset($data['nama_tidak_mampu']);
							unset($data['keterangan']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "78":

							$array['nama_wali'] = $data['nama_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "77":

							$array['alamat_domisili_menikah'] = $data['alamat_domisili_menikah'];

							$array['nama_wali'] = $data['nama_wali'];

							$array['nik_wali'] = $data['nik_wali'];

							$array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							$array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							$array['agama_wali'] = $data['agama_wali'];

							$array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							$array['status_wali'] = $data['status_wali'];

							$array['alamat_wali'] = $data['alamat_wali'];

							$array['uraian'] = $data['uraian'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['nama_imam_kel'] = $data['nama_imam_kel'];

							$array['ceklis_ttd_imam'] = isset($data['ceklis_ttd_imam']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "76":

							$sts  = 'MENINGGAL DUNIA';
							$date = date('Y-m-d H:i:s');
							$up   = $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Kematian Sekda";

							$penduduk = array(

								'status_data' => $sts,

								'update_date' => $date,

								'update_by'   => $up

							);
							$this->db->where('id', $data['tbl_data_penduduk_id'])->update('tbl_data_penduduk', $penduduk);

							$array['tempat_meninggal'] = $data['tempat_meninggal'];

							$array['tgl_meninggal'] = $data['tgl_meninggal'];

							$array['penyebab_kematian'] = $data['penyebab_kematian'];

							$array['alamat_domisili_menikah'] = $data['alamat_domisili_menikah'];

							$array['nama_ayah'] = $data['nama_ayah'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_pengantar_sekda'] = $data['no_pengantar_sekda'];

							$array['tgl_pernyataan_sekda'] = $data['tgl_pernyataan_sekda'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "75":

							$array['nama_anak_sekda'] = $data['nama_anak_sekda'];

							$array['tempat_lahir_anak'] = $data['tempat_lahir_anak'];

							$array['tgl_lahir_anak'] = $data['tgl_lahir_anak'];

							$array['jenis_kelamin_anak'] = $data['jenis_kelamin_anak'];

							$array['anak_ke'] = $data['anak_ke'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['nama_ayah'] = $data['nama_ayah'];

							$array['alamat_orangtua'] = $data['alamat_orangtua'];

							$array['no_pengantar_sekda'] = $data['no_pengantar_sekda'];

							$array['tgl_pernyataan_sekda'] = $data['tgl_pernyataan_sekda'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "74":

							$array['nama_anak'] = $data['nama_anak'];

							$array['tempat_lahir_anak'] = $data['tempat_lahir_anak'];

							$array['tgl_lahir_anak'] = $data['tgl_lahir_anak'];

							$array['jenis_kelamin_anak'] = $data['jenis_kelamin_anak'];

							$array['agama_anak'] = $data['agama_anak'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_kk'] = $data['no_kk'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "73":

							$array['nama_mhs'] = $data['nama_mhs'];
							$array['nim'] = $data['nim'];
							$array['jurusan'] = $data['jurusan'];
							$array['fakultas'] = $data['fakultas'];
							$array['judul_penelitian'] = $data['judul_penelitian'];

							$array['pil_surat_penelitian'] = $data['pil_surat_penelitian'];

							$array['nama_peneliti'] = $data['nama_peneliti'];
							$array['pekerjan_peneliti'] = $data['pekerjan_peneliti'];
							$array['alamat_kantor'] = $data['alamat_kantor'];
							$array['alamat_rumah_peneliti'] = $data['alamat_rumah_peneliti'];
							$array['judul_penelitian'] = $data['judul_penelitian'];

							$array['tujuan_penelitian'] = $data['tujuan_penelitian'];
							$array['tgl_mulai'] = $data['tgl_mulai'];
							$array['tgl_akhir'] = $data['tgl_akhir'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "72":

							$array['pil_surat_usulan'] = $data['pil_surat_usulan'];

							$array['lampiran_pangkat'] = $data['lampiran_pangkat'];
							$array['perihal_pangkat'] = $data['perihal_pangkat'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_usulan']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_usulan' => $data['nama_usulan'][$i],
									'pangkat_akhir'  => $this->template_golongan($data['pangkat_akhir'][$i], $format = "concat(pangkat,', ',nm_golongan)"),
									'tmt1'  => $data['tmt1'][$i],
									'pangkat_usulan'  => $this->template_golongan($data['pangkat_usulan'][$i], $format = "concat(pangkat,', ',nm_golongan)"),
									'tmt2'  => $data['tmt2'][$i],
								);
							}

							unset($data['nama_usulan']);
							unset($data['pangkat_akhir']);
							unset($data['tmt1']);
							unset($data['pangkat_usulan']);
							unset($data['tmt2']);

							$array['uraian'] 	   = htmlspecialchars($data['uraian']);

							$array['data_dokumen1'] = [];
							for ($i = 0; $i < count($data['nama_perpanjangan']); $i++) {
								$array['data_dokumen1'][] = array(
									'nama_perpanjangan' => $data['nama_perpanjangan'][$i],
									'pangkat_akhirp'  => $this->template_golongan($data['pangkat_akhirp'][$i], $format = "concat(pangkat,', ',nm_golongan)"),
									'jab_awal'  => $data['jab_awal'][$i],
									'jab_akhir'  => $data['jab_akhir'][$i],
									'ket_perpanjangan'  => $data['ket_perpanjangan'][$i],
								);
							}

							unset($data['nama_perpanjangan']);
							unset($data['pangkat_akhirp']);
							unset($data['jab_awal']);
							unset($data['jab_akhir']);
							unset($data['ket_perpanjangan']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "71":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];
							$array['nama_meninggal'] = $data['nama_meninggal'];
							$array['tgl_meninggal'] = $data['tgl_meninggal'];
							$array['jam_meninggal'] = $data['jam_meninggal'];
							$array['alamat_meninggal'] = $data['alamat_meninggal'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "70":

							// $array['tgl_pengecekan'] = $data['tgl_pengecekan'];

							$sql = "SELECT * FROM tbl_data_kendaraan where nopol = '" . $data['nopol'] . "' ";
							$data_kendaraan = $this->db->query($sql)->row_array();

							$array['nama_sopir'] = $data_kendaraan['nama_sopir'];
							$array['asal_kelurahan'] = $data_kendaraan['asal_kelurahan'];
							$array['jenis_barang'] = $data_kendaraan['jenis_barang'];
							$array['detail_barang'] = $data_kendaraan['detail_barang'];

							$array['kode_barang'] = $data_kendaraan['kode_barang'];
							$array['tahun_perolehan'] = $data_kendaraan['tahun_perolehan'];
							$array['nilai_perolehan'] = $data_kendaraan['nilai_perolehan'];
							$array['sumber_perolehan'] = $data_kendaraan['sumber_perolehan'];

							$array['dokumen_kepemilikan'] = $data_kendaraan['dokumen_kepemilikan'];
							$array['type_merek'] = $data_kendaraan['type_merek'];
							$array['warna_kendaraan'] = $data_kendaraan['warna_kendaraan'];
							$array['no_rangka'] = $data_kendaraan['no_rangka'];

							$array['no_mesin'] = $data_kendaraan['no_mesin'];
							$array['nopol'] = $data_kendaraan['nopol'];
							$array['ukuran_silinder'] = $data_kendaraan['ukuran_silinder'];
							$array['no_register_kendaraan'] = $data_kendaraan['no_register_kendaraan'];

							$array['kondisi_kendaraan'] = $data['kondisi_kendaraan'];
							// $array['kelayakan_kendaraan'] = $data['kelayakan_kendaraan'];
							// $array['bulan']       = $data['bulan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "69":

							$array['tgl_lahir'] = $data['tgl_lahir'];

							$array['tempat_lahir'] = $data['tempat_lahir'];

							$array['jenis_kelamin_bayi'] = $data['jenis_kelamin_bayi'];

							$array['nama_bayi'] = $data['nama_bayi'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['alamat_ibu'] = $data['alamat_ibu'];

							$array['nama_ayah_bayi'] = $data['nama_ayah_bayi'];

							$array['nama_pelapor'] = $data['nama_pelapor'];

							$array['hubungan_dg_bayi'] = $data['hubungan_dg_bayi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "68":

							$array['bulan']       = $data['bulan'];

							$array['tahun']      = $data['tahun'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_pernyataan_tpp']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_pernyataan_tpp' => $data['nama_pernyataan_tpp'][$i],
									'pangkat_akhir'  => $this->template_golongan($data['pangkat_akhir'][$i], $format = "concat(pangkat,', ',nm_golongan)"),
									'jab_pernyataan_tpp'  => $data['jab_pernyataan_tpp'][$i],
									'skpd_pernyataan_tpp'   => $data['skpd_pernyataan_tpp'][$i],
								);
							}

							unset($data['nama_pernyataan_tpp']);
							unset($data['pangkat_akhir']);
							unset($data['jab_pernyataan_tpp']);
							unset($data['skpd_pernyataan_tpp']);

							$data['info_tambahan']       = json_encode($array);

							break;

						case "67":

							$array['jenis_teguran'] = $data['jenis_teguran'];

							$array['kpd_yth'] = $data['kpd_yth'];

							$array['di_undangan'] = $data['di_undangan'];

							$array['perihal'] = $data['perihal'];

							$array['lampiran'] = $data['lampiran'];

							$array['dasar_teguran'] = $data['dasar_teguran'];

							$array['alasan_teguran'] = $data['alasan_teguran'];

							$array['jns_surat_teguran'] = $data['jns_surat_teguran'];

							$array['tujuan_teguran_rt'] = $data['tujuan_teguran_rt'];

							$array['no_teguran'] = $data['no_teguran'];

							$array['tentang_teguran'] = $data['tentang_teguran'];

							$array['nama_ditegur'] = $data['nama_ditegur'];

							$array['jabatan_ditegur'] = $data['jabatan_ditegur'];

							$array['jns_surat_teguran_rt'] = $data['jns_surat_teguran_rt'];

							$array['tujuan_teguran_warga'] = $data['tujuan_teguran_warga'];

							$array['alasan_teguran2'] = $data['alasan_teguran2'];

							$array['jns_surat_pegawai'] = $data['jns_surat_pegawai'];

							$array['tujuan_teguran_warga2'] = $data['tujuan_teguran_warga2'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "66":

							$array['tgl_kematian'] = $data['tgl_kematian'];

							$array['tempat_kematian'] = $data['tempat_kematian'];

							$array['sebab_kematian'] = $data['sebab_kematian'];

							$array['nama_pelapor'] = $data['nama_pelapor'];

							$array['hubungan_dg_meninggal'] = $data['hubungan_dg_meninggal'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "65":

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nik_rekap']); $i++) {
								$array['data_dokumen'][] = array(
									'nik_rekap' => $data['nik_rekap'][$i],
									'nama_rekap'  => $data['nama_rekap'][$i],
									'tgl_rekap_kematian'  => $data['tgl_rekap_kematian'][$i],
									'alamat_rekap'   => $data['alamat_rekap'][$i],
								);
							}

							unset($data['nik_rekap']);
							unset($data['nama_rekap']);
							unset($data['tgl_rekap_kematian']);
							unset($data['alamat_rekap']);

							$array['keperluan_surat2'] = $data['keperluan_surat2'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "64":

							$array['hubungan_narapidana'] = $data['hubungan_narapidana'];

							$array['hp_penjamin'] = $data['hp_penjamin'];

							$array['nama_narapidana'] = $data['nama_narapidana'];

							$array['umur_narapidana'] = $data['umur_narapidana'];

							$array['lokasi_narapidana'] = $data['lokasi_narapidana'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "63":

							$array['nama_pbb'] = $data['nama_pbb'];

							$array['nop'] = $data['nop'];

							$array['letak_obj_pajak'] = $data['letak_obj_pajak'];

							$array['nilai_pajak'] = $data['nilai_pajak'];

							$array['keperluan_surat2'] = $data['keperluan_surat2'];

							$array['tahun_sppt'] = $data['tahun_sppt'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "62":

							$array['status_tanah2'] = $data['status_tanah2'];

							$array['no_sertifikat'] = $data['no_sertifikat'];

							$array['dikuasai_sejak'] = $data['dikuasai_sejak'];

							$array['luas_tanah'] = $data['luas_tanah'];

							$array['pemekaran_dari'] = $data['pemekaran_dari'];

							$array['pemekaran_baru'] = $data['pemekaran_baru'];

							$array['alamat_baru'] = $data['alamat_baru'];

							$array['no_objek_pajak'] = $data['no_objek_pajak'];

							$array['nama_nob'] = $data['nama_nob'];

							$array['no_pengantar2'] = $data['no_pengantar2'];

							$array['tgl_pengantar2'] = $data['tgl_pengantar2'];

							$array['keperluan_surat2'] = $data['keperluan_surat2'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "61":

							$array['kpd_yth'] = $data['kpd_yth'];

							$array['di_undangan'] = $data['di_undangan'];

							$array['no_pengantar2'] = $data['no_pengantar2'];

							$array['tgl_pengantar2'] = $data['tgl_pengantar2'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar2'] = date('Y-m-d', strtotime($data['tgl_pengantar2']));
							$datax = array(
								'no_surat' => $data['no_pengantar2'],
								'tgl_surat' => $data['tgl_pengantar2'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "60":
							$nm_alm = "SELECT nama_lengkap as nama FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$nm_alm = $this->db->query($nm_alm)->row_array();

							$jk_alm = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$jk_alm = $this->db->query($jk_alm)->row_array();

							if ($jk_alm['jk'] == 'Laki-Laki') {
								$sebutan = 'Almarhum';
							} else {
								$sebutan = 'Almarhumah';
							}

							$sql = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sql = $this->db->query($sql)->row_array();


							if ($sql['jk'] == 'Laki-Laki') {
								$sebutan2 = 'Istrinya (Almarhumah)';
							} else {
								$sebutan2 = 'Suaminya (Almarhum)';
							}

							$array['sebutan'] = $sebutan;
							$array['sebutan2'] = $sebutan2;
							$array['nama_alm'] = $nm_alm['nama'];
							$array['alamat_alm'] = $data['alamat_alm'];
							$array['rt_alm'] = $data['rt_alm'];
							$array['rw_alm'] = $data['rw_alm'];
							$array['tgl_meninggal'] = $data['tgl_meninggal'];
							$array['tempat_meninggal'] = $data['tempat_meninggal'];
							$array['jml_anak'] = $data['jml_anak'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];
							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];
							$array['no_reg_camat'] = $data['no_reg_camat'];
							$array['tgl_reg_camat'] = $data['tgl_reg_camat'];

							$array['tgl_meninggal_istri'] = $data['tgl_meninggal_istri'];
							$array['tempat_meninggal2'] = $data['tempat_meninggal2'];

							$array['nama_camat'] = $data['nama_camat'];
							$array['jabatan_camat'] = $data['jabatan_camat'];
							$array['nip_camat'] = $data['nip_camat'];
							$array['pangkat_camat'] = $data['pangkat_camat'];

							$array['data_ahli_waris'] = [];
							for ($i = 0; $i < count($data['nama_ahli_waris']); $i++) {
								$array['data_ahli_waris'][] = array(
									'nama_ahli_waris' => $data['nama_ahli_waris'][$i],
									'nik_ahli_waris'  => $data['nik_ahli_waris'][$i],
									'hubungan_waris' => $data['hubungan_waris'][$i],
									'status_waris' => $data['status_waris'][$i]
								);
							}
							unset($data['nik_ahli_waris']);

							$array['data_saksi'] = [];
							for ($i = 0; $i < count($data['nama_saksi']); $i++) {
								$array['data_saksi'][] = array(
									'nama' => $data['nama_saksi'][$i],
									'pekerjaan' => $data['pekerjaan_saksi'][$i],
								);
							}

							$nik = "SELECT status_data FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sts_alm = $this->db->query($nik)->row_array();
							$array['nama_ahli_waris'] = $data['nama_ahli_waris'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];
							$array['nama_saksi'] = $data['nama_saksi'];
							$array['status_alm'] = $sts_alm['status_data'];
							$array['pekerjaan_saksi'] = $data['pekerjaan_saksi'];

							$data['info_tambahan'] = json_encode($array);

							break;


						case "59":

							$array['no_hp'] = $data['no_hp'];

							$array['nama_toko'] = $data['nama_toko'];

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['status_tanah2'] = $data['status_tanah2'];

							$array['luas_bangunan'] = $data['luas_bangunan'];

							$array['alamat_usaha'] = $data['alamat_usaha'];

							$array['no_register_usaha'] = $data['no_register_usaha'];

							$array['nama_camat'] = $data['nama_camat'];

							$array['jabatan_camat'] = $data['jabatan_camat'];

							$array['nip_camat'] = $data['nip_camat'];

							$array['pangkat_camat'] = $data['pangkat_camat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "58":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];
							$array['no_pemberitahuan'] = $data['no_pemberitahuan'];
							$array['oleh_panggilan'] = $data['oleh_panggilan'];
							$array['tgl_mediasi'] = $data['tgl_mediasi'];
							$array['nama_mediasi'] = $data['nama_mediasi'];
							$array['kuasa_dari'] = $data['kuasa_dari'];
							$array['atas_panggilan'] = $data['atas_panggilan'];
							$array['status_tanah2'] = $data['status_tanah2'];
							$array['no_panggilan'] = $data['no_panggilan'];
							$array['tgl_panggilan'] = $data['tgl_panggilan'];
							$array['jenis_tanah'] = $data['jenis_tanah'];
							$array['luas_tanah'] = $data['luas_tanah'];
							$array['alamat_penggilan'] = $data['alamat_penggilan'];
							$array['hari_panggilan'] = $data['hari_panggilan'];
							$array['tgl_panggilan'] = $data['tgl_panggilan'];
							$array['jam_panggilan'] = $data['jam_panggilan'];
							$array['tempat_panggilan'] = $data['tempat_panggilan'];
							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "57":


							$array['nama_duplikat_kematian'] = $data['nama_duplikat_kematian'];

							$array['nik_duplikat_kematian'] = $data['nik_duplikat_kematian'];

							$array['umur_duplikat_kematian'] = $data['umur_duplikat_kematian'];

							$array['alamat_duplikat_kematian'] = $data['alamat_duplikat_kematian'];

							$array['tgl_kematian'] = $data['tgl_kematian'];

							$array['dikebumikan_di'] = $data['dikebumikan_di'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['no_reg_duplikat'] = $data['no_reg_duplikat'];

							$data['info_tambahan'] = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);


							break;

						case "56":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['tembusan'] = $data['tembusan'];

							$array['data_dokumen'] = [];
							// var_dump($data['data_dokumen']);
							// 	exit();
							for ($i = 0; $i < count($data['uraian_kec']); $i++) {

								$array['data_dokumen'][] = array(
									'uraian_kec' => $data['uraian_kec'][$i],
									'jml_kec'  => $data['jml_kec'][$i],
									'ket_kec'  => $data['ket_kec'][$i],

								);
							}

							unset($data['uraian_kec']);
							unset($data['jml_kec']);
							unset($data['ket_kec']);


							$data['info_tambahan'] = json_encode($array);

							break;

						case "55":
							$sql = "SELECT * FROM tbl_data_kendaraan where nopol = '" . $data['nama_sopir'] . "' ";
							$data_kendaraan = $this->db->query($sql)->row_array();

							$array['nama_sopir'] = $data_kendaraan['nama_sopir'];
							$array['type_merek'] = $data_kendaraan['type_merek'];
							$array['nopol'] = $data_kendaraan['nopol'];
							$array['no_rangka'] = $data_kendaraan['no_rangka'];
							$array['no_mesin'] = $data_kendaraan['no_mesin'];
							$array['asal_kelurahan'] = $data_kendaraan['asal_kelurahan'];
							$array['jenis_perbaikan'] = $data['jenis_perbaikan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "54":

							$array['ceklis_3ttd'] = isset($data['ceklis_3ttd']);

							$array['judul_surat_bukti'] = $data['judul_surat_bukti'];

							$dir                     = date('Ymd');
							if (!is_dir('./__data/' . $dir)) {
								mkdir('./__data/' . $dir, 0755);
							}

							$config['upload_path']          = './__data/' . $dir;
							$config['allowed_types']        = 'pdf|jpg|jpeg|png';
							$config['max_size']             = 2048;
							$config['encrypt_name']			= true;


							$this->load->library('upload', $config);
							$files = $_FILES['file_foto'];
							$file_foto = array();
							foreach ($data['ket_foto'] as $key => $image) {
								if ($files['name'][$key] != null) {
									$_FILES['images[]']['name'] = $files['name'][$key];
									$_FILES['images[]']['type'] = $files['type'][$key];
									$_FILES['images[]']['tmp_name'] = $files['tmp_name'][$key];
									$_FILES['images[]']['error'] = $files['error'][$key];
									$_FILES['images[]']['size'] = $files['size'][$key];

									$this->upload->initialize($config);

									if ($this->upload->do_upload('images[]')) {
										$file_foto[] = $this->upload->data();
									} else {
										return false;
									}
								} elseif (isset($data['file_name_foto']) && $data['file_name_foto'] != '') {
									$file_foto[] = [
										'file_name' => explode('/', $data['file_name_foto'][$key])[2],
									];
								} else {
									return false;
								}
							}

							$array['data_foto_dokumen'] = [];
							$n = 0;
							foreach ($file_foto as $file) {
								$array['data_foto_dokumen'][] = array(
									'file_foto' => '__data/' . $dir . '/' . $file['file_name'],
									'ket_foto'  => $data['ket_foto'][$n],
								);
								$n++;
							}

							unset($data['file']);
							unset($data['ket_foto']);
							unset($data['file_name_foto']);

							$data['info_tambahan']       = json_encode($array);

							break;

						case "53":

							$file_foto = array();
							// var_dump(count($_FILES['file_foto']['name']));
							// exit();
							if (count($_FILES['file_foto']['name']) == 1) {
								$data['file_foto'] = '';
								$data['ket_foto'] = '';
								$data['file_name_foto'] = '';

								unset($data['file_foto']);
								unset($data['ket_foto']);
								unset($data['file_name_foto']);
							} else {

								if (isset($_FILES['file_foto']) && count($_FILES['file_foto']) > 0) {


									$dir                     = date('Ymd');
									if (!is_dir('./__data/' . $dir)) {
										mkdir('./__data/' . $dir, 0755);
									}
								}

								$files = $_FILES['file_foto'];

								$config['upload_path']          = './__data/' . $dir;
								$config['allowed_types']        = 'pdf|jpg|jpeg|png';
								$config['max_size']             = 2048;
								$config['encrypt_name']			= true;


								$this->load->library('upload', $config);
								foreach ($data['ket_foto'] as $key => $image) {
									if ($files['name'][$key] != null) {
										$_FILES['image']['name'] = $files['name'][$key];
										$_FILES['image']['type'] = $files['type'][$key];
										$_FILES['image']['tmp_name'] = $files['tmp_name'][$key];
										$_FILES['image']['error'] = $files['error'][$key];
										$_FILES['image']['size'] = $files['size'][$key];

										$this->upload->initialize($config);

										if ($this->upload->do_upload('image')) {
											$file_foto[] = $this->upload->data();
										} else {
											return false;
										}
									} elseif (isset($data['file_name_foto']) && $data['file_name_foto'] != '') {
										$file_foto[] = [
											'file_name' => explode('/', $data['file_name_foto'][$key])[2],
										];
									} else {
										return false;
									}
								}

								$array['data_foto_dokumen'] = [];
								$n = 0;
								foreach ($file_foto as $file) {
									$array['data_foto_dokumen'][] = array(
										'file_foto' => '__data/' . $dir . '/' . $file['file_name'],
										'ket_foto'  => $data['ket_foto'][$n],
									);
									$n++;
								}

								unset($data['file_foto']);
								unset($data['ket_foto']);
								unset($data['file_name_foto']);
							}



							$array['alasan_surat'] = $data['alasan_surat'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['nama_pegawai'] = $data['nama_pegawai'];

							$array['nip_pegawai'] = $data['nip_pegawai'];

							$array['pangkat_pegawai'] = $data['pangkat_pegawai'];

							$array['jabatan_pegawai'] = $data['jabatan_pegawai'];

							$array['nama_petugas_camat'] = $data['nama_petugas_camat'];

							$array['jabatan_pegawai_camat'] = $data['jabatan_pegawai_camat'];

							$array['nip_pegawai_camat'] = $data['nip_pegawai_camat'];

							$array['pangkat_pegawai_camat'] = $data['pangkat_pegawai_camat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "52":

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_kuasa']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_kuasa' => $data['nama_kuasa'][$i],
									'umur_kuasa'  => $data['umur_kuasa'][$i],
									'pekerjaan_kuasa'  => $data['pekerjaan_kuasa'][$i],
									'alamat_kuasa'   => $data['alamat_kuasa'][$i],
								);
							}

							unset($data['nama_kuasa']);
							unset($data['umur_kuasa']);
							unset($data['pekerjaan_kuasa']);
							unset($data['alamat_kuasa']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$array['nip_pernyataan'] = $data['nip_pernyataan'];
							$nama = "SELECT nama FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$nama = $this->db->query($nama)->row_array();
							$array['nama'] 		= $nama['nama'];

							$jabatan = "SELECT jabatan FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$jabatan = $this->db->query($jabatan)->row_array();
							$array['jabatan'] 		= $jabatan['jabatan'];

							$pangkat = "SELECT pangkat FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$pangkat = $this->db->query($pangkat)->row_array();
							$array['pangkat'] 		= $pangkat['pangkat'];

							$data['info_tambahan']       = json_encode($array);

							$datax = array(

								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "51":
							$nm_alm = "SELECT nama_lengkap as nama FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$nm_alm = $this->db->query($nm_alm)->row_array();

							$jk_alm = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$jk_alm = $this->db->query($jk_alm)->row_array();

							if ($jk_alm['jk'] == 'Laki-Laki') {
								$sebutan = 'Almarhum';
							} else {
								$sebutan = 'Almarhumah';
							}

							$sql = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sql = $this->db->query($sql)->row_array();


							if ($sql['jk'] == 'Laki-Laki') {
								$sebutan2 = 'Istrinya (Almarhumah)';
							} else {
								$sebutan2 = 'Suaminya (Almarhum)';
							}

							$array['sebutan'] = $sebutan;
							$array['sebutan2'] = $sebutan2;
							$array['nama_alm'] = $nm_alm['nama'];
							$array['alamat_alm'] = $data['alamat_alm'];
							$array['rt_alm'] = $data['rt_alm'];
							$array['rw_alm'] = $data['rw_alm'];
							$array['tgl_meninggal'] = $data['tgl_meninggal'];
							$array['tempat_meninggal'] = $data['tempat_meninggal'];
							$array['jml_anak'] = $data['jml_anak'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];

							$array['no_surat_kematian'] = $data['no_surat_kematian'];
							$array['tgl_surat_kematian'] = $data['tgl_surat_kematian'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];
							$array['no_reg_camat'] = $data['no_reg_camat'];
							$array['tgl_reg_camat'] = $data['tgl_reg_camat'];

							$array['tgl_meninggal_istri'] = $data['tgl_meninggal_istri'];

							$array['data_ahli_waris'] = [];
							for ($i = 0; $i < count($data['nama_ahli_waris']); $i++) {
								$array['data_ahli_waris'][] = array(
									'nama_ahli_waris' => $data['nama_ahli_waris'][$i],
									'nik_ahli_waris'  => $data['nik_ahli_waris'][$i],
									'hubungan_waris' => $data['hubungan_waris'][$i],
									'status_waris' => $data['status_waris'][$i]
								);
							}
							unset($data['nik_ahli_waris']);

							$array['data_saksi'] = [];
							for ($i = 0; $i < count($data['nama_saksi']); $i++) {
								$array['data_saksi'][] = array(
									'nama' => $data['nama_saksi'][$i],
									'pekerjaan' => $data['pekerjaan_saksi'][$i],
								);
							}

							$nik = "SELECT status_data FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sts_alm = $this->db->query($nik)->row_array();


							$array['nama_ahli_waris'] = $data['nama_ahli_waris'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];
							$array['nama_saksi'] = $data['nama_saksi'];
							$array['status_alm'] = $sts_alm['status_data'];
							$array['pekerjaan_saksi'] = $data['pekerjaan_saksi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "50":

							$array['jenis_hibah'] = $data['jenis_hibah'];

							$array['luas_hibah'] = $data['luas_hibah'];

							$array['alamat_tempat_hibah'] = $data['alamat_tempat_hibah'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['no_pernyataan'] = $data['no_pernyataan'];

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['batas_utara_hibah'] = $data['batas_utara_hibah'];

							$array['batas_timur_hibah'] = $data['batas_timur_hibah'];

							$array['batas_selatan_hibah'] = $data['batas_selatan_hibah'];

							$array['batas_barat_hibah'] = $data['batas_barat_hibah'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];

							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_hibah']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_hibah' => $data['nama_hibah'][$i],
									'nik_hibah'  => $data['nik_hibah'][$i],
									'tempat_lahir_hibah'  => $data['tempat_lahir_hibah'][$i],
									'ttl_hibah'  => $data['ttl_hibah'][$i],
									'alamat_hibah'   => $data['alamat_hibah'][$i],
								);
							}

							unset($data['nama_hibah']);
							unset($data['nik_hibah']);
							unset($data['tempat_lahir_hibah']);
							unset($data['ttl_hibah']);
							unset($data['alamat_hibah']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);


							$data['info_tambahan']       = json_encode($array);

							$datax = array(

								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "49":
							$nm_alm = "SELECT nama_lengkap as nama FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$nm_alm = $this->db->query($nm_alm)->row_array();

							$sql = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sql = $this->db->query($sql)->row_array();


							if ($sql['jk'] == 'Laki-Laki') {
								$x = 'Almarhum';
								$y = 'Almarhumah';
							} else {
								$x = 'Almarhumah';
								$y = 'Almarhum';
							}

							$array['sebutan_x'] = $x;
							$array['sebutan_y'] = $y;

							$array['nama_alm'] = $nm_alm['nama'];
							$array['nama_meninggal_istri'] = $data['nama_meninggal_istri'];

							$array['nama_terima_kuasa'] = $data['nama_terima_kuasa'];
							$array['nik_terima_kuasa'] = $data['nik_terima_kuasa'];
							$array['tempat_lahir_kuasa'] = $data['tempat_lahir_kuasa'];
							$array['tgl_lahir_kuasa'] = $data['tgl_lahir_kuasa'];
							$array['agama'] = $data['agama'];
							$array['jenis_kelamin'] = $data['jenis_kelamin'];
							$array['suku_terima_kuasa'] = $data['suku_terima_kuasa'];
							$array['cl_jenis_pekerjaan_id'] = $data['cl_jenis_pekerjaan_id'];
							$array['alamat_terima_kuasa'] = $data['alamat_terima_kuasa'];
							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['data_kuasa_waris'] = [];
							for ($i = 0; $i < count($data['nama_ahli_waris']); $i++) {
								$array['data_kuasa_waris'][] = array(
									'nama_ahli_waris' => $data['nama_ahli_waris'][$i],

								);
							}

							$array['data_saksi'] = [];
							for ($i = 0; $i < count($data['nama_saksi']); $i++) {
								$array['data_saksi'][] = array(
									'nama' => $data['nama_saksi'][$i],
									'pekerjaan' => $data['pekerjaan_saksi'][$i],
								);
							}

							$nik = "SELECT status_data FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sts_alm = $this->db->query($nik)->row_array();


							$array['nama_ahli_waris'] = $data['nama_ahli_waris'];
							$array['nama_saksi'] = $data['nama_saksi'];
							$array['status_alm'] = $sts_alm['status_data'];
							$array['pekerjaan_saksi'] = $data['pekerjaan_saksi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "48":

							$array['data_keterangan_umum'] = [];

							if (isset($data['nama_keterangan_umum']) != '') {
								for ($i = 0; $i < count($data['nama_keterangan_umum']); $i++) {
									$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
										'a.id' => $data['nama_keterangan_umum'][$i],
										'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
									])
										->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
										->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
										->join('cl_agama d', 'a.agama=d.id', 'LEFT')
										->get('tbl_data_penduduk a')->row();
									$array['data_keterangan_umum'][] = $res;
								}
							}
							unset($data['nama_keterangan_umum']);
							// unset($data['keterangan']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$array['kpd_yth'] 				= $data['kpd_yth'];
							$array['di_undangan'] 			= $data['di_undangan'];
							$array['perihal'] 				= $data['perihal'];
							$array['lampiran'] 				= $data['lampiran'];
							$array['uraian'] 				= htmlspecialchars($data['uraian']);
							$array['tembusan'] 				= $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "47":

							$array['kewarganegaraan'] = $data['kewarganegaraan'];

							$array['nama_perusahaan'] = $data['nama_perusahaan'];

							$array['alamat_perusahaan'] = $data['alamat_perusahaan'];

							$array['bidang_usaha'] = $data['bidang_usaha'];

							$array['luas_usaha'] = $data['luas_usaha'];

							$array['bangunan_usaha'] = $data['bangunan_usaha'];

							$array['status_tanah'] = $data['status_tanah'];

							$array['no_sk'] = $data['no_sk'];

							$array['no_register_usaha'] = $data['no_register_usaha'];

							$array['nama_camat'] = $data['nama_camat'];

							$array['jabatan_camat'] = $data['jabatan_camat'];

							$array['nip_camat'] = $data['nip_camat'];

							$array['pangkat_camat'] = $data['pangkat_camat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "46":

							$array['no_skpwni']           = $data['no_skpwni'];

							$array['tgl_datang']           = $data['tgl_datang'];

							$array['alamat_pindah']        = $data['alamat_pindah'];

							$array['alasan_pindah']        = $data['alasan_pindah'];

							$array['klasifikasi_pindah']        = $data['klasifikasi_pindah'];

							$array['jenis_kepindahan']        = $data['jenis_kepindahan'];

							$array['status_kk_tdk_pindah']        = $data['status_kk_tdk_pindah'];

							$array['status_kk_pindah']        = $data['status_kk_pindah'];

							$array['keperluan_surat']      = $data['keperluan_surat'];

							$array['data_pindah_penduduk'] = [];

							for ($i = 0; $i < count($data['nama_pindah_penduduk2']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_pindah_penduduk2'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$res->keterangan_nama_pindah_penduduk2 = $data['keterangan'][$i];
								$array['data_pindah_penduduk'][] = $res;
							}

							unset($data['nama_pindah_penduduk2']);
							unset($data['keterangan']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "45":

							$array['pil_pernyataan_umum'] 		= $data['pil_pernyataan_umum'];

							$array['nama_pernyatan_umum'] 		= $data['nama_pernyatan_umum'];
							$array['nik_pernyatan_umum'] 		= $data['nik_pernyatan_umum'];
							$array['tempat_lahir_pernyataan'] 	= $data['tempat_lahir_pernyataan'];
							$array['tgl_lahir_pernyataan'] 		= $data['tgl_lahir_pernyataan'];
							$array['agama_pernyataan'] 			= $data['agama_pernyataan'];
							$array['pekerjaan_pernyataan'] 		= $data['pekerjaan_pernyataan'];
							$array['alamat_pernyataan'] 		= $data['alamat_pernyataan'];

							$array['judul_surat_ket'] 	= $data['judul_surat_ket'];
							$array['uraian'] 			= htmlspecialchars($data['uraian']);

							$array['judul_pernyataan_mobil'] 	= $data['judul_pernyataan_mobil'];
							$array['uraian_mobil'] 				= htmlspecialchars($data['uraian_mobil']);

							$array['judul_pernyataan_pulsa'] 	= $data['judul_pernyataan_pulsa'];
							$array['uraian_pulsa'] 				= htmlspecialchars($data['uraian_pulsa']);

							$array['judul_pernyataan_pdam'] = $data['judul_pernyataan_pdam'];
							$array['uraian_pdam'] 			= htmlspecialchars($data['uraian_pdam']);

							$array['judul_pernyataan_pbb']  = $data['judul_pernyataan_pbb'];
							$array['uraian_pbb'] 			= htmlspecialchars($data['uraian_pbb']);

							$array['no_register_pernyataan'] = $data['no_register_pernyataan'];

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' 		=> $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);


							$array['nip_pernyataan'] = $data['nip_pernyataan'];
							$nama = "SELECT nama FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$nama = $this->db->query($nama)->row_array();
							$array['nama'] 		= $nama['nama'];

							$jabatan = "SELECT jabatan FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$jabatan = $this->db->query($jabatan)->row_array();
							$array['jabatan'] 		= $jabatan['jabatan'];

							$pangkat = "SELECT pangkat FROM tbl_data_penandatanganan WHERE nip = '" . $data['nip_pernyataan'] . "' ";
							$pangkat = $this->db->query($pangkat)->row_array();
							$array['pangkat'] 		= $pangkat['pangkat'];



							$data['info_tambahan'] = json_encode($array);

							break;

						case "44":

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['data_saksi_tanah'] = [];
							for ($i = 0; $i < count($data['nama_saksi_tanah']); $i++) {
								$array['data_saksi_tanah'][] = array(
									'nama' => $data['nama_saksi_tanah'][$i],
									'pekerjaan' => $data['pekerjaan_saksi_tanah'][$i],
								);
							}
							unset($data['nama_saksi_tanah']);
							unset($data['pekerjaan_saksi_tanah']);


							$array['no_sppt_pbb']       = $data['no_sppt_pbb'];

							$array['lokasi_tanah']   	= $data['lokasi_tanah'];

							$array['rt_pemilik_tanah']  = $data['rt_pemilik_tanah'];

							$array['rw_pemilik_tanah']  = $data['rw_pemilik_tanah'];

							$array['panjang_tanah']     = $data['panjang_tanah'];

							$array['lebar_tanah']  		= $data['lebar_tanah'];

							$array['sejak_tahun']       = $data['sejak_tahun'];

							$array['batas_utara']       = $data['batas_utara'];

							$array['batas_timur']       = $data['batas_timur'];

							$array['batas_selatan']     = $data['batas_selatan'];

							$array['batas_barat']     	= $data['batas_barat'];

							$array['nama_rt']     	= $data['nama_rt'];

							$array['nama_rw']     	= $data['nama_rw'];

							$array['no_register']   = $data['no_register'];

							$array['tgl_register']  = $data['tgl_register'];

							$data['info_tambahan']        = json_encode($array);



							break;

						case "43":

							$this->load->helper('terbilang');

							$array['jumlah_penghasilan2']  = $data['jumlah_penghasilan2'];
							$array['jumlah_penghasilan']  = $data['jumlah_penghasilan'];
							$array['jumlah_terbilang'] = number_to_words(floatval(str_replace(".", "", $data['jumlah_penghasilan'])));


							$array['nama_anak']           = $data['nama_anak'];

							$array['tempat_lahir_anak']   = $data['tempat_lahir_anak'];

							$array['tgl_lahir_anak']      = $data['tgl_lahir_anak'];

							$array['nik_anak']            = $data['nik_anak'];

							$array['agama_anak']          = $data['agama_anak'];

							$array['jenis_kelamin_anak']  = $data['jenis_kelamin_anak'];

							$array['alamat_anak']         = $data['alamat_anak'];

							$array['no_pengantar']        = $data['no_pengantar'];

							$array['tgl_pengantar']       = $data['tgl_pengantar'];

							$array['pernyataan_dari']       = $data['pernyataan_dari'];

							$array['tgl_pernyataan']       = $data['tgl_pernyataan'];

							$array['keperluan_surat']     = $data['keperluan_surat'];

							$data['info_tambahan']        = json_encode($array);

							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "42":

							$array['no_pengantar_pernyataan'] = $data['no_pengantar_pernyataan'];

							$array['tgl_pengantar_pernyataan'] = $data['tgl_pengantar_pernyataan'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$array['data_saksi'] = [];
							if (isset($data['nama_saksi'])) {
								for ($i = 0; $i < count($data['nama_saksi']); $i++) {
									$array['data_saksi'][] = array(
										'nama' => $data['nama_saksi'][$i],
										'pekerjaan' => $data['pekerjaan_saksi'][$i],
									);
								}
							}
							unset($data['nama_saksi']);
							unset($data['pekerjaan_saksi']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "41":

							$array['nama_istri']           = $data['nama_istri'];

							$array['tgl_meninggal']        = $data['tgl_meninggal'];

							//$array['hari_meninggal']       = $data['hari_meninggal'];

							$array['nama_pemakaman']       = $data['nama_pemakaman'];

							$array['no_surat_keterangan']  = $data['no_surat_keterangan'];

							$array['tgl_surat_keterangan'] = $data['tgl_surat_keterangan'];

							$array['tempat_meninggal']     = $data['tempat_meninggal'];

							$array['surat_pengantar']      = $data['surat_pengantar'];

							$array['tgl_pengantar']        = $data['tgl_pengantar'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['keperluan_surat']      = $data['keperluan_surat'];

							$data['info_tambahan']         = json_encode($array);

							$datax = array(
								'no_surat' => $data['surat_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "40":

							$array['no_surat_tukang'] = $data['no_surat_tukang'];

							$array['tgl_surat_tukang'] = $data['tgl_surat_tukang'];

							$array['nama_pembuat_kapal'] = $data['nama_pembuat_kapal'];

							$array['pekerjaan_pembuatan'] = $data['pekerjaan_pembuatan'];

							$array['alamat_pembuat'] = $data['alamat_pembuat'];

							$array['rt_pembuat'] = $data['rt_pembuat'];

							$array['rw_pembuat'] = $data['rw_pembuat'];

							$array['nama_kapal2'] = $data['nama_kapal2'];

							$array['panjang_perahu'] = $data['panjang_perahu'];

							$array['lebar_perahu'] = $data['lebar_perahu'];

							$array['dalam_perahu'] = $data['dalam_perahu'];

							$array['mesin_kapal'] = $data['mesin_kapal'];

							$array['merek_mesin'] = $data['merek_mesin'];

							$array['nama_camat2'] = $data['nama_camat2'];

							$array['jabatan_camat2'] = $data['jabatan_camat2'];

							$array['nip_camat2'] = $data['nip_camat2'];

							$array['pangkat_camat2'] = $data['pangkat_camat2'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "39":

							$array['nama_pemilik_kapal'] = $data['nama_pemilik_kapal'];

							$array['tempat_lahir_pemilik'] = $data['tempat_lahir_pemilik'];

							$array['tgl_lahir_pemilik'] = $data['tgl_lahir_pemilik'];

							$array['pekerjaan_pemilik'] = $data['pekerjaan_pemilik'];

							$array['alamat_pemilik'] = $data['alamat_pemilik'];

							$array['rt_pemilik'] = $data['rt_pemilik'];

							$array['rw_pemilik'] = $data['rw_pemilik'];

							$array['nama_perahu'] = $data['nama_perahu'];

							$array['jenis_perahu'] = $data['jenis_perahu'];

							$array['bahan_perahu'] = $data['bahan_perahu'];

							$array['panjang_perahu'] = $data['panjang_perahu'];

							$array['lebar_perahu'] = $data['lebar_perahu'];

							$array['dalam_perahu'] = $data['dalam_perahu'];

							$array['dibangun_di'] = $data['dibangun_di'];

							$array['tgl_peletakan_lunas'] = $data['tgl_peletakan_lunas'];

							$array['tgl_peluncuran'] = $data['tgl_peluncuran'];

							$array['merek_mesin'] = $data['merek_mesin'];

							$array['type_mesin'] = $data['type_mesin'];

							$array['daya_mesin'] = $data['daya_mesin'];

							$array['putaran_mesin'] = $data['putaran_mesin'];

							$array['no_seri_mesin'] = $data['no_seri_mesin'];

							$array['nama_camat'] = $data['nama_camat'];
							$array['jabatan_camat'] = $data['jabatan_camat'];
							$array['nip_camat'] = $data['nip_camat'];
							$array['pangkat_camat'] = $data['pangkat_camat'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];

							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "38":

							$array['jabatan_kapal'] = $data['jabatan_kapal'];

							$array['nama_kapal'] = $data['nama_kapal'];

							$array['rentang_awal'] = $data['rentang_awal'];

							$array['rentang_akhir'] = $data['rentang_akhir'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "37":

							$array['dasar'] = [];
							if (isset($data['dasar']) != '') {
								for ($i = 0; $i < count($data['dasar']); $i++) {
									$array['dasar'][] = array(
										'dasar_penugasan' => $data['dasar'][$i]
									);
								}
							}
							unset($data['dasar']);

							$array['data_petugas'] = [];
							if (isset($data['jabatan']) != '') {
								for ($i = 0; $i < count($data['nama_bertugas']); $i++) {
									$array['data_petugas'][] = array(
										'nama_petugas' => $data['nama_bertugas'][$i],
										'jabatan_petugas' => $data['jabatan'][$i]
									);
								}
							}
							unset($data['nama_bertugas']);
							unset($data['jabatan']);


							$array['nomor_tugas'] = $data['nomor_tugas'];
							$array['perihal_tugas'] = $data['perihal_tugas'];
							$array['tgl_awal_penugasan'] = $data['tgl_awal_penugasan'];
							$array['tgl_akhir_penugasan'] = $data['tgl_akhir_penugasan'];
							$array['hari_penugasan'] = $data['hari_penugasan'];
							$array['jam_penugasan'] = $data['jam_penugasan'];
							$array['tempat_penugasan'] = $data['tempat_penugasan'];
							$array['tujuan_penugasan'] = $data['tujuan_penugasan'];
							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "36":

							$array['surat_pengantar1'] = $data['surat_pengantar1'];

							$array['tgl_pengantar1'] = $data['tgl_pengantar1'];

							$array['suku_bangsa'] = $data['suku_bangsa'];

							$array['kewarganegaraan'] = $data['kewarganegaraan'];

							// $array['no_surat_skck'] = $data['no_surat_skck'];


							$data['info_tambahan'] = json_encode($array);

							break;

						case "35":

							$array['data_keterangan_umum'] = [];

							if (isset($data['nama_keterangan_umum']) != '') {
								for ($i = 0; $i < count($data['nama_keterangan_umum']); $i++) {
									$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
										'a.id' => $data['nama_keterangan_umum'][$i],
										'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
									])
										->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
										->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
										->join('cl_agama d', 'a.agama=d.id', 'LEFT')
										->get('tbl_data_penduduk a')->row();
									$array['data_keterangan_umum'][] = $res;
								}
							}
							unset($data['nama_keterangan_umum']);
							// unset($data['keterangan']);

							$array['pil_keterangan_umum'] 		= $data['pil_keterangan_umum'];

							$array['judul_surat_ket'] 		= $data['judul_surat_ket'];
							$array['uraian'] 				= htmlspecialchars($data['uraian']);
							$array['status_kop_keterangan'] = isset($data['status_kop_keterangan']);

							$array['nama_anak_mobil']           = $data['nama_anak_mobil'];
							$array['tempat_lahir_anak_mobil']   = $data['tempat_lahir_anak_mobil'];
							$array['tgl_lahir_anak_mobil']      = $data['tgl_lahir_anak_mobil'];
							$array['nik_anak_mobil']            = $data['nik_anak_mobil'];
							$array['agama_anak_mobil']          = $data['agama_anak_mobil'];
							$array['jenis_kelamin_anak_mobil']  = $data['jenis_kelamin_anak_mobil'];
							$array['pekerjaan_anak_mobil']      = $data['pekerjaan_anak_mobil'];
							$array['alamat_anak_mobil']         = $data['alamat_anak_mobil'];
							$array['keperluan_surat_mobil']     = $data['keperluan_surat_mobil'];
							$array['uraian_mobil'] 				= htmlspecialchars($data['uraian_mobil']);

							$array['nama_anak_pulsa']           = $data['nama_anak_pulsa'];
							$array['tempat_lahir_anak_pulsa']   = $data['tempat_lahir_anak_pulsa'];
							$array['tgl_lahir_anak_pulsa']      = $data['tgl_lahir_anak_pulsa'];
							$array['nik_anak_pulsa']            = $data['nik_anak_pulsa'];
							$array['agama_anak_pulsa']          = $data['agama_anak_pulsa'];
							$array['jenis_kelamin_anak_pulsa']  = $data['jenis_kelamin_anak_pulsa'];
							$array['pekerjaan_anak_pulsa']      = $data['pekerjaan_anak_pulsa'];
							$array['alamat_anak_pulsa']         = $data['alamat_anak_pulsa'];
							$array['keperluan_surat_pulsa']     = $data['keperluan_surat_pulsa'];
							$array['uraian_pulsa'] 				= htmlspecialchars($data['uraian_pulsa']);

							$array['keperluan_surat_pdam']     = $data['keperluan_surat_pdam'];
							$array['uraian_pdam'] 				= htmlspecialchars($data['uraian_pdam']);

							$array['keperluan_surat_pbb']     = $data['keperluan_surat_pbb'];
							$array['uraian_pbb'] 				= htmlspecialchars($data['uraian_pbb']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "34":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];

							$array['tindak_lanjut_surat'] = $data['tindak_lanjut_surat'];
							$array['tgl_surat_edaran'] = $data['tgl_surat_edaran'];
							$array['no_surat_edaran'] = $data['no_surat_edaran'];

							$array['nama_peneliti'] = $data['nama_peneliti'];
							$array['pekerjan_peneliti'] = $data['pekerjan_peneliti'];
							$array['alamat_kantor'] = $data['alamat_kantor'];
							$array['alamat_rumah_peneliti'] = $data['alamat_rumah_peneliti'];
							$array['judul_penelitian'] = $data['judul_penelitian'];

							$array['pil_surat_penelitian'] = $data['pil_surat_penelitian'];

							$array['nama_peneliti_mhs'] = $data['nama_peneliti_mhs'];
							$array['nim_peneliti_mhs'] = $data['nim_peneliti_mhs'];
							$array['pekerjaan_peneliti_mhs'] = $data['pekerjaan_peneliti_mhs'];
							$array['alamat_peneliti_mhs'] = $data['alamat_peneliti_mhs'];
							$array['judul_penelitian_mhs'] = $data['judul_penelitian_mhs'];

							$array['tgl_awal_penelitian'] = $data['tgl_awal_penelitian'];
							$array['tgl_akhir_penelitian'] = $data['tgl_akhir_penelitian'];
							$array['tujuan_penelitian_mhs'] = $data['tujuan_penelitian_mhs'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "33":
							$nm_alm = "SELECT nama_lengkap as nama FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$nm_alm = $this->db->query($nm_alm)->row_array();

							$jk_alm = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$jk_alm = $this->db->query($jk_alm)->row_array();

							if ($jk_alm['jk'] == 'Laki-Laki') {
								$sebutan = 'Almarhum';
							} else {
								$sebutan = 'Almarhumah';
							}

							$sql = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sql = $this->db->query($sql)->row_array();

							if ($sql['jk'] == 'Laki-Laki') {
								$sebutan2 = 'Istrinya (Almarhumah)';
							} else {
								$sebutan2 = 'Suaminya (Almarhum)';
							}

							$st_kawin = "SELECT status_kawin FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$st_kawin = $this->db->query($st_kawin)->row_array();

							$array['st_kawin'] = $st_kawin['status_kawin'];
							$array['sebutan'] = $sebutan;
							$array['sebutan2'] = $sebutan2;
							$array['nama_alm'] = $nm_alm['nama'];
							$array['alamat_alm'] = $data['alamat_alm'];
							$array['rt_alm'] = $data['rt_alm'];
							$array['rw_alm'] = $data['rw_alm'];
							$array['tgl_meninggal'] = $data['tgl_meninggal'];
							$array['tempat_meninggal'] = $data['tempat_meninggal'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];

							$array['no_surat_kematian'] = $data['no_surat_kematian'];
							$array['tgl_surat_kematian'] = $data['tgl_surat_kematian'];
							$array['kk_waris'] = $data['kk_waris'];
							$array['tgl_waris'] = $data['tgl_waris'];

							$array['no_reg_lurah'] = $data['no_reg_lurah'];
							$array['tgl_reg_lurah'] = $data['tgl_reg_lurah'];
							$array['no_reg_camat'] = $data['no_reg_camat'];
							$array['tgl_reg_camat'] = $data['tgl_reg_camat'];

							$array['pil_status_pewaris'] = $data['pil_status_pewaris'];

							$array['jml_anak'] = $data['jml_anak'];
							$array['tgl_meninggal_istri'] = $data['tgl_meninggal_istri'];

							$array['kk_waris'] = $data['kk_waris'];
							$array['tgl_waris'] = $data['tgl_waris'];
							$array['akta_waris'] = $data['akta_waris'];
							$array['tempat_meninggal_ayah'] = $data['tempat_meninggal_ayah'];
							$array['tgl_meninggal_ayah'] = $data['tgl_meninggal_ayah'];
							$array['sk_ayah_waris'] = $data['sk_ayah_waris'];
							$array['tgl_sk_ayah'] = $data['tgl_sk_ayah'];
							$array['tempat_meninggal_ibu'] = $data['tempat_meninggal_ibu'];
							$array['tgl_meninggal_ibu'] = $data['tgl_meninggal_ibu'];
							$array['sk_ibu_waris'] = $data['sk_ibu_waris'];
							$array['tgl_sk_ibu'] = $data['tgl_sk_ibu'];

							$array['nama_camat'] = $data['nama_camat'];
							$array['jabatan_camat'] = $data['jabatan_camat'];
							$array['nip_camat'] = $data['nip_camat'];
							$array['pangkat_camat'] = $data['pangkat_camat'];

							$array['data_ahli_waris'] = [];
							for ($i = 0; $i < count($data['nama_ahli_waris']); $i++) {
								$array['data_ahli_waris'][] = array(
									'nama_ahli_waris' => $data['nama_ahli_waris'][$i],
									'nik_ahli_waris'  => $data['nik_ahli_waris'][$i],
									'hubungan_waris' => $data['hubungan_waris'][$i],
									'status_waris' => $data['status_waris'][$i]
								);
							}
							unset($data['nik_ahli_waris']);

							$array['data_saksi'] = [];
							for ($i = 0; $i < count($data['nama_saksi']); $i++) {
								$array['data_saksi'][] = array(
									'nama' => $data['nama_saksi'][$i],
									'pekerjaan' => $data['pekerjaan_saksi'][$i],
								);
							}

							$nik = "SELECT status_data FROM tbl_data_penduduk WHERE nik = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sts_alm = $this->db->query($nik)->row_array();


							$array['nama_ahli_waris'] = $data['nama_ahli_waris'];
							$array['hubungan_waris'] = $data['hubungan_waris'];
							$array['status_waris'] = $data['status_waris'];
							$array['nama_saksi'] = $data['nama_saksi'];
							$array['status_alm'] = $sts_alm['status_data'];
							$array['pekerjaan_saksi'] = $data['pekerjaan_saksi'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "32":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['perihal'] = $data['perihal'];
							$array['lampiran'] = $data['lampiran'];
							$array['tindak_lanjut_surat'] = $data['tindak_lanjut_surat'];
							$array['judul_undangan'] = $data['judul_undangan'];
							$array['hari_undangan'] = $data['hari_undangan'];
							$array['tgl_undangan'] = $data['tgl_undangan'];
							$array['tgl_undangan_akhir'] = $data['tgl_undangan_akhir'];
							$array['jam_undangan'] = $data['jam_undangan'];
							$array['jam_undangan2'] = $data['jam_undangan2'];
							$array['tempat_undangan'] = $data['tempat_undangan'];
							$array['catatan_undangan'] = $data['catatan_undangan'];
							$array['tembusan'] = $data['tembusan'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "31":

							$array['nama_anak'] = $data['nama_anak'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['no_kk_ortu'] = $data['no_kk_ortu'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "30":

							$array['no_pengantar']       = $data['no_pengantar'];

							$array['tgl_pengantar']      = $data['tgl_pengantar'];

							$array['keperluan_surat']    = $data['keperluan_surat'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];


							$array['data_dokumen'] = [];
							for ($i = 0; $i < count($data['nama_dalam_dokumen']); $i++) {
								$array['data_dokumen'][] = array(
									'nama_dalam_dokumen' => $data['nama_dalam_dokumen'][$i],
									'dokumen_pendukung'  => $data['dokumen_pendukung'][$i],
									'no_dokumen'         => $data['no_dokumen'][$i],
									'tempat_lahir'         => $data['tempat_lahir'][$i],
									'tgl_lahir_dokumen'  => $data['tgl_lahir_dokumen'][$i],
									'alamat_dokumen'     => $data['alamat_dokumen'][$i],
									'dikeluarkan_oleh'   => $data['dikeluarkan_oleh'][$i],
									'tgl_dikeluarkan'    => $data['tgl_dikeluarkan'][$i],
								);
							}

							unset($data['nama_dalam_dokumen']);
							unset($data['dokumen_pendukung']);
							unset($data['no_dokumen']);
							unset($data['tempat_lahir']);
							unset($data['tgl_lahir_dokumen']);
							unset($data['alamat_dokumen']);
							unset($data['dikeluarkan_oleh']);
							unset($data['tgl_dikeluarkan']);

							// $array['pilih_judul'] = $data['pilih_judul'];

							// $array['pil_jenis_surat'] = $data['pil_jenis_surat'];

							// $array['nama_wali'] = $data['nama_wali'];

							// $array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							// $array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							// $array['agama_wali'] = $data['agama_wali'];

							// $array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							// $array['status_wali'] = $data['status_wali'];

							// $array['alamat_wali'] = $data['alamat_wali'];

							// $array['no_reg_lurah'] = $data['no_reg_lurah'];

							// $array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$data['info_tambahan']       = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "29":

							$array['alamat_domisili_rumah'] = $data['alamat_domisili_rumah'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['nama_pernyataan_rumah'] = $data['nama_pernyataan_rumah'];

							$array['tgl_pernyataan_rumah'] = $data['tgl_pernyataan_rumah'];

							$array['tempat_tinggal_sekarang'] = $data['tempat_tinggal_sekarang'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "28":

							$sql = "SELECT jenis_kelamin as jk FROM tbl_data_penduduk WHERE id = '" . $data['tbl_data_penduduk_id'] . "' ";
							$sql = $this->db->query($sql)->row_array();


							// print_r($data['istri']);

							// print_r($data['anak']); 

							// print_r($sql['jk']);exit;


							if ($data['istri'] != '' && $data['anak'] != '' && $sql['jk'] == 'Laki-Laki') {
								$sebutan = 'orang istri';
							} elseif ($data['istri'] == '' && $data['anak'] != '' && $sql['jk'] == 'Laki-Laki') {
								$sebutan = 'single dad';
							} elseif ($data['istri'] != '' && $data['anak'] != '' && $sql['jk'] == 'Perempuan') {
								$sebutan = '1orang suami';
							} elseif ($data['istri'] == '' && $data['anak'] != '' && $sql['jk'] == 'Perempuan') {
								$sebutan = 'single mom';
							} else {
								$sebutan = 'belum menikah';
							}

							$this->load->helper('terbilang');

							$array['sebutan'] = $sebutan;

							$array['nama_pekerjaan2'] = $data['nama_pekerjaan2'];

							$array['penghasilan'] = $data['penghasilan'];

							$array['istri'] = $data['istri'];

							$array['istri_terbilang'] = number_to_words((int)$data['istri']);

							$array['anak'] = $data['anak'];

							$array['anak_terbilang'] = number_to_words((int)$data['anak']);
							$array['ttd'] = /*select data ttd d dengan where nip dari form->row() $data['nip']*/

								$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "27":

							$array['nama_suami']           = $data['nama_suami'];

							$array['tgl_meninggal']        = $data['tgl_meninggal'];

							//$array['hari_meninggal']       = $data['hari_meninggal'];

							$array['nama_pemakaman']       = $data['nama_pemakaman'];

							$array['no_surat_keterangan']  = $data['no_surat_keterangan'];

							$array['tgl_surat_keterangan'] = $data['tgl_surat_keterangan'];

							$array['tempat_meninggal']     = $data['tempat_meninggal'];

							$array['surat_pengantar']      = $data['surat_pengantar'];

							$array['tgl_pengantar']        = $data['tgl_pengantar'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['keperluan_surat']      = $data['keperluan_surat'];

							$data['info_tambahan']         = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['surat_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "26":

							$array['tgl_kebakaran']    = $data['tgl_kebakaran'];

							$array['rincian_kerugian'] = $data['rincian_kerugian'];

							$array['no_pengantar_kebakaran']     = $data['no_pengantar_kebakaran'];

							$array['tgl_pengantar_kebakaran']    = $data['tgl_pengantar_kebakaran'];

							$array['peruntukan_surat'] = $data['peruntukan_surat'];

							$array['data_barang_terbakar'] = [];
							if (@count($data['barang_terbakar']) > 0) {
								$array['data_barang_terbakar'] = $data['barang_terbakar'];
								unset($data['barang_terbakar']);
							}
							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar_kebakaran'] = date('Y-m-d', strtotime($data['tgl_pengantar_kebakaran']));
							$datax = array(
								'no_surat' => $data['no_pengantar_kebakaran'],
								'tgl_surat' => $data['tgl_pengantar_kebakaran'],
								'perihal' => $data['peruntukan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "25":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['peruntukan_surat'] = $data['peruntukan_surat'];

							$array['dokumen_pendukung'] = $data['dokumen_pendukung'];

							$array['no_dokumen'] = $data['no_dokumen'];

							$array['nama_dalam_dokumen'] = $data['nama_dalam_dokumen'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "24":

							$array['nama_istri'] = $data['nama_istri'];

							$array['tgl_surat_pernyataan'] = $data['tgl_surat_pernyataan'];

							$array['no_surat_pengantar'] = $data['no_surat_pengantar'];

							$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];

							$array['tgl_pergi'] = $data['tgl_pergi'];

							$array['peruntukan_surat'] = $data['peruntukan_surat'];

							$array['no_akta_nikah'] = $data['no_akta_nikah'];

							$array['tgl_akta_nikah'] = $data['tgl_akta_nikah'];


							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_surat_pengantar'] = date('Y-m-d', strtotime($data['tgl_surat_pengantar']));
							$datax = array(
								'no_surat' => $data['no_surat_pengantar'],
								'tgl_surat' => $data['tgl_surat_pengantar'],
								'perihal' => $data['peruntukan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "23":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "22":

							$array['nama_kegiatan'] = $data['nama_kegiatan'];

							$array['hari'] = $data['hari'];

							$array['tgl_acara'] = $data['tgl_acara'];

							$array['tgl_acara2'] = $data['tgl_acara2'];

							$array['tempat'] = $data['tempat'];

							$array['bunyi_bunyian'] = $data['bunyi_bunyian'];

							$array['jalan_lorong'] = $data['jalan_lorong'];

							$array['tutup_sementara'] = $data['tutup_sementara'];

							$array['no_surat_pengantar'] = $data['no_surat_pengantar'];

							$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];

							$array['satker_polisi'] = $data['satker_polisi'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_surat_pengantar'] = date('Y-m-d', strtotime($data['tgl_surat_pengantar']));

							$datax = array(
								'no_surat' => $data['no_surat_pengantar'],
								'tgl_surat' => $data['tgl_surat_pengantar'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "21":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$array['judul_tidak_bekerja'] = isset($data['judul_tidak_bekerja']);

							// $array['pil_jenis_surat'] = $data['pil_jenis_surat'];

							// $array['nama_wali'] = $data['nama_wali'];

							// $array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							// $array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							// $array['agama_wali'] = $data['agama_wali'];

							// $array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							// $array['status_wali'] = $data['status_wali'];

							// $array['alamat_wali'] = $data['alamat_wali'];

							// $array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							// $array['no_reg_lurah'] = $data['no_reg_lurah'];

							// $array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "20":

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "19":

							$array['nama_toko'] = $data['nama_toko'];

							$array['nama_usaha'] = $data['nama_usaha'];

							$array['masa_berlaku_toko'] = $data['masa_berlaku_toko'];

							$array['alamat_usaha'] = $data['alamat_usaha'];

							$array['telp_usaha'] = $data['telp_usaha'];

							$array['modal_awal'] = $data['modal_awal'];

							$array['lama_usaha'] = $data['lama_usaha'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['status_ttd_pemilik_usaha'] = isset($data['status_ttd_pemilik_usaha']);

							$array['status_ttd_pemilik_usaha2'] = isset($data['status_ttd_pemilik_usaha2']);

							// $array['pil_jenis_surat'] = $data['pil_jenis_surat'];

							// $array['nama_wali'] = $data['nama_wali'];

							// $array['tempat_lahir_wali'] = $data['tempat_lahir_wali'];

							// $array['tgl_lahir_wali'] = $data['tgl_lahir_wali'];

							// $array['agama_wali'] = $data['agama_wali'];

							// $array['pekerjaan_wali'] = $data['pekerjaan_wali'];

							// $array['status_wali'] = $data['status_wali'];

							// $array['alamat_wali'] = $data['alamat_wali'];

							// $array['keperluan_surat_pernyataan'] = $data['keperluan_surat_pernyataan'];

							// $array['no_reg_lurah'] = $data['no_reg_lurah'];

							// $array['ceklis_ttd_pejabat'] = isset($data['ceklis_ttd_pejabat']);

							if (isset($_FILES['foto_usaha']['name'])) {
								$countfiles = count($_FILES['foto_usaha']['name']);
								if (!is_dir('./__data/' . date('Ym'))) {
									mkdir('./__data/' . date('Ym'), 0755);
								}

								$config['upload_path'] = './__data/' . date('Ym') . '/';
								$config['allowed_types'] = 'pdf|jpg|jpeg|png';
								$config['max_size'] = 2048;
								$config['encrypt_name'] = true;

								$this->load->library('upload', $config);
								for ($i = 0; $i < $countfiles; $i++) {
									if (!empty($_FILES['foto_usaha']['name'][$i])) {
										$_FILES['file']['name']     = $_FILES['foto_usaha']['name'][$i];
										$_FILES['file']['type']     = $_FILES['foto_usaha']['type'][$i];
										$_FILES['file']['tmp_name'] = $_FILES['foto_usaha']['tmp_name'][$i];
										$_FILES['file']['error']    = $_FILES['foto_usaha']['error'][$i];
										$_FILES['file']['size']     = $_FILES['foto_usaha']['size'][$i];
										$this->upload->initialize($config);
										if (!$this->upload->do_upload('file')) {
											return $this->upload->display_errors();
											break;
										} else {
											$upload_data = $this->upload->data();
											$upload_data['file_name'] = '__data/' . date('Ym') . '/' . $upload_data['file_name'];
											$array['foto_usaha'][] = [
												'file_name' => $upload_data['file_name'],
												'file_type' => $upload_data['file_type'],
											];
										}
									}
								}
							} else {
								$array['foto_usaha'] = [];
							}

							unset($data['foto_usaha']);
							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);


							break;

						case "18":

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "17":

							$array['nama_pasangan'] = $data['nama_pasangan'];

							$array['nik_pasangan'] = $data['nik_pasangan'];

							$array['alamat_pasangan'] = $data['alamat_pasangan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "16":

							$array['nama_pasangan'] = $data['nama_pasangan'];

							$array['nik_pasangan'] = $data['nik_pasangan'];

							$array['alamat_pasangan'] = $data['alamat_pasangan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "15":

							$array['nama_usaha']      = $data['nama_usaha'];

							$array['alamat_usaha']    = $data['alamat_usaha'];

							$array['jenis_usaha']    = $data['jenis_usaha'];

							$array['telp_usaha']    = $data['telp_usaha'];

							$array['status_usaha']    = $data['status_usaha'];

							$array['pj']              = $data['pj'];

							$array['no_akta_usaha']   = $data['no_akta_usaha'];

							$array['no_pengantar']    = $data['no_pengantar'];

							$array['tgl_pengantar']   = $data['tgl_pengantar'];

							$array['masa_berlaku']    = $data['masa_berlaku'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['status_ttd_pemilik_usaha'] = isset($data['status_ttd_pemilik_usaha']);

							$data['info_tambahan']    = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "14":

							$array['kpd_yth'] = $data['kpd_yth'];
							$array['di_undangan'] = $data['di_undangan'];
							$array['tembusan'] = $data['tembusan'];

							$array['data_dokumen'] = [];
							// var_dump($data['data_dokumen']);
							// 	exit();
							for ($i = 0; $i < count($data['uraian_kec']); $i++) {

								$array['data_dokumen'][] = array(
									'uraian_kec' => $data['uraian_kec'][$i],
									'jml_kec'  => $data['jml_kec'][$i],
									'ket_kec'  => $data['ket_kec'][$i],

								);
							}

							unset($data['uraian_kec']);
							unset($data['jml_kec']);
							unset($data['ket_kec']);


							$data['info_tambahan'] = json_encode($array);

							break;

						case "13":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];


							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "12":

							$sts  = 'PINDAH DOMISILI';
							$date = date('Y-m-d H:i:s');
							$up   = $this->auth['nama_lengkap'] . " - Via Data Surat Keterangan Pindah Penduduk";
							for ($i = 0; $i < count($data['nama_pindah_penduduk']); $i++) {
								$penduduk = array(

									'status_data' => $sts,

									'update_date' => $date,

									'update_by'   => $up,

								);
								$this->db->where('id', $data['nama_pindah_penduduk'][$i])->update('tbl_data_penduduk', $penduduk);
							}

							$array['tgl_pindah']           = $data['tgl_pindah'];

							$array['alasan_pindah']        = $data['alasan_pindah'];

							$array['alamat_pindah']        = $data['alamat_pindah'];

							$array['keperluan_surat']      = $data['keperluan_surat'];

							$array['data_pindah_penduduk'] = [];

							for ($i = 0; $i < count($data['nama_pindah_penduduk']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_pindah_penduduk'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$res->keterangan_nama_pindah_penduduk = $data['keterangan'][$i];
								$array['data_pindah_penduduk'][] = $res;
							}

							unset($data['nama_pindah_penduduk']);
							unset($data['keterangan']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "11":

							$array['nama_pasangan'] = $data['nama_pasangan'];

							$array['nama_ayah_pasangan'] = $data['nama_ayah_pasangan'];

							$array['alamat_pasangan'] = $data['alamat_pasangan'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "10":

							$array['warga_nikah'] = $data['warga_nikah'];

							$array['status_perkawinan'] = $data['status_perkawinan'];

							$array['nama_ayah'] = $data['nama_ayah'];

							$array['tempat_lahir_ayah'] = $data['tempat_lahir_ayah'];

							$array['tgl_lahir_ayah'] = $data['tgl_lahir_ayah'];

							$array['pekerjaan_ayah'] = $data['pekerjaan_ayah'];

							$array['agama_ayah'] = $data['agama_ayah'];

							$array['alamat_ayah'] = $data['alamat_ayah'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['tempat_lahir_ibu'] = $data['tempat_lahir_ibu'];

							$array['tgl_lahir_ibu'] = $data['tgl_lahir_ibu'];

							$array['pekerjaan_ibu'] = $data['pekerjaan_ibu'];

							$array['agama_ibu'] = $data['agama_ibu'];

							$array['alamat_ibu'] = $data['alamat_ibu'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['nama_imam_kel'] = $data['nama_imam_kel'];

							$array['ceklis_ttd_imam'] = isset($data['ceklis_ttd_imam']);

							$data['info_tambahan'] = json_encode($array);

							break;

						case "9":


							$array['imam_kelurahan_opsi'] = $data['imam_kelurahan_opsi'];

							$array['tgl_kematian'] = $data['tgl_kematian'];

							$array['jam_kematian'] = $data['jam_kematian'];

							$array['tempat_kematian'] = $data['tempat_kematian'];

							$array['dikebumikan_di'] = $data['dikebumikan_di'];

							$array['nama_pelapor'] = $data['nama_pelapor'];

							$array['nik_pelapor'] = $data['nik_pelapor'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['hubungan_pelapor'] = $data['hubungan_pelapor'];

							$array['pernyataan_dari'] = $data['pernyataan_dari'];

							$array['tgl_pernyataan'] = $data['tgl_pernyataan'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);


							break;

						case "8":

							$array['usia_kandungan_kematian'] = $data['usia_kandungan_kematian'];

							$array['tgl_kematian'] = $data['tgl_kematian'];

							$array['jam_kematian'] = $data['jam_kematian'];

							$array['tempat_kematian'] = $data['tempat_kematian'];

							$array['nama_pelapor'] = $data['nama_pelapor'];

							$array['hubungan_pelapor'] = $data['hubungan_pelapor'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							break;

						case "7":

							$array['nama_dalam_surat_akta'] = $data['nama_dalam_surat_akta'];

							$array['nik_bayi'] = $data['nik_bayi'];

							$array['tempat_lahir_bayi'] = $data['tempat_lahir_bayi'];

							$array['tgl_lahir_bayi'] = $data['tgl_lahir_bayi'];

							$array['alamat_bayi'] = $data['alamat_bayi'];

							$array['nama_ayah'] = $data['nama_ayah'];

							$array['ktp_ayah'] = $data['ktp_ayah'];

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['ktp_ibu'] = $data['ktp_ibu'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "6":

							$array['nama_ibu'] = $data['nama_ibu'];

							$array['nama_bayi'] = $data['nama_bayi'];

							$array['jenis_kelamin_bayi'] = $data['jenis_kelamin_bayi'];

							$array['jam_lahir'] = $data['jam_lahir'];

							$array['tgl_lahir'] = $data['tgl_lahir'];

							$array['tempat_lahir'] = $data['tempat_lahir'];

							$array['anak_ke'] = $data['anak_ke'];

							$array['nama_ayah_bayi'] = $data['nama_ayah_bayi'];

							$array['alamat_kelahiran'] = $data['alamat_kelahiran'];

							$array['no_pengantar_kel'] = $data['no_pengantar_kel'];

							$array['tgl_pengantar_kel'] = $data['tgl_pengantar_kel'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$datax = array(

								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "5":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "4":

							$array['masa_berlaku_surat']    = $data['masa_berlaku_surat'];

							$array['pernyataan_tdk_mampu'] = $data['pernyataan_tdk_mampu'];

							$array['tgl_pernyataan_tdk_mampu'] = $data['tgl_pernyataan_tdk_mampu'];

							$array['no_pengantar']    = $data['no_pengantar'];

							$array['tgl_pengantar']   = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['alamat_domisili_sktm'] = $data['alamat_domisili_sktm'];

							$array['ceklis_psktm'] = isset($data['ceklis_psktm']);

							$array['data_tidak_mampu'] = [];

							for ($i = 0; $i < count($data['nama_tidak_mampu']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_tidak_mampu'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$res->keterangan_nama_tidak_mampu = $data['keterangan'][$i];
								$array['data_tidak_mampu'][] = $res;
							}
							unset($data['nama_tidak_mampu']);
							unset($data['keterangan']);

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "3":

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						case "2":

							$array['data_berdomisili'] = [];

							for ($i = 0; $i < count($data['nama_berdomisili']); $i++) {
								$res = $this->db->select("a.*,b.nama_pekerjaan,c.nama_status_kawin,d.nama_agama")->where([
									'a.id' => $data['nama_berdomisili'][$i],
									'a.cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id']
								])
									->join('cl_jenis_pekerjaan b', 'a.cl_jenis_pekerjaan_id=b.id', 'LEFT')
									->join('cl_status_kawin c', 'a.status_kawin=c.id', 'LEFT')
									->join('cl_agama d', 'a.agama=d.id', 'LEFT')
									->get('tbl_data_penduduk a')->row();
								$array['data_berdomisili'][] = $res;
							}
							unset($data['nama_berdomisili']);

							$array['masa_berlaku'] = $data['masa_berlaku'];

							$array['jenis_domisili'] = $data['jenis_domisili'];

							$array['no_pengantar'] = $data['no_pengantar'];

							$array['tgl_pengantar'] = $data['tgl_pengantar'];

							$array['keperluan_surat'] = $data['keperluan_surat'];

							$array['no_capil'] = $data['no_capil'];

							if ($data['tbl_data_penduduk_id_penjamin'] <> '') {
								$res = $this->db->where('id', $data['tbl_data_penduduk_id_penjamin'])->get('tbl_data_penduduk')->row();
								$array['id_penjamin'] = $data['tbl_data_penduduk_id_penjamin'];
								$array['nama_penjamin']   = $res->nama_lengkap;
								$array['nik_penjamin']    = $res->nik;
								$array['alamat_penjamin'] = $res->alamat;
							}

							$array['alamat_domisili'] = $data['alamat_domisili'];

							unset($data['tbl_data_penduduk_id_penjamin']);


							$data['info_tambahan'] = json_encode($array);

							$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat' => $data['no_pengantar'],
								'tgl_surat' => $data['tgl_pengantar'],
								'perihal' => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat' => $this->auth['nama_lengkap'],
								'tujuan' => 'Warga',
								'tgl_diterima' => $data['tgl_surat'],
								'no_agenda' => '',
								'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;

						// case "1":

						// 	$array['alamat_domisili_menikah'] = $data['alamat_domisili_menikah'];
						// 	$array['imam_kelurahan_opsi'] = $data['imam_kelurahan_opsi'];
						// 	$array['pernyataan_dari'] = $data['pernyataan_dari'];
						// 	$array['tgl_pernyataan'] = $data['tgl_pernyataan'];
						// 	$array['no_pengantar'] = $data['no_pengantar'];
						// 	$array['tgl_pengantar'] = $data['tgl_pengantar'];
						// 	$array['keperluan_surat'] = $data['keperluan_surat'];

						// 	$data['info_tambahan'] = json_encode($array);

						// 	$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
						// 	$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
						// 	$datax = array(
						// 		'no_surat' => $data['no_pengantar'],
						// 		'tgl_surat' => $data['tgl_pengantar'],
						// 		'perihal' => $data['keperluan_surat'],
						// 		'cl_jenis_surat_masuk_id' => '3',
						// 		'cl_sifat_surat_masuk_id' => '1',
						// 		'asal_surat' => $this->auth['nama_lengkap'],
						// 		'tujuan' => 'Warga',
						// 		'tgl_diterima' => $data['tgl_surat'],
						// 		'no_agenda' => '',
						// 		'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
						// 		'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
						// 		'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
						// 		'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
						// 	);
						// 	$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

						// break;
						case "1":

							$array['alamat_domisili_menikah'] = $data['alamat_domisili_menikah'];
							$array['imam_kelurahan_opsi']     = $data['imam_kelurahan_opsi'];
							$array['pernyataan_dari']         = $data['pernyataan_dari'];
							$array['tgl_pernyataan']          = $data['tgl_pernyataan'];
							$array['no_pengantar']            = $data['no_pengantar'];
							$array['tgl_pengantar']           = $data['tgl_pengantar'];
							$array['keperluan_surat']         = $data['keperluan_surat'];

							// simpan info tambahan seperti biasa
							$data['info_tambahan'] = json_encode($array);

							// ------------------ HANDLE UPDATE NOP PADA TBL_DATA_PENDUDUK ------------------
							// terima NOP dari form (dukung 'nop' atau 'nop_inline' jika ada)
							$nop_input = $this->input->post('nop');
							if (empty($nop_input)) {
								$nop_input = $this->input->post('nop_inline');
							}

							// cari NIK: prefer menggunakan tbl_data_penduduk_id jika ada
							$nik = '';
							if (!empty($data['tbl_data_penduduk_id'])) {
								// ambil record penduduk untuk mendapatkan nik
								$rowPend = $this->db->select('nik')->get_where('tbl_data_penduduk', ['id' => $data['tbl_data_penduduk_id']])->row_array();
								if ($rowPend) {
									$nik = $rowPend['nik'];
								}
							}

							// fallback: jika ada input nama_dalam_surat (format "NIK - NAMA"), ambil kata pertama
							if (empty($nik)) {
								$nama_dalam_surat = $this->input->post('nama_dalam_surat');
								if (!empty($nama_dalam_surat)) {
									$nik = explode(" ", trim($nama_dalam_surat))[0];
								}
							}

							// jika ada nop dan nik -> lakukan update
							if (!empty($nop_input) && !empty($nik)) {
								$cek_pend = $this->db->where('nik', $nik)->get('tbl_data_penduduk');
								if ($cek_pend->num_rows() > 0) {
									$this->db->where('nik', $nik);
									$this->db->update('tbl_data_penduduk', [
										'nop' => $nop_input,
										'update_date' => date('Y-m-d H:i:s'),
										'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat (Lengkapi NOP)"
									]);
								}
							}

							// ------------------ PENTING: pastikan field NOP TIDAK IKUT KE INSERT tbl_data_surat ------------------
							// beberapa view mungkin masih mengirim name="nop" atau name="nop_surat" atau name="nop_inline"
							if (isset($data['nop'])) {
								unset($data['nop']);
							}
							if (isset($data['nop_surat'])) {
								unset($data['nop_surat']);
							}
							if (isset($data['nop_inline'])) {
								unset($data['nop_inline']);
							}
							// ----------------------------------------------------------------------------------------------------

							// format tanggal dan insert surat masuk (tetap seperti sebelumnya)
							$data['tgl_surat']     = date('Y-m-d', strtotime($data['tgl_surat']));
							$data['tgl_pengantar'] = date('Y-m-d', strtotime($data['tgl_pengantar']));
							$datax = array(
								'no_surat'                => $data['no_pengantar'],
								'tgl_surat'               => $data['tgl_pengantar'],
								'perihal'                 => $data['keperluan_surat'],
								'cl_jenis_surat_masuk_id' => '3',
								'cl_sifat_surat_masuk_id' => '1',
								'asal_surat'              => $this->auth['nama_lengkap'],
								'tujuan'                  => 'Warga',
								'tgl_diterima'            => $data['tgl_surat'],
								'no_agenda'               => '',
								'cl_provinsi_id'          => $this->auth['cl_provinsi_id'],
								'cl_kab_kota_id'          => $this->auth['cl_kab_kota_id'],
								'cl_kecamatan_id'         => $this->auth['cl_kecamatan_id'],
								'cl_kelurahan_desa_id'    => $this->auth['cl_kelurahan_desa_id'],
							);
							$insert = $this->db->insert('tbl_data_surat_masuk', $datax);

							break;
					}
				}


				if (!empty($array)) {

					foreach ($array as $k => $v) {

						unset($data[$k]);
					}
				}



				unset($data['tbl_data_penduduk_id_lama']);

				//echo "<pre>";

				//print_r($data);exit;

				break;


			case "data_surat_edit":

				$table = "tbl_data_surat";

				break;

			case "surat_masuk":
				// print_r('xxxxxxx');exit;
				if (isset($data['tgl_surat'])) {
					$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
				}

				if (isset($data['tgl_diterima'])) {
					$data['tgl_diterima'] = date('Y-m-d', strtotime($data['tgl_diterima']));
				}

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|png|jpeg';
				$config['max_size']             = 5120;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;
				$table = "tbl_data_surat_masuk";

				$array = array();



				// $file = array();
				// 	$dir = date('Ymd');
				// 	if (!is_dir('./__data/' . $dir)) {
				// 		mkdir('./__data/' . $dir, 0755);
				// 	}

				// 	$config['upload_path']          = './__data/' . $dir;
				// 	$config['allowed_types']        = 'jpg|jpeg|png';
				// 	$config['max_size']             = 204800;
				// 	$config['encrypt_name']			= true;

				// 	$this->load->library('upload', $config);

				// 	$data_file = [];
				// 	if (!empty($_FILES['files']['name'][0])) {
				// 		$files = $_FILES['files'];
				// 		foreach ($files['name'] as $key => $image) {
				// 			$_FILES['file']['name'] = $files['name'][$key];
				// 			$_FILES['file']['type'] = $files['type'][$key];
				// 			$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
				// 			$_FILES['file']['error'] = $files['error'][$key];
				// 			$_FILES['file']['size'] = $files['size'][$key];
				// 			$this->upload->initialize($config);

				// 			if ($this->upload->do_upload('file')) {
				// 				$data_upload = $this->upload->data();
				// 				$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];

				// 				$data_file[] = array('files' => $data_upload['file_name']);
				// 			} else {
				// 				return $this->upload->display_errors();
				// 			}
				// 		}
				// 	}

				// 	$data['file'] = json_encode($data_file);

				// $table = "tbl_data_surat_masuk";

				// $array = array();


				if ($sts_crud == "add") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}



				if ($sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}



				if ($sts_crud == "delete") {

					$datax = $this->db->get_where('tbl_data_surat_masuk', array('id' => $id))->row_array();

					if ($datax) {

						$aktifkan = array(

							'status_data' => 'AKTIF',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat",

						);

						$this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $datax['tbl_data_penduduk_id']));
					}
				}



				if ($sts_crud == "add" || $sts_crud == "edit") {

					// switch ($data['cl_jenis_surat_id']) {

					// 	case "31":

					// 		$array['nama_anak'] = $data['nama_anak'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "30":

					// 		$array['keperluan'] = $data['keperluan'];

					// 		$array['nama_dalam_sertifikat'] = $data['nama_dalam_sertifikat'];

					// 		$array['no_sertifikat'] = $data['no_sertifikat'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "29":

					// 		$array['nama_ibu'] = $data['nama_ibu'];

					// 		$array['tgl_pernyataan'] = $data['tgl_pernyataan'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "28":

					// 		$this->load->helper('terbilang');

					// 		$array['penghasilan'] = $data['penghasilan'];

					// 		$array['istri'] = $data['istri'];

					// 		$array['istri_terbilang'] = number_to_words((int)$data['istri']);

					// 		$array['anak'] = $data['anak'];

					// 		$array['anak_terbilang'] = number_to_words((int)$data['anak']);



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "27":

					// 		$array['nama_suami'] = $data['nama_suami'];

					// 		$array['tgl_meninggal'] = $data['tgl_meninggal'];

					// 		$array['hari_meninggal'] = $data['hari_meninggal'];

					// 		$array['nama_pemakaman'] = $data['nama_pemakaman'];

					// 		$array['no_surat_keterangan'] = $data['no_surat_keterangan'];

					// 		$array['rumah_sakit'] = $data['rumah_sakit'];

					// 		$array['surat_pengantar'] = $data['surat_pengantar'];

					// 		$array['tgl_pengantar'] = $data['tgl_pengantar'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "26":

					// 		$array['tgl_kebakaran'] = $data['tgl_kebakaran'];

					// 		$array['rincian_kerugian'] = $data['rincian_kerugian'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "25":

					// 		$array['peruntukan_surat'] = $data['peruntukan_surat'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "24":

					// 		$array['nama_istri'] = $data['nama_istri'];

					// 		$array['tgl_surat_pernyataan'] = $data['tgl_surat_pernyataan'];

					// 		$array['no_surat_pengantar'] = $data['no_surat_pengantar'];

					// 		$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];

					// 		$array['tgl_pergi'] = $data['tgl_pergi'];

					// 		$array['peruntukan_surat'] = $data['peruntukan_surat'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "22":

					// 		$array['nama_kegiatan'] = $data['nama_kegiatan'];

					// 		$array['hari'] = $data['hari'];

					// 		$array['tgl_acara'] = $data['tgl_acara'];

					// 		$array['tempat'] = $data['tempat'];



					// 		$array['bunyi_bunyian'] = $data['bunyi_bunyian'];

					// 		$array['jalan_lorong'] = $data['jalan_lorong'];

					// 		$array['tutup_sementara'] = $data['tutup_sementara'];



					// 		$array['no_surat_pengantar'] = $data['no_surat_pengantar'];

					// 		$array['tgl_surat_pengantar'] = $data['tgl_surat_pengantar'];

					// 		$array['satker_polisi'] = $data['satker_polisi'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "19":

					// 		$array['nama_usaha'] = $data['nama_usaha'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "17":

					// 	case "16":

					// 		$array['nama_pasangan'] = $data['nama_pasangan'];

					// 		$array['nik_pasangan'] = $data['nik_pasangan'];

					// 		$array['alamat_pasangan'] = $data['alamat_pasangan'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "15":

					// 		$array['nama_usaha'] = $data['nama_usaha'];

					// 		$array['alamat_usaha'] = $data['alamat_usaha'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "14":

					// 		$array['tgl_mulai_berlaku'] = $data['tgl_mulai_berlaku'];

					// 		$array['tgl_selesai_berlaku'] = $data['tgl_selesai_berlaku'];

					// 		$array['keperluan'] = $data['keperluan'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "12":

					// 		$array['tgl_pindah'] = $data['tgl_pindah'];

					// 		$array['alasan_pindah'] = $data['alasan_pindah'];

					// 		$array['alamat_pindah'] = $data['alamat_pindah'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "11":

					// 		$array['nama_pasangan'] = $data['nama_pasangan'];

					// 		$array['nama_ayah_pasangan'] = $data['nama_ayah_pasangan'];

					// 		$array['alamat_pasangan'] = $data['alamat_pasangan'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "9":

					// 		$array['hari_kematian'] = $data['hari_kematian'];

					// 		$array['tgl_kematian'] = $data['tgl_kematian'];

					// 		$array['jam_kematian'] = $data['jam_kematian'];

					// 		$array['tempat_kematian'] = $data['tempat_kematian'];

					// 		$array['penyebab_kematian'] = $data['penyebab_kematian'];

					// 		$array['nik_pelapor'] = $data['nik_pelapor'];

					// 		$array['hubungan_pelapor'] = $data['hubungan_pelapor'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "8":

					// 		$array['usia_kandungan_kematian'] = $data['usia_kandungan_kematian'];

					// 		$array['hari_kematian'] = $data['hari_kematian'];

					// 		$array['tgl_kematian'] = $data['tgl_kematian'];

					// 		$array['jam_kematian'] = $data['jam_kematian'];

					// 		$array['tempat_kematian'] = $data['tempat_kematian'];

					// 		$array['nama_pelapor'] = $data['nama_pelapor'];

					// 		$array['hubungan_pelapor'] = $data['hubungan_pelapor'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;

					// 	case "6":

					// 		$array['nama_bayi'] = $data['nama_bayi'];

					// 		$array['jenis_kelamin_bayi'] = $data['jenis_kelamin_bayi'];

					// 		$array['jam_lahir'] = $data['jam_lahir'];

					// 		$array['hari_lahir'] = $data['hari_lahir'];

					// 		$array['tgl_lahir'] = $data['tgl_lahir'];

					// 		$array['tempat_lahir'] = $data['tempat_lahir'];



					// 		$data['info_tambahan'] = json_encode($array);

					// 		break;
					// }
				}



				if (!empty($array)) {

					foreach ($array as $k => $v) {

						unset($data[$k]);
					}
				}



				unset($data['tbl_data_penduduk_id_lama']);



				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "surat_lain":

				if (isset($data['tgl_surat'])) {
					$data['tgl_surat'] = date('Y-m-d', strtotime($data['tgl_surat']));
				}

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|png|jpeg';
				$config['max_size']             = 5120;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;
				$table = "tbl_data_surat_lain";

				$array = array();


				if ($sts_crud == "add") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}



				if ($sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}



				if ($sts_crud == "delete") {

					$datax = $this->db->get_where('tbl_data_surat_masuk', array('id' => $id))->row_array();

					if ($datax) {

						$aktifkan = array(

							'status_data' => 'AKTIF',

							'update_date' => date('Y-m-d H:i:s'),

							'update_by' => $this->auth['nama_lengkap'] . " - Via Data Surat",

						);

						$this->db->update('tbl_data_penduduk', $aktifkan, array('id' => $datax['tbl_data_penduduk_id']));
					}
				}

				if ($sts_crud == "add" || $sts_crud == "edit") {
				}

				if (!empty($array)) {

					foreach ($array as $k => $v) {

						unset($data[$k]);
					}
				}

				unset($data['tbl_data_penduduk_id_lama']);



				//echo "<pre>";

				//print_r($data);exit;

				break;

			case "data_keluarga":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|png';
				$config['max_size']             = 5120;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;
				$table = "tbl_kartu_keluarga";

				$array = array();


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$data['create_date'] = date('Y-m-d H:i:s');

					$data['create_by'] = $this->auth['nama_lengkap'];

					$no_kk = $this->input->post('no_kk');

					$nik = $data['nik'];

					$cl_status_hubungan_keluarga_id = $data['cl_status_hubungan_keluarga_id'];


					unset($data['nik']);

					unset($data['cl_status_hubungan_keluarga_id']);
				}



				if ($sts_crud == "delete") {

					$cekdata = $this->db->get_where('tbl_kartu_keluarga', array('id' => $id))->row_array();

					if ($cekdata) {

						$this->db->update('tbl_data_penduduk', array('no_kk' => null, 'cl_status_hubungan_keluarga_id' => null), array('no_kk' => $cekdata['no_kk']));
					}
				}

				break;

			case "data_penduduk":

				if (isset($data['tgl_lahir'])) {
					$data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));
				}

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;
				$table = "tbl_data_penduduk";

				$array = array();


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
					$nik = $this->input->post('nik');
				}
				if ($sts_crud == "add") {
					$data['id'] = $this->db->query("
							SELECT IFNULL(MAX(id),0)+1 as id FROM(
							SELECT id FROM tbl_data_penduduk
							UNION
							SELECT id FROM tbl_data_penduduk_asing
						)a
					")->row('id');
				}

				break;

			// case "data_penduduk_asing":

			// 	if (isset($data['tgl_lahir'])) {
			// 		$data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));
			// 	}

			// 	if (isset($data['tgl_kel_passport'])) {
			// 		$data['tgl_kel_passport'] = date('Y-m-d', strtotime($data['tgl_kel_passport']));
			// 	}

			// 	if (isset($data['tgl_akhir_passport'])) {
			// 		$data['tgl_akhir_passport'] = date('Y-m-d', strtotime($data['tgl_akhir_passport']));
			// 	}

			// 	$file = '';
			// 	$dir                     = date('Ymd');
			// 	if (!is_dir('./__data/' . $dir)) {
			// 		mkdir('./__data/' . $dir, 0755);
			// 	}

			// 	$config['upload_path']          = './__data/' . $dir;
			// 	$config['allowed_types']        = 'pdf|jpg|png';
			// 	$config['max_size']             = 2048;
			// 	$config['encrypt_name']			= true;


			// 	$this->load->library('upload', $config);
			// 	$this->upload->initialize($config);

			// 	if (!$this->upload->do_upload('file')) {
			// 		$error = array('error' => $this->upload->display_errors());
			// 	} else {
			// 		$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
			// 	}

			// 	$data['file'] = $file;

			// 	$table = "tbl_data_penduduk_asing";

			// 	$array = array();


			// 	if ($sts_crud == "add" || $sts_crud == "edit") {

			// 		$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

			// 		$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

			// 		$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

			// 		$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
			// 		$nik = $this->input->post('no_passport');
			// 	}

			// 	if ($sts_crud == "add") {
			// 		$data['id'] = $this->db->query("
			// 				SELECT IFNULL(MAX(id),0)+1 as id FROM(
			// 				SELECT id FROM tbl_data_penduduk
			// 				UNION
			// 				SELECT id FROM tbl_data_penduduk_asing
			// 			)a
			// 		")->row('id');
			// 	}

			// break;
			case "data_penduduk_asing":

				// ================= FORMAT TANGGAL =================
				if (!empty($data['tgl_lahir'])) {
					$data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));
				}

				if (!empty($data['tgl_kel_passport'])) {
					$data['tgl_kel_passport'] = date('Y-m-d', strtotime($data['tgl_kel_passport']));
				}

				if (!empty($data['tgl_akhir_passport'])) {
					$data['tgl_akhir_passport'] = date('Y-m-d', strtotime($data['tgl_akhir_passport']));
				}

				// ================= UPLOAD FILE =================
				$file = '';
				$dir  = date('Ymd');

				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config = [
					'upload_path'   => './__data/' . $dir,
					'allowed_types' => 'pdf|jpg|png',
					'max_size'      => 2048,
					'encrypt_name'  => true
				];

				$this->load->library('upload');
				$this->upload->initialize($config);

				if ($this->upload->do_upload('file')) {
					$file = '__data/' . $dir . '/' . $this->upload->data('file_name');
				}

				$data['file'] = $file;

				// ================= SET WILAYAH =================
				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id']       = $this->auth['cl_provinsi_id'];
					$data['cl_kab_kota_id']       = $this->auth['cl_kab_kota_id'];
					$data['cl_kecamatan_id']      = $this->auth['cl_kecamatan_id'];
					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}

				$table       = "tbl_data_penduduk_asing";
				$no_passport = $this->input->post('no_passport');

				// ================= ADD =================
				if ($sts_crud == "add") {

					// VALIDASI DUPLIKAT (KELURAHAN SAJA)
					$cek = $this->db->where('no_passport', $no_passport)
						->where('cl_kecamatan_id', $this->auth['cl_kecamatan_id'])
						->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id'])
						->get($table)
						->num_rows();

					if ($cek > 0) {
						echo json_encode([
							'status' => false,
							'msg'    => 'No Passport sudah terdaftar di kelurahan ini'
						]);
						exit;
					}

					// INSERT (ID AUTO_INCREMENT)
					$this->db->insert($table, $data);
				}

				// ================= EDIT =================
				if ($sts_crud == "edit") {

					$this->db->where('id', $this->input->post('id'));
					$this->db->where('cl_kelurahan_desa_id', $this->auth['cl_kelurahan_desa_id']);
					$this->db->update($table, $data);
				}

				break;

			case "data_ktp":

				$table = "tbl_data_rekam_ktp";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}

				break;

			case "data_pegawai_kel_kec":

				$table = "tbl_data_pegawai_kel_kec";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					if (isset($data['ceklis_laskar'])) {
						$data['jabatan'] = 'Laskar';
						unset($data['ceklis_laskar']);
					}

					$file = array();
					$dir = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 204800;
					$config['encrypt_name']			= true;

					$this->load->library('upload', $config);
					$data_file = [];
					if ($sts_crud == 'edit') {
						$cek = $this->db->where('id', $_POST['id'])->get('tbl_data_pegawai_kel_kec');
						if ($cek->num_rows() > 0) {
							$data_file = json_decode($cek->row('file'));
							if (!is_array($data_file) || $data_file == null) {
								$data_file = [];
							}
						}
					}
					if (!empty($_FILES['files']['name'][0])) {
						$files = $_FILES['files'];
						foreach ($files['name'] as $key => $image) {
							$_FILES['file']['name'] = $files['name'][$key];
							$_FILES['file']['type'] = $files['type'][$key];
							$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
							$_FILES['file']['error'] = $files['error'][$key];
							$_FILES['file']['size'] = $files['size'][$key];
							$this->upload->initialize($config);

							if ($this->upload->do_upload('file')) {
								$data_upload = $this->upload->data();
								$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];

								$data_file[] = array('files' => $data_upload['file_name']);
							} else {
								return $this->upload->display_errors();
							}
						}
					}

					if ($data['golongan_id'] != '') {
						$data['nama_golongan'] = nama('cl_golongan', 'nm_golongan', ['id' => $data['golongan_id']]);
						$data['pangkat'] = nama('cl_golongan', 'pangkat', ['id' => $data['golongan_id']]);
					} else {
						unset($data['golongan_id']);
					}

					if ($data['no'] == '' || $data['no'] == 0 || !is_numeric($data['no'])) {
						$data['no'] = '-';
					}

					$data['file'] = json_encode($data_file);
				}

				break;

			case "data_dasawisma":

				$table = "tbl_data_dasawisma";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}

				break;

			// case "data_penandatanganan":
			// 	$sql = "SELECT nm_golongan AS id, CONCAT(pangkat,', ',nm_golongan) AS txt FROM cl_golongan";
			// 	$data_pegawai = $this->db->query($sql)->row_array();

			// 	$array['pangkat'] = $data_pegawai['txt'];

			// 	$table = "tbl_data_penandatanganan";

			// 	if ($sts_crud == "add" || $sts_crud == "edit") {

			// 		$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

			// 		$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

			// 		$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

			// 		$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

			// 		$nip = $this->input->post('nip');
			// 	}
			// break;
			case "data_penandatanganan":
				$sql = "SELECT nm_golongan AS id, CONCAT(pangkat, ', ', nm_golongan) AS txt FROM cl_golongan";
				$data_pegawai = $this->db->query($sql)->row_array();

				$array['pangkat'] = $data_pegawai['txt'];
				$table = "tbl_data_penandatanganan";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id']  = $this->auth['cl_provinsi_id'];
					$data['cl_kab_kota_id']  = $this->auth['cl_kab_kota_id'];
					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// 🔹 Ambil kelurahan dari input, jika kosong pakai kelurahan user login
					$cl_kelurahan_desa_id = $this->input->post('cl_kelurahan_desa_id');
					if (empty($cl_kelurahan_desa_id) || $cl_kelurahan_desa_id == 0) {
						$cl_kelurahan_desa_id = $this->auth['cl_kelurahan_desa_id'];
					}
					$data['cl_kelurahan_desa_id'] = $cl_kelurahan_desa_id;

					// $nip     = $this->input->post('nip');
					// $jabatan = $this->input->post('jabatan');

					// // 🔹 Nonaktifkan jabatan lama hanya untuk kelurahan yang sama
					// $this->db->where(array(
					// 	'nip'              => $nip,
					// 	'cl_provinsi_id'       => $this->auth['cl_provinsi_id'],
					// 	'cl_kab_kota_id'       => $this->auth['cl_kab_kota_id'],
					// 	'cl_kecamatan_id'      => $this->auth['cl_kecamatan_id'],
					// 	'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					// 	'status'               => 'Aktif'
					// ));
					// $this->db->update('tbl_data_penandatanganan', [
					// 	'status'      => 'Tidak Aktif',
					// 	'update_by'   => $this->auth['username'],
					// 	'update_date' => date('Y-m-d H:i:s')
					// ]);

					//     // Cek data lama dengan nip yang sama dan status aktif
					// $this->db->where('nip', $nip);
					// $this->db->where('status', 'Aktif');
					// $Zquery = $this->db->get('tbl_data_penandatanganan');

					// if ($Zquery->num_rows() > 0) {
					// 	$row = $Zquery->row();
					// 	// Jika kelurahan lama berbeda, update data lama jadi tidak aktif
					// 	if ($row->cl_kelurahan_desa_id != $this->auth['cl_kelurahan_desa_id']) {
					// 		$this->db->where('nip', $nip);
					// 		$this->db->where('status', 'Aktif');
					// 		$this->db->update('tbl_data_penandatanganan', [
					// 			'status' => 'Tidak Aktif',
					// 			'tingkat_jabatan' => ''
					// 		]);
					// 	}
					// }

					// 🔹 Data baru diset sebagai Aktif
					// $data['status']       = 'Aktif';
					$data['create_by']    = $this->auth['username'];
					$data['create_date']  = date('Y-m-d H:i:s');
					$data['update_by']    = $this->auth['username'];
					$data['update_date']  = date('Y-m-d H:i:s');
				}

				break;

			case "daftar_agenda_kegiatan":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				if (isset($data['tgl_kegiatan'])) {
					$data['tgl_kegiatan'] = date('Y-m-d', strtotime($data['tgl_kegiatan']));
				}

				$table = "tbl_data_daftar_agenda";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}

				break;

			case "laporan_hasil_kegiatan":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				if (isset($data['tgl_hasil_agenda'])) {
					$data['tgl_hasil_agenda'] = date('Y-m-d', strtotime($data['tgl_hasil_agenda']));
				}

				$table = "tbl_data_hasil_agenda";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
				}

				break;

			case "data_kendaraan":
				$data['nilai_perolehan'] = str_replace(",", "", @$data['nilai_perolehan']);

				$table = "tbl_data_kendaraan";


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					if ($this->auth['cl_user_group_id'] == 3) {
						$data['cl_kelurahan_desa_id'] = $data['cl_kelurahan_desa_id'];
						$data['asal_kelurahan'] = $this->db->where('id', $data['cl_kelurahan_desa_id'])->get('cl_kelurahan_desa')->row('nama');
					} else {

						$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];
						$data['asal_kelurahan'] = $this->db->where('id', $this->auth['cl_kelurahan_desa_id'])->get('cl_kelurahan_desa')->row('nama');
					}

					$nopol = $this->input->post('nopol');

					$file = array();
					$dir = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 204800;
					$config['encrypt_name']			= true;

					$this->load->library('upload', $config);
					$data_file = [];
					if ($sts_crud == 'edit') {
						$cek = $this->db->where('id', $_POST['id'])->get('tbl_data_kendaraan');
						if ($cek->num_rows() > 0) {
							$data_file = json_decode($cek->row('file'));
							if (!is_array($data_file) || $data_file == null) {
								$data_file = [];
							}
						}
					}
					if (!empty($_FILES['files']['name'][0])) {
						$files = $_FILES['files'];
						foreach ($files['name'] as $key => $image) {
							$_FILES['file']['name'] = $files['name'][$key];
							$_FILES['file']['type'] = $files['type'][$key];
							$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
							$_FILES['file']['error'] = $files['error'][$key];
							$_FILES['file']['size'] = $files['size'][$key];
							$this->upload->initialize($config);

							if ($this->upload->do_upload('file')) {
								$data_upload = $this->upload->data();
								$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];

								$data_file[] = array('files' => $data_upload['file_name']);
							} else {
								return $this->upload->display_errors();
							}
						}
					}
					$data['file'] = json_encode($data_file);
				}
				break;

			case "data_indikator_skm":

				$table = "tbl_indikator_skm";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					/* $data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id']; */

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					/* $file = array();
					$dir = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 204800;
					$config['encrypt_name']			= true;

					$this->load->library('upload', $config);
					$data_file = [];
					if ($sts_crud == 'edit') {
						$cek = $this->db->where('id', $_POST['id'])->get('tbl_data_kendaraan');
						if ($cek->num_rows() > 0) {
							$data_file = json_decode($cek->row('file'));
							if (!is_array($data_file) || $data_file == null) {
								$data_file = [];
							}
						}
					}
					if (!empty($_FILES['files']['name'][0])) {
						$files = $_FILES['files'];
						foreach ($files['name'] as $key => $image) {
							$_FILES['file']['name'] = $files['name'][$key];
							$_FILES['file']['type'] = $files['type'][$key];
							$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
							$_FILES['file']['error'] = $files['error'][$key];
							$_FILES['file']['size'] = $files['size'][$key];
							$this->upload->initialize($config);

							if ($this->upload->do_upload('file')) {
								$data_upload = $this->upload->data();
								$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];

								$data_file[] = array('files' => $data_upload['file_name']);
							} else {
								return $this->upload->display_errors();
							}
						}
					}
					$data['file'] = json_encode($data_file); */
				}
				break;

			case "data_lorong":

				$table = "tbl_data_lorong";

				if (isset($data['koordinat'])) {
					list($x, $y) = explode(', ', $data['koordinat']);

					$data['lat'] = $x;
					$data['long'] = $y;
					unset($data['koordinat']);
				}

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();



					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;

			case "data_rekap_bulan":

				if (isset($data['tgl_cetak']) && $data['tgl_cetak'] != '') {
					$data['tgl_cetak'] = date('Y-m-d', strtotime($data['tgl_cetak']));
				}

				// if (isset($data['periode_cetak']) && $data['periode_cetak'] != '') {
				// 	$data['periode_cetak'] = date('Y-m-d', strtotime($data['periode_cetak']));
				// }

				if (isset($data['bulan']) && $data['bulan'] !== '') {
				} else {
					$data['bulan'] = null; // atau 0, sesuai kebutuhan DB
				}


				$table = "tbl_data_rekap_bulanan";

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;

			case "data_ekspedisi":

				$table = "tbl_data_ekspedisi";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();



					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;

			case "data_rekap_imb":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "tbl_data_rekap_imb";

				$array = array();


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();



					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;

			case "data_wamis":

				$table = "tbl_data_wamis";


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$data['nik'] = $this->input->post('nama');



					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$nama_penduduk = $this->db->get_where('tbl_data_penduduk', array('nik' => $data['nik']))->row_array();


					$data['kelurahan'] = $nama_kelurahan['nama'];

					$data['nama'] = $nama_penduduk['nama_lengkap'];
				}

				break;


			case "data_pkl":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "tbl_data_pkl";

				$array = array();


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;



			case "data_retribusi_sampah":

				$table = "tbl_data_retribusi_sampah";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];
					$data['nilai2'] = str_replace('.', '', $this->input->post('nilai'));
					$data['total2'] = str_replace('.', '', $this->input->post('total'));

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();



					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;


			case "data_tempat_ibadah":
				if (isset($data['koordinat'])) {
					list($x, $y) = explode(', ', $data['koordinat']);

					$data['lat'] = $x;
					$data['long'] = $y;
					unset($data['koordinat']);
				}

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "tbl_data_tempat_ibadah";

				$array = array();

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;



			case "data_sekolah":

				if (isset($data['koordinat'])) {
					list($x, $y) = explode(', ', $data['koordinat']);

					$data['lat'] = $x;
					$data['long'] = $y;
					unset($data['koordinat']);
				}


				$table = "cl_master_pendidikan";
				if ($sts_crud == "add" || $sts_crud == "edit") {

					$array = array();

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];


					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();


					$data['kel'] = $nama_kelurahan['nama'];

					$file = array();
					$dir = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 204800;
					$config['encrypt_name']			= true;

					$this->load->library('upload', $config);

					if (!empty($_FILES['files']['name'][0])) {
						$files = $_FILES['files'];
						$data_file = [];
						foreach ($files['name'] as $key => $image) {
							$_FILES['file']['name'] = $files['name'][$key];
							$_FILES['file']['type'] = $files['type'][$key];
							$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
							$_FILES['file']['error'] = $files['error'][$key];
							$_FILES['file']['size'] = $files['size'][$key];
							$this->upload->initialize($config);

							if ($this->upload->do_upload('file')) {
								$data_upload = $this->upload->data();
								$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];
								$data_upload['key'] = $data['npsn'];
								$data_upload['stat'] = "skl";
								$data_file[] = $data_upload;
							} else {
								return $this->upload->display_errors();
							}
						}
						$this->db->insert_batch('tbl_data_file', $data_file);
					}
				}

				break;

			case "data_detail_sekolah":

				$table = "cl_master_dapodik";
				if ($sts_crud == "add" || $sts_crud == "edit") {

					$array = array();
				}

				break;

			case "form_sub_indikator_rt_rw":
				$table = "tbl_kategori_penilaian_rt_rw";

				$uraian  = $data['uraian'];
				$satuan  = $data['satuan'];
				$tahun   = $data['tahun'];
				$kategori = $data['kategori'];
				$singkatan = $data['singkatan'];

				$data_temp = [];

				for ($i = 0; $i < count($uraian); $i++) {
					$data_temp[] = array(
						'uraian'    => $uraian[$i],
						'satuan'    => $satuan[$i],
						'tahun'     => $tahun,
						'kategori'  => $kategori,
						'singkatan' => $singkatan,
						'create_date' => date('Y-m-d H:i:s'),
						// 'create_by'   => $this->session->userdata('nama_lengkap')
					);
				}

				$data = $data_temp;
				break;

			case "data_faskes":


				if (!empty($data['koordinat']) && strpos($data['koordinat'], ',') !== false) {

					$koor = explode(',', $data['koordinat']);

					$data['lat']  = trim($koor[0]);
					$data['long'] = trim($koor[1]);

					unset($data['koordinat']);
				}


				$table = "tbl_data_rs";


				if ($sts_crud == "add" || $sts_crud == "edit") {

					$array = array();
					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();


					$data['kelurahan'] = $nama_kelurahan['nama'];

					$file = array();
					$dir = date('Ym');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'jpg|jpeg|png';
					$config['max_size']             = 204800;
					$config['encrypt_name']			= true;

					$this->load->library('upload', $config);

					if (!empty($_FILES['files']['name'][0])) {
						$files = $_FILES['files'];
						$data_file = [];
						foreach ($files['name'] as $key => $image) {
							$_FILES['file']['name'] = $files['name'][$key];
							$_FILES['file']['type'] = $files['type'][$key];
							$_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
							$_FILES['file']['error'] = $files['error'][$key];
							$_FILES['file']['size'] = $files['size'][$key];
							$this->upload->initialize($config);

							if ($this->upload->do_upload('file')) {
								$data_upload = $this->upload->data();
								$data_upload['file_name'] = '__data/' . $dir . '/' . $data_upload['file_name'];
								$data_upload['key'] = $data['kode'];
								$data_upload['stat'] = "faskes";
								$data_file[] = $data_upload;
							} else {
								return $this->upload->display_errors();
							}
						}
						$this->db->insert_batch('tbl_data_file', $data_file);
					}
				}

				break;

			case "data_petugas_kebersihan":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "tbl_data_petugas_kebersihan";

				$array = array();

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();


					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;



			case "data_umkm":
				// if (isset($data['koordinat'])) {
				// 	$koordinat = explode(',', $data['koordinat']);
				// 	$data['latitude'] = (isset($koordinat[0]) ? trim($koordinat[0]) : '');
				// 	$data['longitude'] = (isset($koordinat[1]) ? trim($koordinat[1]) : '');
				// 	unset($data['koordinat']);
				// }

				if (isset($data['koordinat'])) {
					$koordinat = explode(',', $data['koordinat']);
					$latitude = isset($koordinat[0]) ? trim($koordinat[0]) : '';
					$longitude = isset($koordinat[1]) ? trim($koordinat[1]) : '';

					// Batas wilayah Kota Makassar
					$lat_min = -5.230;
					$lat_max = -5.070;
					$lon_min = 119.340;
					$lon_max = 119.520;

					if (
						$latitude >= $lat_min && $latitude <= $lat_max &&
						$longitude >= $lon_min && $longitude <= $lon_max
					) {

						$data['latitude'] = $latitude;
						$data['longitude'] = $longitude;
					} else {
						// Jika AJAX, kirim response JSON
						echo json_encode([
							'status' => 'error',
							'message' => 'Koordinat berada di luar wilayah Kota Makassar. Data tidak disimpan.'
						]);
						return; // atau exit;
					}

					unset($data['koordinat']);
				}


				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "cl_master_umkm";

				$array = array();

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];
				}

				break;

			// case "data_rt_rw":
			// 	// if (@$data['koordinat'] != '') {

			// 	// 	if (isset($data['koordinat'])) {
			// 	// 		list($x, $y) = explode(', ', $data['koordinat']);

			// 	// 		$data['lat'] = $x;
			// 	// 		$data['long'] = $y;
			// 	// 		unset($data['koordinat']);
			// 	// 	}
			// 	// }


			// 	if (isset($data['tgl_mulai_jabat'])) {
			// 		$data['tgl_mulai_jabat'] = date('Y-m-d', strtotime($data['tgl_mulai_jabat']));
			// 	}

			// 	if (isset($data['tgl_sk_rt_rw'])) {
			// 		$data['tgl_sk_rt_rw'] = date('Y-m-d', strtotime($data['tgl_sk_rt_rw']));
			// 	}

			// 	if (!empty($_FILES['file']['name'])) {
			// 		$file = '';
			// 		$dir                     = date('Ymd');
			// 		if (!is_dir('./__data/' . $dir)) {
			// 			mkdir('./__data/' . $dir, 0755);
			// 		}

			// 		$config['upload_path']          = './__data/' . $dir;
			// 		$config['allowed_types']        = 'pdf|jpg|jpeg|png';
			// 		$config['max_size']             = 2048;
			// 		$config['encrypt_name']			= true;


			// 		$this->load->library('upload', $config);
			// 		$this->upload->initialize($config);

			// 		if (!$this->upload->do_upload('file')) {
			// 			$error = array('error' => $this->upload->display_errors());
			// 		} else {
			// 			$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
			// 		}

			// 		$data['file'] = $file;
			// 	}

			// 	$table = "tbl_data_rt_rw";

			// 	$array = array();

			// 	if ($sts_crud == "add" || $sts_crud == "edit") {

			// 		$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

			// 		$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

			// 		$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

			// 		$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

			// 		// Ambil nama kelurahan dari tabel cl_kelurahan_desa
			// 		$kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();
			// 		$data['kelurahan'] = $kelurahan['nama']; // sesuaikan field nama di tabel

			// 		$data['nik'] = $this->input->post('nik');
			// 		if ($this->input->post('rt') != '') {
			// 			$data['rt'] = str_pad($this->input->post('rt'), 3, '0', STR_PAD_LEFT);
			// 		} else {
			// 			$data['rt'] = '';
			// 		}
			// 		$data['rw'] = str_pad($this->input->post('rw'), 3, '0', STR_PAD_LEFT);

			// 		$data['nama_lengkap'] = $this->input->post('nama_lengkap');
			// 	}

			// break;

			case "data_rt_rw":

				if (isset($data['tgl_mulai_jabat'])) {
					$data['tgl_mulai_jabat'] = date('Y-m-d', strtotime($data['tgl_mulai_jabat']));
				}

				if (isset($data['tgl_sk_rt_rw'])) {
					$data['tgl_sk_rt_rw'] = date('Y-m-d', strtotime($data['tgl_sk_rt_rw']));
				}

				if (!empty($_FILES['file']['name'])) {
					$file = '';
					$dir                     = date('Ymd');
					if (!is_dir('./__data/' . $dir)) {
						mkdir('./__data/' . $dir, 0755);
					}

					$config['upload_path']          = './__data/' . $dir;
					$config['allowed_types']        = 'pdf|jpg|jpeg|png';
					$config['max_size']             = 2048;
					$config['encrypt_name']			= true;


					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					if (!$this->upload->do_upload('file')) {
						$error = array('error' => $this->upload->display_errors());
					} else {
						$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
					}

					$data['file'] = $file;
				}

				$table = "tbl_data_rt_rw";

				$array = array();

				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					$data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];

					// Ambil nama kelurahan dari tabel cl_kelurahan_desa
					$kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();
					$data['kelurahan'] = $kelurahan['nama']; // sesuaikan field nama di tabel

					$data['nik'] = $this->input->post('nik');
					
					// ================= RT =================
					$rt = trim($this->input->post('rt'));
					if ($rt !== '' && (int)$rt > 0) {
						$data['rt'] = str_pad($rt, 3, '0', STR_PAD_LEFT);
					} else {
						$data['rt'] = '';
					}

					// ================= RW =================
					$rw = trim($this->input->post('rw'));
					if ($rw !== '' && (int)$rw > 0) {
						$data['rw'] = str_pad($rw, 3, '0', STR_PAD_LEFT);
					} else {
						$data['rw'] = '';
					}

					$data['nama_lengkap'] = $this->input->post('nama_lengkap');
				}

				break;
			case "penilaian_rt_rw":

				$file = '';
				$dir                     = date('Ymd');
				if (!is_dir('./__data/' . $dir)) {
					mkdir('./__data/' . $dir, 0755);
				}

				$config['upload_path']          = './__data/' . $dir;
				$config['allowed_types']        = 'pdf|jpg|jpeg|png';
				$config['max_size']             = 2048;
				$config['encrypt_name']			= true;


				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
				} else {
					$file = '__data/' . $dir . '/' . $this->upload->data()['file_name'];
				}

				$data['file'] = $file;

				$table = "tbl_penilaian_rt_rw";

				$array = array();

				if ($sts_crud == "add") {
					$data_rt_rw = $this->db->get_where('tbl_data_rt_rw', array('id' => $data['tbl_data_rt_rw_id']))->row_array();
					$id = $this->db->select("IFNULL(max(penilaian_id),0)+1 id")->get('tbl_penilaian_rt_rw')->row('id');
					$tgl_surat = date('Y-m-d', strtotime($data['tgl_surat']));
					$kategori_penilaian_rt_rw_id = $this->input->post('kategori_penilaian_rt_rw_id[]');
					$bulan = $this->input->post('bulan');

					$kategori = $this->input->post('kategori[]');
					$uraian = $this->input->post('uraian[]');
					$satuan = $this->input->post('satuan[]');
					$target = $this->input->post('target[]');
					$capaian = $this->input->post('capaian[]');
					$nilai = $this->input->post('nilai[]');
					$data = [];
					for ($i = 0; $i < count($kategori_penilaian_rt_rw_id); $i++) {
						$data[] = [
							'penilaian_id' => $id,
							'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
							'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
							'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
							'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							'tbl_data_rt_rw_id' => $data_rt_rw['id'],
							'nik' => $data_rt_rw['nik'],
							'nama_lengkap' => $data_rt_rw['nama_lengkap'],
							'tgl_surat' => $tgl_surat,
							'kategori_penilaian_rt_rw_id' => $kategori_penilaian_rt_rw_id[$i],
							'bulan' => $bulan,
							'kategori' => $kategori[$i],
							'uraian' => $uraian[$i],
							'satuan' => $satuan[$i],
							'target' => ($target[$i] == '' ? 0 : $target[$i]),
							'capaian' => ($capaian[$i] == '' ? 0 : $capaian[$i]),
							'nilai' => ($nilai[$i] == '' ? 0 : $nilai[$i]),
							'create_date' => date('Y-m-d H:i:s'),
							'create_by' => $this->auth['nama_lengkap'],
							'file' => @$file
						];
					}
				}

				if ($sts_crud == "edit") {
					$id = $this->input->post('id');
					$data_rt_rw = $this->db->get_where('tbl_data_rt_rw', array('id' => $data['tbl_data_rt_rw_id']))->row_array();
					$tgl_surat = date('Y-m-d', strtotime($data['tgl_surat']));
					$kategori_penilaian_rt_rw_id = $this->input->post('kategori_penilaian_rt_rw_id[]');
					$bulan = $this->input->post('bulan');

					$kategori = $this->input->post('kategori[]');
					$uraian = $this->input->post('uraian[]');
					$satuan = $this->input->post('satuan[]');
					$target = $this->input->post('target[]');
					$capaian = $this->input->post('capaian[]');
					$nilai = $this->input->post('nilai[]');
					$this->db->delete('tbl_penilaian_rt_rw', array('penilaian_id' => $id));

					$data = [];
					for ($i = 0; $i < count($kategori_penilaian_rt_rw_id); $i++) {
						$data[] = [
							'penilaian_id' => $id,
							'cl_provinsi_id' => $this->auth['cl_provinsi_id'],
							'cl_kab_kota_id' => $this->auth['cl_kab_kota_id'],
							'cl_kecamatan_id' => $this->auth['cl_kecamatan_id'],
							'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							'tbl_data_rt_rw_id' => $data_rt_rw['id'],
							'nik' => $data_rt_rw['nik'],
							'nama_lengkap' => $data_rt_rw['nama_lengkap'],
							'tgl_surat' => $tgl_surat,
							'kategori_penilaian_rt_rw_id' => $kategori_penilaian_rt_rw_id[$i],
							'bulan' => $bulan,
							'kategori' => $kategori[$i],
							'uraian' => $uraian[$i],
							'satuan' => $satuan[$i],
							'target' => ($target[$i] == '' ? 0 : $target[$i]),
							'capaian' => ($capaian[$i] == '' ? 0 : $capaian[$i]),
							'nilai' => ($nilai[$i] == '' ? 0 : $nilai[$i]),
							'create_date' => date('Y-m-d H:i:s'),
							'create_by' => $this->auth['nama_lengkap'],
							'file' => @$file
						];
					}
					$sts_crud = "add";
				}
				break;

			case "data_kunjungan_rumah":

				$table = "tbl_data_kunjungan_rumah";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					// echo 'file '.$_FILES['foto']['name'];exit;

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];



					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];

					if (!empty($_FILES['foto']['name'])) {

						$ext = explode('.', $_FILES['foto']['name']);

						$exttemp = sizeof($ext) - 1;

						$extension = $ext[$exttemp];

						$upload_path = "./__repository/tmp_upload/";

						$filen = "data_kunjungan_rumah-" . $data['cl_kelurahan_desa_id'] . "-" . $data['no_kk'] . "-" . $_FILES['foto']['name'];

						$filename =  $this->lib->uploadnong($upload_path, 'foto', $filen);

						$folder_aplod = $upload_path . $filename;

						$data['foto'] = $folder_aplod;
					}
				}

				break;



			case "data_kerja_bakti":

				$table = "tbl_data_kerja_bakti";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];



					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];

					if (!empty($_FILES['foto']['name'])) {

						$ext = explode('.', $_FILES['foto']['name']);

						$exttemp = sizeof($ext) - 1;

						$extension = $ext[$exttemp];

						$upload_path = "./__repository/tmp_upload/";

						$filen = "data_kerja_bakti-" . $data['cl_kelurahan_desa_id'] . "-" . $data['tanggal'] . "-" . $data['lokasi'] . "-" . $_FILES['foto']['name'];

						$filename =  $this->lib->uploadnong($upload_path, 'foto', $filen);

						$folder_aplod = $upload_path . $filename;

						$data['foto'] = $folder_aplod;
					}
				}

				break;



			case "data_notulen_rapat":

				$table = "tbl_data_notulen_rapat";



				if ($sts_crud == "add" || $sts_crud == "edit") {

					$data['cl_provinsi_id'] = $this->auth['cl_provinsi_id'];

					$data['cl_kab_kota_id'] = $this->auth['cl_kab_kota_id'];

					$data['cl_kecamatan_id'] = $this->auth['cl_kecamatan_id'];

					// $data['cl_kelurahan_desa_id'] = $this->auth['cl_kelurahan_desa_id'];



					$nama_kelurahan = $this->db->get_where('cl_kelurahan_desa', array('id' => $data['cl_kelurahan_desa_id']))->row_array();

					$data['kelurahan'] = $nama_kelurahan['nama'];

					if (!empty($_FILES['foto_hadir']['name'])) {

						$ext = explode('.', $_FILES['foto_hadir']['name']);

						$exttemp = sizeof($ext) - 1;

						$extension = $ext[$exttemp];

						$upload_path = "./__repository/tmp_upload/";

						$filen = "data_notulen_rapat_1-" . $data['cl_kelurahan_desa_id'] . "-" . $data['tanggal'] . "-" . $data['agenda_rapat'] . "-" . $_FILES['foto_hadir']['name'];

						$filename =  $this->lib->uploadnong($upload_path, 'foto_hadir', $filen);

						$folder_aplod = $upload_path . $filename;

						$data['foto_hadir'] = $folder_aplod;
					}

					if (!empty($_FILES['foto_rapat']['name'])) {

						$ext1 = explode('.', $_FILES['foto_rapat']['name']);

						$exttemp1 = sizeof($ext1) - 1;

						$extension1 = $ext[$exttemp1];

						$upload_path1 = "./__repository/tmp_upload/";

						$filen1 = "data_notulen_rapat_2-" . $data['cl_kelurahan_desa_id'] . "-" . $data['tanggal'] . "-" . $data['agenda_rapat'] . "-" . $_FILES['foto_rapat']['name'];

						$filename1 =  $this->lib->uploadnong($upload_path1, 'foto_rapat', $filen1);

						$folder_aplod1 = $upload_path1 . $filename1;

						$data['foto_rapat'] = $folder_aplod1;
					}
				}

				break;



			case "user_role_group":

				$id_group = $id;

				$this->db->delete('tbl_user_prev_group', array('cl_user_group_id' => $id_group));

				if (isset($data['data'])) {

					$postdata = $data['data'];

					$row = array();

					foreach ($postdata as $v) {

						$pecah = explode("_", $v);

						$row["buat"] = 0;

						$row["baca"] = 0;

						$row["ubah"] = 0;

						$row["hapus"] = 0;



						switch ($pecah[0]) {

							case "C":

								$row["buat"] = 1;

								break;

							case "R":

								$row["baca"] = 1;

								break;

							case "U":

								$row["ubah"] = 1;

								break;

							case "D":

								$row["hapus"] = 1;

								break;
						}



						$row["tbl_menu_id"] = $pecah[1];

						$row["cl_user_group_id"] = $id_group;



						$cek_data = $this->db->get_where('tbl_user_prev_group', array('tbl_menu_id' => $pecah[1], 'cl_user_group_id' => $id_group))->row_array();

						if (!$cek_data) {

							$row['create_date'] = date('Y-m-d H:i:s');

							$row['create_by'] = $this->auth['nama_lengkap'];



							$this->db->insert('tbl_user_prev_group', $row);
						} else {

							if ($row["buat"] == 0) unset($row["buat"]);

							if ($row["baca"] == 0) unset($row["baca"]);

							if ($row["ubah"] == 0) unset($row["ubah"]);

							if ($row["hapus"] == 0) unset($row["hapus"]);



							$row['update_date'] = date('Y-m-d H:i:s');

							$row['update_by'] = $this->auth['nama_lengkap'];



							$this->db->update('tbl_user_prev_group', $row, array('tbl_menu_id' => $pecah[1], 'cl_user_group_id' => $id_group));
						}
					}
				}

				break;

			case "user_mng":

				$table = "tbl_user";

				if ($sts_crud == 'add' || $sts_crud == 'edit') {

					$data['password'] = $this->encrypt->encode($data['password']);
				}

				break;

			case "user_group":

				$table = "cl_user_group";

				break;

			case "ubah_password":

				$this->load->library("encrypt");



				$table = "tbl_user";

				$password_lama = $this->encrypt->decode($this->auth["password"]);

				if ($data["pwd_lama"] != $password_lama) {

					echo 2;

					exit;
				}



				$data["password"] = $this->encrypt->encode($data["pwd_baru"]);



				unset($data["pwd_lama"]);

				unset($data["pwd_baru"]);

				break;
		}

		switch ($sts_crud) {

			case "add":
				if ($table == "tbl_data_penduduk") {
					$kel = $this->auth['cl_kelurahan_desa_id'];
					$cek = $this->db->select('nik')->get_where('tbl_data_penduduk', array('nik' => $nik, 'status_data' => 'AKTIF', 'cl_kelurahan_desa_id' => $kel));
					if ($cek->num_rows() > 0) {
						return '2';
					} else {
						$insert = $this->db->insert($table, $data);
					}
				} else if ($table == "tbl_kartu_keluarga") {

					$cek = $this->db->select('no_kk')->get_where('tbl_kartu_keluarga', array('no_kk' => $no_kk));
					if ($cek->num_rows() > 0) {
						return '2';
					} else {
						$insert = $this->db->insert($table, $data);
					}
				} else if ($table == "tbl_data_penandatanganan") {

					$nip     = $this->input->post('nip');
					$tingkat_jabatan = $this->input->post('tingkat_jabatan');
					$kec = $this->auth['cl_kelurahan_desa_id'];
					$cek = $this->db->select('nip')->get_where('tbl_data_penandatanganan', array('nip' => $nip, 'cl_kelurahan_desa_id' => $kec, 'status' => 'Aktif'));

					if ($cek->num_rows() > 0) {
						return '2';
					} else {

						$this->db->where(array(
							'tingkat_jabatan'      => $tingkat_jabatan,
							'cl_provinsi_id'       => $this->auth['cl_provinsi_id'],
							'cl_kab_kota_id'       => $this->auth['cl_kab_kota_id'],
							'cl_kecamatan_id'      => $this->auth['cl_kecamatan_id'],
							'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							'status'               => 'Aktif'
						));
						$this->db->update('tbl_data_penandatanganan', [
							'status'      => 'Tidak Aktif',
							'update_by'   => $this->auth['username'],
							'update_date' => date('Y-m-d H:i:s')
						]);

						// Cek data lama dengan nip yang sama dan status aktif
						$this->db->where('nip', $nip);
						$this->db->where('status', 'Aktif');
						$Zquery = $this->db->get('tbl_data_penandatanganan');

						if ($Zquery->num_rows() > 0) {
							$row = $Zquery->row();
							// Jika kelurahan lama berbeda, update data lama jadi tidak aktif
							if ($row->cl_kelurahan_desa_id != $this->auth['cl_kelurahan_desa_id']) {
								$this->db->where('nip', $nip);
								$this->db->where('status', 'Aktif');
								$this->db->update('tbl_data_penandatanganan', [
									'status' => 'Tidak Aktif',
									'tingkat_jabatan' => ''
								]);
							}
						}

						$insert = $this->db->insert($table, $data);
					}
				} else if ($table == "tbl_data_kendaraan") {

					$kec = $this->auth['cl_kelurahan_desa_id'];
					$cek = $this->db->select('nopol')->get_where('tbl_data_kendaraan', array('nopol' => $nopol, 'cl_kelurahan_desa_id' => $kec));
					if ($cek->num_rows() > 0) {
						return '2';
					} else {

						$insert = $this->db->insert($table, $data);
					}
				} else if ($table == "tbl_penilaian_rt_rw") {
					$insert = $this->db->insert_batch($table, $data);
				} else if ($table == "tbl_kategori_penilaian_rt_rw") {
					$insert = $this->db->insert_batch($table, $data);
				} else if ($table == "tbl_data_daftar_agenda") {
					$data['batch_key'] = uniqid('batch_', true);
					$insert = $this->db->insert($table, $data);
				} else {

					$insert = $this->db->insert($table, $data);
					if (!empty($file) && $table2 != '') {
						$this->db->insert($table2, $data2);
					}
				}



				// $id = $this->db->insert_id();



				if ($insert) {

					if ($table == "tbl_kartu_keluarga") {

						if (isset($nik)) {

							foreach ($nik as $k => $v) {

								if (trim($v) != "") {

									$array_update = array(

										'no_kk' => $data['no_kk'],

										'cl_status_hubungan_keluarga_id' => $cl_status_hubungan_keluarga_id[$k],

									);

									$this->db->update('tbl_data_penduduk', $array_update, array('id' => $v));
								}
							}
						}
					}
				}

				break;

			case "edit":
				if ($table == "tbl_penilaian_rt_rw") {
					$insert = $this->db->insert_batch($table, $data_to_insert);
					// if($insert){
					// 	$update = $this->db->where('penilaian_id',$_POST['id'])->delete('tbl_penilaian_rt_rw');
					// }
				} elseif ($table == "tbl_kartu_keluarga") {
					$no_kk = $this->db->where('id', $id)->get($table)->row('no_kk');
					$this->db->where('no_kk', $no_kk)->update('tbl_data_penduduk', ['no_kk' => '']);
					$this->db->delete($table, array('id' => $id));
					$this->db->insert($table, $data);

					if (isset($nik)) {

						foreach ($nik as $k => $v) {

							if (trim($v) != "") {

								$array_update = array(

									'no_kk' => $data['no_kk'],

									'cl_status_hubungan_keluarga_id' => $cl_status_hubungan_keluarga_id[$k],

								);

								$this->db->update('tbl_data_penduduk', $array_update, array('id' => $v));
							}
						}
					}
				} elseif ($table == "tbl_data_penandatanganan") {

					$update = $this->db->update($table, $data, array('id' => $id));

					$nip     = $this->input->post('nip');
					$tingkat_jabatan = $this->input->post('tingkat_jabatan');

					// Nonaktifkan user lain dengan jabatan & wilayah sama
					$this->db->where(array(
						'tingkat_jabatan'      => $tingkat_jabatan,
						'cl_provinsi_id'       => $this->auth['cl_provinsi_id'],
						'cl_kab_kota_id'       => $this->auth['cl_kab_kota_id'],
						'cl_kecamatan_id'      => $this->auth['cl_kecamatan_id'],
						'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
					));
					$this->db->where("id<>$id");
					$this->db->update('tbl_data_penandatanganan', [
						'status'      => 'Tidak Aktif',
						'update_by'   => $this->auth['username'],
						'update_date' => date('Y-m-d H:i:s')
					]);

					// Ambil status terbaru dari data yang baru diupdate
					$this->db->where('id', $id);
					$row = $this->db->get('tbl_data_penandatanganan')->row();

					if ($row && strtolower($row->status) == 'tidak aktif') {
						// Jika data yg disimpan sekarang jadi Tidak Aktif,
						// maka aktifkan salah satu data lain di wilayah & jabatan yang sama
						$this->db->where(array(
							'tingkat_jabatan'      => $tingkat_jabatan,
							'cl_provinsi_id'       => $this->auth['cl_provinsi_id'],
							'cl_kab_kota_id'       => $this->auth['cl_kab_kota_id'],
							'cl_kecamatan_id'      => $this->auth['cl_kecamatan_id'],
							'cl_kelurahan_desa_id' => $this->auth['cl_kelurahan_desa_id'],
							'status'               => 'Tidak Aktif'
						));
						$this->db->where("id<>$id");
						$this->db->limit(1);
						$another = $this->db->get('tbl_data_penandatanganan')->row();

						if ($another) {
							$this->db->where('id', $another->id);
							$this->db->update('tbl_data_penandatanganan', [
								'status'      => 'Aktif',
								'update_by'   => $this->auth['username'],
								'update_date' => date('Y-m-d H:i:s')
							]);
						}
					}

					// Cek data lama dengan nip yang sama dan status aktif
					$this->db->where('nip', $nip);
					$this->db->where('status', 'Aktif');
					$Zquery = $this->db->get('tbl_data_penandatanganan');

					if ($Zquery->num_rows() > 0) {
						$row = $Zquery->row();
						// Jika kelurahan lama berbeda, update data lama jadi tidak aktif
						if ($row->cl_kelurahan_desa_id != $this->auth['cl_kelurahan_desa_id']) {
							$this->db->where('nip', $nip);
							$this->db->where('status', 'Aktif');
							$this->db->update('tbl_data_penandatanganan', [
								'status' => 'Tidak Aktif',
								'tingkat_jabatan' => ''
							]);
						}
					}
				} else {

					$update = $this->db->update($table, $data, array('id' => $id));


					if ($update) {

						if ($table == "tbl_kartu_keluarga") {

							if (isset($nik)) {

								foreach ($nik as $k => $v) {

									if (trim($v) != "") {

										$array_update = array(

											'no_kk' => $data['no_kk'],

											'cl_status_hubungan_keluarga_id' => $cl_status_hubungan_keluarga_id[$k],

										);

										$this->db->update('tbl_data_penduduk', $array_update, array('nik' => $v));
									}
								}
							}
						}
					}
				}
				break;

			case "delete":

				if ($table == "tbl_data_penduduk") {
					$cek = $this->db->query("SELECT no_kk from tbl_data_penduduk where id='$id' ")->row('no_kk');
					$cek2 = $this->db->query("SELECT * from tbl_data_surat where tbl_data_penduduk_id='$id' ")->num_rows();
					if (($cek === null or $cek === '') && $cek2 === 0) {
						$this->db->delete($table, array('id' => $id));
					} else if ($cek2 > 0) {
						return '4';
					} else {
						return '3';
					}
				} else if ($table == "tbl_penilaian_rt_rw") {
					// Cek apakah data penilaian di tbl_penilaian_rt_rw ada

					// $pen = $this->db->query("DELETE FROM tbl_penilaian_rt_rw WHERE id='$id'");
					$this->db->delete($table, array('penilaian_id' => $id));
				} else {
					if ($table == "tbl_data_surat") {
						$kon = $this->db->query("SELECT * from tbl_data_surat where id='$id' and arsip!=''")->num_rows();
						if ($kon > 0) {
							return '5';
						}
					}
					$this->db->delete($table, array('id' => $id));
				}

				break;
		}

		if ($this->db->trans_status() == false) {

			$this->db->trans_rollback();

			return 'gagal';
		} else {

			$this->db->trans_commit();
			if ($sts_crud === 'add' && $table === 'tbl_data_daftar_agenda') {

				$payload = [
					'batch_key'             => $data['batch_key'],
					'cl_kecamatan_id'       => (int) $data['cl_kecamatan_id'],
					'cl_kelurahan_desa_id'  => (int) $data['cl_kelurahan_desa_id'],
				];

				$ch = curl_init('https://mobile.kotamakassar.id/mobile/Fcm_v1/receive_agenda');
				curl_setopt_array($ch, [
					CURLOPT_POST            => true,
					CURLOPT_RETURNTRANSFER  => true,
					CURLOPT_HTTPHEADER      => [
						'Content-Type: application/json',
						'X-API-KEY: XGASDJsjkaseryi823ADBDKC98AS'
					],
					CURLOPT_POSTFIELDS      => json_encode($payload),
					CURLOPT_TIMEOUT         => 5, // jangan gantung request utama
				]);

				$res = curl_exec($ch);

				if ($res === false) {
					log_message('error', 'FCM API error: ' . curl_error($ch));
				} else {
					$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					if ($httpCode !== 200) {
						log_message('error', 'FCM API HTTP ' . $httpCode . ' | Response: ' . $res);
					}
				}

				curl_close($ch);
			}

			// PROSES UTAMA TETAP BERHASIL
			return 1;
		}
	}

	function template_golongan($param, $format = '')
	{
		$res = $this->db
			->select("$format as result")
			->where('nm_golongan', $param)
			->or_where('pangkat', $param)
			->or_where('id', $param)->get('cl_golongan')->row('result');
		return $res;
	}

	function hari_otomatis()
	{
		$tanggal = date('d-m-Y');;
		$harix = date('l');
		if ($harix == 'Monday') {
			$hari = "Senin";
		} elseif ($harix == 'Tuesday') {
			$hari = "Selasa";
		} elseif ($harix == 'Wednesday') {
			$hari = "Rabu";
		} elseif ($harix == 'Thursday') {
			$hari = "Kamis";
		} elseif ($harix == 'Friday') {
			$hari = "Jumat";
		} elseif ($harix == 'Saturday') {
			$hari = "Sabtu";
		} else {
			$hari = "Minggu";
		}

		return "Hari : " . $hari . ", Tanggal " . tgl_indo($tanggal);
	}

	public function mutu_skm($nilai)
	{
		if ($nilai >= 88.31 && $nilai <= 100) {
			return 'A (Sangat Baik)';
		} elseif ($nilai >= 76.61 && $nilai <= 88.30) {
			return 'B (Baik)';
		} elseif ($nilai >= 65.00 && $nilai <= 76.60) {
			return 'C (Kurang Baik)';
		} elseif ($nilai >= 25.00 && $nilai <= 64.99) {
			return 'D (Tidak Baik)';
		}
		return '-';
	}

}
