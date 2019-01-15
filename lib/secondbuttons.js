/*
secondbuttons
Simple function to display/hide the div element with the given root ID string (idBase).
Used to display/hide the second group rep or the second co-sponsor
*/

function secondbuttons(idBase) {
	
	// Grab the HTML elements, store them to shorten code
	var exists = document.getElementById(idBase.concat("[exists]"));
	var addButton = document.getElementById(idBase.concat("[addSecond]"));
	var deleteButton = document.getElementById(idBase.concat("[deleteSecond]"));
	var div = document.getElementById(idBase);
	
	// When clicking the add button...
	addButton.addEventListener("click", function() {
		exists.value = "1";		// Change form element that represents the existence of a second person
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
	});
	
	// When clicking the delete button...
	deleteButton.addEventListener("click", function() {
		exists.value = "0";	// Change the form element that represents the existence of a second person
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
	});
}