<!doctype html>
<html lang="id" data-bs-theme="auto">

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="SIMLURAH">
<meta name="author" content="CV.TIRTA MAHARDHIKA UTAMA">
<title>SURVEI KEPUASAN</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.0.2/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body {
        background-image: url('https://simlurahdev.kotamakassar.id/__assets/images/bck.jpg');
        background-position: center center;
        background-size: cover;
        height: 100vh;
    }

    .box-radio {
        padding: 4% 1% 4% 1%;
    }

    input[type="radio"]:checked+.box-radio {
        box-shadow: 0 0 10px rgba(0, 128, 128, 0.5);
        border-radius: 10px;
        padding: 4% 1% 4% 1%;
    }
</style>


</head>

<body>
    <div class="col-lg-8 mx-auto p-lg-4 py-lg-5">
        <div class="card shadow-lg">
            <div class="card-header border-bottom-0 bg-white">
                <div class="row">
                    <div class="col-lg-1 col-12 text-center px-lg-0 px-5">
                        <img src="https://simlurahdev.kotamakassar.id/__assets/images/logo-makassar.png" alt="" class="w-75">
                    </div>
                    <div class="col-lg-10 col-12 text-lg-start text-center mt-4 mt-lg-0">
                        <h3 class="fw-bold">SURVEI KEPUASAN MASYARAKAT</h3>
                        <h4 class="fw-normal"><?= strtoupper($nama_kelurahan_desa) ?></h4>
                    </div>
                </div>
            </div>
            <div class="card-body px-lg-5 px-2">
                <form action="<?= base_url('simpan-survei-kepuasan') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="cl_user_group_id" value="<?= $cl_user_group_id ?>">
                    <input type="hidden" name="cl_kecamatan_id" value="<?= $cl_kecamatan_id ?>">
                    <input type="hidden" name="cl_kelurahan_desa_id" value="<?= $cl_kelurahan_desa_id ?>">
                    <input type="hidden" name="nama_kelurahan_desa" value="<?= $nama_kelurahan_desa ?>">
                    <div class="row mb-3">
                        <div class="col-lg-2 col-auto">Jenis Kelamin</div>
                        <div class="col-lg-auto col-7">:</div>
                        <div class="col-lg-auto col-6 text-center p-lg-0">
                            <input type="radio" class="d-none" id="jenis_kelamin_1" name="jenis_kelamin" value="L" required>
                            <label for="jenis_kelamin_1" class="box-radio w-100 px-2">
                                <i class="fa-solid fa-person fa-3x text-primary"></i>
                                <h6 class="m-0 text-nowrap">Laki-Laki</h6>
                            </label>
                        </div>
                        <div class="col-lg-auto col-6 text-center p-lg-0">
                            <input type="radio" class="d-none" id="jenis_kelamin_2" name="jenis_kelamin" value="P" required>
                            <label for="jenis_kelamin_2" class="box-radio w-100 px-2">
                                <i class="fa-solid fa-person-dress fa-3x text-danger"></i>
                                <h6 class="m-0 text-nowrap">Perempuan</h6>
                            </label>
                        </div>

                        <div class="col-lg-2 d-lg-block d-none"></div>
                        <div class="col-lg-auto col-auto mt-3 mt-lg-0">Usia</div>
                        <div class="col-lg-auto col-9 mt-3 mt-lg-0">:</div>
                        <div class="col-lg-3 col-12">
                            <input type="text" name="umur" class="form-control" placeholder="Umur..." required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-2 col-auto">Pendidikan</div>
                        <div class="col-lg-auto col-7">:</div>
                        <div class="col-lg-9 col-12 p-lg-0">
                            <select class="form-control select2" name="cl_pendidikan_id" id="cl_pendidikan_id" required>
                                <option value=""></option>
                                <?php
                                $res = $this->db->order_by('nama_pendidikan')->get('cl_pendidikan')->result_array();
                                foreach ($res as $row):
                                ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['nama_pendidikan'] ?></option>
                                <?php
                                endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-2 col-auto">Pekerjaan</div>
                        <div class="col-lg-auto col-7">:</div>
                        <div class="col-lg-9 col-12 p-lg-0">
                            <select class="form-control select2" name="cl_jenis_pekerjaan_id" id="cl_jenis_pekerjaan_id" required>
                                <option value=""></option>
                                <?php
                                $res = $this->db->order_by('nama_pekerjaan')->get('cl_jenis_pekerjaan')->result_array();
                                foreach ($res as $row):
                                ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['nama_pekerjaan'] ?></option>
                                <?php
                                endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-2 col-auto">Jenis Layanan yang Diterima</div>
                        <div class="col-lg-auto col-3">:</div>
                        <div class="col-lg-9 col-12 p-lg-0">
                            <select class="form-control select2" name="cl_jenis_surat_id" id="cl_jenis_surat_id" required>
                                <option value=""></option>
                                <?php
                                $res = $this->db->where('cl_user_group_id', $cl_user_group_id)->order_by('jenis_surat')->get('cl_jenis_surat')->result_array();
                                foreach ($res as $row):
                                ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['jenis_surat'] ?></option>
                                <?php
                                endforeach; ?>
                                <option value="">Lainnya</option>
                            </select>
                            <input type="text" name="deskripsi_jenis_surat" class="form-control mt-3" placeholder="Masukan dan Saran...">
                        </div>
                    </div>
                    <!-- <div class="alert alert-info">
                        <?php
                        $i = 1;
                        $res = $this->db->where('tahun', 2025)->order_by('id', 'asc')->get('tbl_indikator_skm')->result_array();
                        foreach ($res as $row):
                        ?>

                            <div class="row mb-5">
                                <div class="col-lg-auto col-1"><?= $i; ?>.</div>
                                <div class="col-lg-11 col-11">
                                    <?= $row['uraian'] ?>
                                    <div class="row mt-3">
                                        <div class="col-lg-3 col-3 text-center px-0">
                                            <input type="radio" class="d-none" id="pil<?= $i; ?>_stb" name="skala[<?= $i; ?>]" value="1" required>
                                            <label for="pil<?= $i; ?>_stb" class="box-radio w-100">
                                                <i class="fa-regular fa-face-angry fa-3x text-danger"></i>
                                                <h6><?= $row['p1'] ?></h6>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-3 text-center px-0">
                                            <input type="radio" class="d-none" id="pil<?= $i; ?>_tb" name="skala[<?= $i; ?>]" value="2" required>
                                            <label for="pil<?= $i; ?>_tb" class="box-radio w-100">
                                                <i class="fa-regular fa-face-meh fa-3x text-warning"></i>
                                                <h6><?= $row['p2'] ?></h6>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-3 text-center px-0">
                                            <input type="radio" class="d-none" id="pil<?= $i; ?>_b" name="skala[<?= $i; ?>]" value="3" required>
                                            <label for="pil<?= $i; ?>_b" class="box-radio w-100">
                                                <i class="fa-regular fa-face-smile fa-3x text-info"></i>
                                                <h6><?= $row['p3'] ?></h6>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-3 text-center px-0">
                                            <input type="radio" class="d-none" id="pil<?= $i; ?>_sb" name="skala[<?= $i; ?>]" value="4" required>
                                            <label for="pil<?= $i; ?>_sb" class="box-radio w-100">
                                                <i class="fa-regular fa-face-laugh-beam fa-3x text-success"></i>
                                                <h6><?= $row['p4'] ?></h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="indikator_skm_id[<?= $i ?>]" value="<?= $row['id'] ?>">

                        <?php
                            $i++;
                        endforeach;
                        ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">SUBMIT</button>
                    </div> -->

                    <div class="alert alert-info">
                        <?php
                        $i = 1;
                        $res = $this->db->where('tahun', 2025)->order_by('id', 'asc')->get('tbl_indikator_skm')->result_array();
                        $total = count($res);
                        foreach ($res as $row):
                        ?>
                            <div class="question-step" data-step="<?= $i; ?>" style="<?= $i == 1 ? '' : 'display:none;' ?>">
                                <div class="row mb-5">
                                    <div class="col-lg-auto col-1"><?= $i; ?>.</div>
                                    <div class="col-lg-11 col-11 div_option">
                                        <?= $row['uraian'] ?>
                                        <div class="row mt-3">
                                            <div class="col-lg-3 col-3 text-center px-0">
                                                <input type="radio" class="d-none option" id="pil<?= $i; ?>_stb" name="skala[<?= $i; ?>]" value="1" required>
                                                <label for="pil<?= $i; ?>_stb" class="box-radio w-100">
                                                    <i class="fa-regular fa-face-angry fa-3x text-danger"></i>
                                                    <h6><?= $row['p1'] ?></h6>
                                                </label>
                                            </div>
                                            <div class="col-lg-3 col-3 text-center px-0">
                                                <input type="radio" class="d-none option" id="pil<?= $i; ?>_tb" name="skala[<?= $i; ?>]" value="2" required>
                                                <label for="pil<?= $i; ?>_tb" class="box-radio w-100">
                                                    <i class="fa-regular fa-face-meh fa-3x text-warning"></i>
                                                    <h6><?= $row['p2'] ?></h6>
                                                </label>
                                            </div>
                                            <div class="col-lg-3 col-3 text-center px-0">
                                                <input type="radio" class="d-none option" id="pil<?= $i; ?>_b" name="skala[<?= $i; ?>]" value="3" required>
                                                <label for="pil<?= $i; ?>_b" class="box-radio w-100">
                                                    <i class="fa-regular fa-face-smile fa-3x text-info"></i>
                                                    <h6><?= $row['p3'] ?></h6>
                                                </label>
                                            </div>
                                            <div class="col-lg-3 col-3 text-center px-0">
                                                <input type="radio" class="d-none option" id="pil<?= $i; ?>_sb" name="skala[<?= $i; ?>]" value="4" required>
                                                <label for="pil<?= $i; ?>_sb" class="box-radio w-100">
                                                    <i class="fa-regular fa-face-laugh-beam fa-3x text-success"></i>
                                                    <h6><?= $row['p4'] ?></h6>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <?php if ($i > 1): ?>
                                                <button type="button" class="btn btn-secondary back-btn">Kembali</button>
                                            <?php else: ?>
                                                <div></div>
                                            <?php endif; ?>

                                            <?php if ($i < $total): ?>
                                                <button type="button" class="btn-trigger btn btn-primary next-btn" style="display: none;">Selanjutnya</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn-trigger btn btn-success"  style="display: none;">SUBMIT</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="indikator_skm_id[<?= $i ?>]" value="<?= $row['id'] ?>">
                            </div>
                        <?php
                            $i++;
                        endforeach;
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(e) {
            $('.select2').select2({
                theme: "bootstrap-5",
                placeholder: 'Pilih...'
            });

            const $steps = $('.question-step');
            const $nextButtons = $('.next-btn');
            const $backButtons = $('.back-btn');

            $nextButtons.each(function (index) {
                $(this).on('click', function () {
                    if ($(this).closest('.div_option').find('[type="radio"]:checked').length>0) {
                        $steps.eq(index).hide();
                        if ($steps.eq(index + 1).length) {
                            $steps.eq(index + 1).show();
                        }
                    }
                });
            });

            $backButtons.each(function (index) {
                $(this).on('click', function () {
                    $steps.eq(index + 1).hide();
                    if ($steps.eq(index).length) {
                        $steps.eq(index).show();
                    }
                });
            });

            $('.option').click(function(e) {
                $(this).closest('.div_option').find('.btn-trigger').show();
            });
        });

    </script>
</body>

</html>