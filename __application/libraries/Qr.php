<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Memuat library QR Code dari phpqrcode
$mpdf['base_directory'] = APPPATH . 'third_party/phpqrcode/';
require_once($mpdf['base_directory'] . 'qrlib.php');

class Qr
{
    // Fungsi untuk menghasilkan QR Code
    public function generate($data, $logoPath = null, $savePath = null)
    {
        require APPPATH . 'third_party/phpqrcode/qrlib.php';

        // Generate QR Code
        $qrCodePath = ($savePath) ? $savePath : tempnam(sys_get_temp_dir(), 'qr_') . '.png';

        // Generate QR Code dengan level error H dan ukuran 10
        QRcode::png($data, $qrCodePath, QR_ECLEVEL_H, 10, 2);

        if ($logoPath !== null) {
            // Jika logo disediakan, tambahkan logo ke QR Code
            $qrCode = imagecreatefrompng($qrCodePath); // Membaca QR Code
            $logo = imagecreatefrompng($logoPath); // Membaca logo

            // Menentukan ukuran QR Code dan logo
            $qrWidth = imagesx($qrCode);
            $qrHeight = imagesy($qrCode);
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);

            // Menentukan ukuran dan posisi logo di tengah QR Code
            $logoQrWidth = $qrWidth / 3;
            $scale = $logoWidth / $logoQrWidth;
            $logoQrHeight = $logoHeight / $scale;
            $logoX = ($qrWidth - $logoQrWidth) / 2;
            $logoY = ($qrHeight - $logoQrHeight) / 2;

            // Menempelkan logo ke QR Code
            imagecopyresampled($qrCode, $logo, $logoX, $logoY, 0, 0, $logoQrWidth, $logoQrHeight, $logoWidth, $logoHeight);

            // Simpan atau tampilkan gambar final
            if ($savePath !== null) {
                imagepng($qrCode, $savePath); // Simpan ke file
            } else {
                header('Content-Type: image/png');
                imagepng($qrCode); // Tampilkan langsung di browser
            }

            // Hapus gambar untuk melepaskan memori
            imagedestroy($qrCode);
            imagedestroy($logo);
        } else {
            // Jika tidak ada logo, hanya menghasilkan QR Code
            if ($savePath === null) {
                header('Content-Type: image/png');
                readfile($qrCodePath); // Tampilkan QR Code
            }
        }
    }
}
