/*
Function to make tabs unnecessary between different form fields.  Not used
*/

window.addEventListener('load', function() {
	// Go to phone2 field if just entered last key in phone1 field
	document.getElementById("RepPhone1").addEventListener("keyup", function(){ 
		if (document.getElementById("RepPhone1").value.length == 3) {
			document.getElementById("RepPhone2").focus();
		}
	});
	// Go to phone3 field if just entered last key in phone2 field
	document.getElementById("RepPhone2").addEventListener("keyup", function(){ 
		if (document.getElementById("RepPhone2").value.length == 3) {
			document.getElementById("RepPhone3").focus();
		}
	});
});