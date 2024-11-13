<?php
include("includes/header.php");

//
// Insert form data into Database
//

$dbconn = new Database;

if (isset($_POST["submit"])) {
	//Insert new staff data into database
	//Preare values
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
		header("Location:add_staff.php?msgtype=error&msg=" . urlencode($error));
	} else {
		//Create a query
		$query = "	INSERT INTO `staff` (`staff_name`, `staff_role_id`, `staff_birthday`, `staff_phone1`, `staff_phone2`, `staff_email`, `staff_address`, `staff_color`)
					VALUES ('$staff_name', '$staff_role_id', '$staff_birthday', '$staff_phone1', '$staff_phone2', '$staff_email', '$staff_address', '$staff_color' )";
		//Run query
		$insert_status = $dbconn->insert($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($insert_status == 1) {
			//Redirect
			header("Location:staff.php?msgtype=info&msg=" . urlencode("Staff member successfully added to the system"));
		} else {
			header("Location:add_staff.php?msgtype=error&msg=" . urlencode($dbconn->error));
		}
	}
}
?>

<?php
//Get staff role data 
$query = "	SELECT staff_role_id,staff_role_name
			FROM `staff_role`
			ORDER BY staff_role_id ASC";
//Run query
$staff_roles = $dbconn->select($query);
?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Add new Staff member</h1>
	<p>Add new Staff members here</p>
	<p><?php print_url_message() ?></p>
</div>

<!--Registration form-->
<div class="container my-5 ">
	<div class="row">
		<div class="col-lg-5 col-md-8 box-white">
			<form class="mb-3" method="post" action="add_staff.php">
				<div class="mb-3">
					<label class="form-label">Staff member Name <span class="form-required">*</span></label>
					<input name="staff_name" type="text" class="form-control" placeholder="Enter Staff member's name">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member role</label>
					<select name="staff_role_id" class="form-select">
						<?php if (!is_int($staff_roles)) : ?>
							<?php while ($row = $staff_roles->fetch_assoc()) : ?>
								<option value="<?php echo $row["staff_role_id"] ?>"><?php echo $row["staff_role_name"] ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member birthday</label>
					<input name="staff_birthday" type="date" class="form-control">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Phone 1</label>
					<input name="staff_phone1" type="text" class="form-control" placeholder="Enter Staff member's Phone number. Eg:0771232344">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Phone 2</label>
					<input name="staff_phone2" type="text" class="form-control" placeholder="Enter additional Staff member's Phone number. Eg:0771232344">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member E-mail</label>
					<input name="staff_email" type="email" class="form-control" placeholder="Enter Staff member's e-mail address">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member Address</label>
					<textarea name="staff_address" class="form-control" rows="4" placeholder="Enter Staff member's address"></textarea>
				</div>
				<div class="mb-3">
					<label class="form-label">Staff member color <span type="button" class="badge text-bg-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="in the 'Queue' management section, this color will be displayed to easily identify the patients assigned to this staff member.">?</span></label>
					<input name="staff_color" type="color" class="form-control form-control-color" value="#203040">
				</div>
				<div>
					<p><span class="form-required">* Required</span></p>
				</div>
				<div class="my-4">
					<input class="me-2 btn btn-outline-primary" name="submit" type="submit" value="Add Staff member">
					<a class="me-2 btn btn-outline-secondary" href="staff.php">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>



<?php
include("includes/footer.php");
?>