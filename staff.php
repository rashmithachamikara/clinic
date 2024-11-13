<?php
include("includes/header.php");

//DB Connect
$dbconn = new Database;

//If any special parameter is set
if (isset($_GET["role"])) {
	//Select staff members of a certain role
	$staff_role = $_GET["role"];
	$staff_role_name = $_GET["rolename"];
	//Create a query
	$query = "	SELECT staff_id, staff_name, staff_role_name
				FROM `staff` 
				LEFT JOIN staff_role ON staff.staff_role_id=staff_role.staff_role_id
				WHERE staff.staff_role_id=$staff_role
				
				ORDER BY staff_id ASC";
	//Run query
	$staff_members = $dbconn->select($query);
	//Set title value
	$title_name = "Staff members of " . $staff_role_name . " role";
} else {
	//Select all staff members since no parameters are set
	//Create a query
	$query = "	SELECT staff_id, staff_name, staff_role_name
				FROM `staff` 
				LEFT JOIN staff_role ON staff.staff_role_id=staff_role.staff_role_id
				ORDER BY staff_id ASC";
	//Run query
	$staff_members = $dbconn->select($query);
	//Set title value
	$title_name = "All staff members";
}
?>

<!--Head section-->
<div class="container my-5 box-white">
	<h1>Staff management</h1>
	<p>List and manage staff members.</p>
	<a href="add_staff.php"><button type="button" class="btn btn-success">Add new staff member</button></a>
	<a href="staff_roles.php"><button type="button" class="btn btn-secondary">Staff roles</button></a>
	<p class="py-3"><?php print_url_message() ?></p>
</div>

<!--Staff member list section-->
<div id="staff-member-list" class="container my-5 ">
	<div class="row">
		<div class="col-lg-8 box-white">
			<!--Staff member Search tool-->
			<div class="">
				<form class="">
					<div class="col-md-8">
						<label class="me-3" for="inlineFormInputGroupUsername">Search</label>
						<div class="input-group align-bottom">
							<input type="text" class="form-control" id="searchInput" onkeyup="searchInTable()" placeholder="Search for name, role, etc...">
						</div>
					</div>
				</form>
			</div>
			<!--Staff member List table-->
			<h2 class="my-4	"><?php echo $title_name ?></h2>
			<div class="table-holder">
				<table id="searchTable" class="table table-bordered">
					<thead>
						<tr>
							<th scope="col">Name</th>
							<th scope="col">Role</th>
							<th scope="col">Profile</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!is_int($staff_members)) : ?>
							<?php while ($row = $staff_members->fetch_assoc()) : ?>
								<tr>
									<td><?php echo $row["staff_name"] ?></td>
									<td><?php echo $row["staff_role_name"] ?></td>
									<td><a href="staff_profile.php?staff_id=<?php echo $row["staff_id"] ?>"><button type="button" class="btn btn-primary btn-table">view</button></a></td>
								</tr>
							<?php endwhile; ?>
						<?php else : ?>
							<td class="url-message-info" colspan="100%">No Staff members in this context.</td>
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