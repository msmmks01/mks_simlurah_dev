<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends JINGGA_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('encrypt', 'lib'));

		$this->nsmarty->assign("main_css", $this->lib->assetsmanager('css', 'login'));
		$this->nsmarty->assign("main_js", $this->lib->assetsmanager('js', 'login'));
	}

	public function index()
	{
		$this->nsmarty->display('backend/main-login.html');
	}

	function get_data_pbb()
	{
		$data = $this->input->post(null, true);
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
		CURLOPT_POSTFIELDS =>'{
			"jenis_pajak":"pbbp2",
			"nop":"737103000801603340",
			"tahun_pajak":"2025",
			"merchant":"MSM"
		}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: 8f5f90ec1ba148d8cb39fc9749993f6b'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		if (curl_errno($curl)) {
			echo json_encode(array('status' => 'error', 'message' => "cURL Error (" . curl_errno($curl) . "): " . curl_error($curl)));
		} else {
			echo $response;
		}
		curl_close($curl);
	}

	
}
