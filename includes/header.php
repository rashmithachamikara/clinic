<?php
//Includes
include("config.php");
include("lib/Database.php");
include("util/common_util.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Clinic</title>
	<meta content='maximum-scale=1.0, initial-scale=1.0, width=device-width' name='viewport'>
	<!--styles and skins-->
	<link rel="stylesheet" href="css/style.css">
	<?php if (SKIN != 0) : ?>
		<link rel="stylesheet" href="css/skin<?php echo SKIN ?>.css">
	<?php endif; ?>

	<!--Bootstrap CSS-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<!--Bootstrap CSS ends-->
</head>

<body>

	<!--Header-->
	<div class="header-customization">
		<div class="container">
			<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
				<a href="<?php echo SITE_URL?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
					<span class="btn btn-outline-primary fw-bold fs-4">Clinic Management</span>
				</a>

				<ul class="nav nav-pills">
					<li class="nav-item"><a href="queue.php" class="nav-link active" aria-current="page">Reception</a></li>
					<li class="nav-item"><a href="patients.php" class="nav-link">Patients</a></li>
					<li class="nav-item"><a href="staff.php" class="nav-link">Staff</a></li>
					<li class="nav-item"><a href="#" class="nav-link">FAQs</a></li>
					<li class="nav-item"><a href="#" class="nav-link">About</a></li>
				</ul>
			</header>
		</div>
	</div>
	<!--Header ends-->

	<!-- Javascripts -->
	<script src="js/common.js"></script>
	<!-- JQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<!-- Tooltips -->
	<script>
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>