<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pbb extends JINGGA_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('encrypt', 'lib'));
	}

	public function index()
	{
		$this->nsmarty->assign('base_url', base_url());
		$this->nsmarty->assign('data', array(
			'judul' => 'CEK PBB',
			'hasil' => null
		));
		$this->nsmarty->display('backend/form/menu_cek_pbb.html');
	}

	public function get_data_pbb()
	{
		// pastikan ini dipanggil via POST
		$nop   = trim($this->input->post('nop'));
		$tahun = $this->input->post('tahun') ? $this->input->post('tahun') : date('Y');

		// jika nop kosong, kembalikan error JSON langsung
		if (empty($nop)) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'error', 'message' => 'NOP harus diisi.']);
			return;
		}

		$url = "https://pakinta.makassarkota.go.id/api/data/check";

		$data = [
			"jenis_pajak" => "pbbp2",
			"nop" => "$nop",
			"tahun_pajak" => "$tahun",
			"merchant" => "MSM"
		];

		$ch = curl_init();

		// URL tujuan
		curl_setopt($ch, CURLOPT_URL, $url);

		// Kembalikan hasil sebagai string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Metode POST
		curl_setopt($ch, CURLOPT_POST, true);

		// Data body dalam format JSON
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		// Header
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer 8f5f90ec1ba148d8cb39fc9749993f6b'
		]);

		// untuk development lokal (XAMPP) jika sertifikat bermasalah:
		// Hanya pakai ini untuk test di localhost. Hapus di production.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$response = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		header('Content-Type: application/json');

		if ($curl_errno) {
			echo json_encode(['status' => 'error', 'message' => 'cURL Error: '.$curl_error]);
			return;
		}

		// coba decode response API — jika sudah JSON, kirim balik apa adanya
		$decoded = json_decode($response, true);
	
		if (json_last_error() === JSON_ERROR_NONE) {
			// jika API mengirim struktur berbeda, kita normalisasi sedikit
			if (!isset($decoded['status'])) {
				// asumsi: jika ada 'data' maka sukses
				$decoded['status'] = isset($decoded['data']) ? 'success' : 'unknown';
			}
			echo json_encode($decoded);
			
			return;
		}

		// kalau bukan JSON, kirim raw dalam field message (untuk debugging)
		echo json_encode(['status' => 'error', 'message' => 'Response API bukan JSON: '.substr($response,0,500)]);
	}

}
