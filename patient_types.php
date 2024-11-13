<?php
include("includes/header.php");

/*
 * ===================== Getting patient type Data   =====================
 */

//DB Connect
$dbconn = new Database;

//Create a query - Get patient types
$query = "	SELECT patient_type_id, patient_type_name, patient_type_description
			FROM `patient_types` 
			ORDER BY patient_type_id ASC";
//Run query
$patient_types = $dbconn->select($query);
?>

<?php
/*
 * ===================== Insert form data into Database  =====================
 */

if (isset($_POST["submit"])) {
	//Insert new patient data into database
	//Preare values
	$patient_type_name	= mysqli_real_escape_string($dbconn->link, $_POST["patient_type_name"]);
	$patient_type_description	= mysqli_real_escape_string($dbconn->link, $_POST["patient_type_description"]);

	//Basic validation - Empty fields check
	if ($patient_type_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:patient_types.php?msgtype=error&msg=". urlencode($error));
	} else {
		//Create a query
		$query = "	INSERT INTO `patient_types` (`patient_type_name`, `patient_type_description`)
					VALUES ('$patient_type_name', '$patient_type_description')";
		//Run query
		$insert_status = $dbconn->insert($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($insert_status == 1){
			//Redirect
			header("Location:patient_types.php?msgtype=info&msg=".urlencode("Patient type successfully added to the system"));
		} else {
			header("Location:patient_types.php?msgtype=error&msg=". urlencode($dbconn->error));
		}	
	}
}
?>

<?php
/*
 * =====================  Delete a patient type if requested   =====================
 */

if (isset($_POST["delete"])) {
	//Assign vars
	$deletable_patient_type_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["deletable_patient_type_id"]));
	$deletable_patient_type_name = (mysqli_real_escape_string($dbconn->link, $_POST["deletable_patient_type_name"]));
	//Delete
	if ($deletable_patient_type_id <= 2) {
		$error = "The patient type '" . $deletable_patient_type_name . "' can't be deleted since it's a system default role.";
		header("Location:patient_types.php?msg=" . urlencode($error) . "&msgtype=error");
	} else {
		//Proceed to deletion
		//Reassign currently assigned patient members to default patient type - 'Clinic'.
		$query = "	UPDATE `patients`
					SET `patient_type` = 1
					WHERE `patient_type` = $deletable_patient_type_id";
		$update_status = $dbconn->update($query);
		//Validate update
		if ($update_status == 0) {
			$error = $dbconn->error;
			if ($error != ("Error : No matches available in the system for the given input")){
				header("Location:patient_types.php?id=$id&msg=" . urlencode($error) . "&msgtype=error");
			}	
		}
		//Proceed to delete role if previous update query was successful
		//Delete role
		$query = "	DELETE FROM patient_types
					WHERE patient_type_id = $deletable_patient_type_id";
		$delete_status = $dbconn->delete($query);
		if ($delete_status == 1) {
			$info = "The patient type '" . $deletable_patient_type_name . "' successfully removed.<br>The patients previously assigned to it has been reassigned to 'Clinic' type";
			header("Location:patient_types.php?msg=" . urlencode($info) . "&msgtype=info");
		} else {
			$error = $dbconn->error;
			header("Location:patient_types.php?id=$id&msg=" . urlencode($error) . "&msgtype=error");
		}
	}
}
?>

<?php
/*
 * ===================== Update a staff role if requested =====================
 */
if (isset($_POST["update"])) {
	//Assign vars
	$updatable_patient_type_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_type_id"]));
	$updatable_patient_type_name = (mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_type_name"]));

	//Get default form data
	$query = "	SELECT *
				FROM patient_types
				WHERE patient_type_id = '$updatable_patient_type_id' ";
	$updatable_details = $dbconn->select($query);
	$updatable_details = $updatable_details->fetch_assoc();
}

if (isset($_POST["submit_update"])) {
	//Proceed to updation
	//update role
	$updatable_patient_type_id = mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_type_id"]);
	$patient_type_name = mysqli_real_escape_string($dbconn->link, $_POST["patient_type_name"]);
	$patient_type_description = mysqli_real_escape_string($dbconn->link, $_POST["patient_type_description"]);
	$query = "	UPDATE patient_types
				SET	patient_type_name = '$patient_type_name',
					patient_type_description = '$patient_type_description'
				WHERE patient_type_id = $updatable_patient_type_id";
	echo $query;
	$update_status = $dbconn->update($query);
	if ($update_status == 1) {
		$info = "The staff role '" . $patient_type_name . "' successfully edited.";
		header("Location:patient_types.php?msg=" . urlencode($info) . "&msgtype=info");
	} else {
		$error = $dbconn->error;
		header("Location:patient_types.php?msg=" . urlencode($error) . "&msgtype=error");
	}
}

?>


<!-- Title -->
<div class="container my-5 box-white">
	<h1>Patient types</h1>
	<p>Add and manage types of patients here.</p>
	<p><?php print_url_message() ?></p>
</div>

<?php if (isset($updatable_details)) : ?>
	<!--Edit staff form-->
	<div class="container my-5">
		<div class="row">
			<div class="col-lg-5 col-md-8 box-white ">
				<h2>Edit patient type : <?php echo $updatable_details["patient_type_name"] ?> </h2>
				<form class="mb-3" method="post" action="patient_types.php">
					<div class="mb-3">
						<label class="form-label">Staff role <span class="form-required">*</span></label>
						<input name="patient_type_name" type="text" class="form-control" placeholder="Enter new Staff role" value="<?php echo $updatable_details["patient_type_name"] ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Staff role description</label>
						<textarea name="patient_type_description" class="form-control" rows="3" placeholder="Describe staff role"><?php echo $updatable_details["patient_type_description"] ?></textarea>
					</div>
					<div>
						<p><span class="form-required">* Required</span></p>
					</div>
					<div class="my-4">
						<input type="hidden" name="updatable_patient_type_id" value="<?php echo $updatable_patient_type_id?>">
						<input class="me-2 btn btn-outline-primary" name="submit_update" type="submit" value="Change">
						<a class="me-2 btn btn-outline-secondary" href="patient_types.php">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Patient type list -->
<div class="container my-5 box-white">
	<div class="row">
		<div class="col-8">
			<h2>Patient types</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th scope="col">Patient Type</th>
						<th scope="col">Description</th>
						<th scope="col">Amount of patients</th>
						<th scope="col">Patients list</th>
						<th scope="col">Edit type</th>
						<th scope="col">Delete type</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $patient_types->fetch_assoc()) : ?>
						<tr>
							<td><?php echo $row["patient_type_name"] ?></td>
							<td><?php echo $row["patient_type_description"] ?></td>
							<?php
							//Create a query - Get amount of patients of patient types
							$pateint_type_id = $row['patient_type_id'];
							$query = "	SELECT COUNT(patient_id)
											FROM patients
											WHERE patient_type=$pateint_type_id";
							//Run query
							$patient_types_amount = $dbconn->select($query);
							$patient_types_amount = $patient_types_amount->fetch_assoc();
							//print_r($patient_types_amount);
							$patient_types_amount = $patient_types_amount["COUNT(patient_id)"];
							//print_r($patient_types_amount);
							?>
							<td><?php echo $patient_types_amount ?></td>
							<td><a href="patients.php?type=<?php echo urlencode($row["patient_type_id"]) ?>&typename=<?php echo urlencode($row["patient_type_name"]) ?>"><button type="button" class="btn btn-primary btn-table">list</button></a></td>
							<td>
								<form action="patient_types.php" method="POST">
									<input type="hidden" name="updatable_patient_type_id" value="<?php echo $row["patient_type_id"] ?>">
									<input type="hidden" name="updatable_patient_type_name" value="<?php echo $row["patient_type_name"] ?>">
									<input class="btn btn-outline-secondary btn-table" type="submit" name="update" value="Edit">
								</form>
							</td>
							<td>
								<form action="patient_types.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this patient type? All the mebmers previously assigned to it will be reassigned to the default type.');">
									<input type="hidden" name="deletable_patient_type_id" value="<?php echo $row["patient_type_id"] ?>">
									<input type="hidden" name="deletable_patient_type_name" value="<?php echo $row["patient_type_name"] ?>">
									<input class="btn btn-outline-danger btn-table" type="submit" name="delete" value="Remove">
								</form>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<!--Add patient type form-->
<div class="container my-5">
	<div class="row">
		<div class="col-lg-5 col-md-7 box-white ">
			<h2>Add new patient type</h2>
			<form class="mb-3" method="post" action="patient_types.php">
				<div class="mb-3">
					<label class="form-label">Patient type <span class="form-required">*</span></label>
					<input name="patient_type_name" type="text" class="form-control" placeholder="Enter new patient type">
				</div>
				<div class="mb-3">
					<label class="form-label">Patient type description</label>
					<textarea name="patient_type_description" class="form-control" rows="3" placeholder="Describe staff role"></textarea>
				</div>
				<div>
					<p><span class="form-required">* Required</span></p>
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="submit" type="submit" value="Add Type">
				</div>
			</form>
		</div>
	</div>
</div>


<?php
include("includes/footer.php");
?>