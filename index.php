<?php
include("includes/header.php");

?>
<!-- Welcome -->
<div class="container box-white">
	<h1>Welcome</h1>
	<p>Here are some features available in the system.</p>
</div>

<!--Features-->
<div class="container my-5">
	<div class="row">
		<!--Queue features-->
		<div class="col-lg-4 me-auto col-auto box-white">
			<h2 class="mb-3">Manage queues</h2>
			<div class="row row-cols-1">
				<div class="col mb-4">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Reception desk</h5>
							<p class="card-text">Manage queues and mark patient attendance on the reception desk.</p>
							<a href="queue.php" class="btn btn-primary">Reception</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Queue types</h5>
							<p class="card-text">Add and manage queue types. Assign colors to queues for fast identification</p>
							<a href="queue_types.php" class="btn btn-primary">Queue types</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--Manage patients-->
		<div class="col-lg-7 my-5 my-lg-0 box-white">
			<h2>Manage patients</h2>
			<div class="row row-cols-1 row-cols-md-2 g-4" data-masonry='{"percentPosition": true }'>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Patient list</h5>
							<p class="card-text">List of all registered patients. Search for patients and view patient profiles for further information</p>
							<a href="patients.php" class="btn btn-primary">Patients</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Patient profiles</h5>
							<p class="card-text">View and change patient details. Add special notes about the patient.</p>
							<a href="patient_profile.php" class="btn btn-primary">Profiles</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Patient types</h5>
							<p class="card-text">Add and manage patient types, Group patients based on their treatment.</p>
							<a href="patient_types.php" class="btn btn-primary">Patient types</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Add patients</h5>
							<p class="card-text">Add new patients to the system.</p>
							<a href="add_patient.php" class="btn btn-primary">Add patients</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row mt-5">
		<!--Manage staff-->
		<div class="col-lg-6 my-5 my-lg-0 box-white">
			<h2>Manage staff</h2>
			<div class="row row-cols-1 row-cols-md-2 g-4" data-masonry='{"percentPosition": true }'>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Staff list</h5>
							<p class="card-text">List of all staff members. Search for staff members and view their profiles for further information.</p>
							<a href="staff.php" class="btn btn-primary">Staff</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Staff profiles</h5>
							<p class="card-text">View and change staff member details. Change staff member colors, assign patients to staff mebmer.</p>
							<a href="staff_profile.php" class="btn btn-primary">Profiles</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Staff roles</h5>
							<p class="card-text">Add and manage Staff roles, Group staff members based on their roles.</p>
							<a href="staff_roles.php" class="btn btn-primary">Staff roles</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Add staff</h5>
							<p class="card-text">Add new staff mebmers to the system.</p>
							<a href="add_staff.php" class="btn btn-primary">Add staff</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
include("includes/footer.php");
?>