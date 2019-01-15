<?php
/* 
Primitives class collection

These objects are the bare bones elements for the 'datatypes' classes that they are extended by.
They have basic elements, like their standard formatted outputs and default values in case there are
no inputs to the constructor.

 */

include_once 'interfaces.php';

// Generic class to handle booleans, redubbed flags becuase 'bool' and 'booleans' are protected keywords
class flag implements primitive {
	protected $trueFormatted = 'Yes';
	protected $falseFormatted = 'No';
	protected $defaultValue = null;
	protected $className = 'flag';
	
	function __construct() {
		if (func_num_args() == 0) {
			$this->value = $this->defaultValue;
			$this->format();
		}
		
		else if (func_num_args() == 1) {
			$this->value = func_get_arg(0);
			$this->format();
		}
		else {
			die('Invalid number of arguments for ' . $this->className . ' class!');
		}
	}
	
	// Set to true if number greater than zero or non-empty string
	protected function format() {
		if ($this->value || !(empty($this->value))) {
			$this->value = true;
			$this->formatted = $this->trueFormatted;
		}
		else {
			$this->value = false;
			$this->formatted = $this->falseFormatted;
		}
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getFormatted() {
		return $this->formatted;
	}
}

// Generic class to handle one line text (notes, names of institutions, names with initials, etc.
class text implements primitive {
	protected $defaultValue = null;
	protected $className = 'text';

	function __construct() {
		if (func_num_args() == 0) {
			$this->value = $this->defaultValue;
			$this->format();
		}
		// Grab arg, set initial to null automatically if only one arg
		else if (func_num_args() == 1) {
			$this->value = func_get_arg(0);
			$this->format();
			
		}
		else {
			die('Invalid number of arguments for ' . $this->className . ' class!');
		}
	}
	
	// Nullify empty strings, format non-empty strings
	protected function format() {
		if (empty($this->value) || is_null($this->value)) {
			$this->value = null;
			$this->formatted = '';
		}
		else {
			$this->formatted = htmlspecialchars($this->value);
		}
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getFormatted() {
		return $this->formatted;
	}
}

// Generic class to handle numbers
class number implements primitive {
	protected $defaultValue = null;
	protected $defaultFormatted = '';
	protected $className = 'number';

	function __construct() {
		if (func_num_args() == 0) {
			$this->value = null;
			$this->format();
		}
		// Grab arg, set initial to null automatically if only one arg
		else if (func_num_args() == 1) {
			$this->value = func_get_arg(0);
			$this->format();
		}
		else {
			die('Invalid number of arguments for ' . $this->className . ' class!');
		}
	}
	
	// Nullify empty strings, format non-empty strings
	protected function format() {
		if (empty($this->value) || is_null($this->value)) {
			$this->formatted = $this->defaultFormatted;
		}
		else {
			// Format numbers for HTML output (might be unnecessary)
			$this->formatted = htmlspecialchars((string)$this->value);
		}
	}
	
	// Return formatted notes
	public function getValue() {
		return $this->value;
	}
	
	public function getFormatted() {
		return $this->formatted;
	}
}

?>