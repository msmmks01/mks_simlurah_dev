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

		$payload = '{
				"jenis_pajak":"pbbp2",
				"nop":"'.$nop.'",
				"tahun_pajak":"'.$tahun.'",
				"merchant":"MSM"
			}';

		$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://pakinta.makassarkota.go.id/api/data/check',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			 CURLOPT_POSTFIELDS =>$payload,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: 8f5f90ec1ba148d8cb39fc9749993f6b'
			),
			));

			$response = curl_exec($curl);

curl_close($curl);
		var_dump($response);
		exit();
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
