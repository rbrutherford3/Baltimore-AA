window.addEventListener('load', function() {
	
	// Store initial values of fields
	var InstitutionID = document.getElementById("InstitutionID").value;
	var InstitutionName = document.getElementById("InstitutionName").value;
	var InstitutionAddress = document.getElementById("InstitutionAddress").value;
	var InstitutionCity = document.getElementById("InstitutionCity").value;
	var InstitutionZip = document.getElementById("InstitutionZip").value;
	var InstitutionBG = document.getElementById("InstitutionBG").value;
	var InstitutionNotes = document.getElementById("InstitutionNotes").value;
	
	// When clicking the "Add new Institution button...
	document.getElementById("addNewInstitution").addEventListener("click", function(){
		
		// Re-enable Institution input
		document.getElementById("InstitutionID").disabled = false;
		document.getElementById("InstitutionName").disabled = false;
		document.getElementById("InstitutionAddress").disabled = false;
		document.getElementById("InstitutionCity").disabled = false;
		document.getElementById("InstitutionZip").disabled = false;
		document.getElementById("InstitutionBG").disabled = false;
		document.getElementById("InstitutionNotes").disabled = false;
		
		// Clear input values
		document.getElementById("InstitutionID").value = "";
		document.getElementById("InstitutionName").value = "";
		document.getElementById("InstitutionAddress").value = "";
		document.getElementById("InstitutionCity").value = "";
		document.getElementById("InstitutionZip").value = "";
		document.getElementById("InstitutionBG").value = "";
		document.getElementById("InstitutionNotes").value = "";
		
		// Disable the institution select pulldown menu if it exists
		if (document.getElementById("InstitutionSelect") !== null) {
			document.getElementById("InstitutionSelect").disabled = true;
		}
	});
	
	// When clicking the "Edit existing institution" button (if it exists)...
	if (document.getElementById("EditExistingInstitution") !== null) {
		document.getElementById("EditExistingInstitution").addEventListener("click", function(){ 
			
			// Re-enable institution input fields
			document.getElementById("InstitutionID").disabled = false;
			document.getElementById("InstitutionName").disabled = false;
			document.getElementById("InstitutionAddress").disabled = false;
			document.getElementById("InstitutionCity").disabled = false;
			document.getElementById("InstitutionZip").disabled = false;
			document.getElementById("InstitutionBG").disabled = false;
			document.getElementById("InstitutionNotes").disabled = false;
			
			// Re-load the existing institution values into the institution input fields
			document.getElementById("InstitutionID").value = InstitutionID;
			document.getElementById("InstitutionName").value = InstitutionName;
			document.getElementById("InstitutionAddress").value = InstitutionAddress;
			document.getElementById("InstitutionCity").value = InstitutionCity;
			document.getElementById("InstitutionZip").value = InstitutionZip;
			document.getElementById("InstitutionBG").value = InstitutionBG;
			document.getElementById("InstitutionNotes").value = InstitutionNotes;
			
			// Disable the institution select pulldown menu if it exists
			if (document.getElementById("InstitutionSelect") !== null) {
				document.getElementById("InstitutionSelect").disabled = true;
			}
		});
	}
	
	// When clicking the "Select institution" button (if it exists)...
	if (document.getElementById("SelectExistingInstitution") !== null) {
		document.getElementById("SelectExistingInstitution").addEventListener("click", function(){ 
			
			// Disable the institution input fields
			document.getElementById("InstitutionID").disabled = true;
			document.getElementById("InstitutionName").disabled = true;
			document.getElementById("InstitutionAddress").disabled = true;
			document.getElementById("InstitutionCity").disabled = true;
			document.getElementById("InstitutionZip").disabled = true;
			document.getElementById("InstitutionBG").disabled = true;
			document.getElementById("InstitutionNotes").disabled = true;
			
			// Enable the institution select button
			document.getElementById("InstitutionSelect").disabled = false;
		});
	}

});