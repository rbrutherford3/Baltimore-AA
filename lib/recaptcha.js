function mixedpostback() {
	var thisForm = document.forms[0];
	var theseElements = thisForm.elements;
	var elementValues = [];

	for (var i = 0; i < theseElements.length; i++) {
		var elementName = theseElements[i].name;
		var elementType = theseElements[i].getAttribute('type');
		var elementValue = theseElements[i].value;
		if ((elementName != 'g-recaptcha-response') && (elementName != 'token') && (elementType != 'submit')) {
			elementValues.push(encodeURIComponent(elementName) + '=' + encodeURIComponent(elementValue));
		}
	}

	thisForm.action += '?' + elementValues.join('&');
}

function saveToken(thisToken) {
	var thisForm = document.forms[0];
	var theseElements = thisForm.elements;
	var numSubmits = 0;
	for (var i = 0; i < theseElements.length; i++) {
		if (theseElements[i].getAttribute('type') == 'submit') {
			numSubmits++;
		}
	}
	if (numSubmits > 1) {
		var tokenInput = document.getElementById("token");
		tokenInput.value=thisToken;
	}
}

function onMixedSubmit(token) {
	var thisForm = document.forms[0];
	saveToken(token, thisForm);
	mixedpostback(thisForm);
	thisForm.submit();
}

function onSubmit(token) {
	var thisForm = document.forms[0];
	saveToken(token, thisForm);
	thisForm.submit();
}

