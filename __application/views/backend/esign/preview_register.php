<!DOCTYPE html>
<html>

<head>
    <title>E-SIGN-SIMLURAH</title>
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
        h1 {
            font-family: "Raleway", sans-serif
        }

        body,
        html {
            height: 100%
        }

        .bgimg {
            background-image: url('<?= base_url() ?>__assets/esign/bg.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;
        }
    </style>
</head>

<body>
    <div class="bgimg">
        <div class="container py-3">
            <header>
                <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
                    <a href="/" class="d-flex align-items-center link-body-emphasis text-decoration-none">
                        <img src="<?= base_url() ?>__assets/images/Simlurah White Color.svg" alt="" width="140px">
                    </a>
                </div>
            </header>

            <main>
                <div class="row mb-3">
                    <div class="col">
                        <div class="card mb-4 rounded-3 shadow-sm">
                            <div class="card-header border-0 py-3 bg-white">
                                <h5 class="my-0 fw-normal text-info"><i class="fa fa-folder-open-o me-2" aria-hidden="true"></i><?= $data_register->jenis_surat ?></h5>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="mx-lg-5">
                                    <h6 class="fw-bold text-info">Informasi Register</h6>
                                    <hr class="mb-1 mt-0">
                                    <div class="row">
                                        <div class="col-lg-6 col-12 mb-4">
                                            <h6 class="text-muted">No. Register</h6>
                                            <span class="fw-bold"><?= $data_register->nomor_register ?></span>
                                        </div>
                                        <div class="col-lg-6 col-12 mb-4">
                                            <h6 class="text-muted">Lembaga</h6>
                                            <span class="fw-bold"><?= $data_register->nama_lembaga ?></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-info">Informasi Penandatanganan</h6>
                                    <hr class="mb-1 mt-0">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <h6 class="text-muted">Penandatanganan</h6>
                                            <span class="fw-bold"><?= $data_register->nama_penandatanganan ?> - <?= $data_register->nip_penandatanganan ?></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-info">Informasi Tanggal</h6>
                                    <hr class="mb-1 mt-0">
                                    <div class="row">
                                        <div class="col-lg-6 col-12 mb-4">
                                            <h6 class="text-muted">Tanggal Penandatanganan</h6>
                                            <span class="fw-bold"><i class="fa fa-calendar" aria-hidden="true"></i> <?= date('d/m/Y', strtotime($data_register->tanggal_register)) ?> <i class="fa fa-clock-o ms-2" aria-hidden="true"></i> <?= date('H:i:s', strtotime($data_register->tanggal_register)) ?></span>
                                        </div>
                                        <div class="col-lg-6 col-12 mb-4">
                                            <h6 class="text-muted">Tanggal Pindai</h6>
                                            <span class="fw-bold"><i class="fa fa-calendar" aria-hidden="true"></i> <?= date('d/m/Y') ?> <i class="fa fa-clock-o ms-2" aria-hidden="true"></i> <?= date('H:i:s') ?></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-info">Lampiran</h6>
                                    <hr class="mb-1 mt-0">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <a href="<?= base_url() . $data_register->file_lampiran ?>" class="btn btn-primary btn-sm" download>Download</a>
                                            <button id="btn_sertifikat_preview" onclick="get_data_sertifikat('<?= $data_register->file_lampiran ?>',this)" class="btn btn-danger btn-sm">Tampilkan Sertifikat</button>
                                        </div>
                                        <div class="col-12 mb-4" id="sertifikat_preview" style="display: none;">
                                            <fieldset style="border: 1px solid #c1c1c1;border-radius: 2px;background-color: #eee;">
                                                <legend class="small bg-light text-muted w-auto p-1 px-2 rounded border" style="margin-top: -12px;">Sertifikat</legend>
                                                <div class="" style="padding:11px;">
                                                    <div class="mb-3 bg-danger text-white p-2 rounded notes">
                                                        Dokumen asli dan valid, tetapi sertifikat penandatangan tidak dapat diverifikasi
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0"> Ditandatangani oleh :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 signer_name"></div>
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0"> Lokasi :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 location"></div>
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0"> Alasan :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 reason"></div>
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0">Validity :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 signer_cert_validity"></div>
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0">Subject :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 signer_dn"></div>
                                                    </div>
                                                    <div class="row" style="border-bottom: 1px solid #c1c1c1;">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0">Issuer :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 issuer_dn"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 col-9 py-lg-3 py-3 pb-0">Summary :</div>
                                                        <div class="col-md-9 fw-bold pt-lg-3 py-3 pt-0 summary"></div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-1 col-5 px-1 text-lg-end text-center d-lg-flex align-items-center justify-content-end">
                                        <img src="<?= base_url() ?>__assets/esign/logo_bsre.png" alt="" width="100%">
                                    </div>
                                    <div class="col-lg-8 col-12 px-lg-0 fw-bold d-lg-flex align-items-center text-lg-start text-center small text-muted">
                                        Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), BSSN
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
    <script>
        function get_data_sertifikat(path, e) {
            var btn = $('#btn_sertifikat_preview').text();
            $('#btn_sertifikat_preview').prop('disabled', true);
            $.ajax({
                url: '<?= base_url() ?>Esign/get_data_sertifikat',
                dataType: 'json',
                type: 'post',
                data: ({
                    path
                }),
                beforeSend: function(e) {
                    $('#btn_sertifikat_preview').text('Loading...');
                },
                success: function(data) {
                    if (data.jumlah_signature > 0) {
                        $.each(data, function(i, v) {
                            $('#sertifikat_preview .' + i).text(v);
                            if (i == 'details') {
                                $.each(v[0], function(i, d) {
                                    if (typeof d.length == 'undefined') {
                                        $.each(d, function(i, v) {
                                            $('#sertifikat_preview .' + i).text(v);
                                        });
                                    } else {
                                        $('#sertifikat_preview .' + i).text(d);
                                    }
                                });
                            }
                        });
                        $('#sertifikat_preview').show();
                    }
                    $('#btn_sertifikat_preview').text(btn);
                    $('#btn_sertifikat_preview').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    $('#btn_sertifikat_preview').text(error);
                    setTimeout(() => {
                        $('#btn_sertifikat_preview').text(btn);
                        $('#btn_sertifikat_preview').prop('disabled', false);
                    }, 3000);
                }
            });
        }
    </script>
</body>

</html>