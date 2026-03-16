<?php
date_default_timezone_set("Asia/Makassar");
session_start();

function url()
{
	$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
	$base .= preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . '/';
	return $base;
}

function dirToArray($dir)
{
	$result = array();
	$cdir = scandir($dir);

	foreach ($cdir as $value) {
		if (!in_array($value, array(".", ".."))) {
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
				$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
			} else {
				$result[] = $value;
			}
		}
	}
	return $result;
}

function submenu($value, $keys, $dir)
{
	$len = md5(strlen($keys) . time() . rand(10, 100));

	$html = "
	<li>
		<a class='nav-link text-white' data-bs-toggle='collapse' href='#sub$len'>
			📁 $keys
		</a>
		<div class='collapse ms-3' id='sub$len'>
	";

	foreach ($value as $key => $value) {

		if (is_numeric($key)) {
			$html .= "<a target='_blank' href='upload.php?dir=$dir/$value' class='nav-link small text-warning confirmation'>📄 $value</a>";
		} else {

			if (is_array($value)) {
				$html .= submenu($value, $key, $dir . '/' . $key);
			}
		}
	}

	$html .= "</div></li>";

	return $html;
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Manajemen FTP</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<style>
		body {
			background: #f4f6f9;
		}

		.sidebar {
			width: 280px;
			height: 100vh;
			background: #212529;
			position: fixed;
		}

		.sidebar a {
			color: #ccc;
			text-decoration: none;
		}

		.sidebar a:hover {
			background: #343a40;
			color: white;
		}

		.content {
			margin-left: 280px;
			padding: 30px;
		}

		.logo {
			font-size: 20px;
			font-weight: bold;
			color: white;
			padding: 20px;
		}
	</style>

</head>

<body>

	<?php if (isset($_SESSION['login'])) { ?>

		<div class="sidebar">

			<div class="logo">
				SIMLURAH FTP
			</div>

			<div class="p-3">

				<ul class="nav flex-column">

					<li class="nav-item mb-2">
						<a class="nav-link text-white" data-bs-toggle="collapse" href="#dev">
							📂 SIMLURAH DEV
						</a>

						<div class="collapse" id="dev">

							<?php

							$dir = '../../simlurah_dev';

							foreach (dirToArray($dir) as $key => $value) {

								if (!in_array($key, ['.git', 'deploy'])) {

									if (is_numeric($key)) {
										echo "<a target='_blank' href='upload.php?dir=$value' class='nav-link small confirmation'>📄 $value</a>";
									} else {

										if (is_array($value)) {
											echo submenu($value, $key, $key);
										}
									}
								}
							}

							?>

						</div>

					</li>

				</ul>

			</div>

		</div>

		<div class="content">

			<div class="d-flex justify-content-between mb-4">

				<h3>Manajemen File FTP</h3>

				<div>

					<a href="<?= url() ?>forceclose.php" class="btn btn-warning me-2" target="_blank">
						Force Close
					</a>

					<a href="<?= url() ?>logout.php" class="btn btn-danger">
						Logout
					</a>

				</div>

			</div>

			<div class="alert alert-info">
				Pilih folder di sidebar untuk melakukan upload file.
			</div>

		</div>

	<?php } else { ?>

		<div class="container mt-5" style="max-width:400px">

			<div class="card shadow">

				<div class="card-header text-center">
					Login FTP Manager
				</div>

				<div class="card-body">

					<form method="post" action="<?= url() ?>login.php">

						<div class="mb-3">

							<label>Password</label>

							<input type="password" name="password" class="form-control">

						</div>

						<button class="btn btn-primary w-100">
							Login
						</button>

					</form>

				</div>

			</div>

		</div>

	<?php } ?>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

	<script>
		var elems = document.getElementsByClassName('confirmation');

		var confirmIt = function(e) {

			if (!confirm('Anda yakin ingin upload file ini?')) {
				e.preventDefault();
			}

		};

		for (var i = 0, l = elems.length; i < l; i++) {
			elems[i].addEventListener('click', confirmIt, false);
		}
	</script>

</body>

</html>