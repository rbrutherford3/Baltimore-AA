/*
validate.js (for group/form.php)
Calls validate.js in lib
*/

function validateForm(formName, groupBase, rep1Base, rep2Base) {
	// Validate group (just validate day of week)
	groupValid = validateGroup(formName, groupBase);

	// Valid rep1
	rep1Valid = validatePerson(formName, rep1Base);

	// If second rep, validate that
	if (document.forms[formName][rep2Base.concat("[exists]")].value == 1) {
		rep2Valid = validatePerson(formName, rep2Base);
	}
	else {
		rep2Valid = true;
	}

	// Return combined validations
	return (groupValid && rep1Valid && rep2Valid);
}

