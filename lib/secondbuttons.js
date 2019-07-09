/*
secondbuttons
Function to display/hide the div element with the given root ID string (idBase).
Used to display/hide the second group rep or a co-sponsor or a second co-sponsor
*/

function secondbuttons(idBase, idBase2) {
	
	// Grab the HTML elements for the first element, store them to shorten code
	var exists = document.getElementById(idBase.concat("[exists]"));
	var addButton = document.getElementById(idBase.concat("[show]"));
	var deleteButton = document.getElementById(idBase.concat("[hide]"));
	var div = document.getElementById(idBase);
	
	// Check to see if there is a second element (i.e.: second co-sponsor)
	if (idBase2 == undefined) {
		var secondBase = false;
	}
	else {
		var secondBase = true;
	}
	
	// Grab the HTML elements of the second element
	if (secondBase) {
		var exists2 = document.getElementById(idBase2.concat("[exists]"));
		var addButton2 = document.getElementById(idBase2.concat("[show]"));
		var deleteButton2 = document.getElementById(idBase2.concat("[hide]"));
		var div2 = document.getElementById(idBase2);
	}
	
	// When clicking the add button for the first element...
	addButton.addEventListener("click", function() {
		exists.value = "1";		// Change form element that represents the existence of the first element
		deleteButton.style.display = "inline-block";	// Show the delete button
		addButton.style.display = "none";	// Hide the add button
		div.style.display = "inline-block";	// Finally, show the div element
		// Had to add the below code because the inputs need to be disabled if hidden as 
		// not to cause an error on submitting form (some fields are marked as required)
		document.getElementById(idBase.concat("[name]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[initial]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[phone][1]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[phone][2]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[phone][3]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[active]")).removeAttribute("disabled");
		document.getElementById(idBase.concat("[notes]")).removeAttribute("disabled");
		
		// If there is  a second element, show the 'add' button for it
		if (secondBase) {
			addButton2.style.display = "inline-block";
		}
	});
	
	// When clicking the delete button for the first element...
	deleteButton.addEventListener("click", function() {
		exists.value = "0";	// Change form element that represents the existence of the first element
		deleteButton.style.display = "none"; // Hide the delete button
		addButton.style.display = "inline-block";	// Show the add button
		div.style.display = "none";	// Finally, hide the div element
	
		// Had to add the below code because the inputs need to be disabled if hidden as 
		// not to cause an error on submitting form (some fields are marked as required)	
		document.getElementById(idBase.concat("[name]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[initial]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[phone][1]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[phone][2]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[phone][3]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[active]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[notes]")).setAttribute("disabled","disabled");
		document.getElementById(idBase.concat("[pulldown]")).setAttribute("disabled","disabled");
		
		// Reset the values to their defaults on reappearance (a personal choice of mine)
/* 		document.getElementById(idBase.concat("[add]")).checked = true;
		document.getElementById(idBase.concat("[name]")).value = "";
		document.getElementById(idBase.concat("[initial]")).value = "";
		document.getElementById(idBase.concat("[phone][1]")).value = "";
		document.getElementById(idBase.concat("[phone][2]")).value = "";
		document.getElementById(idBase.concat("[phone][3]")).value = "";
		document.getElementById(idBase.concat("[active]")).checked = true;
		document.getElementById(idBase.concat("[notes]")).value = "";	 */
		
		// If there is a second element, hide the 'add' button for it
		if (secondBase) {
			addButton2.style.display = "none";
		}
	});
	
	// Add listeners for the buttons of the second element
	if (secondBase) {
		// When clicking the add button for the second element...
		addButton2.addEventListener("click", function() {
			exists2.value = "1";		// Change form element that represents the existence of a second element
			deleteButton2.style.display = "inline-block";	// Show the delete button
			addButton2.style.display = "none";	// Hide the add button
			div2.style.display = "inline-block";	// Finally, show the div element
			// Had to add the below code because the inputs need to be disabled if hidden as 
			// not to cause an error on submitting form (some fields are marked as required)
			document.getElementById(idBase2.concat("[name]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[initial]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[phone][1]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[phone][2]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[phone][3]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[active]")).removeAttribute("disabled");
			document.getElementById(idBase2.concat("[notes]")).removeAttribute("disabled");
			
			// Hide the delete button for the first element
			deleteButton.style.display = "none";
		});
		
		// When clicking the delete button for the second person...
		deleteButton2.addEventListener("click", function() {
			exists2.value = "0";	// Change the form element that represents the existence of a second element
			deleteButton2.style.display = "none"; // Hide the delete button
			addButton2.style.display = "inline-block";	// Show the add button
			div2.style.display = "none";	// Finally, hide the div element
		
			// Had to add the below code because the inputs need to be disabled if hidden as 
			// not to cause an error on submitting form (some fields are marked as required)	
			document.getElementById(idBase2.concat("[name]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[initial]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[phone][1]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[phone][2]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[phone][3]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[active]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[notes]")).setAttribute("disabled","disabled");
			document.getElementById(idBase2.concat("[pulldown]")).setAttribute("disabled","disabled");
			
			// Show the delete button for the first element
			deleteButton.style.display = "inline-block";
		});
	}
}