<?php
include("includes/header.php");

//DB Connect
$dbconn = new Database;
$today = date("Y-m-d");


/*
 * ===================== Get requested patient data =====================
 */

//Check if patient_id parameter is set
if (isset($_POST["submit_check"])) {
	$patient_id = $_POST["patient_id"];
} elseif (isset($_GET["patient_id"])) {
	//set patient id
	$patient_id = $_GET["patient_id"];
}

//Check if $patient_id is set by any means
if (isset($patient_id)) {
	//Select the details of the given patient
	//Create a query
	$query = "	SELECT `id`, `patients`.`patient_id`, `patient_name`, `patient_phone1`, `patient_district`, `patient_type_id`, `patient_type_name`, `staff_id`, `staff_name`, `staff_role`.`staff_role_id` , `staff_role_name`
				FROM `patients`
				JOIN patient_types ON patient_types.patient_type_id=patients.patient_type
				LEFT JOIN staff ON staff.staff_id=patients.patient_supervisor_id
				LEFT JOIN staff_role ON staff.staff_role_id=staff_role.staff_role_id
				WHERE patient_id='$patient_id'";
	//Run query
	$details = $dbconn->select($query);
	//Validation - Database class return 0 for errors
	if (is_int($details)) {
		header("Location:queue.php?msgtype=error&msg=" . urlencode($dbconn->error));
	} else {
		$details = $details->fetch_assoc();
	}
	//var_dump($details);

	$id = $details['id'];
	//Check if the patient is already in the today's queue
	$query = "	SELECT patient_attendance_id 
				FROM patient_attendance
				WHERE patient_attendance_date = '$today' AND patient_id=$id";
	$attendance_record = $dbconn->select($query);
	if (is_int($attendance_record)) { //If int, the returned value is 0. 0 means no records availbale
		$in_todays_queue = false;
	} else { //Not int means a record is avaialble
		$in_todays_queue = true;
	}
	//var_dump($in_todays_queue);

	//Check patient method
	//Automatically check the patient method depending on the staff_role_id.
	if ($details["staff_role_id"] != "") { //If staff_role_id is non-empty, the patient is assigned
		$assigned_to_supervisor = true;
	} else { //If empty, not assigned
		$assigned_to_supervisor = false;
	}

	//Set default queue (If not a supervisor queue)
	if ($assigned_to_supervisor == false) {
		//Check in which queue this patient was in at the last attendance
		$query = "	SELECT patient_attendance_queue_id, patient_attendance_supervisor_id
					FROM patient_attendance 
					WHERE patient_id=$id
					ORDER BY patient_attendance_id DESC
					LIMIT 1";
		$patient_last_queue_id = $dbconn->select($query);
		//var_dump($patient_last_queue_id);

		if (is_int($patient_last_queue_id) or is_null($patient_last_queue_id)) { //Db return 0 if no record
			//If never added to a queue before, set:
			$default_attendance_queue_id = "1";
		} else {
			//If added to a queue before, set:
			$patient_last_queue_id = ($patient_last_queue_id->fetch_assoc())["patient_attendance_queue_id"];
		}

		if (is_null($patient_last_queue_id)) {
			//Never added to a normal queue, but added to a supervisor queue before. If so:
			$default_attendance_queue_id = "1";
		} else {
			$default_attendance_queue_id = $patient_last_queue_id;
		}
		//var_dump($patient_default_queue_id);
	}
}
?>

<?php
/*
 * ===================== Get data to fill the default values of the form =====================
 */

//Create a query - Get staff
$query = "	SELECT staff_id, staff_name
			FROM `staff` 
			ORDER BY staff_name ASC";
//Run query
$staff_members = $dbconn->select($query);
//Validation - Database class return 0 for errors
if (is_int($staff_members)) {
	header("Location:queue.php?msgtype=error&msg=" . urlencode($dbconn->error));
}

//Create a query - Get attendance queues
$query = "	SELECT patient_attendance_queue_id, patient_attendance_queue_name
			FROM `patient_attendance_queue` 
			WHERE is_deleted = false
			ORDER BY patient_attendance_queue_id ASC";
//Run query
$attendance_queues = $dbconn->select($query);
//Validation - Database class return 0 for errors
if (is_int($attendance_queues)) {
	header("Location:queue.php?msgtype=error&msg=" . urlencode($dbconn->error));
}

/*
 * ===================== Select all patients for popup =====================
 */
//Create a query
$query = "	SELECT id,patient_id, patient_name, patient_phone1, patient_registered_date, patient_type_name
			FROM `patients`,`patient_types` 
			WHERE  patients.patient_type=patient_types.patient_type_id
			ORDER BY id ASC";
//Run query
$patients = $dbconn->select($query);


/*
 * ===================== Get attendance details =====================
 */
//Create a query - 
$query = "	SELECT patients.patient_id, patient_name, patient_attendance_time, patient_attendance_position, patient_attendance_queue_name, patient_attendance_queue_color, staff_name, staff_color
			FROM patient_attendance
			JOIN patients ON patients.id=patient_attendance.patient_id
			LEFT JOIN patient_attendance_queue ON patient_attendance_queue.patient_attendance_queue_id = patient_attendance.patient_attendance_queue_id
			LEFT JOIN staff ON staff.staff_id = patient_attendance.patient_attendance_supervisor_id
			WHERE patient_attendance_date = '$today'
			ORDER BY patient_attendance_id ASC";
$attendance_details = $dbconn->select($query);
?>

<?php
/*
 * ===================== Add attendance record to database =====================
 */

//If adding to a normal queue
if (isset($_POST["submit_attendance_queue"])) {
	//Insert new patient data into database
	//Preare values
	$id 	= mysqli_real_escape_string($dbconn->link, $_POST["id"]); //This is patient system id
	$patient_id = mysqli_real_escape_string($dbconn->link, $_POST["patient_id"]); //This is patinet id (Changable)
	$patient_attendance_queue_id 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_queue_id"]);
	$patient_attendance_remark 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_remark"]);

	//Get last position of the queue
	$query = "	SELECT MAX(`patient_attendance_position`)
				FROM `patient_attendance`
				WHERE patient_attendance_queue_id='$patient_attendance_queue_id' AND patient_attendance_date = '$today' ";
	//Run query
	$queue_last_position = $dbconn->select($query);
	//Set data into variables
	$queue_last_position = intval($queue_last_position->fetch_row()[0]);
	$patient_attendance_position = $queue_last_position + 1;

	//Create a query
	$query = "	INSERT INTO `patient_attendance`(`patient_id`, `patient_attendance_queue_id`, `patient_attendance_position`, `patient_attendance_remark`)
				VALUES ($id, $patient_attendance_queue_id, $patient_attendance_position, '$patient_attendance_remark' ) ";
	//Run query
	$insert_status = $dbconn->insert($query);
	//Validate insertion (The Database class return 1 on success, error on failure)
	if ($insert_status == 1) {
		//Redirect
		header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=info&msg=" . urlencode("Patient successfully added to the queue"));
	} else {
		if (strpos($dbconn->error, "Duplicate") !== false) {
			//Word Found!
			header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=error&msg=" . urlencode("Patient is already in the today's queue"));
		} else {
			header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=error&msg=" . urlencode($dbconn->error) . urlencode($dbconn->link->errorno));
		}
	}
}

//If adding to a supervisor queue
if (isset($_POST["submit_supervisor_queue"])) {
	//Insert new patient data into database
	//Preare values
	$id 	= mysqli_real_escape_string($dbconn->link, $_POST["id"]); //This is patient system id
	$patient_id = mysqli_real_escape_string($dbconn->link, $_POST["patient_id"]); //This is patinet id (Changable)
	$patient_attendance_supervisor_id 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_supervisor_id"]);
	$patient_attendance_remark 	= mysqli_real_escape_string($dbconn->link, $_POST["patient_attendance_remark"]);

	//Get last position of the queue
	$query = "	SELECT MAX(`patient_attendance_position`)
				FROM `patient_attendance`
				WHERE patient_attendance_supervisor_id='$patient_attendance_supervisor_id' AND patient_attendance_date = '$today' ";
	//Run query
	$queue_last_position = $dbconn->select($query);
	//Set data into variables
	$queue_last_position = intval($queue_last_position->fetch_row()[0]);
	$patient_attendance_position = $queue_last_position + 1;

	//Create a query
	$query = "	INSERT INTO `patient_attendance`(`patient_id`, `patient_attendance_supervisor_id`, `patient_attendance_position`, `patient_attendance_remark`)
				VALUES ($id, $patient_attendance_supervisor_id, $patient_attendance_position, '$patient_attendance_remark' ) ";
	//echo ($query);
	//Run query
	$insert_status = $dbconn->insert($query);
	//Validate insertion (The Database class return 1 on success, error on failure)
	if ($insert_status == 1) {
		//Redirect
		header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=info&msg=" . urlencode("Patient successfully added to the queue"));
	} else {
		if (strpos($dbconn->error, "Duplicate") !== false) {
			//Word Found!
			header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=error&msg=" . urlencode("Patient is already in the today's queue"));
		} else {
			header("Location:queue.php?patient_id=" . urlencode($patient_id) . "&msgtype=error&msg=" . urlencode($dbconn->error) . urlencode($dbconn->link->errorno));
		}
	}
}

?>






<!-- Welcome -->
<div class="container box-white">
	<h1>Queue management</h1>
	<form action="queue.php#patient-identification" method="POST" class="row row-cols-lg-auto g-3 align-items-center">
		<div class="col-lg-4 col-md-6">
			<label for="inlineFormInputGroupUsername">Patient ID</label>
			<div class="input-group">
				<input type="text" class="form-control" name="patient_id" placeholder="Patient ID" value="<?php if (isset($details)) {
																												echo $details["patient_id"];
																											} ?>">

				<button type="submit" name="submit_check" class="btn btn-primary">Check</button>
			</div>
		</div>
		<div class="col-12">
			<br class="d-none d-lg-block">
			<button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#patinetListPopup">Select from list</button>
			<a href="queue_types.php"><button type="button" class="btn btn-secondary">Queue types</button></a>
		</div>
	</form>
	<p><?php print_url_message() ?></p>
</div>

<!-- Modal popup - Patient list -->
<div class="modal modal-lg fade" id="patinetListPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Select a patient to identify</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!--Patient list section-->
				<div id="patient-list" class="container ">
					<div class="row">
						<div>
							<!--Patient Search tool-->
							<div class="">
								<form class="">
									<div class="col-md-8">
										<label class="me-3" for="inlineFormInputGroupUsername">Search</label>
										<div class="input-group align-bottom">
											<input type="text" class="form-control" id="searchInput" onkeyup="searchInTable()" placeholder="Search for id, name, registered date, etc...">
										</div>
									</div>
								</form>
							</div>
							<br>
							<!--Patient List table-->
							<div class="table-holder table-holder-short">
								<table id="searchTable" class="table table-bordered">
									<thead>
										<tr>
											<th scope="col">ID</th>
											<th scope="col">Name</th>
											<th scope="col">Registered date</th>
											<th scope="col">Phone</th>
											<th scope="col">Type</th>
											<th scope="col">Select</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!is_int($patients)) : ?>
											<?php while ($row = $patients->fetch_assoc()) : ?>
												<tr>
													<th scope="row"><?php echo $row["patient_id"] ?></th>
													<td><?php echo $row["patient_name"] ?></td>
													<td><?php echo formatDate($row["patient_registered_date"]) ?></td>
													<td><?php echo $row["patient_phone1"] ?></td>
													<td><?php echo $row["patient_type_name"] ?></td>
													<td><a href="queue.php?patient_id=<?php echo $row["patient_id"] ?>#patient-identification"><button type="button" class="btn btn-primary btn-table">select</button></a></td>
												</tr>
											<?php endwhile; ?>
										<?php else : ?>
											<td class="url-message-info" colspan="100%">No patients in this context.</td>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!--Patient identification and Patient list-->
<div class="container my-5">
	<div class="row">
		<!--Patient Identification-->
		<div id="patient-identification" class="col-lg-4 box-white">
			<h2>Patient identification</h2>
			<?php if (isset($details)) : ?>
				<p>
					ID : <?php echo $details["patient_id"] ?><br>
					Name : <?php echo $details["patient_name"] ?><br>
					Type : <?php echo $details["patient_type_name"] ?><br>
					<?php if ($details["staff_role_id"] != "") : ?>
						Supervisor : <?php echo $details["staff_name"] ?> <?php echo "(" . $details["staff_role_name"] . ")" ?>
					<?php else : ?>
						Supervisor : Not assigned
					<?php endif; ?>
				</p>
				<?php if ($in_todays_queue == false) : ?>
					<!-- Add patient to queue forms-->
					<?php
					//Automatically check the Patient method in below form depend on whether patient is assigned to a supervisor or not.
					if ($assigned_to_supervisor == true) {
						$queue_checked = "";
						$supervisor_checked = "checked";
						$default_hidden_section_id = "queue_section";
					} else {
						$queue_checked = "checked";
						$supervisor_checked = "";
						$default_hidden_section_id = "supervisor_section";
					}
					?>
					<div class="mb-3">
						<label class="form-label">Patient method</label><br>
						<input id="queueCheck" name="queue_method" value="1" type="radio" <?php echo $queue_checked ?>> Queue<br>
						<input id="supervisorCheck" name="queue_method" value="2" type="radio" <?php echo $supervisor_checked ?>> Supervisor<br>
					</div>
					<!-- Queue form -->
					<form id="queue_section" class="mb-3" method="post" action="queue.php">
						<div class="mb-3">
							<label class="form-label">Attendance Queue</label>
							<select name="patient_attendance_queue_id" class="form-select">
								<?php while ($attendance_queue = $attendance_queues->fetch_assoc()) : ?>
									<?php
									if ($attendance_queue["patient_attendance_queue_id"] == $default_attendance_queue_id) {
										$option_selected = "SELECTED";
									} else {
										$option_selected = "";
									}
									?>
									<option value="<?php echo $attendance_queue["patient_attendance_queue_id"] ?>" <?php echo $option_selected ?>><?php echo $attendance_queue["patient_attendance_queue_name"] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="mb-3">
							<label class="form-label">Remarks</label>
							<textarea class="form-control" name="patient_attendance_remark" rows="2" cols="50"></textarea>
						</div>
						<input type="hidden" name="id" value="<?php echo $details["id"] ?>">
						<input type="hidden" name="patient_id" value="<?php echo $details["patient_id"] ?>">
						<button type="submit" name="submit_attendance_queue" class="btn btn-primary">Add attendance</button>
					</form>
					<!-- Supervisor form -->
					<form id="supervisor_section" class="mb-3" method="post" action="queue.php">
						<div class="mb-3">
							<label class="form-label">Supervisor</label>
							<select name="patient_attendance_supervisor_id" class="form-select">
								<?php while ($staff_member = $staff_members->fetch_assoc()) : ?>
									<?php
									//Set default option
									if ($details["staff_id"] == $staff_member["staff_id"]) {
										$option_selected = "SELECTED";
									} else {
										$option_selected = "";
									}
									?>
									<option value="<?php echo $staff_member["staff_id"] ?>" <?php echo $option_selected ?>><?php echo $staff_member["staff_name"] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="mb-3">
							<label class="form-label">Remarks</label>
							<textarea class="form-control" name="patient_attendance_remark" rows="2" cols="50"></textarea>
						</div>
						<input type="hidden" name="id" value="<?php echo $details["id"] ?>">
						<input type="hidden" name="patient_id" value="<?php echo $details["patient_id"] ?>">
						<button type="submit" name="submit_supervisor_queue" class="btn btn-primary">Add attendance</button>
					</form>
					<script>
						//Change form based on select lists
						/*
						$('#patient_attendance_queue_id').on('change', function() {
							if (this.value == '2')
								$("#supervisor_section").show();
							else
								$("#supervisor_section").hide();
						}).trigger("change");
						*/

						//Initially hide or show
						$('#<?php echo $default_hidden_section_id ?>').hide();

						//Change form based on radios

						$('#queueCheck').click(function() {
							$('#queue_section').show();
							$('#supervisor_section').hide();
						});
						$('#supervisorCheck').click(function() {
							$('#queue_section').hide();
							$('#supervisor_section').show();
						});
					</script>
				<?php else : ?>
					<p class="url-message-info">The patient is in today's queue!</p>
				<?php endif; ?>
				<a href="patient_profile.php?patient_id=<?php echo $details["patient_id"] ?>" class="btn btn-secondary">view profile</a>
			<?php else : ?>
				<p class="url-message-info">No valid patient ID provided to analyze. <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Either type in a valid patient ID or use the 'Select from list' option above.">?</span> </p>
			<?php endif; ?>
		</div>

		<div class="col-lg-1"></div>
		<!--Patient queue-->
		<div class="col-lg-7 mt-md-5 mt-lg-0 box-white">
			<h2>Patient queue summary</h2>
			<div class="col-md-8">
				<label class="me-3" for="inlineFormInputGroupUsername">Search</label>
				<div class="input-group align-bottom mb-3">
					<input type="text" class="form-control" id="queueTableSearchInput" onkeyup="searchInTable('queueTableSearchInput','queueTable')" placeholder="Search for id, name">
				</div>
			</div>
			<div class="table-holder">
				<table id="queueTable" class="table table-bordered">
					<thead>
						<tr>
							<th scope="col">ID <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Click a patient ID to identify the patient. Click identify, then patient profile on the Patient identification panel to view patient profile.">?</span></th>
							<th scope="col">Name </th>
							<th scope="col">Queue</th>
							<th scope="col">Time <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Time patient added to the queue. (hh:mm:ss)">?</span></th>
							<th scope="col">Pos</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!is_int($attendance_details)) : ?>
							<?php while ($row = $attendance_details->fetch_assoc()) : ?>
								<?php
								//Check whether queue or supervisor
								if (is_null($row["patient_attendance_queue_name"])) {
									$row_queue = $row["staff_name"];
									$row_color = $row["staff_color"];
								} else {
									$row_queue = $row["patient_attendance_queue_name"];
									$row_color = $row["patient_attendance_queue_color"];
								}
								?>
								<tr>
									<th scope="row"><a class="" href="queue.php?patient_id=<?php echo $row["patient_id"] ?>#patient-identification"> <?php echo $row["patient_id"] ?> </a></th>
									<td><?php echo $row["patient_name"] ?></td>
									<td><?php echo $row_queue ?></td>
									<td class="text-nowrap"><?php echo formatTime($row["patient_attendance_time"]) ?></td>
									<td><span style="background-color:<?php echo $row_color ?>;" class="badge queue-pos-badge rounded-pill"><?php echo $row["patient_attendance_position"] ?></span></td>
								</tr>
							<?php endwhile; ?>
						<?php else : ?>
							<td class="url-message-info" colspan="100%">Greetings! No patients in the todays queue yet.</td>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<?php
include("includes/footer.php");
?>