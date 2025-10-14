<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Esign extends JINGGA_Controller
{
    function __construct()
    {

        parent::__construct();
    }



    function scan()
    {
        $register = $this->input->get('k');
        $register = decrypt_url($register);
        $res = $this->db->where("concat(nomor_register,'_',cl_kelurahan_desa_id)=", $register)->get('tbl_register_esign');
        if ($res->num_rows() > 0) {
            $data['data_register'] = $res->row();
            $this->load->view('backend/esign/preview_register.php', $data);
        } else {
            redirect(base_url());
        }
    }

    public function get_qrcodeOLD()
    {
        $key = $this->input->get('nomor_register');
        $this->load->library('Ciqrcode');
        $img  = QRcode::png(base_url('esign/scan?key=' . $key), false, 'M', 5, 1, true);
        // QRcode::png('text',$save_path,$level,$size,$margin,$saveandprint,);
        return $img;
    }

    public function get_data_sertifikat()
    {
        $path = $this->input->post('path');
        $this->load->helper('file');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => '103.151.191.67/api/sign/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'signed_file' => new CURLFILE(FCPATH . $path, 'application/pdf', basename($path)),
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic c21hcnRvZmZpY2V2MzpiaXNtaWxsYWg='
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function get_qrcode()
    {
        $key = '0001';
        $logo = "__assets/images/logo_qr.png";
        $forecolor = '0,0,0';
        $backcolor = '255,255,255';
        $text = base_url('qscan?k=' . $key);
        $dir  = date('Ymd');
        if (!is_dir('./__data/' . $dir)) {
            mkdir('./__data/' . $dir, 0755);
        }
        // $path = "__data/$dir/qr_logo.png";
        $path = false;
        $this->load->library('Ciqrcode');
        QRcode::png($text, $path, "H", 5, 2, 0, $forecolor, $backcolor, $logo);
        if (!file_exists($path)) {
            return false;
        } else {
            return $path;
        }
    }


    function get_qrcode_keaslian()
    {
        $urlfile = base_url("cek-dokumen/" . $this->auth['cl_user_group_id'] . "/" . $this->auth['cl_kecamatan_id'] . "/" . $this->auth['cl_kelurahan_desa_id'] . "");
        $this->load->library('Qr_with_logo');

        $dir  = date('Ymd');
        $tempdir = FCPATH . "__data/" . $dir . "/";
        $filename = 'qr_' . md5($urlfile) . '.png';
        $filepath = $tempdir . $filename;


        $urfile = $urlfile; //Isi dari QRCode Saat discan
        $namafile = 'qrtoday.png'; //Nama file yang akan disimpan pada folder tempqr 
        $quality = 'H'; //Kualitas dari QRCode
        $ukuran = 8; //Ukuran besar QRCode
        $padding = 0;
        $logo = "__assets/images/logo_qr.png";
        $this->qr_with_logo->generate($urfile, $filepath, $logo);
        // QRCode::png($urfile, $tempdir . $namafile, $quality, $ukuran, $padding);
    }
}
