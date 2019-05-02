/*
validate.js (for group/form.php)
Calls validate.js in lib
*/

function validateForm(formName, institutionBase, sponsorBase, cosponsorBase, cosponsor2Base) {
	// Validate institution
	institutionValid = validateInstitution(formName, institutionBase);
	
	// Validate sponsor and cosponsor
	sponsorValid = validatePerson(formName, sponsorBase);
	
	// If cosponsor exists, validate that
	if (document.forms[formName][cosponsorBase.concat("[exists]")].value == 1) {
		cosponsorValid = validatePerson(formName, cosponsorBase);
	}
	else {
		cosponsorValid = true;
	}
	
	// If second cosponsor, validate that
	if (document.forms[formName][cosponsor2Base.concat("[exists]")].value == 1) {
		cosponsor2Valid = validatePerson(formName, cosponsor2Base);
	}
	else {
		cosponsor2Valid = true;
	}
	
	// Return combined validations
	return institutionValid && sponsorValid && cosponsorValid && cosponsor2Valid;
}