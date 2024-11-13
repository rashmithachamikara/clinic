<?php
include("includes/header.php");

//
// Insert updated form data into Database
//

$dbconn = new Database;

if (isset($_POST["submit_update"])) {
	//Insert new patient data into database
	//Preare values
	$id 			= mysqli_real_escape_string($dbconn->link, $_POST["id"]);
	$patient_id 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_id"]);
	$patient_name	= mysqli_real_escape_string($dbconn->link, $_POST["patient_name"]);
	$patient_type	= mysqli_real_escape_string($dbconn->link, $_POST["patient_type"]);
	$patient_birthday	= mysqli_real_escape_string($dbconn->link, $_POST["patient_birthday"]);
	$patient_registered_date = mysqli_real_escape_string($dbconn->link, $_POST["patient_registered_date"]);
	$patient_phone1	= mysqli_real_escape_string($dbconn->link, $_POST["patient_phone1"]);
	$patient_phone2	= mysqli_real_escape_string($dbconn->link, $_POST["patient_phone2"]);
	$patient_email	= mysqli_real_escape_string($dbconn->link, $_POST["patient_email"]);
	$patient_address	= mysqli_real_escape_string($dbconn->link, $_POST["patient_address"]);
	$patient_district	= mysqli_real_escape_string($dbconn->link, $_POST["patient_district"]);
	//Basic validation - Empty fields check
	if ($patient_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:update_patient.php?id=$id&msgtype=error&msg=" . urlencode($error));
		exit();
	} else {
		//Create a query
		$query = "	UPDATE `patients`
					SET 	`patient_name`= '$patient_name',
							`patient_type`= '$patient_type',
							`patient_birthday`='$patient_birthday',
							`patient_registered_date`='$patient_registered_date',
							`patient_phone1`='$patient_phone1',
							`patient_phone2`='$patient_phone2',
							`patient_email`='$patient_email',
							`patient_address`='$patient_address',
							`patient_district`='$patient_district'
					WHERE `id`='$id'";
		//Run query
		$update_status = $dbconn->update($query);
		//Validate insertion (The Database class return 1 on success, 0 on failure)
		if ($update_status == 1) {
			//Redirect
			header("Location:patient_profile.php?patient_id=" . urlencode($patient_id) . "&msgtype=info&msg=" . urlencode("Patient details successfully changed"));
		} elseif (($dbconn->error) == "Error : No matches available in the system for the given input") {
			header("Location:patient_profile.php?patient_id=" . urlencode($patient_id) . "&msgtype=warning&msg=" . urlencode("Nothing about patient has changed!"));
		} else {
			header("Location:update_patient.php?id=" . urlencode($id) . "&msgtype=error&msg=" . urlencode($dbconn->error));
		}
	}
}
?>

<?php
//
//Get patient type data
//

$query = "	SELECT patient_type_id,patient_type_name
			FROM `patient_types`
			ORDER BY patient_type_id ASC";
//Run query
$patient_types = $dbconn->select($query);
?>

<?php
//
// Prepare update form
//

//Check whether the id is set
if (isset($_GET["id"])) {
	$id = $_GET["id"];
	$id = mysqli_real_escape_string($dbconn->link, $id);
	//Select the detials of the patient
	//Create a query
	$query = "	SELECT `id`, `patient_id`, `patient_name`, `patient_registered_date`, `patient_birthday`, `patient_phone1`, `patient_phone2`, `patient_email`, `patient_address`, `patient_district`, `patient_type`, `staff_name`
				FROM `patients`
				LEFT JOIN staff ON staff.staff_id=patients.patient_supervisor_id
				WHERE id='$id'";
	//Run query
	$details = $dbconn->select($query);
	//Validation - Database class return 0 for errors
	if (is_int($details)) {
		header("Location:patient_profile.php?msgtype=error&msg=" . urlencode($dbconn->error));
	} else {
		$details = $details->fetch_assoc();
	}
}


?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Change patient details</h1>
	<p>Change patient details here.</p>
	<p>Patients' system ID - <?php if (isset($id)) {
									echo $id;
								} ?></p>
	<p><?php print_url_message() ?></p>
</div>

<!--Registration form-->
<div class="container my-5 ">
	<div class="row">
		<div class="col-lg-5 col-md-7 box-white">
			<form class="mb-3" method="post" action="update_patient.php">
				<div class="mb-3">
					<label class="form-label">Patient ID (Recommended to keep unchanged)</label>
					<input name="patient_id" type="text" class="form-control" placeholder="Enter patient ID" value="<?php echo $details["patient_id"] ?>" disabled>
					<input name="patient_id" type="hidden" value="<?php echo $details["patient_id"] ?>">
					<input name="id" type="hidden" value="<?php echo $details["id"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Name <span class="form-required">*</span></label>
					<input name="patient_name" type="text" class="form-control" placeholder="Enter patient's name" value="<?php echo $details["patient_name"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient type</label>
					<select name="patient_type" class="form-select">
						<?php if (!is_int($patient_types)) : ?>
							<?php while ($row = $patient_types->fetch_assoc()) : //Add custom patient types to the form. (Above 2 types are defaults) 
							?>
								<?php
								if ($details["patient_type"] == $row["patient_type_id"]) {
									$option_selected = "SELECTED";
								} else {
									$option_selected = "";
								}
								?>
								<option value="<?php echo $row["patient_type_id"] ?>" <?php echo $option_selected ?>> <?php echo $row["patient_type_name"] ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class=" mb-3">
					<label class="form-label">Patient birthday</label>
					<input name="patient_birthday" type="date" class="form-control" value="<?php echo $details["patient_birthday"] ?>">
				</div>
				<div class=" mb-3">
					<label class="form-label">Patient registered date</label>
					<input name="patient_registered_date" type="datetime-local" class="form-control" value="<?php echo $details["patient_registered_date"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Phone 1</label>
					<input name="patient_phone1" type="text" class="form-control" placeholder="Enter patient's Phone number. Eg:0771232344" value="<?php echo $details["patient_phone1"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Phone 2</label>
					<input name="patient_phone2" type="text" class="form-control" placeholder="Enter additional patient's Phone number. Eg:0771232344" value="<?php echo $details["patient_phone2"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient E-mail</label>
					<input name="patient_email" type="email" class="form-control" placeholder="Enter patient's e-mail address" value="<?php echo $details["patient_email"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Address</label>
					<textarea name="patient_address" class="form-control" rows="4" placeholder="Enter patient's address"><?php echo $details["patient_address"] ?></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Patient District</label>
					<input name="patient_district" type="text" class="form-control" placeholder="Enter patient's District" value="<?php echo $details["patient_district"] ?>">
				</div>
				<div>
					<p><span class="form-required">* Required</span></p>
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="submit_update" type="submit" value="Change Details">
					<a class="me-2 btn btn-outline-secondary" href="patient_profile.php?patient_id=<?php echo $details["patient_id"] ?>">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>



<?php
include("includes/footer.php");
?>