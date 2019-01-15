/* 
personbuttons.js is a file that holds one function to handle the input method buttons
for the input HTML for a person (add/edit/select).  Clicking these buttons will enable
and disable certain inputs, which is what this function accomplishes.  It is separate
so it can be used in both the groups form and the institutions form
*/

function personbuttons(idBase) {

	// Grab inputs
	var addButton = document.getElementById(idBase.concat("[add]"));
	var editButton = document.getElementById(idBase.concat("[edit]"));
	var selectButton = document.getElementById(idBase.concat("[select]"));
	
	//var id = document.getElementById(idBase.concat("[id]"));
	var name = document.getElementById(idBase.concat("[name]"));
	var initial = document.getElementById(idBase.concat("[initial]"));
	var phone1 = document.getElementById(idBase.concat("[phone][1]"));
	var phone2 = document.getElementById(idBase.concat("[phone][2]"));
	var phone3 = document.getElementById(idBase.concat("[phone][3]"));
	var active = document.getElementById(idBase.concat("[active]"));
	var notes = document.getElementById(idBase.concat("[notes]"));
	var pulldown = document.getElementById(idBase.concat("[pulldown]"));

	// Store initial values of fields
	var nameI = name.value;
	var initialI = initial.value;
	var phone1I = phone1.value;
	var phone2I = phone2.value;
	var phone3I = phone3.value;
	var activeI = active.value;
	var notesI = notes.value;

	
	// When clicking the "Add new rep button...
	addButton.addEventListener("click", function(){

		// Re-enable rep input
		name.disabled = false;		
		initial.disabled = false;
		phone1.disabled = false;
		phone2.disabled = false;
		phone3.disabled = false;
		active.disabled = false;
		notes.disabled = false;
		
		// Clear input values
		name.value = "";
		initial.value = "";
		phone1.value = "";
		phone2.value = "";
		phone3.value = "";
		active.checked = true;		// Default is checked
		notes.value = "";
		
		// disable the pulldown menu
		pulldown.disabled = true;
	});
	
	// When clicking the "Edit existing rep" button (if it exists)...
	if (editButton !== null) {
		editButton.addEventListener("click", function(){ 
			
			// Re-enable rep input fields
			name.disabled = false;		
			initial.disabled = false;
			phone1.disabled = false;
			phone2.disabled = false;
			phone3.disabled = false;
			active.disabled = false;
			notes.disabled = false;
			
			// Re-load the existing rep values into the rep input fields
			name.value = nameI;
			initial.value = initialI;
			phone1.value = phone1I;
			phone2.value = phone2I;
			phone3.value = phone3I;
			active.value = activeI;
			notes.value = notesI;
				
			// Disable the rep select pulldown menu if it exists
			pulldown.disabled = true;
		});
	}
	selectButton.addEventListener("click", function(){

		// Disable rep input
		name.disabled = true;		
		initial.disabled = true;
		phone1.disabled = true;
		phone2.disabled = true;
		phone3.disabled = true;
		active.disabled = true;
		notes.disabled = true;
		
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
