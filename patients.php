<?php
include("includes/header.php");

//DB Connect
$dbconn = new Database;

//If any special parameter is set
if (isset($_GET["type"])) {
	//Select patients of a certain patient type
	$patient_type = $_GET["type"];
	$patient_type_name = $_GET["typename"];
	//Create a query
	$query = "	SELECT id,patient_id, patient_name, patient_registered_date, patient_type_name
				FROM `patients`,`patient_types` 
				WHERE  patients.patient_type=patient_types.patient_type_id AND patients.patient_type=$patient_type
				ORDER BY id ASC";
	//Run query
	$patients = $dbconn->select($query);
	//Set title value
	$title_name = "Patients of " . $patient_type_name . " type";
} else {
	//Select all patients since no parameters are set
	//Create a query
	$query = "	SELECT id,patient_id, patient_name, patient_registered_date, patient_type_name
				FROM `patients`,`patient_types` 
				WHERE  patients.patient_type=patient_types.patient_type_id
				ORDER BY id ASC";
	//Run query
	$patients = $dbconn->select($query);
	//Set title value
	$title_name = "All patients";
}
?>

<!--Head section-->
<div class="container my-5 box-white">
	<h1>Patients</h1>
	<p>See the list of registered patients, add new patients and manage patients.</p>
	<a href="add_patient.php"><button type="button" class="btn btn-success">Add new patient</button></a>
	<a href="patient_profile.php"><button type="button" class="btn btn-secondary">Patient profiles</button></a>
	<a href="patient_types.php"><button type="button" class="btn btn-secondary">Patient types</button></a>
	<p class="py-3"><?php print_url_message() ?></p>
</div>

<!--Patient list section-->
<div id="patient-list" class="container my-5 ">
	<div class="row">
		<div class="col-lg-8 box-white">
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
			<!--Patient List table-->
			<h2 class="my-4	"><?php echo $title_name ?></h2>
			<div class="table-holder">
				<table id="searchTable" class="table table-bordered">
					<thead>
						<tr>
							<th scope="col">ID</th>
							<th scope="col">Name</th>
							<th scope="col">Registered date</th>
							<th scope="col">Dates attended</th>
							<th scope="col">Type</th>
							<th scope="col">Profile</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!is_int($patients)) : ?>
							<?php while ($row = $patients->fetch_assoc()) : ?>
								<tr>
									<th scope="row"><?php echo $row["patient_id"] ?></th>
									<td><?php echo $row["patient_name"] ?></td>
									<td><?php echo formatDate($row["patient_registered_date"]) ?></td>
									<td>x</td>
									<td><?php echo $row["patient_type_name"] ?></td>
									<td><a href="patient_profile.php?patient_id=<?php echo $row["patient_id"] ?>"><button type="button" class="btn btn-primary btn-table">view</button></a></td>
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


<?php
include("includes/footer.php");
?>