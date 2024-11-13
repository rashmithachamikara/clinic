<?php
include("includes/header.php");


/*
 * ===================== Getting queue type Data  =====================
 */

//DB Connect
$dbconn = new Database;

//Create a query - Get queue types
$query = "	SELECT patient_attendance_queue_id, patient_attendance_queue_name, patient_attendance_queue_description, patient_attendance_queue_color, patient_attendance_queue_color
			FROM `patient_attendance_queue` 
			WHERE is_deleted = 0
			ORDER BY patient_attendance_queue_id ASC";
//Run query
$queue_types = $dbconn->select($query);
?>

<?php
/*
 * ===================== Insert form data into Database =====================
 */

if (isset($_POST["submit"])) {
	//Insert new queue type into database
	//Prepare values
	$patient_attendance_queue_name	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_name"]);
	$patient_attendance_queue_description	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_description"]);
	$patient_attendance_queue_color	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_color"]);

	//Basic validation - Empty fields check
	if ($patient_attendance_queue_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:queue_types.php?msgtype=error&msg=" . urlencode($error));
	} else {
		//Create a query
		$query = "	INSERT INTO `patient_attendance_queue` (`patient_attendance_queue_name`, `patient_attendance_queue_description`,`patient_attendance_queue_color`)
					VALUES ('$patient_attendance_queue_name', '$patient_attendance_queue_description', '$patient_attendance_queue_color')";
		//Run query
		$insert_status = $dbconn->insert($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($insert_status == 1) {
			//Redirect
			header("Location:queue_types.php?msgtype=info&msg=" . urlencode("New queue type '$patient_attendance_queue_name' successfully added to the system"));
		} else {
			header("Location:queue_types.php?msgtype=error&msg=" . urlencode($insert_status));
		}
	}
}
?>

<?php
/*
 * ===================== Delete a queue type if requested =====================
 */

if (isset($_POST["delete"])) {
	//Assign vars
	$deletable_patient_attendance_queue_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["deletable_patient_attendance_queue_id"]));
	$deletable_patient_attendance_queue_name = (mysqli_real_escape_string($dbconn->link, $_POST["deletable_patient_attendance_queue_name"]));
	//Delete
	if ($deletable_patient_attendance_queue_id <= 3) {
		$error = "The queue type '" . $deletable_patient_attendance_queue_name . "' can't be deleted since it's a system default queue.";
		header("Location:queue_types.php?msg=" . urlencode($error) . "&msgtype=error");
	} else {
		//Delete queue type (Instead of deleting, make is_deleted = true. Affect historical data!)
		$query = "	UPDATE patient_attendance_queue
					SET is_deleted = 1 
					WHERE patient_attendance_queue_id = '$deletable_patient_attendance_queue_id'";
		var_dump($query);
		$delete_status = $dbconn->update($query);
		if ($delete_status == 1) {
			$info = "The queue type '" . $deletable_patient_attendance_queue_name . "' successfully removed.";
			header("Location:queue_types.php?msg=" . urlencode($info) . "&msgtype=info");
		} else {
			$error = $dbconn->error;
			header("Location:queue_types.php?msg=" . urlencode($error) . "&msgtype=error");
		}
	}
}
?>

<?php
/*
 * ===================== Update a queue type if requested =====================
 */
if (isset($_POST["update"])) {
	//Assign vars
	$updatable_patient_attendance_queue_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_attendance_queue_id"]));
	$updatable_patient_attendance_queue_name = (mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_attendance_queue_name"]));
	$patient_attendance_queue_color = (mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_color"]));

	//Get default form data
	$query = "	SELECT *
				FROM patient_attendance_queue
				WHERE patient_attendance_queue_id = '$updatable_patient_attendance_queue_id' ";
	$updatable_details = $dbconn->select($query);
	$updatable_details = $updatable_details->fetch_assoc();
}

if (isset($_POST["submit_update"])) {
	//Proceed to updation
	//update role
	$updatable_patient_attendance_queue_id = mysqli_real_escape_string($dbconn->link, $_POST["updatable_patient_attendance_queue_id"]);
	$patient_attendance_queue_name = mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_name"]);
	$patient_attendance_queue_description = mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_description"]);
	$patient_attendance_queue_color = mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_color"]);
	$query = "	UPDATE patient_attendance_queue
				SET	patient_attendance_queue_name = '$patient_attendance_queue_name',
					patient_attendance_queue_description = '$patient_attendance_queue_description',
					patient_attendance_queue_color = '$patient_attendance_queue_color'
				WHERE patient_attendance_queue_id = $updatable_patient_attendance_queue_id";
	echo $query;
	$update_status = $dbconn->update($query);
	if ($update_status == 1) {
		$info = "The queue type '" . $patient_attendance_queue_name . "' successfully edited.";
		header("Location:queue_types.php?msg=" . urlencode($info) . "&msgtype=info");
	} else {
		$error = $dbconn->error;
		header("Location:queue_types.php?msg=" . urlencode($error) . "&msgtype=error");
	}
}

?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Queue types</h1>
	<p>Add and manage queue types here.</p>
	<p><?php print_url_message() ?></p>
</div>

<?php if (isset($updatable_details)) : ?>
	<!--Edit queue type form-->
	<div class="container my-5">
		<div class="row">
			<div class="col-lg-5 col-md-8 box-white ">
				<h2>Edit queue type: <?php echo $updatable_details["patient_attendance_queue_name"] ?> </h2>
				<form class="mb-3" method="post" action="queue_types.php">
					<div class="mb-3">
						<label class="form-label">Queue name<span class="form-required">*</span></label>
						<input name="patient_attendance_queue_name" type="text" class="form-control" placeholder="Enter new queue type name" value="<?php echo $updatable_details["patient_attendance_queue_name"] ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Queue description</label>
						<textarea name="patient_attendance_queue_description" class="form-control" rows="3" placeholder="Describe queue type"><?php echo $updatable_details["patient_attendance_queue_description"] ?></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Queue type color <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="in the 'Queue' management section, this color will be displayed to easily identify the patients assigned to this queue.">?</span></label>
						<input name="patient_attendance_queue_color" type="color" class="form-control form-control-color" value="<?php echo $updatable_details["patient_attendance_queue_color"] ?>">
					</div>
					<div>
						<p><span class="form-required">* Required</span></p>
					</div>
					<div class="my-4">
						<input type="hidden" name="updatable_patient_attendance_queue_id" value="<?php echo $updatable_patient_attendance_queue_id ?>">
						<input class="me-2 btn btn-outline-primary" name="submit_update" type="submit" value="Change">
						<a class="me-2 btn btn-outline-secondary" href="queue_types.php">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- queue type list -->
<div class="container my-5 box-white">
	<div class="row">
		<div class="col-lg-8">
			<h2>Queue types</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th scope="col">Queue type</th>
						<th scope="col">Description</th>
						<th scope="col">Color</th>
						<th scope="col">Edit queue</th>
						<th scope="col">Delete queue</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $queue_types->fetch_assoc()) : ?>
						<tr>
							<td><?php echo $row["patient_attendance_queue_name"] ?></td>
							<td><?php echo $row["patient_attendance_queue_description"] ?></td>
							<td><span style="background-color:<?php echo $row["patient_attendance_queue_color"] ?>;width:50px" class="badge rounded-pill">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
							<td>
								<form action="queue_types.php" method="POST">
									<input type="hidden" name="updatable_patient_attendance_queue_id" value="<?php echo $row["patient_attendance_queue_id"] ?>">
									<input type="hidden" name="updatable_patient_attendance_queue_name" value="<?php echo $row["patient_attendance_queue_name"] ?>">
									<input type="hidden" name="patient_attendance_queue_color" value="<?php echo $row["patient_attendance_queue_color"] ?>">
									<input class="btn btn-outline-secondary btn-table" type="submit" name="update" value="Edit">
								</form>
							</td>
							<td>
								<form action="queue_types.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this queue type?');">
									<input type="hidden" name="deletable_patient_attendance_queue_id" value="<?php echo $row["patient_attendance_queue_id"] ?>">
									<input type="hidden" name="deletable_patient_attendance_queue_name" value="<?php echo $row["patient_attendance_queue_name"] ?>">
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

<!--Add queue types form-->
<div class="container my-5">
	<div class="row">
		<div class="col-lg-5 col-md-8 box-white ">
			<h2>Add new queue types</h2>
			<form class="mb-3" method="post" action="queue_types.php">
				<div class="mb-3">
					<label class="form-label">Queue type <span class="form-required">*</span></label>
					<input name="patient_attendance_queue_name" type="text" class="form-control" placeholder="Enter new Queue type">
				</div>
				<div class="mb-3">
					<label class="form-label">Queue type description</label>
					<textarea name="patient_attendance_queue_description" class="form-control" rows="3" placeholder="Describe Queue type"></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Queue type color <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="in the 'Queue' management section, this color will be displayed to easily identify the patients assigned to this queue.">?</span></label>
					<input name="patient_attendance_queue_color" type="color" class="form-control form-control-color" value="#d9d9d9">
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