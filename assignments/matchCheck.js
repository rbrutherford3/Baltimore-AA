/* matchCheck.js
This js function is called whenever there is a change in an assignment and checks each assignment
to see whether there is a conflict.  If there is a conflict, then it resets the assignment and jumps
to the conflicting assignment.  The only exception is sponsor's night. */
function matchCheck(count, countMax) {
	match = false;
	var pulldown = document.forms["form"]["group[" + count + "]"];
	if (pulldown.value != 0) { // if not sponsor's night...
		for (i = 0; i < countMax; i++) { 
			if (i != count) {
				if (pulldown.value == document.forms["form"]["group[" + i + "]"].value) {
					match = true;
					conflict = i;
				}
			}
		}
	}
	if (match) {
		alert("WARNING: The group you just selected is assigned elsewhere!  Resetting this assignment and taking you to the conflicting entry.");
		for (i=0; i < pulldown.length; i++) {
			pulldown[i].selected = pulldown[i].defaultSelected;
		}
		document.forms["form"]["group[" + conflict + "]"].focus();
	}
}
