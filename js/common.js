function searchInTable(searchInput="searchInput", searchTable="searchTable") {
	// Declare variables
	var input, filter, table, trList, td, txtValue;
	input = document.getElementById(searchInput);
	filter = input.value.toUpperCase();
	table = document.getElementById(searchTable);
	trList = table.getElementsByTagName("tr"); //A list of rows
	
	// Loop through all table rows and scan columns, and hide those who don't match the search query
	//Row start by 1 to skip the header
	for (row = 1; row < trList.length; row++) {
		tdList = trList[row].querySelectorAll("th, td");
		for (col = 0; col < tdList.length; col++) {
			td = tdList[col];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					trList[row].style.display = "";
					break
				} else {
					trList[row].style.display = "none";				
				}
			}
		}
	}
}