<?php
include("includes/header.php");


/*
 * ===================== Getting staff role Data  =====================
 */

//DB Connect
$dbconn = new Database;

//Create a query - Get staff
$query = "	SELECT staff_role_id, staff_role_name, staff_role_description
			FROM `staff_role` 
			ORDER BY staff_role_id ASC";
//Run query
$staff_roles = $dbconn->select($query);
?>

<?php
/*
 * ===================== Insert form data into Database =====================
 */

if (isset($_POST["submit"])) {
	//Insert new staff role into database
	//Prepare values
	$staff_role_name	= mysqli_real_escape_string($dbconn->link, $_POST["staff_role_name"]);
	$staff_role_description	= mysqli_real_escape_string($dbconn->link, $_POST["staff_role_description"]);

	//Basic validation - Empty fields check
	if ($staff_role_name == "") {
		$error = "Please fill out all the required fields!";
		//Redirect
		header("Location:staff_roles.php?msgtype=error&msg=" . urlencode($error));
	} else {
		//Create a query
		$query = "	INSERT INTO `staff_role` (`staff_role_name`, `staff_role_description`)
					VALUES ('$staff_role_name', '$staff_role_description')";
		//Run query
		$insert_status = $dbconn->insert($query);
		//Validate insertion (The Database class return 1 on success, error on failure)
		if ($insert_status == 1) {
			//Redirect
			header("Location:staff_roles.php?msgtype=info&msg=" . urlencode("New staff role '$staff_role_name' successfully added to the system"));
		} else {
			header("Location:staff_roles.php?msgtype=error&msg=" . urlencode($insert_status));
		}
	}
}
?>

<?php
/*
 * ===================== Delete a staff role if requested =====================
 */

if (isset($_POST["delete"])) {
	//Assign vars
	$deletable_staff_role_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["deletable_staff_role_id"]));
	$deletable_staff_role_name = (mysqli_real_escape_string($dbconn->link, $_POST["deletable_staff_role_name"]));
	//Delete
	if ($deletable_staff_role_id <= 3) {
		$error = "The staff role '" . $deletable_staff_role_name . "' can't be deleted since it's a system default role.";
		header("Location:staff_roles.php?msg=" . urlencode($error) . "&msgtype=error");
	} else {
		//Proceed to deletion
		//Reassign currently assigned staff members to default staff role - 'Member'.
		$query = "	UPDATE `staff`
					SET `staff_role_id` = 3
					WHERE `staff_role_id` = $deletable_staff_role_id";
		$update_status = $dbconn->update($query);
		//Validate update
		if ($update_status == 0) {
			$error = $dbconn->error;
			if ($error != ("Error : No matches available in the system for the given input")) {
				header("Location:staff_roles.php?msg=" . urlencode($error) . "&msgtype=error");
			}
		}
		//Proceed to delete role if previous update query was successful
		//Delete role
		$query = "	DELETE FROM staff_role
					WHERE staff_role_id = $deletable_staff_role_id";
		$delete_status = $dbconn->delete($query);
		if ($delete_status == 1) {
			$info = "The staff role '" . $deletable_staff_role_name . "' successfully removed.<br>The staff members previously assigned to it has been reassigned to 'Member' role";
			header("Location:staff_roles.php?msg=" . urlencode($info) . "&msgtype=info");
		} else {
			$error = $dbconn->error;
			header("Location:staff_roles.php?msg=" . urlencode($error) . "&msgtype=error");
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
	$updatable_staff_role_id = intval(mysqli_real_escape_string($dbconn->link, $_POST["updatable_staff_role_id"]));
	$updatable_staff_role_name = (mysqli_real_escape_string($dbconn->link, $_POST["updatable_staff_role_name"]));

	//Get default form data
	$query = "	SELECT *
				FROM staff_role
				WHERE staff_role_id = '$updatable_staff_role_id' ";
	$updatable_details = $dbconn->select($query);
	$updatable_details = $updatable_details->fetch_assoc();
}

if (isset($_POST["submit_update"])) {
	//Proceed to updation
	//update role
	$updatable_staff_role_id = mysqli_real_escape_string($dbconn->link, $_POST["updatable_staff_role_id"]);
	$staff_role_name = mysqli_real_escape_string($dbconn->link, $_POST["staff_role_name"]);
	$staff_role_description = mysqli_real_escape_string($dbconn->link, $_POST["staff_role_description"]);
	$query = "	UPDATE staff_role
				SET	staff_role_name = '$staff_role_name',
					staff_role_description = '$staff_role_description'
				WHERE staff_role_id = $updatable_staff_role_id";
	echo $query;
	$update_status = $dbconn->update($query);
	if ($update_status == 1) {
		$info = "The staff role '" . $staff_role_name . "' successfully edited.";
		header("Location:staff_roles.php?msg=" . urlencode($info) . "&msgtype=info");
	} else {
		$error = $dbconn->error;
		header("Location:staff_roles.php?msg=" . urlencode($error) . "&msgtype=error");
	}
}

?>

<!-- Title -->
<div class="container my-5 box-white">
	<h1>Staff Roles</h1>
	<p>Add and manage staff roles here.</p>
	<p><?php print_url_message() ?></p>
</div>

<?php if (isset($updatable_details)) : ?>
	<!--Edit staff form-->
	<div class="container my-5">
		<div class="row">
			<div class="col-lg-5 col-md-8 box-white ">
				<h2>Edit staff role : <?php echo $updatable_details["staff_role_name"] ?> </h2>
				<form class="mb-3" method="post" action="staff_roles.php">
					<div class="mb-3">
						<label class="form-label">Staff role <span class="form-required">*</span></label>
						<input name="staff_role_name" type="text" class="form-control" placeholder="Enter new Staff role" value="<?php echo $updatable_details["staff_role_name"] ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Staff role description</label>
						<textarea name="staff_role_description" class="form-control" rows="3" placeholder="Describe staff role"><?php echo $updatable_details["staff_role_description"] ?></textarea>
					</div>
					<div>
						<p><span class="form-required">* Required</span></p>
					</div>
					<div class="my-4">
						<input type="hidden" name="updatable_staff_role_id" value="<?php echo $updatable_staff_role_id ?>">
						<input class="me-2 btn btn-outline-primary" name="submit_update" type="submit" value="Change">
						<a class="me-2 btn btn-outline-secondary" href="staff_roles.php">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Staff role list -->
<div class="container my-5 box-white">
	<div class="row">
		<div class="col-lg-8">
			<h2>Staff roles</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th scope="col">Staff role</th>
						<th scope="col">Description</th>
						<th scope="col">Amount of staff members</th>
						<th scope="col">List members</th>
						<th scope="col">Edit role</th>
						<th scope="col">Delete role</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $staff_roles->fetch_assoc()) : ?>
						<tr>
							<td><?php echo $row["staff_role_name"] ?></td>
							<td><?php echo $row["staff_role_description"] ?></td>
							<?php
							//Create a query - Get amount of staff members of staff role
							$staff_role_id = $row['staff_role_id'];
							$query = "	SELECT COUNT(staff_id)
											FROM staff
											WHERE staff_role_id=$staff_role_id";
							//Run query
							$staff_role_amount = $dbconn->select($query);
							$staff_role_amount = $staff_role_amount->fetch_assoc();
							//print_r($staff_roles_amount);
							$staff_role_amount = $staff_role_amount["COUNT(staff_id)"];
							//print_r($staff_roles_amount);
							?>
							<td><?php echo $staff_role_amount ?></td>
							<td><a href="staff.php?role=<?php echo urlencode($row["staff_role_id"]) ?>&rolename=<?php echo urlencode($row["staff_role_name"]) ?>"><button type="button" class="btn btn-primary btn-table">list</button></a></td>
							<td>
								<form action="staff_roles.php" method="POST">
									<input type="hidden" name="updatable_staff_role_id" value="<?php echo $row["staff_role_id"] ?>">
									<input type="hidden" name="updatable_staff_role_name" value="<?php echo $row["staff_role_name"] ?>">
									<input class="btn btn-outline-secondary btn-table" type="submit" name="update" value="Edit">
								</form>
							</td>
							<td>
								<form action="staff_roles.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff role? All the mebmers previously assigned to it will be reassigned to the default role.');">
									<input type="hidden" name="deletable_staff_role_id" value="<?php echo $row["staff_role_id"] ?>">
									<input type="hidden" name="deletable_staff_role_name" value="<?php echo $row["staff_role_name"] ?>">
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

<!--Add staff form-->
<div class="container my-5">
	<div class="row">
		<div class="col-lg-5 col-md-8 box-white ">
			<h2>Add new staff role</h2>
			<form class="mb-3" method="post" action="staff_roles.php">
				<div class="mb-3">
					<label class="form-label">Staff role <span class="form-required">*</span></label>
					<input name="staff_role_name" type="text" class="form-control" placeholder="Enter new Staff role">
				</div>
				<div class="mb-3">
					<label class="form-label">Staff role description</label>
					<textarea name="staff_role_description" class="form-control" rows="3" placeholder="Describe staff role"></textarea>
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