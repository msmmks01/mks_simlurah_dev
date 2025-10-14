<?php defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'backendxx';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//Routing Global
$route['obackendpage'] = 'backendxx';
$route['login-app'] = 'login/loginnya';
$route['logout-app'] = 'login/logoutnya';
$route['get-token-retribusi'] = 'login/get_token_retribusi';
$route['do_login_mr'] = 'login/do_login_mr';
$route['get-qrcode-dokumen'] = 'cek_dokumen/get_qrcode_dokumen';
$route['cek-dokumen/(:any)'] = 'cek_dokumen/view_dokumen/$1/$2/$3';
$route['validasi-dokumen'] = 'cek_dokumen/cek_validasi';


$route['backoffice-grid/(:any)'] = 'backendxx/get_grid/$1';
$route['backoffice-grid-report/(:any)'] = 'backendxx/get_grid_report/$1';
$route['backoffice-getdatachart'] = 'backendxx/get_chart';
$route['backoffice-status/(:any)'] = 'backendxx/set_flag/$1';
$route['backoffice-form/(:any)'] = 'backendxx/get_form/$1';
$route['backoffice-data/(:any)'] = 'backendxx/getdata/$1';
$route['backoffice-display/(:any)'] = 'backendxx/getdisplay/$1';
$route['backoffice-report/(:any)/(:any)'] = 'backendxx/get_report/$1/$2';
$route['backoffice-simpan/(:any)'] = 'backendxx/simpandata/$1';
$route['backoffice-getmodul/(:any)/(:any)'] = 'backendxx/modul/$1/$2';
$route['backoffice-cetak/(:any)'] = 'backendxx/cetak/$1';
$route['backoffice-cetak/(:any)/(:any)/(:any)'] = 'backendxx/cetak/$1/$2/$3';
$route['backoffice-cetak/(:any)/(:any)/(:any)/(:any)'] = 'backendxx/cetak/$1/$2/$3/$4';
$route['backoffice-cetak/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'backendxx/cetak/$1/$2/$3/$4/$5';

$route['backoffice-simpan-format-nomor-surat'] = 'backendxx/simpan_format_nomor_surat';
$route['backoffice-reset-format-nomor-surat'] = 'backendxx/reset_format_nomor_surat';
$route['backoffice-get-format-nomor-surat'] = 'backendxx/get_format_nomor_surat';
$route['backoffice-simpan-favorit-surat'] = 'backendxx/simpan_favorit_surat';
//End Routing Global

//Esign
$route['qscan'] = 'Esign/scan';

$route['hapus-foto-usaha/(:num)'] = 'backendxx/hapus_foto_usaha/$1';
$route['hapus-foto-pegawai-kel-kec/(:num)'] = 'backendxx/hapus_foto_pegawai_kel_kec/$1';
$route['hapus-foto-kendaraan/(:num)'] = 'backendxx/hapus_foto_kendaraan/$1';
$route['survei-kepuasan/(:num)/(:num)'] = 'backendxx/survei_kepuasan/$1/$2';
$route['survei-kepuasan/(:num)/(:num)/(:num)'] = 'backendxx/survei_kepuasan/$1/$2/$3';
$route['simpan-survei-kepuasan'] = 'backendxx/simpan_survei';

$route['get-data-penilaian-rt-rw-id'] = 'backendxx/get_data_penilaian_rt_rw_id';
$route['salin-data-penilaian-rt-rw']['post'] = 'backendxx/salin_data_penilaian_rt_rw';
$route['get-data-penduduk']['post'] = 'backendxx/get_data_penduduk';
