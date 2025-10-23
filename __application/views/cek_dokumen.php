<!DOCTYPE html>
<html>

<head>
    <title>VALIDATE DOKUMEN - SIMLURAH</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.111.3">
    <link rel="stylesheet" href="<?= base_url() ?>__assets/esign/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="<?= base_url() ?>__assets/backendxx/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>__assets/backendxx/bower_components/font-awesome/css/font-awesome.min.css">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        body,
        h1,
        h3,
        h4,
        h5 {
            font-family: "Raleway", sans-serif !important
        }

        body,
        html {
            height: 100%
        }

        /* .bgimg {
            background-image: url('<?= base_url() ?>__assets/images/5330558.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;
        } */


        #search-wrapper {
            display: flex;
            border: 1px solid rgba(0, 0, 0, 0.276);
            align-items: stretch;
            border-radius: 40px;
            background-color: #fff;
            overflow: hidden;
            max-width: 400px;
            box-shadow: 2px 1px 5px 1px rgba(0, 0, 0, 0.273);

        }

        #search {
            border: none;
            width: 350px;
            font-size: 15px;
        }

        #search:focus {
            outline: none;
        }

        .search-icon {
            margin: 10px;
            color: rgba(0, 0, 0, 0.564);
        }

        #search-button {
            border: none;
            cursor: pointer;
            color: #fff;
            background-color: #1dbf73;
            padding: 0px 10px;
        }

        .area {
            background-image: url('<?= base_url() ?>__assets/images/5330558.jpg');
            background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);

            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;


        }

        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            animation: animate 25s linear infinite;
            bottom: -150px;

        }

        .circles li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }


        .circles li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles li:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .circles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles li:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }



        @keyframes animate {

            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }

        }
    </style>
</head>

<body>
    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>



        <div class="container py-3">
            <header style="border-bottom: 1px solid #dee2e6;">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom">
                            <!-- Left logo -->
                            <img src="<?= base_url() ?>__assets/images/Simlurah Color.svg" alt="Logo Makassar" width="260px" class="img-fluid ms-md-1 mb-1 mb-md-0" onerror="this.onerror=null;this.src='<?= base_url() ?>__assets/images/logo-blank.png';" style="height: 100px" />
                            <!-- <img src="<?= base_url() ?>__assets/images/logo-makassar.png" alt="Logo Makassar" class="img-fluid ms-md-3 mb-2 mb-md-0" onerror="this.onerror=null;this.src='<?= base_url() ?>__assets/images/logo-blank.png';" style="height: 100px" /> -->

                            <!-- Center text -->
                            <!-- <h5 class="text-center fw-bold mx-auto pl-3 ml-3 mb-0">Pemerintah Kota Makassar <br> Kecamatan <?= ucfirst(strtolower($nama_kecamatan)); ?> <br><?= $nama_kelurahan ?></h5> -->
                            <!-- Right logo -->
                            <!-- <a href="/" class="d-flex align-items-center link-body-emphasis text-decoration-none">
                                <img src="<?= base_url() ?>__assets/images/Simlurah Color.svg" alt="" width="160px">
                            </a> -->

                        </div>
                    </div>
                </div>
            </header>


            <main>
                <div class="row mb-3">
                    <div class="col">
                        <div class="card mb-4 rounded-3 shadow-sm " style="background-color: rgba(255, 255, 255, 0.69)">
                            <div class="card-header border-0 py-3 ">
                                <h5 class="my-0 fw-normal text-secondary"><i class="fa fa-bookmark me-2" aria-hidden="true"></i>Kecamatan <?= ucfirst(strtolower($nama_kecamatan)); ?> <?= $nama_kelurahan ?> </h5>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="col-12 d-flex justify-content-center mb-3">
                                    <h5 class="text-secondary strong">Validasi Keaslian Dokumen Persuratan</h5>
                                </div>
                                <div class="col-12 d-flex justify-content-center mb-3">
                                    <div id="search-wrapper">
                                        <i class="search-icon fa fa-search"></i>
                                        <input type="text" id="kode_unik" style="border:none;" placeholder="Masukkan 8 Digit Kode :)">
                                        <button id="search-button">Cari</button>
                                    </div><br>

                                </div>
                                <div class="d-lg-flex justify-content-center text-lg-start text-center small text-muted">
                                    <i>*Pastikan kode unik yang dimasukkan adalah 8 digit kode dari surat.</i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="informasi_dokumen">
                    <div class="col">
                        <div class="card mb-4 rounded-3 shadow-sm" style="background-color: rgba(255, 255, 255, 0.82);">
                            <div class="card-header border-0 py-3 ">
                                <div class="pull-right"><button class="btn btn-sm btn-danger" onclick="close_info();"><i class="fa fa-times"></i></button></div>
                                <h5 class="my-0 fw-normal text-info"><i class="fa fa-folder-open-o me-2" aria-hidden="true"></i>Data Surat</h5>
                            </div>
                            <div class="card-body px-lg-5">
                                <div style="border:2px solid #2d2d2dff; border-radius: 10px; padding: 20px;overflow: auto;">
                                    <div id="data_dokumen" style="width: 1200px;"></div>
                                </div>

                            </div>
                            <div class=" card-footer bg-white border-0">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-10 col-12 px-lg-0 fw-bold d-lg-flex align-items-center text-lg-start text-center small text-muted">
                                        <i class="text-danger">*Dokumen ini sah apabila memiliki data yang sesuai dengan cetakan yang ada di atas, jika tidak sesuai silahkan hubungi kelurahan terkait.</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="pt-1 border-top">
                <div class="row">
                    <div class="col-12 col-md">
                        <small class="d-block mb-3 text-white">&copy; Simlurah</small>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="<?= base_url() ?>__assets/backendxx/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?= base_url() ?>__assets/backendxx/dist/js/loading-overlay.js"></script>
    <script>
        var kode = '<?= $kode ?>';
        if (kode != '') {
            $('#kode_unik').val(kode);
            $.LoadingOverlay("show");
            postData(kode);
        }
        $('#informasi_dokumen').hide();

        document.getElementById('search-button').addEventListener('click', function() {
            var searchValue = document.getElementById('kode_unik').value;
            $.LoadingOverlay("show");

            // Validasi input: pastikan panjang kode adalah 8 digit
            if (searchValue.length === 8 && searchValue != '') {
                // Memanggil fungsi POST jika panjang kode valid
                postData(searchValue);
            } else {
                $.LoadingOverlay("hide", true);
                alert("Harap masukkan 8 digit kode yang valid!");
            }
        });

        function postData(kode) {
            $.ajax({
                url: '<?= base_url() ?>validasi-dokumen',
                type: 'POST',
                dataType: 'json',
                data: {
                    kode: kode
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#informasi_dokumen').show();
                        $('#data_dokumen').html(response.content);
                        //    setTimeout(function () {
                        //        $('.kop-surat').hide();
                        //         $('#ttd').hide();
                        //     }, 3000);
                        // setTimeout(function () {
                        //     $('.kop-surat, #ttd').hide();
                        // }, 3000);
                        setTimeout(function() {
                            console.log("Jumlah .kop-surat:", $('.kop-surat').length);
                            console.log("Jumlah #ttd:", $('#ttd').length);

                            $('.kop-surat, #ttd').hide();
                        }, 2000);

                        $.LoadingOverlay("hide", true);

                    } else {
                        $.LoadingOverlay("hide", true);
                        alert("Dokumen tidak ditemukan atau tidak valid.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    $.LoadingOverlay("hide", true);
                    alert("Terjadi kesalahan saat memproses permintaan.");
                }
            });
        }

        function close_info() {
            $('#informasi_dokumen').hide();
            $('#data_dokumen').html('');
            $('#kode_unik').val('');
        }
    </script>
</body>

</html>