<?php
include("includes/header.php");

//DB Connect
$dbconn = new Database;

//If any special parameter is set
if (isset($_POST["submit"])) {
	$staff_id = $_POST["staff_id"];
} elseif (isset($_GET["staff_id"])) {
	//set $staff_id
	$staff_id = $_GET["staff_id"];
}

//Check if $staff_id is set by any means
if (isset($staff_id)) {
	$staff_id = mysqli_real_escape_string($dbconn->link, $staff_id);
	//Select the detials of the staff member
	//Create a query
	$query = "	SELECT `staff_id`, `staff_name`, `staff_birthday`, `staff_phone1`, `staff_phone2`, `staff_email`, `staff_address`, `staff_role_name`, `staff_color`
				FROM `staff`,`staff_role` 
				WHERE  staff.staff_role_id=staff_role.staff_role_id AND staff_id='$staff_id'";
	//Run query
	$details = $dbconn->select($query);
	//Validation - Database class return 0 for errors
	if (is_int($details)) {
		header("Location:staff_profile.php?msgtype=error&msg=" . urlencode($dbconn->error));
	} else {
		$details = $details->fetch_assoc();
	}
}

//Load assigned patients if present
if (isset($details)) {
	$staff_id = $details["staff_id"];
	$query = " 	SELECT id,patient_id, patient_name, patient_registered_date
				FROM `patients`,`staff` 
				WHERE  patients.patient_supervisor_id=staff.staff_id AND staff_id=$staff_id
				ORDER BY patient_id ASC";
	$assigned_patients = $dbconn->select($query);
}

//Assign new patients on request
if (isset($_POST["assign_patient"])) {
	$assignable_patient_id = mysqli_real_escape_string($dbconn->link, $_POST["assignable_patient_id"]);
	$query = "	UPDATE `patients`
				SET `patient_supervisor_id` = '$staff_id'
				WHERE `patients`.`patient_id` = '$assignable_patient_id'";
	$update_status = $dbconn->update($query);
	//Validate update
	if ($update_status == 0) {
		$error = $dbconn->error;
		if ($error = "Error : No matches available in the system for the given input");
		$error = "Either the given patient ID '$assignable_patient_id' is invalid or the patient is already assigned to this staff member";
		header("Location:staff_profile.php?staff_id=" . urlencode($staff_id) . "&msg=" . urlencode($error) . "&msgtype=error");
	} else {
		//Proceed if validation success
		$info = "Patient '" . $assignable_patient_id . "' has been successfully assigned to this staff member.";
		header("Location:staff_profile.php?staff_id=" . urlencode($staff_id) . "&msg=" . urlencode($info) . "&msgtype=info");
	}
}
?>

<!--Head section-->
<div class="container box-white">
	<h1>Staff member Profile</h1>
	<p>See details of individual staff members here.</p>
	<div class="col-12">
		<a href="staff.php#staff-member-list" class="btn btn-primary">Select another member</a>
	</div>

	<div class="pt-3">
		<p>Staff member system ID - <?php if (isset($staff_id)) {
										echo $staff_id;
									} ?></p>
		<p><?php print_url_message() ?></p>
	</div>


</div>

<!--Staff member detail section-->
<div class="container my-5 ">
	<!-- Detial Box -->
	<div class="row">
		<div class="col-lg-6 box-white">
			<h2 class="my-3">Details</h2>
			<?php if (isset($details)) : ?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th scope="col">Detail</th>
							<th scope="col">Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Name</td>
							<td><?php echo $details["staff_name"] ?></td>
						</tr>
						<tr>
							<td>Staff member Type</td>
							<td><?php echo $details["staff_role_name"] ?></td>
						</tr>
						<tr>
							<td>Staff member Birthday</td>
							<td><?php echo formatDate($details["staff_birthday"]) ?></td>
						</tr>
						<tr>
							<td>Staff member Phone 1</td>
							<td><?php echo $details["staff_phone1"] ?></td>
						</tr>
						<tr>
							<td>Staff member Phone 2</td>
							<td><?php echo $details["staff_phone2"] ?></td>
						</tr>
						<tr>
							<td>Staff member email</td>
							<td><?php echo $details["staff_email"] ?></td>
						</tr>
						<tr>
							<td>Staff member address</td>
							<td><?php echo $details["staff_address"] ?></td>
						</tr>
						<tr>
							<td>Staff member color <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="in the 'Queue' management section, this color will be displayed to easily identify the patients assigned to this staff member.">?</span></td>
							<td><span style="background-color:<?php echo $details["staff_color"] ?>;width:50px" class="badge rounded-pill">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
						</tr>
					</tbody>
				</table>
				<div class="col-12">
					<form action="update_staff.php?staff_id=<?php echo $details["staff_id"] ?>" method="POST">
						<button name="submit" type="submit" class="btn btn-primary">Change details</button>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-1"></div>
		<!-- Assigned patients section -->
		<div class="col-lg-5 my-5 my-lg-0 box-white">
			<h2 class="my-3">Assigned patients</h2>
			<?php if (isset($assigned_patients)) : //Check if file query processed
			?>
				<?php if (is_int($assigned_patients)) : //Check if file is empty (check int since it database returns 0 on empty)
				?>
					<p>No patients assigned to this staff member.</p>
				<?php else : ?>
					<div class="table-holder">
						<table class="table table-bordered">
							<tbody>
								<tr>
									<th>Patient ID</th>
									<th>Patient Name</th>
									<th>Profile</th>
								</tr>
								<?php while ($assigned_patient = $assigned_patients->fetch_assoc()) : ?>
									<tr>

										<td><?php echo $assigned_patient["patient_id"] ?></td>
										<td><?php echo $assigned_patient["patient_name"] ?></td>
										<td><a href="patient_profile.php?patient_id=<?php echo $assigned_patient["patient_id"] ?>"><button type="button" class="btn btn-primary btn-table">view</button></a></td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<!--Assign patients form-->
<div class="container my-5">
	<div class="row">
		<div class="col-lg-5 col-md-7 box-white ">
			<h2>Assign patients</h2>
			<p>Assign patients to this staff member here.</p>
			<form class="mb-3" method="post" action="staff_profile.php?staff_id=<?php echo urlencode($staff_id) ?>">
				<div class="mb-3">
					<label class="form-label">Patient ID</label>
					<input name="assignable_patient_id" type="text" class="form-control" placeholder="Enter the patient ID to assign">
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="assign_patient" type="submit" value="Assign">
				</div>
			</form>
		</div>
	</div>
</div>


<?php
include("includes/footer.php");
?>