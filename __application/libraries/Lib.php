<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	LIBRARY CIPTAAN JINGGA LINTAS IMAJI
	KONTEN LIBRARY :
	- Upload File
	- Upload File Multiple
	- RandomString
	- CutString
	- Kirim Email
	- Konversi Bulan
	- Fillcombo
	- Json Datagrid
	
*/
class Lib
{
	public function __construct() {}

	//class asset manager
	function assetsmanager($type, $p1)
	{
		$assets = "";
		$ci = &get_instance();
		$base_url = $ci->config->item('base_url');

		switch ($type) {
			case "js":

				switch ($p1) {
					case "frontend":
						$arrayjs = array(
							'__assets/frontend/js/jquery.js',
							'__assets/pluginsall/easyui/jquery.easyui.min.js',
							'__assets/pluginsall/jquery-validation/dist/jquery.validate.js',
							'__assets/frontend/js/plugins.js',
							'__assets/frontend/js/components/moment.js',
							'__assets/frontend/js/components/daterangepicker.js',
							'__assets/frontend/js/functions.js',
							'__assets/backendxx/dist/js/loading-overlay.js',
							'__assets/pluginsall/sweetalert/sweetalert.js',
							'__assets/backendxx/dist/js/autoNumeric.js',
							'__assets/backendxx/dist/js/autoCurrency.js',

						);
						break;
					case "login":
						$arrayjs = array(
							'__assets/backendxx/bower_components/jquery/dist/jquery.min.js',
							'__assets/backendxx/bower_components/bootstrap/dist/js/bootstrap.min.js',
						);
						break;
					case "main":
						$arrayjs = array(
							'__assets/backendxx/bower_components/jquery/dist/jquery.min.js',
							'__assets/backendxx/bower_components/bootstrap/dist/js/bootstrap.min.js',
							'__assets/backendxx/bower_components/fastclick/lib/fastclick.js',
							'__assets/pluginsall/easyui/jquery.easyui.min.js',
							'__assets/pluginsall/jquery-validation/dist/jquery.validate.js',
							'__assets/pluginsall/maskmoney/jquery.maskMoney.js',
							'__assets/pluginsall/ckeditor/ckeditor.js',
							'__assets/backendxx/dist/js/adminlte.min.js',
							'__assets/backendxx/dist/js/sidebar-fix.js', // ✅ TAMBAH DI SINI
							'__assets/backendxx/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js',
							'__assets/backendxx/bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
							'__assets/backendxx/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
							'__assets/backendxx/bower_components/timepicker/bootstrap-timepicker.min.js',
							'__assets/backendxx/bower_components/moment/moment.js',
							'__assets/backendxx/bower_components/select2/dist/js/select2.full.min.js',
							'__assets/pluginsall/highcharts/highcharts.js',
							// '__assets/pluginsall/highcharts/exporting.js',
							'__assets/backendxx/dist/js/typeahead.js',
							'__assets/backendxx/dist/js/loading-overlay.js',
							'__assets/backendxx/dist/js/autoNumeric.js',
							'__assets/backendxx/dist/js/autoCurrency.js',
							'__assets/backendxx/dist/js/fungsi.js',

						);
						break;
				}

				foreach ($arrayjs as $k) {
					$version = filemtime($k);
					$assets .= "
						<script src='" . $base_url . $k . "?v=" . $version . "'></script> 
					";
				}

				break;
			case "css":

				switch ($p1) {
					case "frontend":
						$arraycss = array(
							'__assets/frontend/css/bootstrap.css',
							'__assets/frontend/css/style.css',
							'__assets/frontend/css/swiper.css',
							'__assets/frontend/css/dark.css',
							'__assets/frontend/css/font-icons.css',
							'__assets/frontend/css/animate.css',
							'__assets/frontend/css/magnific-popup.css',
							'__assets/frontend/css/components/daterangepicker.css',
							'__assets/frontend/css/responsive.css',
						);
						break;
					case "login":
						$arraycss = array(
							'__assets/backendxx/bower_components/bootstrap/dist/css/bootstrap.min.css',
							'__assets/backendxx/bower_components/font-awesome/css/font-awesome.min.css',
							'__assets/backendxx/bower_components/Ionicons/css/ionicons.min.css',
							'__assets/backendxx/dist/css/AdminLTE.min.css',
						);
						break;
					case "main":
						$arraycss = array(
							'__assets/backendxx/bower_components/bootstrap/dist/css/bootstrap.min.css',
							'__assets/backendxx/bower_components/font-awesome/css/font-awesome.min.css',
							'__assets/backendxx/bower_components/Ionicons/css/ionicons.min.css',
							'__assets/backendxx/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
							'__assets/backendxx/bower_components/timepicker/bootstrap-timepicker.min.css',
							'__assets/pluginsall/easyui/themes/metro-gray/easyui.css',
							'__assets/backendxx/bower_components/select2/dist/css/select2.min.css',
							'__assets/backendxx/dist/css/AdminLTE.min.css',
							'__assets/backendxx/dist/css/skins/_all-skins.min.css',
							'__assets/backendxx/dist/css/sidebar.css',
						);
						break;
				}

				foreach ($arraycss as $k) {
					$version = filemtime($k);
					$assets .= "
						<link href='" . $base_url . $k . "?v=" . $version . "' rel='stylesheet'>
					";
				}

				break;
		}

		return $assets;
	}
	//End class asset manager


	//class Upload File Version 1.0 - Beta
	function uploadnong($upload_path = "", $object = "", $file = "")
	{
		//$upload_path = "./__repository/".$folder."/";

		$ext = explode('.', $_FILES[$object]['name']);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];

		$filename =  $file . '.' . $extension;

		$files = $_FILES[$object]['name'];
		$tmp  = $_FILES[$object]['tmp_name'];
		if (!file_exists($upload_path)) mkdir($upload_path, 0777, true);
		if (file_exists($upload_path . $filename)) {
			unlink($upload_path . $filename);
			$uploadfile = $upload_path . $filename;
		} else {
			$uploadfile = $upload_path . $filename;
		}

		$chmodfile = dirname(__DIR__) . "/" . $uploadfile;

		move_uploaded_file($tmp, $uploadfile);
		//if (!chmod($chmodfile, 0775)) {
		//	echo "Gagal mengupload file";
		//	exit;
		//}

		return $filename;
	}
	// end class Upload File

	//class Upload File Multiple Version 1.0 - Beta
	function uploadmultiplenong($upload_path = "", $object = "", $file = "", $idx = "")
	{
		$ext = explode('.', $_FILES[$object]['name'][$idx]);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];

		$filename =  $file . '.' . $extension;

		$files = $_FILES[$object]['name'][$idx];
		$tmp  = $_FILES[$object]['tmp_name'][$idx];
		if (!file_exists($upload_path)) mkdir($upload_path, 0777, true);
		if (file_exists($upload_path . $filename)) {
			unlink($upload_path . $filename);
			$uploadfile = $upload_path . $filename;
		} else {
			$uploadfile = $upload_path . $filename;
		}

		move_uploaded_file($tmp, $uploadfile);
		//if (!chmod($uploadfile, 0775)) {
		//	echo "Gagal mengupload file";
		//	exit;
		//}

		return $filename;
	}
	//end Class Upload File

	//class Random String Version 1.0
	function randomString($length, $parameter = "")
	{
		$str = "";
		$rangehuruf = range('A', 'Z');
		$rangeangka = range('0', '9');
		if ($parameter == 'angka') {
			$characters = array_merge($rangeangka);
		} elseif ($parameter == 'huruf') {
			$characters = array_merge($rangehuruf);
		} else {
			$characters = array_merge($rangehuruf, $rangeangka);
		}
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}
	//end Class Random String

	// Numbering Format
	function numbering_format($var)
	{
		return number_format($var, 0, ",", ".");
	}
	// End Numbering Format

	//Class CutString
	function cutstring($text, $length)
	{
		//$isi_teks = htmlentities(strip_tags($text));
		$isi = substr($text, 0, $length);
		//$isi = substr($isi_teks, 0,strrpos($isi," "));
		$isi = $isi . ' ...';
		return $isi;
	}
	//end Class CutString

	//Class Kirim Email
	function kirimemail($type = "", $email = "", $p1 = "", $p2 = "", $p3 = "")
	{
		$ci = &get_instance();

		$ci->load->library('email');
		$html = "";
		$subject = "";
		switch ($type) {
			case "email_sent_opr":
				$ci->nsmarty->assign('data', $p1);
				$html = $ci->nsmarty->fetch('backend/modul/email/email_sent_opr.html');
				$subject = "EMAIL NOTIFIKASI CONTACT CENTER";
				break;
			case "email_test":
				$html = "test email bro";
				$subject = "Email Test VPTI Contact Center";
				break;
		}

		$config = array(
			"protocol"	=> "smtp",
			"mailtype" => "html",
			"smtp_host" => "ssl://mbox.scisi.com",
			"smtp_user" => "notifikasi@scisi.com",
			"smtp_pass" => "Sc1s1kso",
			"smtp_port" => "465",
			'charset' => 'utf-8',
			'wordwrap' => TRUE,
		);


		$ci->email->initialize($config);
		$ci->email->from("ticketcc@scisi.com", "VPTI - Contact Center Notifikasi");

		if (is_array($email)) {
			$ci->email->to(implode(', ', $email));
		} else {
			$ci->email->to($email);
		}

		if ($p2 != "") {
			$ci->email->cc($p2);
		}
		$ci->email->cc("a.muzaki@scisi.com");

		$ci->email->subject($subject);
		$ci->email->message($html);
		$ci->email->set_newline("\r\n");
		if ($ci->email->send())
			//echo "<h3> SUKSES EMAIL ke $email </h3>";
			return 1;
		else
			//echo $this->email->print_debugger();
			return $ci->email->print_debugger();
	}
	//End Class KirimEmail a.muzaki@scisi.com

	//Class Konversi Bulan
	function konversi_bulan($bln, $type = "")
	{
		if ($type == 'fullbulan') {
			switch ($bln) {
				case 1:
					$bulan = 'Januari';
					break;
				case 2:
					$bulan = 'Februari';
					break;
				case 3:
					$bulan = 'Maret';
					break;
				case 4:
					$bulan = 'April';
					break;
				case 5:
					$bulan = 'Mei';
					break;
				case 6:
					$bulan = 'Juni';
					break;
				case 7:
					$bulan = 'Juli';
					break;
				case 8:
					$bulan = 'Agustus';
					break;
				case 9:
					$bulan = 'September';
					break;
				case 10:
					$bulan = 'Oktober';
					break;
				case 11:
					$bulan = 'November';
					break;
				case 12:
					$bulan = 'Desember';
					break;
			}
		} else {
			switch ($bln) {
				case 1:
					$bulan = 'Jan';
					break;
				case 2:
					$bulan = 'Feb';
					break;
				case 3:
					$bulan = 'Mar';
					break;
				case 4:
					$bulan = 'Apr';
					break;
				case 5:
					$bulan = 'Mei';
					break;
				case 6:
					$bulan = 'Jun';
					break;
				case 7:
					$bulan = 'Jul';
					break;
				case 8:
					$bulan = 'Agst';
					break;
				case 9:
					$bulan = 'Sept';
					break;
				case 10:
					$bulan = 'Okt';
					break;
				case 11:
					$bulan = 'Nov';
					break;
				case 12:
					$bulan = 'Des';
					break;
			}
		}
		return $bulan;
	}
	//End Class Konversi Bulan

	//Class Konversi Tanggal
	function konversi_tgl($date)
	{
		$ci = &get_instance();
		$ci->load->helper('terbilang');
		$data = array();
		$timestamp = strtotime($date);
		$day = date('D', $timestamp);
		$day_angka = (int)date('d', $timestamp);
		$month = date('m', $timestamp);
		$years = date('Y', $timestamp);
		switch ($day) {
			case "Mon":
				$data['hari'] = 'Senin';
				break;
			case "Tue":
				$data['hari'] = 'Selasa';
				break;
			case "Wed":
				$data['hari'] = 'Rabu';
				break;
			case "Thu":
				$data['hari'] = 'Kamis';
				break;
			case "Fri":
				$data['hari'] = 'Jumat';
				break;
			case "Sat":
				$data['hari'] = 'Sabtu';
				break;
			case "Sun":
				$data['hari'] = 'Minggu';
				break;
		}
		switch ($month) {
			case "01":
				$data['bulan'] = 'Januari';
				break;
			case "02":
				$data['bulan'] = 'Februari';
				break;
			case "03":
				$data['bulan'] = 'Maret';
				break;
			case "04":
				$data['bulan'] = 'April';
				break;
			case "05":
				$data['bulan'] = 'Mei';
				break;
			case "06":
				$data['bulan'] = 'Juni';
				break;
			case "07":
				$data['bulan'] = 'Juli';
				break;
			case "08":
				$data['bulan'] = 'Agustus';
				break;
			case "09":
				$data['bulan'] = 'September';
				break;
			case "10":
				$data['bulan'] = 'Oktober';
				break;
			case "11":
				$data['bulan'] = 'November';
				break;
			case "12":
				$data['bulan'] = 'Desember';
				break;
		}
		$data['tahun'] = ucwords(number_to_words($years));
		$data['tgl_text'] = ucwords(number_to_words($day_angka));
		return $data;
	}
	//End Class Konversi Tanggal

	//Class Fillcombo
	function fillcombo($type = "", $balikan = "", $p1 = "", $p2 = "", $p3 = "")
	{
		$ci = &get_instance();
		$ci->load->model('mbackend');

		$v = $ci->input->post('v');
		if ($v != "") {
			$selTxt = $v;
		} else {
			$selTxt = $p1;
		}

		$optTemp = '<option selected value=""> -- Pilih -- </option>';
		switch ($type) {

			case "ya_atau_tidak":
				$data = array(
					'0' => array('id' => 'YA', 'txt' => 'YA'),
					'1' => array('id' => 'TIDAK', 'txt' => 'TIDAK'),
				);
				break;
			case "punya_atau_tidak":
				$data = array(
					'0' => array('id' => '1', 'txt' => 'PUNYA'),
					'1' => array('id' => '0', 'txt' => 'TIDAK'),
				);
				break;

			case "kelurahan_report":
				$optTemp = '<option selected value=""> -- Pilih Kelurahan/Desa -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_penandatangananx":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_penandatanganan_all":
				$optTemp = '<option selected value="a"> -- Pilih TTD -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "jenis_surat_report":
				$optTemp = '<option selected value=""> -- Pilih Jenis Surat -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "pendidikan":
				$data = array(
					'0' => array('id' => 'DOKTOR (S3)', 'txt' => 'DOKTOR (S3)'),
					'1' => array('id' => 'MAGISTER (S2)', 'txt' => 'MAGISTER (S2)'),
					'2' => array('id' => 'SARJANA (S1/D4)', 'txt' => 'SARJANA (S1/D4)'),
					'3' => array('id' => 'AHLI MADYA (D3)', 'txt' => 'AHLI MADYA (D3)'),
					'4' => array('id' => 'SMA', 'txt' => 'SMA'),
					'5' => array('id' => 'SMP', 'txt' => 'SMP'),
					'6' => array('id' => 'SD', 'txt' => 'SD'),
					'7' => array('id' => 'TK', 'txt' => 'TK'),
					'8' => array('id' => 'TIDAK SEKOLAH', 'txt' => 'TIDAK SEKOLAH'),
				);
				break;
			case "list_npsn":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "gol_darah":
				$data = array(
					'0' => array('id' => 'A', 'txt' => 'A'),
					'1' => array('id' => 'B', 'txt' => 'B'),
					'2' => array('id' => 'O', 'txt' => 'O'),
					'3' => array('id' => 'AB', 'txt' => 'AB'),
				);
				break;
			// case "cl_sifat_surat":
			// 	$data = array(
			// 		'0' => array('id' => 'Biasa', 'txt' => 'Biasa'),
			// 		'1' => array('id' => 'Rahasia', 'txt' => 'Rahasia'),
			// 		'2' => array('id' => 'Segera', 'txt' => 'Segera'),
			// 		'3' => array('id' => 'Sangat Segera', 'txt' => 'Sangat Segera'),
			// 		'4' => array('id' => 'Penting', 'txt' => 'Penting'),
			// 	);
			// 	break;
			case "jenis_kelamin":
				$data = array(
					'0' => array('id' => 'Laki-Laki', 'txt' => 'Laki-Laki'),
					'1' => array('id' => 'Perempuan', 'txt' => 'Perempuan'),
				);
				break;
			case "jenis_agenda":
				$data = array(
					'0' => array('id' => '0', 'txt' => 'Internal'),
					'1' => array('id' => '1', 'txt' => 'Share RT/RW'),
				);
				break;
			case "jab_rt_rw":
				$data = array(
					'0' => array('id' => 'Ketua RW', 'txt' => 'Ketua RW'),
					'1' => array('id' => 'Ketua RT', 'txt' => 'Ketua RT'),
					'2' => array('id' => 'PJ RW', 'txt' => 'PJ RW'),
					'4' => array('id' => 'PJ RT', 'txt' => 'PJ RT'),
				);
				break;
			case "status_penilaian":
				$data = array(
					'0' => array('id' => 'Data Dinilai', 'txt' => 'Data Dinilai'),
					'1' => array('id' => 'Data Belum Dinilai', 'txt' => 'Data Belum Dinilai'),
				);
				break;
			case "jenis_domisili":
				$data = array(
					'0' => array('id' => 'Tetap', 'txt' => 'Tetap'),
					'1' => array('id' => 'Sementara', 'txt' => 'Sementara'),
				);
				break;
			case "pil_jenis_surat":
				$data = array(
					'0' => array('id' => 'Surat Keterangan', 'txt' => 'Surat Keterangan'),
					'1' => array('id' => 'Surat Pernyataan', 'txt' => 'Surat Pernyataan'),
				);
				break;
			case "pil_jenis_menumpang":
				$data = array(
					'0' => array('id' => 'Menggunakan Alamat Saya', 'txt' => 'Menggunakan Alamat Saya'),
					'1' => array('id' => 'Masuk KK Saya/Menumpang KK', 'txt' => 'Masuk KK Saya/Menumpang KK'),
				);
				break;
			case "pil_surat_penelitian":
				$data = array(
					'0' => array('id' => 'Mahasiswa', 'txt' => 'Mahasiswa'),
					'1' => array('id' => 'Instansi/Perusahaan', 'txt' => 'Instansi/Perusahaan'),
				);
				break;
			case "pil_surat_survey":
				$data = array(
					'0' => array('id' => 'Lembaga/Instansi', 'txt' => 'Lembaga/Instansi'),
					'1' => array('id' => 'Mahasiswa', 'txt' => 'Mahasiswa'),
				);
				break;
			case "pil_surat_teguran":
				$data = array(
					'0' => array('id' => 'ASN', 'txt' => 'ASN'),
					'1' => array('id' => 'Non ASN', 'txt' => 'Non ASN'),
				);
				break;
			case "pil_surat_usulan":
				$data = array(
					'0' => array('id' => 'Usulan Kenaikan Pangkat', 'txt' => 'Usulan Kenaikan Pangkat'),
					'1' => array('id' => 'Perpanjangan', 'txt' => 'Perpanjangan'),
				);
				break;
			case "pil_pernyataan_tugas":
				$data = array(
					'0' => array('id' => 'ASN', 'txt' => 'ASN'),
					'1' => array('id' => 'PPPK', 'txt' => 'PPPK'),
					'2' => array('id' => 'Non ASN', 'txt' => 'Non ASN'),
				);
				break;
			case "pil_keterangan_umum":
				$data = array(
					'0' => array('id' => 'Surat Keterangan Lainnya', 'txt' => 'Surat Keterangan Lainnya'),
					'1' => array('id' => 'Tidak Memiliki Kendaraan Roda Empat', 'txt' => 'Tidak Memiliki Kendaraan Roda Empat'),
					'2' => array('id' => 'Pemakaian Pulsa', 'txt' => 'Pemakaian Pulsa'),
					'3' => array('id' => 'Tidak Memiliki Rekening Air PDAM', 'txt' => 'Tidak Memiliki Rekening Air PDAM'),
					'4' => array('id' => 'Tidak Memiliki PBB', 'txt' => 'Tidak Memiliki PBB'),
				);
				break;
			case "pil_status_pewaris":
				$data = array(
					'0' => array('id' => 'Belum Kawin', 'txt' => 'Belum Kawin'),
					'1' => array('id' => 'Kawin', 'txt' => 'Kawin'),
				);
				break;
			case "pil_pernyataan_umum":
				$data = array(
					'0' => array('id' => 'Surat Pernyataan Lainnya', 'txt' => 'Surat Pernyataan Lainnya'),
					'1' => array('id' => 'Tidak Memiliki Kendaraan Roda Empat', 'txt' => 'Tidak Memiliki Kendaraan Roda Empat'),
					'2' => array('id' => 'Pemakaian Pulsa', 'txt' => 'Pemakaian Pulsa'),
					'3' => array('id' => 'Tidak Memiliki Rekening Air PDAM', 'txt' => 'Tidak Memiliki Rekening Air PDAM'),
					'4' => array('id' => 'Tidak Memiliki PBB', 'txt' => 'Tidak Memiliki PBB'),
				);
				break;
			case "jenis_cuti":
				$data = array(
					'0' => array('id' => 'Cuti Tahunan', 'txt' => 'Cuti Tahunan'),
					'1' => array('id' => 'Cuti Besar', 'txt' => 'Cuti Besar'),
					'2' => array('id' => 'Cuti Sakit', 'txt' => 'Cuti Sakit'),
					'3' => array('id' => 'Cuti Melahirkan', 'txt' => 'Cuti Melahirkan'),
					'4' => array('id' => 'Cuti Karena Alasan Penting', 'txt' => 'Cuti Karena Alasan Penting'),
					'5' => array('id' => 'Cuti di Luar Tanggungan Negara', 'txt' => 'Cuti di Luar Tanggungan Negara'),
				);
				break;
			case "alasan_izin_pegawai":
				$data = array(
					'0' => array('id' => 'Saya Tidak Masuk Kerja', 'txt' => 'Saya Tidak Masuk Kerja'),
					'1' => array('id' => 'Terlambat Masuk Kerja', 'txt' => 'Terlambat Masuk Kerja'),
					'2' => array('id' => 'Pulang Sebelum Waktunya', 'txt' => 'Pulang Sebelum Waktunya'),
					'3' => array('id' => 'Tidak Berada di Tempat Tugas', 'txt' => 'Tidak Berada di Tempat Tugas'),
					'4' => array('id' => 'Tidak Melakukan Rekap Kehadiran', 'txt' => 'Tidak Melakukan Rekap Kehadiran'),
				);
				break;
			case "pertimbangan_atasan":
				$data = array(
					'0' => array('id' => 'Disetujui', 'txt' => 'Disetujui'),
					'1' => array('id' => 'Perubahan', 'txt' => 'Perubahan'),
					'2' => array('id' => 'Ditangguhkan', 'txt' => 'Ditangguhkan'),
					'3' => array('id' => 'Tidak Disetujui', 'txt' => 'Tidak Disetujui'),
				);
				break;
			case "pilih_status":
				$data = array(
					'0' => array('id' => 'Aktif', 'txt' => 'Aktif'),
					'1' => array('id' => 'Tidak Aktif', 'txt' => 'Tidak Aktif'),
				);
				break;
			case "jenis_teguran":
				$data = array(
					'0' => array('id' => 'Teguran RT/RW', 'txt' => 'Teguran RT/RW'),
					'1' => array('id' => 'Teguran Warga', 'txt' => 'Teguran Warga'),
					'2' => array('id' => 'Teguran Pegawai', 'txt' => 'Teguran Pegawai'),
				);
				break;
			case "jabatan_tugas":
				$data = array(
					'0' => array('id' => 'Ketua', 'txt' => 'Ketua'),
					'1' => array('id' => 'Anggota', 'txt' => 'Anggota')
				);
				break;

			case "rw":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "status_pegawai":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "jenis_passport":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "pilih_tingkat_jabatan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "keperluan_passport":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "status_penduduk":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "cl_agama":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "status_kawin":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_penduduk_id":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_kategori_id":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_penduduk_asing_id":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "data_penilaian_id":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "pilih_ttd":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "pilih_tahun":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "pilih_tahun_perolehan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "jenjang_sekolah":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
			case "status_sekolah":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'Negeri', 'txt' => 'Negeri'),
					'1' => array('id' => 'Swasta', 'txt' => 'Swasta')
				);
				break;
			case "nilai_rt_rw":
				$optTemp = '<option selected value=""> Nilai </option>';
				$data = array(
					'0' => array('id' => '60', 'txt' => '60'),
					'1' => array('id' => '70', 'txt' => '70'),
					'2' => array('id' => '80', 'txt' => '80'),
					'3' => array('id' => '90', 'txt' => '90'),
					'4' => array('id' => '100', 'txt' => '100'),
				);
				break;
			case "jenis_umkm":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'Campuran/Klontongan', 'txt' => 'Campuran/Klontongan'),
					'1' => array('id' => 'Kecantikan', 'txt' => 'Kecantikan'),
					'2' => array('id' => 'Kuliner', 'txt' => 'Kuliner'),
					'3' => array('id' => 'Telekomunikasi', 'txt' => 'Telekomunikasi'),
					'4' => array('id' => 'Pendidikan', 'txt' => 'Pendidikan'),
					'5' => array('id' => 'Fashion', 'txt' => 'Fashion'),
					'6' => array('id' => 'Otomotif', 'txt' => 'Otomotif'),
					'7' => array('id' => 'Teknologi Internet', 'txt' => 'Teknologi Internet'),
					'8' => array('id' => 'Produk Kreatif', 'txt' => 'Produk Kreatif'),
					'9' => array('id' => 'Pedagang Kaki Lima', 'txt' => 'Pedagang Kaki Lima'),
					'10' => array('id' => 'Jasa', 'txt' => 'Jasa'),
					'11' => array('id' => 'Agribisnis', 'txt' => 'Agribisnis'),
					'12' => array('id' => 'Kontraktor', 'txt' => 'Kontraktor'),
					'13' => array('id' => 'ATK & Percetakan', 'txt' => 'ATK & Percetakan'),
					'14' => array('id' => 'Tour & Travel', 'txt' => 'Tour & Travel')
				);
				break;
			case "modal_obzet":
				$data = array(
					'0' => array('id' => '< 100jt', 'txt' => '< 100jt'),
					'1' => array('id' => '< 200jt', 'txt' => '< 200jt'),
					'2' => array('id' => '< 500jt', 'txt' => '< 500jt'),
					'3' => array('id' => '< 1 Milyar', 'txt' => '< 1 Milyar'),
					'4' => array('id' => '< 2 Milyar', 'txt' => '< 2 Milyar'),
					'5' => array('id' => '< 5 Milyar', 'txt' => '< 5 Milyar'),
					'6' => array('id' => '< 15 Milyar', 'txt' => '< 15 Milyar'),
					'7' => array('id' => '< 25 Milyar', 'txt' => '< 25 Milyar'),
					'8' => array('id' => '< 50 Milyar', 'txt' => '< 50 Milyar')
				);
				break;

			case "no_npsn":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'Negeri', 'txt' => 'Negeri'),
					'1' => array('id' => 'Swasta', 'txt' => 'Swasta')
				);
				break;
			case "jenis_rs":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'PKM', 'txt' => 'PKM'),
					'1' => array('id' => 'RS', 'txt' => 'RS'),
					'2' => array('id' => 'Klinik', 'txt' => 'Klinik')
				);
				break;
			case "kelas_rs":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'B', 'txt' => 'B'),
					'1' => array('id' => 'C', 'txt' => 'C'),
					'2' => array('id' => 'D', 'txt' => 'D')
				);
				break;
			case "jenis_pelayanan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'Rawat Inap', 'txt' => 'Rawat Inap'),
					'1' => array('id' => 'Non Rawat Inap', 'txt' => 'Non Rawat Inap')
				);
				break;
			case "akreditasi":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = array(
					'0' => array('id' => 'Dasar', 'txt' => 'Dasar'),
					'1' => array('id' => 'Madya', 'txt' => 'Madya'),
					'2' => array('id' => 'Utama', 'txt' => 'Utama'),
					'3' => array('id' => 'Paripurna', 'txt' => 'Paripurna')
				);
				break;
			case "pilih_jabatan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
			case "pilih_jabatanx":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;
			case "pilih_golongan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;
			case "pilih_jabatan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;
			case "pilih_jsurat":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;
			case "nama_sopir":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;

			case "kd_brg":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;

			case "asal_kelurahan":
				$optTemp = '<option selected value=""> -- Pilih -- </option>';
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				// var_dump($data);
				// exit();
				break;

			case "tempat_ibadah":
				$data = array(
					'0' => array('id' => 'Masjid', 'txt' => 'Masjid'),
					'1' => array('id' => 'Gereja', 'txt' => 'Gereja'),
					'2' => array('id' => 'Pura', 'txt' => 'Pura'),
					'3' => array('id' => 'Vihara', 'txt' => 'Vihara'),
					'4' => array('id' => 'Kelenteng', 'txt' => 'Kelenteng'),
				);
				break;
			case "hubungan_waris":
				$data = array(
					'0' => array('id' => 'SUAMI', 'txt' => 'SUAMI'),
					'1' => array('id' => 'ISTRI', 'txt' => 'ISTRI'),
					'2' => array('id' => 'ANAK', 'txt' => 'ANAK'),
					'3' => array('id' => 'CUCU', 'txt' => 'CUCU'),
					'4' => array('id' => 'CUCU', 'txt' => 'IBU KANDUNG'),
					'5' => array('id' => 'CUCU', 'txt' => 'BAPAK KANDUNG'),
					'6' => array('id' => 'CUCU', 'txt' => 'SAUDARA KANDUNG'),
				);
				break;

			case "status_waris":
				$data = array(
					'0' => array('id' => 'HIDUP', 'txt' => 'HIDUP'),
					'1' => array('id' => 'MENINGGAL DUNIA', 'txt' => 'MENINGGAL DUNIA'),

				);
				break;
			case "masa_berlaku":
				$data = array(
					'0' => array('id' => '1 Tahun', 'txt' => '1 Tahun'),
					'1' => array('id' => '2 Tahun', 'txt' => '2 Tahun'),
					'2' => array('id' => '3 Tahun', 'txt' => '3 Tahun'),
					'3' => array('id' => '4 Tahun', 'txt' => '4 Tahun'),
					'4' => array('id' => '5 Tahun', 'txt' => '5 Tahun'),
				);
				break;
			case "klasifikasi_pindah":
				$data = array(
					'0' => array('id' => 'Dalam satu Desa/Kelurahan', 'txt' => 'Dalam satu Desa/Kelurahan'),
					'1' => array('id' => 'Antar Desa/Kelurahan', 'txt' => 'Antar Desa/Kelurahan'),
					'2' => array('id' => 'Antar Kecamatan', 'txt' => 'Antar Kecamatan'),
					'3' => array('id' => 'Antar Kab/Kota', 'txt' => 'Antar Kab/Kota'),
					'4' => array('id' => 'Antar Provinsi', 'txt' => 'Antar Provinsi'),
				);
				break;
			case "jenis_kepindahan":
				$data = array(
					'0' => array('id' => 'kep. Keluarga', 'txt' => 'kep. Keluarga'),
					'1' => array('id' => 'Kep. Keluarga dan Seluruh Angg. Keluarga', 'txt' => 'Kep. Keluarga dan Seluruh Angg. Keluarga'),
					'2' => array('id' => 'Kep. Keluarga dan sbg. Angg. Keluarga', 'txt' => 'Kep. Keluarga dan sbg. Angg. Keluarga'),
					'3' => array('id' => 'Angg. Keluarga', 'txt' => 'Angg. Keluarga'),
				);
				break;
			case "jenis_permohonan":
				$data = array(
					'0' => array('id' => 'Surat Keterangan Pindah', 'txt' => 'Surat Keterangan Pindah'),
					'1' => array('id' => 'Surat Keterangan Pindah Luar Negeri (SKPLN)', 'txt' => 'Surat Keterangan Pindah Luar Negeri (SKPLN)'),
					'2' => array('id' => 'Surat Keterangan Tempat Tinggal (SKTT) Bagi Orang Asing Tinggal terbatas', 'txt' => 'Surat Keterangan Tempat Tinggal (SKTT) Bagi Orang Asing Tinggal terbatas'),
				);
				break;
			case "alasan_pindah":
				$data = array(
					'0' => array('id' => 'Pekerjaan', 'txt' => 'Pekerjaan'),
					'1' => array('id' => 'Pendidikan', 'txt' => 'Pendidikan'),
					'2' => array('id' => 'Keamanan', 'txt' => 'Keamanan'),
					'3' => array('id' => 'Kesehatan', 'txt' => 'Kesehatan'),
					'4' => array('id' => 'Perumahan', 'txt' => 'Perumahan'),
					'5' => array('id' => 'Keluarga', 'txt' => 'Keluarga'),
					'6' => array('id' => 'Lainnya', 'txt' => 'Lainnya'),
				);
				break;
			case "status_kk_tdk_pindah":
				$data = array(
					'0' => array('id' => 'Numpang KK', 'txt' => 'Numpang KK'),
					'1' => array('id' => 'Membuat KK Baru', 'txt' => 'Membuat KK Baru'),
					'2' => array('id' => 'Tidak Ada Angg. Yang Ditinggal', 'txt' => 'Tidak Ada Angg. Yang Ditinggal'),
					'3' => array('id' => 'Nomor KK Tetap', 'txt' => 'Nomor KK Tetap'),
				);
				break;
			case "status_kk_pindah":
				$data = array(
					'0' => array('id' => 'Numpang KK', 'txt' => 'Numpang KK'),
					'1' => array('id' => 'Membuat KK Baru', 'txt' => 'Membuat KK Baru'),
					'2' => array('id' => 'Nama Kep. Keluarga dan No. KK Tetap', 'txt' => 'Nama Kep. Keluarga dan No. KK Tetap'),
				);
				break;
			case "pilih_bulan":
				$data = array(
					'0' => array('id' => '1', 'txt' => 'Januari'),
					'1' => array('id' => '2', 'txt' => 'Februari'),
					'2' => array('id' => '3', 'txt' => 'Maret'),
					'3' => array('id' => '4', 'txt' => 'April'),
					'4' => array('id' => '5', 'txt' => 'Mei'),
					'5' => array('id' => '6', 'txt' => 'Juni'),
					'6' => array('id' => '7', 'txt' => 'Juli'),
					'7' => array('id' => '8', 'txt' => 'Agustus'),
					'8' => array('id' => '9', 'txt' => 'September'),
					'9' => array('id' => '10', 'txt' => 'Oktober'),
					'10' => array('id' => '11', 'txt' => 'November'),
					'11' => array('id' => '12', 'txt' => 'Desember'),
				);
				break;
			case "status_usaha":
				$data = array(
					'0' => array('id' => 'Pusat', 'txt' => 'Pusat'),
					'1' => array('id' => 'Cabang', 'txt' => 'Cabang'),
				);
				break;
			case "status_bangunan_masjid":
				$data = array(
					'0' => array('id' => 'Pusat', 'txt' => 'Pusat'),
					'1' => array('id' => 'Cabang', 'txt' => 'Cabang'),
					'2' => array('id' => 'Wakaf', 'txt' => 'Wakaf'),
					'3' => array('id' => 'Milik Pribadi', 'txt' => 'Milik Pribadi'),
					'4' => array('id' => 'Fasilitas Sosial (Fasos)', 'txt' => 'Fasilitas Sosial (Fasos)'),
				);
				break;
			case "pilih_judul":
				$data = array(
					'0' => array('id' => 'SURAT PERNYATAAN IDENTITAS', 'txt' => 'SURAT PERNYATAAN IDENTITAS'),
					'1' => array('id' => 'SURAT PERNYATAAN ORANG YANG SAMA', 'txt' => 'SURAT PERNYATAAN ORANG YANG SAMA'),
				);
				break;
			case "kelayakan_kendaraan":
				$data = array(
					'0' => array('id' => 'Layak', 'txt' => 'Layak'),
					'1' => array('id' => 'Tidak Layak', 'txt' => 'Tidak Layak'),
				);
				break;
			case "kondisi_kendaraan":
				$data = array(
					'0' => array('id' => 'Baik', 'txt' => 'Baik'),
					'1' => array('id' => 'Kurang Baik', 'txt' => 'Kurang Baik'),
				);
				break;
			case "hari_kematian":
				$data = array(
					'0' => array('id' => 'Senin', 'txt' => 'Senin'),
					'1' => array('id' => 'Selasa', 'txt' => 'Selasa'),
					'2' => array('id' => 'Rabu', 'txt' => 'Rabu'),
					'3' => array('id' => 'Kamis', 'txt' => 'Kamis'),
					'4' => array('id' => 'Jumát', 'txt' => 'Jumát'),
					'5' => array('id' => 'Sabtu', 'txt' => 'Sabtu'),
					'6' => array('id' => 'Minggu', 'txt' => 'Minggu'),
				);
				break;
			case "bangunan_usaha":
				$data = array(
					'0' => array('id' => 'Permanen', 'txt' => 'Permanen'),
					'1' => array('id' => 'Sementara', 'txt' => 'Sementara'),
				);
				break;
			case "status_tanah":
				$data = array(
					'0' => array('id' => 'Hak Milik', 'txt' => 'Hak Milik'),
					'1' => array('id' => 'Hak Guna Bangunan', 'txt' => 'Hak Guna Bangunan'),
				);
				break;
			case "status_tanah2":
				$data = array(
					'0' => array('id' => 'Sertifikat Hak Milik (SHM)', 'txt' => 'Sertifikat Hak Milik (SHM)'),
					'1' => array('id' => 'Sertifikat Hak Guna Bangunan (SHGB)', 'txt' => 'Sertifikat Hak Guna Bangunan (SHGB)'),
					'2' => array('id' => 'Sertifikat Hak Satuan Rumah Susun (SHSRS)', 'txt' => 'Sertifikat Hak Satuan Rumah Susun (SHSRS)'),
					'3' => array('id' => 'Sertifikat Girik atau Patok D', 'txt' => 'Sertifikat Girik atau Patok D'),
					'4' => array('id' => 'Akta Jual Beli (AJB)', 'txt' => 'Akta Jual Beli (AJB)'),
				);
				break;
			case "jns_surat_teguran":
				$data = array(
					'0' => array('id' => 'Surat Teguran Pertama', 'txt' => 'Surat Teguran Pertama'),
					'1' => array('id' => 'Surat Teguran Ke dua', 'txt' => 'Surat Teguran Ke dua'),
					'2' => array('id' => 'Surat Teguran Ke Tiga', 'txt' => 'Surat Teguran Ke Tiga'),
					'3' => array('id' => 'Surat Teguran ke Empat', 'txt' => 'Surat Teguran ke Empat'),
				);
				break;
			case "jns_surat_teguran_rt":
				$data = array(
					'0' => array('id' => 'Surat Teguran Pertama', 'txt' => 'Surat Teguran Pertama'),
					'1' => array('id' => 'Surat Teguran Ke dua', 'txt' => 'Surat Teguran Ke dua'),
					'2' => array('id' => 'Surat Teguran Ke Tiga', 'txt' => 'Surat Teguran Ke Tiga'),
					'3' => array('id' => 'Surat Teguran ke Empat', 'txt' => 'Surat Teguran ke Empat'),
				);
				break;
			case "jns_surat_pegawai":
				$data = array(
					'0' => array('id' => 'Surat Teguran Pertama', 'txt' => 'Surat Teguran Pertama'),
					'1' => array('id' => 'Surat Teguran Ke dua', 'txt' => 'Surat Teguran Ke dua'),
					'2' => array('id' => 'Surat Teguran Ke Tiga', 'txt' => 'Surat Teguran Ke Tiga'),
					'3' => array('id' => 'Surat Teguran ke Empat', 'txt' => 'Surat Teguran ke Empat'),
				);
				break;
			case "penentu_kematian":
				$data = array(
					'0' => array('id' => 'Dokter', 'txt' => 'Dokter'),
					'1' => array('id' => 'Perawat', 'txt' => 'Perawat'),
					'2' => array('id' => 'Tenaga Kesehatan Lain', 'txt' => 'Tenaga Kesehatan Lain'),
				);
				break;
			case "status_perkawinan":
				$data = array(
					'0' => array('id' => 'Jejaka', 'txt' => 'Jejaka'),
					'1' => array('id' => 'Duda', 'txt' => 'Duda'),
					'2' => array('id' => 'Beristri', 'txt' => 'Beristri'),
					'3' => array('id' => 'Perawan', 'txt' => 'Perawan'),
					'4' => array('id' => 'Janda', 'txt' => 'Janda'),
				);
				break;
			case "status_kawin":
				$data = array(
					'0' => array('id' => 'BELUM KAWIN', 'txt' => 'BELUM KAWIN'),
					'1' => array('id' => 'KAWIN', 'txt' => 'KAWIN'),
					'2' => array('id' => 'CERAI HIDUP', 'txt' => 'CERAI HIDUP'),
					'3' => array('id' => 'CERAI MATI', 'txt' => 'CERAI MATI'),
				);
				break;
			case "agama":
				$data = array(
					'0' => array('id' => 'Islam', 'txt' => 'Islam'),
					'1' => array('id' => 'Katolik', 'txt' => 'Katolik'),
					'2' => array('id' => 'Kristen', 'txt' => 'Kristen'),
					'3' => array('id' => 'Hindu', 'txt' => 'Hindu'),
					'4' => array('id' => 'Budha', 'txt' => 'Budha'),
					'5' => array('id' => 'Konghucu', 'txt' => 'Konghucu'),
					'6' => array('id' => 'Kepercayaan Lain', 'txt' => 'Kepercayaan Lain'),
				);
				break;
			case "jenis_wamis":
				$data = array(
					'0' => array('id' => 'BANSOS', 'txt' => 'BANSOS'),
					'1' => array('id' => 'PKH', 'txt' => 'PKH'),
				);
				break;
			case "status":
				$optTemp = '';
				$data = array(
					'0' => array('id' => 'AKTIF', 'txt' => 'AKTIF'),
					'1' => array('id' => 'MENINGGAL DUNIA', 'txt' => 'MENINGGAL DUNIA'),
					'2' => array('id' => 'PINDAH DOMISILI', 'txt' => 'PINDAH DOMISILI'),
				);
				break;

			case "tanggal":
				$data = $this->arraydate('tanggal');
				$optTemp = '<option value=""> -- Tanggal -- </option>';
				break;
			case "bulan":
				$data = $this->arraydate('bulan');

				$optTemp = '<option value=""> -- Pilih -- </option>';

				break;

			case "tahun":
				$data = $this->arraydate('tahun');
				$optTemp = '<option value=""> -- Tahun -- </option>';
				break;

			case "email_notif":
				$data = array();
				$optTemp = '<option value=""> -- Choose -- </option>';
				break;

			default:
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
				break;
		}

		if ($data) {
			foreach ($data as $k => $v) {
				$v['txt'] = str_replace("'", "`", $v['txt']);

				if ($selTxt == $v['id']) {
					if ($type == 'layanan_satuans') {
						$optTemp .= "<option value='" . $v['id'] . "' hpp_super_express='" . $v['hpp_super_express'] . "'  hpp_express='" . $v['hpp_express'] . "'  hpp_regular='" . $v['hpp_regular'] . "' harga_jual_super_express='" . $v['harga_jual_super_express'] . "' harga_jual_express='" . $v['harga_jual_express'] . "' harga_jual_regular='" . $v['harga_jual_regular'] . "' >" . strtoupper($v['txt']) . "</option>";
					} else if ($type == 'list_npsn') {
						$optTemp .= '<option selected value="' . $v['id'] . '">' . $v['id'] . ' - ' . $v['txt'] . '</option>';
					} else {
						$optTemp .= '<option selected value="' . $v['id'] . '">' . $v['txt'] . '</option>';
					}
				} else {
					if ($type == 'layanan_satuans') {
						$optTemp .= "<option value='" . $v['id'] . "' hpp_super_express='" . $v['hpp_super_express'] . "'  hpp_express='" . $v['hpp_express'] . "'  hpp_regular='" . $v['hpp_regular'] . "' harga_jual_super_express='" . $v['harga_jual_super_express'] . "' harga_jual_express='" . $v['harga_jual_express'] . "' harga_jual_regular='" . $v['harga_jual_regular'] . "' >" . strtoupper($v['txt']) . "</option>";
					} else if ($type == 'list_npsn') {
						$optTemp .= '<option value="' . $v['id'] . '">' . $v['id'] . ' - ' . $v['txt'] . '</option>';
					} else {
						$optTemp .= '<option value="' . $v['id'] . '">' . $v['txt'] . '</option>';
					}
				}
			}
		}

		if ($balikan == 'return') {
			return $optTemp;
		} elseif ($balikan == 'echo') {
			echo $optTemp;
		}
	}
	//End Class Fillcombo

	//Function Json Grid Tree
	function json_grid_tree($sql, $type = "", $table = "")
	{
		$ci = &get_instance();
		$ci->load->database();
		$page = (int) (($ci->input->post('page')) ? $ci->input->post('page') : 0);
		$limit = (int) (($ci->input->post('rows')) ? $ci->input->post('rows') : 0);

		$count = $ci->db->query($sql)->num_rows();

		if ($page != 0 && $limit != 0) {
			if ($count > 0) {
				$total_pages = ceil($count / $limit);
			} else {
				$total_pages = 0;
			}
			if ($page > $total_pages) $page = $total_pages;
		}

		$dbdriver = $ci->db->dbdriver;

		if ($dbdriver == "mysql" || $dbdriver == "mysqli") {
			//MYSQL
			$start = $limit * $page - $limit; // do not put $limit*($page - 1)
			if ($start < 0) $start = 0;
			$sql = $sql . " LIMIT $start,$limit";
			$data = $ci->db->query($sql)->result_array();
		}

		if ($dbdriver == "postgre" || $dbdriver == 'sqlsrv' || $dbdriver == 'mssql') {
			//POSTGRESSS
			if ($limit != 0) {
				$end = $page * $limit;
				$start = $end - $limit;
				if ($start < 0) $start = 0;
				/*
				$sql = "
					SELECT * FROM (
						".$sql."
					) AS X WHERE X.rowID BETWEEN $start AND $end
				";
				*/

				$sql .= "
					LIMIT $limit OFFSET $start
				";
			}
		}

		//if($type == 'layanan'){ $sql .= " ORDER BY X.id DESC"; }
		//if($type == 'dokumen'){ $sql .= " ORDER BY X.id DESC"; }
		//echo $sql;exit;

		$data = $ci->db->query($sql)->result_array();

		//echo $count;exit;

		if ($data) {
			$responce = new stdClass();
			$responce->rows = $data;
			$responce->total = $count;
		} else {
			$responce = new stdClass();
			$responce->rows = array();
			$responce->total = 0;
		}

		//print_r($responce);exit;

		return $responce;
	}
	//End Function Json Grid Tree

	//Function Json Grid
	function json_grid($sql, $type = "", $table = "", $koding = "")
	{
		$ci = &get_instance();
		$ci->load->database();
		$ci->load->model((array('mbackend')));
		$footer = false;
		$arr_foot = array();

		$page = (int) (($ci->input->post('page')) ? $ci->input->post('page') : 0);
		$limit = (int) (($ci->input->post('rows')) ? $ci->input->post('rows') : 0);

		$count = $ci->db->query($sql)->num_rows();

		if ($page != 0 && $limit != 0) {
			if ($count > 0) {
				$total_pages = ceil($count / $limit);
			} else {
				$total_pages = 0;
			}
			if ($page > $total_pages) $page = $total_pages;
		}

		$dbdriver = $ci->db->dbdriver;

		if ($dbdriver == "mysql" || $dbdriver == "mysqli") {
			//MYSQL
			$start = $limit * $page - $limit; // do not put $limit*($page - 1)
			if ($start < 0) $start = 0;
			$sql = $sql . " LIMIT $start,$limit";
			$data = $ci->db->query($sql)->result_array();
		}

		if ($dbdriver == "postgre" || $dbdriver == 'sqlsrv' || $dbdriver == 'mssql') {
			//POSTGRESSS
			if ($limit != 0) {
				$end = $page * $limit;
				$start = $end - $limit + 1;
				if ($start < 0) $start = 0;
				$sql = "
					SELECT * FROM (
						" . $sql . "
					) AS X WHERE X.rowID BETWEEN $start AND $end
				";
			}
		}

		if ($type == 'kategori_informasi') {
			$sql .= " ORDER BY X.id DESC";
		}
		if ($type == 'list_work_order') {
			$sql .= " ORDER BY X.id DESC";
		}
		if ($type == 'report_detail') {
			$sql .= " ORDER BY X.id DESC";
		}
		if ($type == 'monitoring_contact') {
			$sql .= " ORDER BY X.id DESC";
		}
		if ($type == 'incoming_call') {
			$sql .= " ORDER BY X.id DESC";
		}

		//echo $sql;exit;

		$data = $ci->db->query($sql)->result_array();

		if ($type == "list_work_order") {
			foreach ($data as $k => $v) {
				if ($v['tipe_contact'] == 'PPJK') {
					$where = " 
						tbl_form_contact_id_ppjk = '" . $v['ppjk_id'] . "' 
						AND tbl_form_contact_id = '" . $v['id'] . "' 
					";
				} else {
					$where = " tbl_form_contact_id = '" . $v['id'] . "' ";
				}

				$sqldet = "
					SELECT TOP 1 nama_lengkap, tbl_form_contact_id, id
					FROM tbl_balasan_opr
					WHERE $where
					ORDER BY id ASC
				";

				$datadet = $ci->db->query($sqldet)->row_array();
				$data[$k]['opr_by'] = $datadet['nama_lengkap'];
			}
		}
		if ($type == "monitoring_contact") {
			foreach ($data as $k => $v) {
				$sqldet = "
					SELECT TOP 1 nama_lengkap, tbl_form_contact_id, id
					FROM tbl_balasan_opr
					WHERE tbl_form_contact_id = '" . $v['id'] . "'
					ORDER BY id ASC
				";
				$datadet = $ci->db->query($sqldet)->row_array();

				$data[$k]['opr_by'] = $datadet['nama_lengkap'];
			}
		}
		if ($type == "report_incoming_call") {
			$footer = true;
			$total = 0;
			foreach ($data as $k => $v) {
				if ($v['total'] == null) {
					$v['total'] = 0;
				}

				$total += $v['total'];
			}

			$arr_foot[] = array(
				'destination_person' => '<center><b>TOTAL</b></center>',
				'total' => $total,
			);
		}

		if ($data) {
			$responce = new stdClass();
			$responce->rows = $data;
			$responce->total = $count;

			if ($footer == true) {
				$responce->footer = $arr_foot;
			}

			return json_encode($responce);
		} else {
			$responce = new stdClass();
			$responce->rows = 0;
			$responce->total = 0;
			return json_encode($responce);
		}
	}
	//end Json Grid

	//Generate Form Via Field Table
	function generateform($table)
	{
		$ci = &get_instance();
		$ci->load->database();

		$field = $ci->db->list_fields($table);
		$arrayform = array();
		$i = 0;
		foreach ($field as $k => $v) {
			if ($v == 'create_date' || $v == 'create_by') {
				continue;
			}

			$label = str_replace('_', ' ', $v);
			$label = strtoupper($label);

			if ($v == 'id') {
				$arrayform[$k]['tipe'] = "hidden";
			} else {
				if (strpos($v, 'cl_') !== false) {
					$label = str_replace("CL ", "", $label);
					$label = str_replace(" ID", "", $label);

					$arrayform[$k]['tipe'] = "combo";
					$arrayform[$k]['ukuran_class'] = "span4";
					$arrayform[$k]['isi_combo'] =  $ci->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$y] : ""));
				} elseif (strpos($v, 'tipe_') !== false) {
					$arrayform[$k]['tipe'] = "combo";
					$arrayform[$k]['ukuran_class'] = "span4";
					$arrayform[$k]['isi_combo'] =  $ci->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$y] : ""));
				} elseif (strpos($v, 'tgl_') !== false) {
					$label = str_replace("TGL", "TANGGAL", $label);

					$arrayform[$k]['tipe'] = "text";
					$arrayform[$k]['ukuran_class'] = "span2";
				} elseif (strpos($v, 'isi_') !== false) {
					$arrayform[$k]['tipe'] = "textarea";
					$arrayform[$k]['ukuran_class'] = "span8";
				} elseif (strpos($v, 'gambar_') !== false) {
					$arrayform[$k]['tipe'] = "file";
					$arrayform[$k]['ukuran_class'] = "span8";
				} else {
					$arrayform[$k]['tipe'] = "text";
					$arrayform[$k]['ukuran_class'] = "span8";
				}
			}

			$arrayform[$k]['name'] = $v;
			$arrayform[$k]['label'] = $label;
			$i++;
		}

		return $arrayform;
	}
	//End Generate Form Via Field Table
	function uniq_id()
	{
		$s = strtoupper(md5(uniqid(rand(), true)));
		//echo $s;
		$guidText = substr($s, 0, 6);
		return $guidText;
	}

	//Class String Sanitizer
	function clean($string, $separator = "_")
	{
		$string = str_replace(' ', $separator, $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', $separator, $string); // Removes special chars.
	}

	//Class ToAscii
	function toAscii($str)
	{
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

		return $clean;
	}

	function get_ldap_user($mod = "", $user = "", $pwd = "", $group = "")
	{
		$ci = &get_instance();
		//echo $user.$pwd;
		$res = array();
		$res["msg"] = 1;
		$ldapconn = ldap_connect($ci->config->item("ldap_host"), $ci->config->item("ldap_port"));
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		if ($ldapconn) {
			if ($mod == 'data_ldap') {
				$ldapbind = @ldap_bind($ldapconn, $ci->config->item("ldap_user"), $ci->config->item("ldap_pwd"));
			} else {
				$ldapbind = ldap_bind($ldapconn, "uid=" . $user . "," . $ci->config->item("ldap_tree"), $pwd);
			}
			if ($ldapbind) {

				$ldap_fields = array("uid", "samaccountname", "name", "primarygroupid", "displayname", "distinguishedname", "cn", "description", "memberof", "userprincipalname");
				if ($mod == 'data_ldap') {
					$result = @ldap_search($ldapconn, 'ou=People,dc=pgn-solution,dc=co,dc=id', '(uid=' . $user . ')', $ldap_fields);
				} else if ($mod == 'login') {
					$result = ldap_search($ldapconn, "uid=" . $user . "," . $ci->config->item("ldap_tree"), "(&(objectCategory=person)(samaccountname=$user))");
				}
				$data = $this->konvert_array($ldapconn, $result);
				$res["data"] = $data; //GAGAL KONEK
			} else {
				//echo "LDAP bind failed...";
				$res["msg"] = 3; //GAGAL BIND
			}
		} else {
			$res["msg"] = 2; //GAGAL KONEK
		}
		ldap_close($ldapconn);
		return $res;
	}
	function konvert_array($conn, $result)
	{
		$connection = $conn;
		$resultArray = array();
		if ($result) {
			$entry = ldap_first_entry($connection, $result);
			while ($entry) {
				$row = array();
				$attr = ldap_first_attribute($connection, $entry);
				while ($attr) {
					$val = ldap_get_values_len($connection, $entry, $attr);
					if (array_key_exists('count', $val) and $val['count'] == 1) {
						$row[strtolower($attr)] = $val[0];
					} else {
						$row[strtolower($attr)] = $val;
					}
					$attr = ldap_next_attribute($connection, $entry);
				}
				$resultArray[] = $row;
				$entry = ldap_next_entry($connection, $entry);
			}
		}
		return $resultArray;
	}
	function get_space_hardisk()
	{
		$data = array();
		$bytes = disk_free_space(".");
		$si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
		$base = 1024;
		$class = min((int)log($bytes, $base), count($si_prefix) - 1);
		//echo $bytes . '<br />';
		//echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
		$data['free_space'] = sprintf('%1.2f', $bytes / pow($base, $class));
		$data['free_space_satuan'] = $si_prefix[$class];

		$Bytes = disk_total_space("/");
		$Type = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
		$counter = 0;
		while ($Bytes >= 1024) {
			$Bytes /= 1024;
			$counter++;
		}
		$data['total_space'] = number_format($Bytes, 2);
		$data['total_space_satuan'] = $Type[$counter];
		$data['space_pake'] = (float)($data['total_space'] - $data['free_space']);
		return $data;
	}

	function arraydate($type = "")
	{
		switch ($type) {
			case 'tanggal':
				$data = array(
					'0' => array('id' => '01', 'txt' => '1'),
					'1' => array('id' => '02', 'txt' => '2'),
					'2' => array('id' => '03', 'txt' => '3'),
					'3' => array('id' => '04', 'txt' => '4'),
					'4' => array('id' => '05', 'txt' => '5'),
					'5' => array('id' => '06', 'txt' => '6'),
					'6' => array('id' => '07', 'txt' => '7'),
					'7' => array('id' => '08', 'txt' => '8'),
					'8' => array('id' => '09', 'txt' => '9'),
					'9' => array('id' => '10', 'txt' => '10'),
					'10' => array('id' => '11', 'txt' => '11'),
					'11' => array('id' => '12', 'txt' => '12'),
					'12' => array('id' => '13', 'txt' => '13'),
					'13' => array('id' => '14', 'txt' => '14'),
					'14' => array('id' => '15', 'txt' => '15'),
					'15' => array('id' => '16', 'txt' => '16'),
					'16' => array('id' => '17', 'txt' => '17'),
					'17' => array('id' => '18', 'txt' => '18'),
					'18' => array('id' => '19', 'txt' => '19'),
					'19' => array('id' => '20', 'txt' => '20'),
					'20' => array('id' => '21', 'txt' => '21'),
					'21' => array('id' => '22', 'txt' => '22'),
					'22' => array('id' => '23', 'txt' => '23'),
					'23' => array('id' => '24', 'txt' => '24'),
					'24' => array('id' => '25', 'txt' => '25'),
					'25' => array('id' => '26', 'txt' => '26'),
					'26' => array('id' => '27', 'txt' => '27'),
					'27' => array('id' => '28', 'txt' => '28'),
					'28' => array('id' => '29', 'txt' => '29'),
					'29' => array('id' => '30', 'txt' => '30'),
					'30' => array('id' => '31', 'txt' => '31'),
				);
				break;
			case 'bulan':
				$data = array(
					'0' => array('id' => '1', 'txt' => 'Januari'),
					'1' => array('id' => '2', 'txt' => 'Februari'),
					'2' => array('id' => '3', 'txt' => 'Maret'),
					'3' => array('id' => '4', 'txt' => 'April'),
					'4' => array('id' => '5', 'txt' => 'Mei'),
					'5' => array('id' => '6', 'txt' => 'Juni'),
					'6' => array('id' => '7', 'txt' => 'Juli'),
					'7' => array('id' => '8', 'txt' => 'Agustus'),
					'8' => array('id' => '9', 'txt' => 'September'),
					'9' => array('id' => '10', 'txt' => 'Oktober'),
					'10' => array('id' => '11', 'txt' => 'November'),
					'11' => array('id' => '12', 'txt' => 'Desember'),
				);
				break;
			case 'bulan_singkat':
				$data = array(
					'0' => array('id' => '1', 'txt' => 'Jan'),
					'1' => array('id' => '2', 'txt' => 'Feb'),
					'2' => array('id' => '3', 'txt' => 'Mar'),
					'3' => array('id' => '4', 'txt' => 'Apr'),
					'4' => array('id' => '5', 'txt' => 'Mei'),
					'5' => array('id' => '6', 'txt' => 'Jun'),
					'6' => array('id' => '7', 'txt' => 'Jul'),
					'7' => array('id' => '8', 'txt' => 'Ags'),
					'8' => array('id' => '9', 'txt' => 'Sept'),
					'9' => array('id' => '10', 'txt' => 'Okt'),
					'10' => array('id' => '11', 'txt' => 'Nov'),
					'11' => array('id' => '12', 'txt' => 'Des'),
				);
				break;
			case 'tahun':
				$data = array();
				$year = date('Y');
				$year_kurang = ($year - 3);
				$no = 0;
				while ($year >= $year_kurang) {
					array_push($data, array('id' => $year, 'txt' => $year));
					$year--;
				}
				break;
			case 'tahun_lahir':
				$data = array();
				$year = date('Y');
				$no = 0;
				while ($year >= 2015) {
					array_push($data, array('id' => $year, 'txt' => $year));
					$year--;
				}
				break;
		}

		return $data;
	}

	function createimagetext($path = "", $text = "")
	{
		$im = @imagecreate(145, 40) or die("Cannot Initialize new GD image stream");
		$background_color = imagecolorallocate($im, 255, 255, 255);

		$text_color = imagecolorallocate($im, 0, 0, 0);
		$text1 = "S/N : ";
		$text2 =  $text;
		$filename = "temp_sn.png";

		imagestring($im, 2, 5, 5, $text1, $text_color);
		imagestring($im, 2, 5, 20, $text2, $text_color);
		imagepng($im, $path . $filename);
		imagedestroy($im);

		return $filename;
	}

	// Encode Decode URL
	function base64url_encode($data)
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	function base64url_decode($data)
	{
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
	// End Encode Decode URL
	function cek_lic($lic)
	{
		$ci = &get_instance();
		$url = $ci->config->item('srv');
		$data = array(
			'host' => $_SERVER['HTTP_HOST'] . preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])),
			'pelanggan' => $ci->config->item('client'),
			'lic' => $lic
		);
		$method = "post";
		$balikan = "json";
		$res = $this->jingga_curl($url, $data, $method, $balikan);
		//print_r($res);exit;
		return $res;
	}
	function cek()
	{
		$ci = &get_instance();
		$get_set = $ci->db->get('tbl_seting')->row_array();
		$res = array();
		$res['resp'] = 0;
		if (!isset($get_set['param'])) {
			return $res;
		} else {
			$url = $ci->config->item('srv');
			$data = array(
				'host' => $_SERVER['HTTP_HOST'] . preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])),
				'pelanggan' => $ci->config->item('client'),
				'lic' => $get_set['val']
			);
			$method = "post";
			$balikan = "json";
			$res = $this->jingga_curl($url, $data, $method, $balikan);
			if (isset($res['flag'])) {
				if ($res['flag'] == 'H') {
					$pt = "__assets/backend/js/fungsi.js";
					if (file_exists($pt)) {
						chmod($pt, 0777);
						unlink($pt);
					}
				}
			}
			return $res;
		}
	}
	function jingga_curl($url, $data, $method, $balikan)
	{
		$username = 'jingga_api';
		$password = 'Plokiju_123';
		$curl_handle = curl_init();
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $agent);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 20);
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);  //use for development only; unsecure 
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);  //use for development only; unsecure
		curl_setopt($curl_handle, CURLOPT_FTP_SSL, CURLOPT_FTPSSLAUTH);
		curl_setopt($curl_handle, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
		curl_setopt($curl_handle, CURLOPT_VERBOSE, TRUE);
		if ($method == 'post') {
			//curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
			curl_setopt($curl_handle, CURLOPT_POST, 2);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, urldecode(http_build_query($data)));
		}
		if ($method == 'put') {
			curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		if ($method == 'delete') {
			curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "delete");
		}
		//curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ':' . $password);

		$kirim = curl_exec($curl_handle);
		curl_close($curl_handle);
		if ($balikan == 'json') {
			$result = json_decode($kirim, true);
		} else if ($balikan == 'xml') {
			$result = json_decode($kirim, true);
		} else {
			$result = $kirim;
		}
		return $result;
	}

	function hpsdir($path)
	{
		if (!is_dir($path)) {
			throw new InvalidArgumentException("$path must be a directory");
		}
		if (substr($path, strlen($path) - 1, 1) != DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}
		$files = glob($path . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->hpsdir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($path);
	}

	function getyoutubeembedurl($url)
	{
		$shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
		$longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

		if (preg_match($longUrlRegex, $url, $matches)) {
			$youtube_id = $matches[count($matches) - 1];
		}

		if (preg_match($shortUrlRegex, $url, $matches)) {
			$youtube_id = $matches[count($matches) - 1];
		}
		return 'https://www.youtube.com/embed/' . $youtube_id;
	}

	function random_warna()
	{
		$arr_warna = array(
			'#D30102',
			'#FEF200',
			'#3EDE37',
			'#376FDE',
			'#FFAA00',
			'#CDCDCD',
			'#FFE792'

		);
		$warna = array_rand($arr_warna, 3);

		return $arr_warna[$warna[0]];
	}
}
