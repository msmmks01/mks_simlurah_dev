<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Simhonor extends JINGGA_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Api_simhonor');
    }

	public function get_data_penilaian_rt_rw()
	{
		$kelurahan_id = $this->input->get_post('KELURAHAN_ID');
		$bulan        = $this->input->get_post('BULAN');
		$status       = strtoupper($this->input->get_post('STATUS')); // RT / RW

		if (empty($kelurahan_id) || empty($bulan) || empty($status)) {
			return $this->_json([
				"status" => 0,
				"message" => "Parameter tidak lengkap",
				"data" => []
			]);
		}

		$data = $this->Api_simhonor->get_penilaian_rtrw($kelurahan_id,$bulan,$status);

		if (empty($data)) {
			return $this->_json([
				"status" => 0,
				"message" => "GAGAL KELURAHAN UNTUK BULAN $bulan BELUM DILAKUKAN PENILAIAN",
				"data" => []
			]);
		}

		return $this->_json([
			"status" => 1,
			"message" => "BERHASIL",
			"data" => $data
		]);
	}

	private function _json($data)
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}
}