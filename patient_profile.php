<?php
include("includes/header.php");

//DB Connect
$dbconn = new Database;

//Check if patient_id parameter is set
if (isset($_POST["submit"])) {
	$patient_id = $_POST["patient_id"];
} elseif (isset($_GET["patient_id"])) {
	//set patient id
	$patient_id = $_GET["patient_id"];
}

//Check if $patient_id is set by any means
if (isset($patient_id)) {
	$patient_id = mysqli_real_escape_string($dbconn->link, $patient_id);
	//Select the detials of the patient
	//Create a query
	$query = "	SELECT `id`, `patient_id`, `patient_name`, `patient_registered_date`, `patient_birthday`, `patient_phone1`, `patient_phone2`, `patient_email`, `patient_address`, `patient_district`, `patient_type_name`, `staff_id`, `staff_name`
				FROM `patients`
				JOIN patient_types ON patient_types.patient_type_id=patients.patient_type
				LEFT JOIN staff ON staff.staff_id=patients.patient_supervisor_id
				WHERE patient_id='$patient_id'";
	//Run query
	$details = $dbconn->select($query);
	//Validation - Database class return 0 for errors
	if (is_int($details)) {
		header("Location:patient_profile.php?msgtype=error&msg=" . urlencode($dbconn->error));
	} else {
		$details = $details->fetch_assoc();
	}
}

//Load patient file if present
if (isset($details)) {
	$id = $details["id"];
	$query = " SELECT `patient_file_id`, `patient_file_version`, `patient_file_date_created`, `patient_file_body`
				FROM `patient_files`
				WHERE patient_id = '$id'
				ORDER BY patient_file_version DESC";
	$file = $dbconn->select($query);
}

?>

<!--Head section-->
<div class="container box-white">
	<h1>Patient Profile</h1>
	<p>See details of individual patients here.</p>
	<form action="patient_profile.php" method="post" class="row row-cols-lg-auto g-3 align-items-center">
		<div class="col-12">
			<label for="inlineFormInputGroupUsername">Patient ID</label>
			<div class="input-group">
				<input name="patient_id" type="text" class="form-control" id="inlineFormInputGroupUsername" placeholder="Patient ID" <?php if (isset($patient_id)) {
																																			echo "value='$patient_id'";
																																		} ?>>
				<button name="submit" type="submit" class="btn btn-primary">Check</button>
			</div>
		</div>
		<div class="col-12">
			<br class="d-none d-lg-block">
			<a href="patients.php#patient-list" class="btn btn-primary">Select another patient</a>
		</div>
	</form>

	<div class="pt-3">
		<p>Patient ID - <?php if (isset($patient_id)) {
							echo $patient_id;
						} ?>
			<br>(Patients' system ID - <?php if (isset($id)) {
											echo $id;
										} ?>)
			<span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="This ID is a system ID automatically assigned by the system. It can't be changed.">?</span>
		</p>
		<p><?php print_url_message() ?></p>
	</div>


</div>

<!--Patient detail section-->
<div class="container my-5 ">
	<div class="row">
		<div class="col-lg-6 me-auto box-white">
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
							<td>Patient ID</td>
							<td><?php echo $details["patient_id"] ?></td>
						</tr>
						<tr>
							<td>Name</td>
							<td><?php echo $details["patient_name"] ?></td>
						</tr>
						<tr>
							<td>Registered Date <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="The registered date is the date patient added to the system unless otherwise changed.">?</span></td>
							<td><?php echo formatDateTime($details["patient_registered_date"]) ?></td>
						</tr>
						<tr>
							<td>Patient Type</td>
							<td><?php echo $details["patient_type_name"] ?></td>
						</tr>
						<tr>
							<td>Patient Supervisor <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Patients can be assigned to a staff member in 'staff' page.">?</span></td>
							<td><?php echo $details["staff_name"] ?> <?php if (!is_null($details["staff_name"])) : ?> <a href="staff_profile.php?staff_id=<?php echo $details["staff_id"] ?>"><button type="button" class="btn btn-primary btn-table">profile</button></a><?php else : ?>Not assigned<?php endif; ?></td>
						</tr>
						<tr>
							<td>Patient Birthday</td>
							<td><?php echo formatDate($details["patient_birthday"]) ?></td>
						</tr>
						<tr>
							<td>Patient Phone 1</td>
							<td><?php echo $details["patient_phone1"] ?></td>
						</tr>
						<tr>
							<td>Patient Phone 2</td>
							<td><?php echo $details["patient_phone2"] ?></td>
						</tr>
						<tr>
							<td>Patient email</td>
							<td><?php echo $details["patient_email"] ?></td>
						</tr>
						<tr>
							<td>Patient address</td>
							<td><?php echo $details["patient_address"] ?></td>
						</tr>
						<tr>
							<td>patient district</td>
							<td><?php echo $details["patient_district"] ?></td>
						</tr>
					</tbody>
				</table>
				<div class="col-12">
					<form action="update_patient.php?id=<?php echo $details["id"] ?>" method="POST">
						<button name="submit" type="submit" class="btn btn-primary">Change details</button>
					</form>
				</div>
			<?php endif; ?>

		</div>
		<div class="col-lg-5 my-5 my-lg-0 box-white">
			<h2 class="my-3">File</h2>
			<?php if (isset($file)) : //Check if file query processed
			?>
				<?php if (is_int($file)) : //Check if file is empty (check int since it database returns 0 on empty)
				?>
					<p>No file created for this patient yet.</p>
				<?php else : $file = $file->fetch_assoc() ?>

					<table class="table table-bordered">
						<tbody>
							<tr>
								<td>Time created</td>
								<td><?php echo formatDateTime($file["patient_file_date_created"]) ?></td>
							</tr>
							<tr>
								<td>File version</td>
								<td><?php echo $file["patient_file_version"] ?></td>
							</tr>
						</tbody>
					</table>
					<div class="file-holder">
						<?php echo $file["patient_file_body"] ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>


<?php
include("includes/footer.php");
?>