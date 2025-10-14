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

			// echo "<pre>";print_r($cek_user);exit; MENGGUNAKAN ENCRYPSI SH256

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
}
