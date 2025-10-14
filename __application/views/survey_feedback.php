<!doctype html>
<html lang="id" data-bs-theme="auto">

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="SIMLURAH">
<meta name="author" content="CV.TIRTA MAHARDHIKA UTAMA">
<title>SURVEI KEPUASAN</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<style>
    body {
        background-image: url('https://simlurahdev.kotamakassar.id/__assets/images/bck.jpg');
        background-position: center center;
        background-size: cover;
        height: 100vh;
    }
</style>
</head>

<body>
    <div class="col-lg-6 m-auto p-lg-4 py-lg-5">
        <div class="card shadow-lg">
            <div class="card-header border-bottom-0 bg-white">
                <div class="row">
                    <div class="col-2 text-center px-lg-0 px-5 mx-auto">
                        <img src="https://simlurahdev.kotamakassar.id/__assets/images/logo-makassar.png" alt="" class="w-100">
                    </div>
                    <div class="col-lg-12 col-12 text-center mt-4 mt-lg-0">
                        <h3 class="fw-bold">SURVEI KEPUASAN MASYARAKAT</h3>
                        <h4 class="fw-normal"><?= strtoupper($nama_kelurahan_desa) ?></h4>
                    </div>
                </div>
            </div>
            <div class="card-body px-lg-5 px-2 text-center">
                <i class="<?= $icon ?> mb-4"></i>
                <p class="fs-4"><?= $msg ?></p>
                <div class="d-grid">
                    <a href="<?= base_url("survei-kepuasan/$cl_user_group_id/$cl_kecamatan_id/$cl_kelurahan_desa_id") ?>" class="btn btn-primary">SELESAI</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>