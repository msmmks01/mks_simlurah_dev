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

	function loginnya()
	{
		$this->load->model('mbackend');
		$user = $this->db->escape_str($this->input->post('user'));
		$pass = $this->db->escape_str($this->input->post('pwd'));

		$tahun = $this->db->escape_str($this->input->post('tahun'));
		$error = false;
		//echo  $this->encrypt->encode('administrator');exit;
		if ($user && $pass) {
			//CEK LDAP
			$cek_user = $this->mbackend->getdata('data_login', 'row_array', $user);
			$cek_user['tahun'] = $tahun;

			// echo "<pre>";print_r($cek_user);exit;
			if (count($cek_user) > 0) {
				if ($pass == $this->encrypt->decode($cek_user['password'])) {
					$this->session->set_userdata('s3ntr4lb0', base64_encode(serialize($cek_user)));
				} else {
					$error = true;
					$this->session->set_flashdata('error', 'Password Tidak Benar');
				}
			} else {
				$this->session->set_flashdata('error', 'User Tidak Terdaftar');
			}
		} else {
			$error = true;
			$this->session->set_flashdata('error', 'Isi User Dan Password');
		}

		header("Location: " . $this->host . "obackendpage");
	}

	function logoutnya()
	{
		$this->session->unset_userdata('puskesmaskemayoran', 'limit');
		$this->session->sess_destroy();
		header("Location: " . $this->host . "");
	}

	function cek_lic()
	{
		$lic = $this->db->escape_str($this->input->post('lic'));
		$tok = $this->lib->cek_lic($lic);
		//print_r($tok);exit;
		if ($tok['resp'] == 1) {
			$data = array('param' => "TK", 'val' => $tok['token']);
			$this->db->insert('tbl_seting', $data);
		} else {
			$error = true;
			$this->session->set_flashdata('error', $tok['token']);
		}
		header("Location: " . $this->host);
	}

	function test2()
	{
		//echo $this->db->dbdriver;
		echo $_SERVER['HTTP_HOST'] . preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME']));
	}

	function get_token_retribusi()
	{
		$data = $this->input->post(null, true);
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://157.119.222.163/retribusi_sampah/api/login',
			// CURLOPT_URL => 'http://103.186.32.74',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('email' => $data['email'], 'password' => $data['password']),
		));
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			echo json_encode(array('status' => 'error', 'message' => "cURL Error (" . curl_errno($curl) . "): " . curl_error($curl)));
		} else {
			echo $response;
		}
		curl_close($curl);
	}

	public function do_login_mr()
	{

		$username = $this->input->post('username_mr');
		$password = $this->input->post('password_mr');
		// panggil API App A
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://app.kotamakassar.id/auth/login_api");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
			'username' => $username,
			'password' => $password
		]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($response, true);

		if ($result['status']) {
			// sukses → bisa redirect langsung ke App A
			redirect("https://app.kotamakassar.id/admin");
		} else {
			// gagal → tampilkan error
			$this->session->set_flashdata('error', $result['message']);
			redirect('login');
		}
	}
}
