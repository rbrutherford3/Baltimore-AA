/* 
institutionbuttons.js is a file that holds one function to handle the input method buttons
for the input HTML for an institution (add/edit/select).  Clicking these buttons will enable
and disable certain inputs, which is what this function accomplishes.
*/

function institutionbuttons(idBase) {

	// Grab inputs
	var addButton = document.getElementById(idBase.concat("[add]"));
	var editButton = document.getElementById(idBase.concat("[edit]"));
	var selectButton = document.getElementById(idBase.concat("[select]"));

	//var id = document.getElementById(idBase.concat("[id]"));
	var name = document.getElementById(idBase.concat("[name]"));
	var address = document.getElementById(idBase.concat("[address]"));
	var city = document.getElementById(idBase.concat("[city]"));
	var zip = document.getElementById(idBase.concat("[zip]"));
	var bg = document.getElementById(idBase.concat("[bg]"));
	var active = document.getElementById(idBase.concat("[active]"));
	var notesPublic = document.getElementById(idBase.concat("[notesPublic]"));
	var notesPrivate = document.getElementById(idBase.concat("[notes]"));
	var pulldown = document.getElementById(idBase.concat("[pulldown]"));

	// Store initial values of fields
	var nameI = name.value;
	var addressI = address.value;
	var cityI = city.value;
	var zipI = zip.value;
	var bgI = bg.value;
	var activeI = active.value;
	var notesPublicI = notesPublic.value;
	var notesPrivateI = notesPrivate.value;


	// When clicking the "Add new institution button...
	addButton.addEventListener("click", function(){

		// Re-enable institution input
		name.disabled = false;
		address.disabled = false;
		city.disabled = false;
		zip.disabled = false;
		bg.disabled = false;
		active.disabled = false;
		notesPublic.disabled = false;
		notesPrivate.disabled = false;

		// Clear input values
		name.value = "";
		address.value = "";
		city.value = "";
		zip.value = "";
		bg.checked = true;		//	bg default is UNFORTUNATELY true right now
		active.checked = true;		// Default is checked
		notesPublic.value = "";
		notesPrivate.value = "";

		// disable the pulldown menu
		pulldown.disabled = true;
	});

	// When clicking the "Edit existing institution" button (if it exists)...
	if (editButton !== null) {
		editButton.addEventListener("click", function(){ 

			// Re-enable rep input fields
			name.disabled = false;
			address.disabled = false;
			city.disabled = false;
			zip.disabled = false;
			bg.disabled = false;
			active.disabled = false;
			notesPublic.disabled = false;
			notesPrivate.disabled = false;

			// Re-load the existing rep values into the rep input fields
			name.value = nameI;
			address.value = addressI;
			city.value = cityI;
			zip.value = zipI;
			bg.value = bgI;
			active.value = activeI;
			notesPublic.value = notesPublicI;
			notesPrivate.value = notesPrivateI;

			// Disable the rep select pulldown menu if it exists
			pulldown.disabled = true;
		});
	}
	selectButton.addEventListener("click", function(){

		// Re-enable rep input
		name.disabled = true;
		address.disabled = true;
		city.disabled = true;
		zip.disabled = true;
		bg.disabled = true;
		active.disabled = true;
		notesPublic.disabled = true;
		notesPrivate.disabled = true;

		// Clear input values
/* 		name.value = "";
		initial.value = "";
		phone1.value = "";
		phone2.value = "";
		phone3.value = "";
		active.value = "1";		// Default is checked
		notes.value = ""; */

		// enable the pulldown menu
		pulldown.disabled = false;
	});
}
