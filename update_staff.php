<?php
include("includes/header.php");

//
// Insert updated form data into Database
//

$dbconn = new Database;

if (isset($_POST["submit_update"])) {
	//Insert new patient data into database
	//Preare values
	$staff_id		= mysqli_real_escape_string($dbconn->link, $_POST["staff_id"]);
	$staff_name		= mysqli_real_escape_string($dbconn->link, $_POST["staff_name"]);
	$staff_role_id	= mysqli_real_escape_string($dbconn->link, $_POST["staff_role_id"]);
	$staff_birthday	= mysqli_real_escape_string($dbconn->link, $_POST["staff_birthday"]);
	$staff_phone1	= mysqli_real_escape_string($dbconn->link, $_POST["staff_phone1"]);
	$staff_phone2	= mysqli_real_escape_string($dbconn->link, $_POST["staff_phone2"]);
	$staff_email	= mysqli_real_escape_string($dbconn->link, $_POST["staff_email"]);
	$staff_address	= mysqli_real_escape_string($dbconn->link, $_POST["staff_address"]);
	$staff_color 	= mysqli_real_escape_string($dbconn->link, $_POST["staff_color"]); 
	//Basic validation - Empty fields check
	if ($staff_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:update_staff.php?staff_id=$staff_id&msgtype=error&msg=" . urlencode($error));
		exit();
	} else {
		//Create a query
		$query = "	UPDATE `staff`
					SET 	`staff_name`= '$staff_name',
							`staff_role_id`='$staff_role_id',
							`staff_birthday`='$staff_birthday',
							`staff_phone1`='$staff_phone1',
							`staff_phone2`='$staff_phone2',
							`staff_email`='$staff_email',
							`staff_address`='$staff_address',
							`staff_color`='$staff_color'
					WHERE `staff_id`='$staff_id'";
		//Run query
		$update_status = $dbconn->update($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($update_status == 1) {
			//Redirect
			header("Location:staff_profile.php?staff_id=" . urlencode($staff_id) . "&msgtype=info&msg=" . urlencode("Staff member details successfully changed"));
		} elseif (($dbconn->error) == "Error : No matches available in the system for the given input") {
			header("Location:staff_profile.php?staff_id=" . urlencode($staff_id) . "&msgtype=warning&msg=" . urlencode("Nothing about staff member has changed!"));
		} else {
			header("Location:update_staff.php?staff_id=" . urlencode($staff_id) . "&msgtype=error&msg=" . urlencode($dbconn->error));
		}
	}
}
?>

<?php
//
//Get staff role data
$query = "	SELECT staff_role_id, staff_role_name
			FROM `staff_role`
			ORDER BY staff_role_id ASC";
//Run query
$staff_roles = $dbconn->select($query);
?>

<?php
//
// Prepare update form
//

//Check whether the id is set
if (isset($_GET["staff_id"])) {
	$staff_id = $_GET["staff_id"];
	$staff_id = mysqli_real_escape_string($dbconn->link, $staff_id);
	//Select the detials of the staff
	//Create a query
	$query = "	SELECT `staff_id`, `staff_name`, `staff_birthday`, `staff_phone1`, `staff_phone2`, `staff_email`, `staff_address`, staff.`staff_role_id`, `staff_color`
				FROM `staff`
				LEFT JOIN staff_role ON staff.staff_role_id=staff_role.staff_role_id
				WHERE staff_id='$staff_id'";
	//Run query
	$details = $dbconn->select($query);
	//Validation - Database class return 0 for errors
	if (is_int($details)) {
		header("Location:staff_profile.php?staff_id=$staff_id&msgtype=error&msg=" . urlencode($dbconn->error));
	} else {
		$details = $details->fetch_assoc();
	}
}


?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Change staff member details</h1>
	<p>Change staff member details here.</p>
	<p>staff member' system ID - <?php echo $staff_id ?></p>
	<p><?php print_url_message() ?></p>
</div>

<!--Registration form-->
<div class="container my-5 ">
	<div class="row">
		<div class="col-lg-5 col-md-7 box-white">
		<form class="mb-3" method="post" action="update_staff.php">
				<div class="mb-3">
					<label class="form-label">Staff member Name <span class="form-required">*</span></label>
					<input name="staff_name" type="text" class="form-control" placeholder="Enter Staff member's name" value="<?php echo $details["staff_name"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member role</label>
					<select name="staff_role_id" class="form-select">
						<option value="3" selected>Member</option>
						<?php if (!is_int($staff_roles)) : ?>
							<?php while ($row = $staff_roles->fetch_assoc()) :?>
								<?php
								if ($details["staff_role_id"] == $row["staff_role_id"]) {
									$option_selected = "SELECTED";
								} else {
									$option_selected = "";
								}
								?>
								<option value="<?php echo $row["staff_role_id"] ?>" <?php echo $option_selected?> ><?php echo $row["staff_role_name"] ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member birthday</label>
					<input name="staff_birthday" type="date" class="form-control" value="<?php echo $details["staff_birthday"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Phone 1</label>
					<input name="staff_phone1" type="text" class="form-control" placeholder="Enter Staff member's Phone number. Eg:0771232344" value="<?php echo $details["staff_phone1"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Phone 2</label>
					<input name="staff_phone2" type="text" class="form-control" placeholder="Enter additional Staff member's Phone number. Eg:0771232344" value="<?php echo $details["staff_phone2"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member E-mail</label>
					<input name="staff_email" type="email" class="form-control" placeholder="Enter Staff member's e-mail address" value="<?php echo $details["staff_email"] ?>">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Address</label>
					<textarea name="staff_address" class="form-control" rows="4" placeholder="Enter Staff member's address"><?php echo $details["staff_address"] ?></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member color <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="in the 'Queue' management section, this color will be displayed to easily identify the patients assigned to this staff member.">?</span></label>
					<input name="staff_color" type="color" class="form-control form-control-color" value="<?php echo $details["staff_color"] ?>">
				</div>
				<div>
					<p><span class="form-required">* Required</span></p>
				</div>
				<div>
					<input type="hidden" name="staff_id" value="<?php echo $staff_id?>">
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="submit_update" type="submit" value="Change details">
					<a class="me-2 btn btn-outline-secondary" href="staff_profile.php?staff_id=<?php echo $staff_id?>">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>



<?php
include("includes/footer.php");
?>