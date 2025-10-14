<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Include PHP QR Code core library
require_once APPPATH . 'third_party/phpqrcode/qrlib.php';

class Qr_with_logo
{
    /**
     * Generate QR Code with logo in center
     * 
     * @param string $text        Text/URL to encode
     * @param string|false $outfile Path to save PNG, or false to output directly
     * @param string $logo_path   Path to logo PNG
     * @param int $size           QR Code size (scale)
     * @param int $margin         Margin around QR
     * @param string $level       Error correction level (L, M, Q, H)
     * 
     * @return string|void Path of saved file, or directly outputs image
     */
    public function generate($text, $outfile = false, $logo_path = '', $size = 5, $margin = 2, $level = 'H')
    {
        // Make sure save directory exists
        if ($outfile) {
            $save_dir = dirname($outfile);
            if (!is_dir($save_dir)) {
                mkdir($save_dir, 0755, true);
            }
        }

        // Generate base QR code image
        $temp_qr = tempnam(sys_get_temp_dir(), 'qr_');
        QRcode::png($text, $temp_qr, $level, $size, $margin);
        $qr_image = imagecreatefrompng($temp_qr);

        // Optional: add logo
        if ($logo_path && file_exists($logo_path)) {
            $logo = imagecreatefrompng($logo_path);

            $qr_width = imagesx($qr_image);
            $qr_height = imagesy($qr_image);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);

            $logo_target_width = $qr_width / 4; // 1/4 of QR
            $scale = $logo_width / $logo_target_width;
            $logo_target_height = $logo_height / $scale;

            $from_width = ($qr_width - $logo_target_width) / 2;

            imagecopyresampled(
                $qr_image,
                $logo,
                $from_width,
                $from_width,
                0,
                0,
                $logo_target_width,
                $logo_target_height,
                $logo_width,
                $logo_height
            );
        }

        // Output or save
        if ($outfile) {
            imagepng($qr_image, $outfile);
            imagedestroy($qr_image);
            unlink($temp_qr);
            return $outfile;
        } else {
            header('Content-Type: image/png');
            imagepng($qr_image);
            imagedestroy($qr_image);
            unlink($temp_qr);
        }
    }
}
