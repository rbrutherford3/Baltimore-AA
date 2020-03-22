/*
validate.js
Function library for validating form input, attempted to make as generic as possible
to allow reuse.  Thanks to HTML5's 'required' tag, I could eliminate most of these functions!
*/

// Validate a group (the only thing needing validation is the DOW!)
function validateGroup(formName, nameBase) {
	return validateDOW(formName, nameBase);
}

// Validate an instituion (only need to validate zip)!
function validateInstitution(formName, nameBase) {
	method = document.forms[formName][nameBase.concat("[method]")].value;
	if((method==1) || (method==2)) {
		return validateZip(formName, nameBase);
	}
	else {
		return true;
	}
}

// Validate an instituion (without input methods)
function validateInstitutionSimple(formName, nameBase) {
	return validateZip(formName, nameBase);

}

// Validate a zip code to make sure that it's five numbers
function validateZip(formName, nameBase) {
	zip = document.forms[formName][nameBase.concat("[zip]")];
	if (zip.disabled == false) {
		if (!isNumeric(zip.value)) {
			alert("Zip code must be all numbers");
			return false;
		}
		else if (zip.value.length != 5) {
			alert("Zip code must be 5 digits long");
			return false;
		}
		else {
			return true;
		}
	}
}

// Make sure that at least one day of the week is selected
function validateDOW(formName, nameBase) {
	var Sunday = document.forms[formName][nameBase.concat("[dow][0]")].checked;
	var Monday = document.forms[formName][nameBase.concat("[dow][1]")].checked;
	var Tuesday = document.forms[formName][nameBase.concat("[dow][2]")].checked;
	var Wednesday = document.forms[formName][nameBase.concat("[dow][3]")].checked;
	var Thursday = document.forms[formName][nameBase.concat("[dow][4]")].checked;
	var Friday = document.forms[formName][nameBase.concat("[dow][5]")].checked;
	var Saturday = document.forms[formName][nameBase.concat("[dow][6]")].checked;
	if (!(Sunday || Monday || Tuesday || Wednesday || Thursday || Friday || Saturday)) {
		alert("At least one day of the week must be selected");
		return false;
	}
	else {
		return true;
	}
}

// Validate a person's input
function validatePerson(formName, nameBase) {
	title = document.forms[formName][nameBase.concat("[title]")].value;
	method = document.forms[formName][nameBase.concat("[method]")].value;
	if((method==1) || (method==2)) {	// If adding or editing...
		// Grab phone inputs
		var phone1 = document.forms[formName][nameBase.concat("[phone][1]")].value;
		var phone2 = document.forms[formName][nameBase.concat("[phone][2]")].value;
		var phone3 = document.forms[formName][nameBase.concat("[phone][3]")].value;

		// Validate each piece of the phone number.  Note that the area code and phone
		// number cannot begin with zero or one, hence the 200 minimum
		if(!validatePhone(phone1, 200, 999, 3, title)) {
			phoneValid = false;
		}
		else if(!validatePhone(phone2, 200, 999, 3, title)) {
			phoneValid = false;
		}
		else if(!validatePhone(phone3, 0, 9999, 4, title)) {
			phoneValid = false;
		}
		else {
			phoneValid = true;
		}
		return phoneValid;
	}
	else {
		return true;
	}
}

// Version without input methods
function validatePersonSimple(formName, nameBase) {
	alert("Entered 2");
	title = document.forms[formName][nameBase.concat("[title]")].value;
	// Grab phone inputs
	var phone1 = document.forms[formName][nameBase.concat("[phone][1]")].value;
	var phone2 = document.forms[formName][nameBase.concat("[phone][2]")].value;
	var phone3 = document.forms[formName][nameBase.concat("[phone][3]")].value;

	// Validate each piece of the phone number.  Note that the area code and phone
	// number cannot begin with zero or one, hence the 200 minimum
	if(!validatePhone(phone1, 200, 999, 3, title)) {
		phoneValid = false;
	}
	else if(!validatePhone(phone2, 200, 999, 3, title)) {
		phoneValid = false;
	}
	else if(!validatePhone(phone3, 0, 9999, 4, title)) {
		phoneValid = false;
	}
	else {
		phoneValid = true;
	}
	return phoneValid;
}


// Validate phone number portion (n=number, d=number of digits)
function validatePhone(n, minN, maxN, d, title) {

		// Check if length is appropriate
		if (n.length != d) {
			alert(title.concat(" phone number not filled completely"));
			return false;
		}

		// Check if string is a number
		if (!isNumeric(n)) {
			alert(title.concat(" phone number must contain all numbers"));
			return false;
		}

		// Check acceptable range (i.e.: area code can't lead with 1, nor can first number)
		if ((parseInt(n) < minN) || (parseInt(n) > maxN)) {
			alert(title.concat(" phone number invalid"));
			return false;
		}

		return true;
}

// Tests to see whether the text is a number
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
