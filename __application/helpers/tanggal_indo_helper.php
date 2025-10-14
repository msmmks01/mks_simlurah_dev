<?php
function tgl_indo($tanggal)
{
    $tanggal = str_replace(' ', '', $tanggal);
    if ($tanggal == '' || $tanggal == 'null' || $tanggal === null || strlen(trim($tanggal)) < 8 || strlen(trim($tanggal)) > 10) {
        return '';
    }
    
    $tanggal = date('Y-m-d',strtotime($tanggal));
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function bln_indo($tanggal)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    return $bulan[(int)$pecahkan[1]];
}
function  ubah_kata($word)
{
    switch ($word) {
        case  1:
            return  "Satu";
            break;
        case  2:
            return  "Dua";
            break;
        case  3:
            return  "Tiga";
            break;
        case  4:
            return  "Empat";
            break;
        case  5:
            return  "Lima";
            break;
        case  6:
            return  "Enam";
            break;
        case  7:
            return  "Tujuh";
            break;
        case  8:
            return  "Delapan";
            break;
        case  9:
            return  "Sembilan";
            break;
        case  10:
            return  "Sepuluh";
            break;
    }
}

function get_hari($tanggal)
{
    $hari = date('D', strtotime($tanggal));
    $hari_indonesia = array(
        'Sun' => 'Minggu',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => 'Jumat',
        'Sat' => 'Sabtu',
    );
    return $hari_indonesia[$hari];
}

function tgl_indo_hari($tanggal)
{
    $tanggal = str_replace(' ', '', $tanggal);
    if ($tanggal == '' || $tanggal == 'null' || $tanggal === null || strlen(trim($tanggal)) < 8 || strlen(trim($tanggal)) > 10) {
        return '';
    }
    
    $getday = date('N',strtotime($tanggal));
    $tanggal = date('Y-m-j',strtotime($tanggal));
    $hari = array(
        1 =>   'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu'
    );
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    return $hari[$getday].', '.$pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function tgl_indo_format($tanggal)
{
    if ($tanggal <> '') {
        $tgl = date('d-m-Y', strtotime($tanggal));
    } else {
        $tgl = "";
    }
    return $tgl;
}

function hitungUmur($tanggal_lahir)
{
    // Ubah tanggal lahir menjadi objek DateTime
    $tgl_lahir = new DateTime($tanggal_lahir);

    // Ambil tanggal hari ini
    $tgl_hari_ini = new DateTime();

    // Hitung selisih antara tanggal lahir dan tanggal hari ini
    $selisih = $tgl_hari_ini->diff($tgl_lahir);

    // Dapatkan umur dalam tahun
    $umur = $selisih->y;

    return $umur;
}

function romawi($num = 0)
{
    $var = [0 => '', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    if ($num != '') {
        return $var[intval($num)];
    }
    return $num;
}

function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang_hari($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}

//Fungsi ambil tanggal aja
function tgl_aja($tgl_a){
    $tanggal = substr($tgl_a,8,2);
    return $tanggal;  
  }
 
  //Fungsi Ambil bulan aja
  function bln_aja($bulan_a){
    $bulan = getBulan(substr($bulan_a,5,2));
    return $bulan;  
  } 
 
  //Fungsi Ambil tahun aja
  function thn_aja($thn){
    $tahun = substr($thn,0,4);
    return $tahun;  
  }

  //Fungsi konversi nama bulan ke dalam bahasa indonesia
 function getBulan($bln){
    switch ($bln){
     case 1:
      return "Januari";
      break;
     case 2:
      return "Februari";
      break;
     case 3:
      return "Maret";
      break;
     case 4:
      return "April";
      break;
     case 5:
      return "Mei";
      break;
     case 6:
      return "Juni";
      break;
     case 7:
      return "Juli";
      break;
     case 8:
      return "Agustus";
      break;
     case 9:
      return "September";
      break;
     case 10:
      return "Oktober";
      break;
     case 11:
      return "November";
      break;
     case 12:
      return "Desember";
      break;
    }
 }

 function konversi_nip($nip, $batas = " ")
{
    $nip = trim($nip, " ");
    $panjang = strlen($nip);

    if ($panjang == 18) {
        $sub[] = substr($nip, 0, 8); // tanggal lahir
        $sub[] = substr($nip, 8, 6); // tanggal pengangkatan
        $sub[] = substr($nip, 14, 1); // jenis kelamin
        $sub[] = substr($nip, -3); // nomor urut

        return $sub[0] . $batas . $sub[1] . $batas . $sub[2] . $batas . $sub[3];
    } elseif ($panjang == 15) {
        $sub[] = substr($nip, 0, 8); // tanggal lahir
        $sub[] = substr($nip, 8, 6); // tanggal pengangkatan
        $sub[] = substr($nip, 14, 1); // jenis kelamin

        return $sub[0] . $batas . $sub[1] . $batas . $sub[2];
    } elseif ($panjang == 9) {
        $sub = str_split($nip, 3);

        return $sub[0] . $batas . $sub[1] . $batas . $sub[2];
    } else {
        return $nip;
    }
}
