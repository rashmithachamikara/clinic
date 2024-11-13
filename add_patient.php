<?php
include("includes/header.php");

//
// Insert form data into Database
//

$dbconn = new Database;

if (isset($_POST["submit"])) {
	//Insert new patient data into database
	//Preare values
	$patient_id 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_id"]);
	$patient_name	= mysqli_real_escape_string($dbconn->link, $_POST["patient_name"]);
	$patient_type	= mysqli_real_escape_string($dbconn->link, $_POST["patient_type"]);
	$patient_birthday	= mysqli_real_escape_string($dbconn->link, $_POST["patient_birthday"]);
	$patient_phone1	= mysqli_real_escape_string($dbconn->link, $_POST["patient_phone1"]);
	$patient_phone2	= mysqli_real_escape_string($dbconn->link, $_POST["patient_phone2"]);
	$patient_email	= mysqli_real_escape_string($dbconn->link, $_POST["patient_email"]);
	$patient_address	= mysqli_real_escape_string($dbconn->link, $_POST["patient_address"]);
	$patient_district	= mysqli_real_escape_string($dbconn->link, $_POST["patient_district"]);
	//Basic validation - Empty fields check
	if ($patient_id == "" || $patient_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:add_patient.php?msgtype=error&msg=" . urlencode($error));
	} else {
		//Create a query
		$query = "	INSERT INTO `patients` (`patient_id`, `patient_name`, `patient_type`, `patient_birthday`, `patient_phone1`, `patient_phone2`, `patient_email`, `patient_address`, `patient_district`)
					VALUES ('$patient_id', '$patient_name', '$patient_type', '$patient_birthday', '$patient_phone1', '$patient_phone2', '$patient_email', '$patient_address', '$patient_district'  )";
		//Run query
		$insert_status = $dbconn->insert($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($insert_status == 1) {
			//Redirect
			header("Location:patients.php?msgtype=info&msg=" . urlencode("Patient successfully added to the system"));
		} else {
			header("Location:add_patient.php?msgtype=error&msg=" . urlencode($dbconn->error));
		}
	}
}
?>

<?php
//Get patient type data

//Query only for patient_type_id>2 because <2 are default patient types which are statically rendered in the form.
$query = "	SELECT patient_type_id,patient_type_name
			FROM `patient_types` 
			WHERE patient_type_id>2
			ORDER BY patient_type_id ASC";
//Run query
$patient_types = $dbconn->select($query);
?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Add new patient</h1>
	<p>Add new patients here</p>
	<p><?php print_url_message() ?></p>
</div>

<!--Registration form-->
<div class="container my-5 ">
	<div class="row">
		<div class="col-lg-5 col-md-7 box-white">
			<form class="mb-3" method="post" action="add_patient.php">
				<div class="mb-3">
					<label class="form-label">Patient ID <span class="form-required">*</span></label>
					<input name="patient_id" type="text" class="form-control" placeholder="Enter patient ID">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Name <span class="form-required">*</span></label>
					<input name="patient_name" type="text" class="form-control" placeholder="Enter patient's name">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient type</label>
					<select name="patient_type" class="form-select">
						<option value="1">Clinic</option>
						<option value="2">Appointment</option>
						<?php if (!is_int($patient_types)) : ?>
							<?php while ($row = $patient_types->fetch_assoc()) : //Add custom patient types to the form. (Above 2 types are defaults) ?>
								<option value="<?php echo $row["patient_type_id"] ?>"><?php echo $row["patient_type_name"] ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Patient birthday</label>
					<input name="patient_birthday" type="date" class="form-control">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Phone 1</label>
					<input name="patient_phone1" type="text" class="form-control" placeholder="Enter patient's Phone number. Eg:0771232344">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Phone 2</label>
					<input name="patient_phone2" type="text" class="form-control" placeholder="Enter additional patient's Phone number. Eg:0771232344">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient E-mail</label>
					<input name="patient_email" type="email" class="form-control" placeholder="Enter patient's e-mail address">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient Address</label>
					<textarea name="patient_address" class="form-control" rows="4" placeholder="Enter patient's address"></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Patient District</label>
					<input name="patient_district" type="text" class="form-control" placeholder="Enter patient's District">
				</div>
				<div>
					<p><span class="form-required">* Required</span></p>
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="submit" type="submit" value="Add Patient">
					<a class="me-2 btn btn-outline-secondary" href="patients.php">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>



<?php
include("includes/footer.php");
?>