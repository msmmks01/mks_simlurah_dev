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
			'judul' => 'CEK MENU PBB',
			'hasil' => null
		));
		$this->nsmarty->display('backend/form/menu_cek_pbb.html');
	}

	public function get_data_pbb()
	{
		$nop   = trim($this->input->post('nop'));
		$tahun = $this->input->post('tahun') ? $this->input->post('tahun') : date('Y');

		$payload = json_encode(array(
			"jenis_pajak" => "pbbp2",
			"nop" => $nop,
			"tahun_pajak" => $tahun,
			"merchant" => "MSM"
		));

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => 'https://pakinta.makassarkota.go.id/api/data/check',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: 8f5f90ec1ba148d8cb39fc9749993f6b'
			),
		));

		$response = curl_exec($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($curl_error) {
			$result = array('status' => 'error', 'message' => $curl_error);
		} else {
			$result = json_decode($response, true);
		}

		// kirim hasil ke view
		$data = array(
			'judul' => 'CEK MENU PBB',
			'nop'   => $nop,
			'tahun' => $tahun,
			'hasil' => $result
		);

		// ✅ tambahkan baris ini
		$this->nsmarty->assign('base_url', base_url());

		$this->nsmarty->assign('data', $data);
		$this->nsmarty->display('backend/form/menu_cek_pbb.html');
	}

}
