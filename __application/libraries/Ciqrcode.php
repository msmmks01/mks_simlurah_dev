<?php

class Ciqrcode
{
    function __construct()
    {
        $this->initialize();
    }

    public function initialize()
    {
        include "qr_code/qrconst.php";
        include "qr_code/qrconfig.php";
        include "qr_code/qrtools.php";
        include "qr_code/qrspec.php";
        include "qr_code/qrimage.php";
        include "qr_code/qrinput.php";
        include "qr_code/qrbitstream.php";
        include "qr_code/qrsplit.php";
        include "qr_code/qrrscode.php";
        include "qr_code/qrmask.php";
        include "qr_code/qrencode.php";
    }
}

/* end of file */