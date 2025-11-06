<?php
if (!function_exists('ttd')) {
    function ttd()
    {
        $ci = &get_instance();
        $ci->auth = unserialize(base64_decode($ci->session->userdata('s3ntr4lb0')));
        $cl_kecamatan_id = $ci->auth['cl_kecamatan_id'];
        $cl_kelurahan_desa_id = $ci->auth['cl_kelurahan_desa_id'];
        $ci->load->database();
        $res = $ci->db->where([
            'a.cl_kecamatan_id' => $cl_kecamatan_id,
            'a.cl_kelurahan_desa_id' => $cl_kelurahan_desa_id,
        ])->join('tbl_setting_apps b', "a.nip=b.nip_kepala_desa and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')->get('tbl_data_penandatanganan a');

        $html = "<select class=\"form-control-sm\" style=\"width: 100%\" id='pil_ttd'>";
        $html .= "<option value=\"\">Pilih Penandatanganan</option>";
        foreach ($res->result() as $row) {
            if ($row->nip == $row->nip_kepala_desa) {
                $html .= "<option selected value=\"$row->nip\">$row->nip - $row->nama</option>";
            } else {
                $html .= "<option value=\"$row->nip\">$row->nip - $row->nama</option>";
            }
        }
        $html .= "</select>";

        return $html;
    }
}

if (!function_exists('ttd_nip')) {
    function ttd_nip($nip)
    {
        $ci = &get_instance();
        $ci->auth = unserialize(base64_decode($ci->session->userdata('s3ntr4lb0')));
        $cl_kecamatan_id = $ci->auth['cl_kecamatan_id'];
        $cl_kelurahan_desa_id = $ci->auth['cl_kelurahan_desa_id'];
        $ci->load->database();
        $res = $ci->db->where([
            'a.cl_kecamatan_id' => $cl_kecamatan_id,
            'a.nip' => $nip,
            'a.cl_kelurahan_desa_id' => $cl_kelurahan_desa_id,
        ])->join('tbl_setting_apps b', "a.nip=b.nip_kepala_desa and a.cl_kecamatan_id=b.cl_kecamatan_id and a.cl_kelurahan_desa_id=b.cl_kelurahan_desa_id ", 'left')->get('tbl_data_penandatanganan a');

        $html = "<select class=\"form-control-sm\" style=\"width: 100%\" id='pil_ttd'>";
        $html .= "<option value=\"\">Pilih Penandatanganan</option>";
        foreach ($res->result() as $row) {
            if ($row->nip == $row->nip_kepala_desa) {
                $html .= "<option selected value=\"$row->nip\">$row->nip - $row->nama</option>";
            } else {
                $html .= "<option value=\"$row->nip\">$row->nip - $row->nama</option>";
            }
        }
        $html .= "</select>";

        return $html;
    }
}

if (!function_exists('ttd_1')) {
    function ttd_1($data, $setting)
    {
        $ttd_pengirim = '<br>';
        if (isset($data['surat']['info_tambahan']['ttd_srikandi']) && $data['surat']['info_tambahan']['ttd_srikandi'] != '') {
            $data['pemohon']['nama'] = '${nama_pengirim}';
            $ttd_pengirim = '<br><br><br>${ttd_pengirim}<br><br><br><br>';
        }
        $html = "<div id=\"ttd\" style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 16px;\">
                        <table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
                            <tr>
                                <td width=\"100%\" align=\"right\">";
        if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
            $html .= "<table style=\"border-collapse: collapse;width: 350px;\" border=\"0\" cellpadding=\"0\">";
        } else {
            $html .= "<table style=\"border-collapse: collapse;width:auto;\" border=\"0\" cellpadding=\"0\">";
        }
        $html .= " <tr>
                        <td align=\"left\">
                            <table style=\"float: right;\" border=\"0\" cellspacing=\"0\">
                                        <tr>
                                            <td></td>
                                            <td>
                                                " . ucwords(strtolower(str_replace('KOTA', '', $setting['nama_kab_kota']))) . ", " . $data['surat']['tanggal_surat'] . "
                                            </td>
                                        </tr>";

                                        for ($i = 0; $i < count($data['ttd']); $i++) {
                                            $html .= "                  <tr>
                                                <td align=\"right\">" . $data['ttd'][$i]['start'] . "</td><td>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</td>
                                            </tr>";
                                        }
                                        $html .= "
                                        <tr>
                                            <td></td>
                                            <td valign=\"middle\" style=\"height:70px;padding-left:20px;padding-bottom:10px;\">$ttd_pengirim</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                " . $data['pemohon']['nama'] . "
                                            </td>
                                        </tr>
                                        <tr> 
                                            <td></td>  
                                            <td>
                                                Pangkat: " . $data['pemohon']['pangkat'] . "
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                NIP: " . $data['pemohon']['nip'] . "
                                            </td>
                                        </tr>
                                    </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>";
        return $html;
    }
}

// if (!function_exists('ttd_1muf')) {
//     function ttd_1muf($data, $setting, $tgl_cetak)
//     {
//         $html = "<div  id=\"ttd\" style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
//                         <table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
//                             <tr>
//                                 <td width=\"100%\" align=\"right\">";
//         if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
//             $html .= "<table style=\"border-collapse: collapse;width: 350px;\" border=\"0\" cellpadding=\"0\">";
//         } else {
//             $html .= "<table style=\"border-collapse: collapse;\" border=\"0\" cellpadding=\"0\">";
//         }
//         $html .= " <tr>
//                         <td align=\"left\">
//                             <table style=\"float: right;\" border=\"0\" cellspacing=\"0\">
//                                 <tr>
//                                     <td></td>
//                                     <td>
//                                         " . ucwords(strtolower(str_replace('KOTA', '', $setting['nama_kab_kota']))) . ", " . tgl_indo($tgl_cetak) . "
//                                     </td>
//                                 </tr>";

//                                             for ($i = 0; $i < count($data['ttd']); $i++) {
//                                                 $html .= "<tr><td align=\"right\"><b>" . $data['ttd'][$i]['start'] . "</b></td><td><b>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</b></td></tr>";
//                                             }
//                                             $html .= "
//                                                     <tr>
//                                                         <td></td>
//                                                         <td valign=\"middle\" style=\"height:70px;padding-left:70px;padding-bottom:10px;color:white\">~<br></td>
//                                                     </tr>
//                                                     <tr>
//                                                         <td></td>
//                                                         <td>
//                                                             <u><b>" . $setting['nama'] . "</b></u>
//                                                             <br>
//                                                             Pangkat: " . $setting['pangkat'] . "
//                                                             <br>
//                                                             NIP: " . $setting['nip'] . "
//                                                         </td>
//                                                     </tr>
//                                                 </table>
//                                             </td>
//                                         </tr>
//                                     </table>
//                                 </td>
//                             </tr>
//                         </table>
//                     </div>";
//         return $html;
//     }
// }

if (!function_exists('ttd_1muf')) {
    function ttd_1muf($data, $setting, $tgl_cetak)
    {
        $html = "<div id=\"ttd\" style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
                    <table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
                        <tr>
                            <td width=\"100%\" align=\"right\">";

        if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || 
            (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
            $html .= "<table style=\"border-collapse: collapse;width: 350px;\" border=\"0\" cellpadding=\"0\">";
        } else {
            $html .= "<table style=\"border-collapse: collapse;\" border=\"0\" cellpadding=\"0\">";
        }

        $html .= "<tr>
                    <td align=\"left\">
                        <table style=\"float: right;\" border=\"0\" cellspacing=\"0\">
                            <tr>
                                <td></td>
                                <td>
                                    " . ucwords(strtolower(str_replace('KOTA', '', $setting['nama_kab_kota']))) . ", " . tgl_indo($tgl_cetak) . "
                                </td>
                            </tr>";

        // baris ttd (misal: bila ada baris khusus di data['ttd'])
        for ($i = 0; $i < count($data['ttd']); $i++) {
            $html .= "<tr>
                        <td></td>
                        <td><b>" . $data['ttd'][$i]['center'] . "</b></td>
                    </tr>";
        }

        // tampilkan dulu jabatan_ttd (mis. "a.n Lurah") jika tersedia,
        // jika tidak ada gunakan fallback 'a.n Lurah'
        $jabatan_ttd = isset($setting['jabatan_ttd']) && $setting['jabatan_ttd'] !== '' 
                        ? $setting['jabatan_ttd'] 
                        : 'a.n Lurah';

        $html .= "<tr>
                    <td></td>
                    <td><b>" . $jabatan_ttd . "</b></td>
                </tr>";

        // lalu tampilkan jabatan asli (mis. "KASI PEMERINTAHAN...") jika ada
        if (!empty($setting['jabatan_asli'])) {
            // pakai strtoupper agar sesuai contoh
            $html .= "<tr>
                        <td></td>
                        <td><b>" . strtoupper($setting['jabatan_asli']) . "</b></td>
                      </tr>";
        }

        $html .= "
                <tr>
                    <td></td>
                    <td valign=\"middle\" style=\"height:70px;padding-left:70px;padding-bottom:10px;color:white\">~<br></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <u><b>" . $setting['nama'] . "</b></u><br>
                        Pangkat: " . $setting['pangkat'] . "<br>
                        NIP: " . $setting['nip'] . "
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
</div>";

        return $html;
    }
}


if (!function_exists('ttd_xalamat')) {
    function ttd_xalamat($data, $setting)
    {
        $html = "<div id=\"ttd\" style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
                        <table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
                            <tr>
                                <td width=\"100%\" align=\"right\">";
        if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
            $html .= "<table style=\"border-collapse: collapse;width: 350px;\" border=\"0\" cellpadding=\"0\">";
        } else {
            $html .= "<table style=\"border-collapse: collapse;\" border=\"0\" cellpadding=\"0\">";
        }
        $html .= " <tr>
                        <td align=\"left\">
                            <table style=\"float: right;\" border=\"0\" cellspacing=\"0\">
                                
                                ";

        for ($i = 0; $i < count($data['ttd']); $i++) {
            $html .= "<tr><td align=\"right\"><b>" . $data['ttd'][$i]['start'] . "</b></td><td><b>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</b></td></tr>";
        }
        $html .= "
                                                    <tr>
                                                        <td></td>
                                                        <td valign=\"middle\" style=\"height:70px;padding-left:70px;padding-bottom:10px;color:white\">~<br></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <u><b>" . $data['pemohon']['nama'] . "</b></u>
                                                            <br>
                                                            Pangkat: " . $data['pemohon']['pangkat'] . "
                                                            <br>
                                                            NIP: " . $data['pemohon']['nip'] . "
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>";
        return $html;
    }
}

if (!function_exists('ttd_2')) {
    // function ttd_2($data, $setting)
    // {
    //     $html = "<div style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
    //                     <table style=\"border-collapse: collapse;\" border=\"1\" cellpadding=\"0\">
    //                         <tr>
    //                             <td width=\"100%\" align=\"right\">";
    //     if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
    //         $html .= "<table style=\"border-collapse: collapse;width: 350px;color:res;\" border=\"1\" cellpadding=\"0\">";
    //     } else {
    //         $html .= "<table style=\"border-collapse: collapse;color:blue; font-size:12pt;\" border=\"1\" cellpadding=\"0\">";
    //     }
    //     $html .= " <tr>
    //                                         <td align=\"left\">
    //                                             <table style=\"float: right;\" border=\"1\" cellspacing=\"0\">";

    //     for ($i = 0; $i < count($data['ttd']); $i++) {
    //         $html .= "<tr><td align=\"right\"><b>" . $data['ttd'][$i]['start'] . "</b></td><td><b>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</b></td></tr>";
    //     }
    //     $html .= "
    //                                                 <tr>
    //                                                     <td></td>
    //                                                     <td valign=\"middle\" style=\"height:70px;padding-left:70px;padding-bottom:10px;color:white\">~<br></td>
    //                                                 </tr>
    //                                                 <tr>
    //                                                     <td></td>
    //                                                     <td>
    //                                                         <u><b>" . $data['pemohon']['nama'] . "</b></u>
    //                                                         <table style=\"border-collapse: collapse;\" border=\"0\" cellpadding=\"0\">
    //                                                             <tr>
    //                                                                 <td>Pangkat</td>
    //                                                                 <td>&nbsp;:&nbsp;</td>
    //                                                                 <td>" . $data['pemohon']['pangkat'] . "</td>
    //                                                             </tr>
    //                                                             <tr>
    //                                                                 <td>NIP</td>
    //                                                                 <td>&nbsp;:&nbsp;</td>
    //                                                                 <td>" . $data['pemohon']['nip'] . "</td>
    //                                                             </tr>
    //                                                         </table>
    //                                                     </td>
    //                                                 </tr>
    //                                             </table>
    //                                         </td>
    //                                     </tr>
    //                                 </table>
    //                             </td>
    //                         </tr>
    //                     </table>
    //                 </div>";
    //     return $html;
    // }

    function ttd_2($data, $setting)
    {
        $html = "<div id=\"ttd\" style=\"padding-right: 10.82mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
        <table table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
        <tr>
            <td width=\"100%\" align=\"right\">";
        if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
            $html .= "<table style=\"border-collapse: collapse;width: 350px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
        } else {
            $html .= "<table style=\"border-collapse: collapse;width: auto;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
        }
        for ($i = 0; $i < count($data['ttd']); $i++) {
            $html .= "<tr><td align=\"right\" style=\"padding-right:0px\"><b>" . $data['ttd'][$i]['start'] . "</b></td><td align=\"left\"><b>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</b></td></tr>";
        }
        $html .= "
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                  
                    <tr>
                        <td></td>
                        <td align=\"left\" valign=\"bottom\" style=\"padding-bottom:0px;padding-top: 6px;\"><u><b>" . $data['pemohon']['nama'] . "</b></u></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align=\"left\" style=\"padding-left:0px\">
                            <table style=\"border-collapse: collapse;\" border=\"0\">
                                <tr>
                                    <td width=\"50px\">Pangkat, Gol</td>
                                    <td width=\"5\">:</td>
                                    <td width=\"164\">" . $data['pemohon']['pangkat'] . "</td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td>" . $data['pemohon']['nip'] . "</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>";
        return $html;
    }
}

if (!function_exists('ttd_fullkanan')) {

    function ttd_fullkanan($data, $setting)
    {

        $ttd_pengirim = '<br>';
        $padding_left = 'padding-left:20px;';
        if (isset($data['surat']['info_tambahan']['ttd_srikandi']) && $data['surat']['info_tambahan']['ttd_srikandi'] != '') {
            $data['pemohon']['nama'] = '${nama_pengirim}';
            $ttd_pengirim = '<br><br><br>${ttd_pengirim}<br><br><br><br>';
            switch ($data['surat']['cl_jenis_surat_id']) {
                default:
                    $padding_left = 'padding-left:20px;';
                    break;
            }
        }
        $html = "<div id=\"ttd\" style=\"padding-right: 0mm;padding-left: 15.44mm; padding-bottom: 1mm; font-size: 14px;\">
        <table style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
        <tr>
            <td width=\"100%\" align=\"right\">";
        
        $html .= "<table style=\"border-collapse: collapse;width: auto;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
        for ($i = 0; $i < count($data['ttd']); $i++) {
            $html .= "<tr><td align=\"left\" style=\"padding-right:0px\"><b>" . $data['ttd'][$i]['start'] . "</b></td><td align=\"left\"><b>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</b></td></tr>";
        }
        $html .= "
                    <tr>
                        <td>&nbsp;</td>
                        <td valign=\"middle\" style=\"height:70px;padding-left:20px;padding-bottom:10px;\">$ttd_pengirim</td>
                    </tr>
                  
                    <tr>
                        <td></td>
                        <td align=\"middle\" valign=\"bottom\" style=\"padding-bottom:0px;padding-top: 6px;white-space:nowrap\"><u><b>" . $data['pemohon']['nama'] . "</b></u></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align=\"left\" style=\"padding-left:0px\">
                            <table style=\"border-collapse: collapse;\" border=\"0\">
                                <tr>
                                    <td width=\"100px\">Pangkat, Gol</td>
                                    <td width=\"5\">:</td>
                                    <td>" . $data['pemohon']['pangkat'] . "</td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td>" . konversi_nip($data['pemohon']['nip'], ' ') . "</td> 
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table></div>";
        return $html;
    }
}

if (!function_exists('ttd_3')) {
    function ttd_3($data, $setting)
    {
        $ttd_pengirim = '<br>';
        if (isset($data['surat']['info_tambahan']['ttd_srikandi']) && $data['surat']['info_tambahan']['ttd_srikandi'] != '') {
            $data['pemohon']['nama'] = '${nama_pengirim}';
            $ttd_pengirim = '<br><br><br>${ttd_pengirim}<br><br><br><br>';
        }
        $html = "<div id=\"ttd\">
                        <table class=\"fs-custom fm-custom\" style=\"border-collapse: collapse;width: 100%;\" border=\"0\" cellpadding=\"0\">
                            <tr>
                                <td width=\"100%\" align=\"right\">";
        if ((count($data['ttd']) == 1 && strlen($data['ttd'][0]['center']) > 45) || (count($data['ttd']) == 2 && strlen($data['ttd'][1]['center']) > 45)) {
            $html .= "<table class=\"fs-custom fm-custom\" style=\"border-collapse: collapse;width: 60%;\" border=\"0\" cellpadding=\"0\">";
        } else {
            $html .= "<table class=\"fs-custom fm-custom\" style=\"border-collapse: collapse;\" border=\"0\" cellpadding=\"0\">";
        }
        $html .= " <tr>
                                            <td align=\"left\">
                                                <table class=\"fs-custom fm-custom\" style=\"float: right;\" border=\"0\" cellspacing=\"0\">
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            " . ucwords(strtolower(str_replace('KOTA', '', $setting['nama_kab_kota']))) . ", " . $data['surat']['tanggal_surat'] . "
                                                        </td>
                                                    </tr>";

                                                for ($i = 0; $i < count($data['ttd']); $i++) {
                                                    $html .= "<tr><td align=\"right\">" . $data['ttd'][$i]['start'] . "</td><td>" . $data['ttd'][$i]['center'] . $data['ttd'][$i]['end'] . "</td></tr>";
                                                }
                                                $html .= "
                                                    <tr>
                                                        <td></td>
                                                        <td valign=\"middle\" style=\"height:70px;padding-left:20px;padding-bottom:10px;\">$ttd_pengirim</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <u style=\"white-space:nowrap\">" . $data['pemohon']['nama'] . "</u>
                                                            <br>
                                                            Pangkat: " . $data['pemohon']['pangkat'] . "
                                                            <br>
                                                            NIP: " . $data['pemohon']['nip'] . "
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </div>";
        return $html;
    }
}

if (!function_exists('datepicker_range')) {
    function datepicker_range($tahun, $value)
    {
        $yeartoday = date('Y');
        $monthtoday = date('n');
        $today = date('j');
        if ($value == 'endDate') {
            if ($tahun <> $yeartoday) {
                return $tahun . ',12,31';
            } else {
                // return $tahun . ',' . ($monthtoday) . ',' . $today;
                return $tahun . ',12,31';
            }
        } elseif ($value == 'startDate') {
            return $tahun . ',1,1';
        } else {
            return '';
        }
    }
}

if (!function_exists('encrypt_url')) {
    function encrypt_url($string)
    {

        $output = false;
        /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */
        $security       = security_ini();
        $secret_key     = $security["encryption_key"];
        $secret_iv      = $security["iv"];
        $encrypt_method = $security["encryption_mechanism"];

        // hash
        $key    = hash("sha256", $secret_key);

        // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
        $iv     = substr(hash("sha256", $secret_iv), 0, 16);

        //do the encryption given text/string/number
        $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($result);
        return $output;
    }
}

if (!function_exists('decrypt_url')) {
    function decrypt_url($string)
    {

        $output = false;
        /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */

        $security       = security_ini();
        $secret_key     = $security["encryption_key"];
        $secret_iv      = $security["iv"];
        $encrypt_method = $security["encryption_mechanism"];

        // hash
        $key    = hash("sha256", $secret_key);

        // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
        $iv = substr(hash("sha256", $secret_iv), 0, 16);

        //do the decryption given text/string/number

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}

if (!function_exists('security_ini')) {
    function security_ini()
    {
        return array(
            'encryption_key' => 'b&%41f!v@',
            'iv' => 91827364,
            'encryption_mechanism' => 'aes-256-cbc'
        );
    }
}

if (!function_exists('nama')) {
    function nama($table, $field, $where)
    {
        $ci = &get_instance();
        $res = $ci->db->select($field)->where($where)->get($table)->row($field);
        return $res;
    }
}

if (!function_exists('format_nomor_surat')) {
    function format_nomor_surat($cl_kecamatan_id, $cl_kelurahan_desa_id, $cl_jenis_surat_id, $tanggal_surat, $nomor = '')
    {
        $romawi = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $ci = &get_instance();
        $ci->db->where('cl_kecamatan_id', $cl_kecamatan_id);
        $ci->db->where('cl_kelurahan_desa_id', $cl_kelurahan_desa_id);
        $ci->db->where('cl_jenis_surat_id', $cl_jenis_surat_id);
        $m = $ci->db->get('cl_nomor_surat')->row_array();
        if (count($m) > 0) {

            $m['tahun'] = date('Y', strtotime($tanggal_surat));
            $m['bulan'] = intval(date('m', strtotime($tanggal_surat)));

            if ($nomor == '') {
                $param = json_decode($m['param_nomor']);
                $ci->db->select("IFNULL(MAX(nomor),0)+1 as nomor");
                $ci->db->where('cl_kecamatan_id', $cl_kecamatan_id);
                $ci->db->where('cl_kelurahan_desa_id', $cl_kelurahan_desa_id);

                for ($i = count($param) - 1; $i >= 0; $i--) {
                    if ($param[$i] == 'cl_jenis_surat_id') {
                        $ci->db->where($param[$i], $m[$param[$i]]);
                    } else {
                        $ci->db->where("param_" . $param[$i], $m[$param[$i]]);
                    }
                }

                $m['nomor'] = $ci->db->get('tbl_data_surat')->row('nomor');
            } else {
                $m['nomor'] = $nomor;
            }
            $n = 0;
            $m['bulan'] = $romawi[$m['bulan']];
            $m['nomor_surat'] = '';
            foreach (json_decode($m['format_nomor']) as $key) {
                if ($key == 'nomor') {
                    $m[$key] = sprintf("%03d", $m[$key]);
                }
                if ($n == 0) {
                    $m['nomor_surat'] .= $m[$key];
                } else {
                    $m['nomor_surat'] .= "/" . $m[$key];
                }
                $n++;
            }
            $m['nomor'] = intval($m['nomor']);
            $m['bulan'] = intval(date('m', strtotime($tanggal_surat)));
            return $m;
        }
        return [];
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function nm_client()
{
    $res = explode('.', $_SERVER['HTTP_HOST']);
    if ($res[0] == 'www') {
        $res = $res[1];
    } else {
        $res = $res[0];
    }
    $data = [
        'localhost' => 'Biringkanaya',
        'biringkanayakec' => 'Biringkanaya',
        'bontoalakec' => 'Bontoala',
        'kepsangkarrangkec' => 'Kepulauan Sangkarrang',
        'makassarkec' => 'Makassar',
        'mamajangkec' => 'Mamajang',
        'manggalakec' => 'Manggala',
        'marisokec' => 'Mariso',
        'panakkukangkec' => 'Panakkukang',
        'rappocinikec' => 'Rappocini',
        'tamalanreakec' => 'Tamalanrea',
        'tamalatekec' => 'Tamalate',
        'tallokec' => 'Tallo',
        'ujungpandangkec' => 'Ujung Pandang',
        'ujungtanahkec' => 'Ujung Tanah',
        'wajokec' => 'Wajo',
        'simlurahdev' => 'Biringkanaya',
    ];
    return $data[$res];
}

function hitungSelisihTanggal($tgl_awal, $tgl_akhir)
{
    $awal = new DateTime($tgl_awal);
    $akhir = new DateTime($tgl_akhir);
    $interval = $awal->diff($akhir);
    return $interval->y . " Tahun, " . $interval->m . " Bulan, " . $interval->d . " Hari";
}

if (!function_exists('generate_unique_string')) {
    function generate_unique_string($length = 8)
    {
        $CI = &get_instance(); // Mengambil instance CodeIgniter
        $CI->load->database(); // Memuat database

        $allChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; // Karakter A-Z dan a-z

        // Ulangi hingga menemukan string yang tidak ada dalam database
        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $allChars[random_int(0, strlen($allChars) - 1)];
            }

            // Memeriksa apakah string ini ada di database
            $CI->db->where('kode_unik', $randomString);
            $query = $CI->db->get('tbl_data_surat'); // Ganti dengan tabel yang relevan

        } while ($query->num_rows() > 0); // Pastikan string tidak ada dalam database

        return $randomString;
    }
}

if (!function_exists('randomize_letters')) {
    function randomize_letters($digits)
    {
        // Definisikan rentang huruf yang diinginkan (A-Z, a-z)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $result = '';

        $totalLength = strlen($digits) * 2; // Menghasilkan dua kali panjang angka

        // Loop untuk menghasilkan string acak
        for ($i = 0; $i < $totalLength; $i++) {
            // Jika indeks genap, sisipkan angka
            if ($i % 2 == 0) {
                $result .= $digits[$i / 2];
            } else {
                $randomChar = $characters[rand(0, strlen($characters) - 1)];
                $result .= $randomChar;
            }
        }

        return $result;
    }
}
