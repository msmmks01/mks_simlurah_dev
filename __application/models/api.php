<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Model
{

	public function get_penilaian_rtrw($kelurahan_id,$bulan,$status)
	{

		$tahun_login = date('Y');

		$filter_status = "";

		if($status == "RT"){
			$filter_status = "AND a.jab_rt_rw LIKE '%RT%'";
		}

		if($status == "RW"){
			$filter_status = "AND a.jab_rt_rw LIKE '%RW%'";
		}

		$sql = "SELECT 
					a.nik AS NIK,
					a.nama_lengkap AS NAMA,
					a.alamat AS ALAMAT,
					a.no_npwp AS NPWP,
					a.no_hp AS NO_HP,
					a.no_rekening AS NOREK,

					CEIL(SUM(b.nilai)/COUNT(b.id)) AS JUMLAH,

					a.jab_rt_rw AS STATUS,
					b.bulan AS BULAN,
					a.cl_kelurahan_desa_id AS KELURAHAN_ID

					FROM tbl_penilaian_rt_rw b
					JOIN tbl_data_rt_rw a ON a.id = b.tbl_data_rt_rw_id

					WHERE
					a.cl_kelurahan_desa_id = '$kelurahan_id'
					AND a.status = 'Aktif'
					AND a.pilih_tahun = '$tahun_login'
					AND b.bulan = '$bulan'
					$filter_status

					GROUP BY a.id";

		return $this->db->query($sql)->result();
	}

}