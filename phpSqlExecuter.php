<?php
$conn = new mysqli('localhost', 'root', '' , 'sql_auto_test_table');

$query = ''; //Set an empty query variable to hold the query
$sqlScript = file('db.sql'); //Set the file location

//Read each line of the file
foreach ($sqlScript as $line)	{

	//Get the starting character and the ending character of each line
	$startWith = substr(trim($line), 0 ,2);
	$endWith = substr(trim($line), -1 ,1);
	
	//Check for empty or comment lines. (If the line start with --,/*,// or the line is empty, skip to next line)
	if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
		continue;
	}
	
	//Add the line to the query. (Additional optional commented out <br> tag added to query for easy error identification)
	$query = $query . $line . "/*<br>*/"; 
	//If the line end with a ";" assume the last query has ended in this line
	if ($endWith == ';') {
		//Therefore, try to execute the query. Upon failure, display the last formed query with the SQL error message
		mysqli_query($conn,$query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>,<br><br>' . $query. '</b><br><br>'.$conn->error.'</div>');
		//Reset the query variable and continue to loop next lines
		$query= '';		
	}
}
//If nothing went wrog, display success message after executing all the lines in file
echo '<div class="success-response sql-import-response">SQL file imported successfully</div>';

/*
If failed with a invalid DEAFULT value for a DATE column, try adding the following line to top of your sql file
SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
*/
?>