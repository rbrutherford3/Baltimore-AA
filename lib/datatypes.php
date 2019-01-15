<?php
/* 
Datatypes class collection

These objects are the backbone of the classes that extend the data class.  They comprise
it's members and contain their output HTML and processing, most notably the day of the week
class.  They hold all the information.  Note that HTML inputs defer to CSS classes for formatting.
 */

include_once 'primitives.php';
include_once 'interfaces.php';
date_default_timezone_set('America/New_York');

/* active is a boolean that states whether a person, meeting, group, etc. is active (taking
assignments, chairing meetings, etc.).  The idea behind this is that we can hold records
in the database without showing them if they are obsolete, so data doesn't ever get destroyed	 */
class active extends flag implements HTML {
	protected $defaultValue = true;
	protected $trueFormatted = 'Active';
	protected $falseFormatted = 'Inactive';
	protected $className = 'active';
	
	// Basic label
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[active]">' . $label . '</label>';

	}
	
	// Checkbox HTML with default checked or unchecked
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		if ($this->getValue()) {
			$checked = ' checked';
		}
		else {
			$checked = '';
		}
		echo '
					<input type="checkbox" id="' . $idBase . '[active]" name="' . $nameBase . '[active]"' . $enabledTag . $checked . ' />';
	}
}

/* Probation is a boolean for groups that simply states whether the group is on probation for missing their meeting assignments */
class probation extends flag implements HTML {
	protected $defaultValue = false;
	protected $trueFormatted = 'On Probation';
	protected $falseFormatted = 'Not On Probation';
	protected $className = 'probation';
	
	// Basic label
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[probation]">' . $label . '</label>';

	}
	
	// Checkbox HTML with default checked or unchecked
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		if ($this->getValue()) {
			$checked = ' checked';
		}
		else {
			$checked = '';
		}
		echo '
					<input type="checkbox" id="' . $idBase . '[probation]" name="' . $nameBase . '[probation]"' . $enabledTag . $checked . ' />';
	}
}

/* Background check boolean.  In group class, true means that they allow themselves to be subjected background checks.
In institution class, true means they require background checks. */
class bg extends flag implements HTML {
	protected $defaultValue = true;
	protected $className = 'bg';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[bg]">' . $label . '</label>';

	}

	// Checkbox HTML with default checked or unchecked
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		if ($this->getValue()) {
			$checkedTag = ' checked';
		}
		else {
			$checkedTag = '';
		}
		echo '
					<input type="checkbox" id="' . $idBase . '[bg]" name="' . $nameBase . '[bg]"' . $enabledTag . $checkedTag . ' />';
	}	
}

/* dow class is a complex class.  This class stores the days of the week for both groups and 
institutions.  In an effort not to have several fields in the database for each day of the week, 
I decided to go with bitmasking (https://en.wikipedia.org/wiki/Bitwise_operation) to store the 
days of the week in 7 bits.  The 0th bit is sunday, the 1st is Monday, etc., up to the 6th bit (Saturday).  
This proved to be very successful, as the operations are very simple. */
class dow extends number implements dowHTML {
	
	private $dows = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

	function __construct() {
		
		// No days of the week if nothing passed
		if (func_num_args() == 0) {
			$this->value = 0;
			$this->decode();
			$this->format();
		}
		
		// If passing in a coded number...
		else if (func_num_args() == 1) {
			$this->value = func_get_arg(0);
			$this->decode();
			$this->format();
		}
		
		// If passing in uncoded true/falses
		else if (func_num_args() == 7) {
			$this->dow = array(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), 
				func_get_arg(4), func_get_arg(5), func_get_arg(6));
			$this->encode();
			$this->format();
		}
		else {
			die('Invalid number of arguments for dow class!');
		}
	}

	// Raise each day of the week to it's respective power of 2 and sum the total
	// (this allows having just one field that can represent one day or multiple days of the week
	private function encode() {
		$this->value = 0;
		for ($i=0; $i<7; $i++) {
			if($this->dow[$i]) {
				$this->value = $this->value + $this->getNumber($i);
			}
		}
	}
	
	// Reverse the encoding process through bitmasking
	// Example: 01000100 (saturday and tuesday) & 01000000 (saturday) = 01000000 (true, saturday is present)
	// Example #2: 00100001 (friday and sunday) $ 01000000 (saturday) = 00000000 (false, saturday is NOT present)
	private function decode() {
		$this->dow = array();
		for ($i=0; $i<7; $i++) {
			if ($this->getBoolean($i)) {
				$this->dow[$i] = true;
			}
			else {
				$this->dow[$i] = false;
			}
		}
	}
	
	// Yields formatted string (i.e.: 'Monday, Tuesday' or 'Saturday')
	protected function format() {
		// Format days of week
		if ($this->value==0) {
			$this->formatted = 'No days!';
		}
		// 00111110
		else if ($this->value==62) {
			$this->formatted = 'Weekdays';
		}
		// 01111111
		else if ($this->value==127) {
			$this->formatted = 'Everyday';
		}
		else {
			// Display commas between days of week only
			$isFirst=true;
			for($i=0; $i<7; $i++) {
				if($this->dow[$i]) {
					if($isFirst) {
						$this->formatted = $this->dows[$i];
					}
					else {
						$this->formatted = $this->formatted . ', ' . $this->dows[$i];
					}
					$isFirst=false;
				}
			}
		}
	}
	
	// Assumes 0-6 for days of week, returns string
	public function getString($i) {
		return $this->dows[$i];
	}
	
	// Assumes 0-6 for days of week, returns binary equivelant
	public function getNumber($i) {
		return pow(2, $i);
	}
	
	// Get a boolean value for a particular day of the week
	public function getBoolean($i) {
		return pow(2, $i) & $this->value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	// Returns formatted string from format() function	
	public function getFormatted() {
		return $this->formatted;
	}
	
	public function labelHTMLpulldown($idBase, $label) {
		echo '
					<label for="' . $idBase . '[dow]">' . $label . '</label>';

	}
	
	// Pulldown menu HTML
	public function inputHTMLpulldown($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabled = '';
		}
		else {
			$enabled = ' disabled';
		}
		echo '
					<select id="' . $idBase . '[dow]" name="' . $nameBase . '[dow]"' . $enabled . ' required>';
		if ($this->getValue() == 0) {	// Show "select day of week option" if there are none selected
			echo '
						<option value="" selected>***Select a day of the week***</option>';
		}
		for($i=0; $i<7; $i++) {
			if($this->getValue() == $this->getNumber($i)) {
				$selected = ' selected';
			}
			else {
				$selected = '';
			}
			echo '
						<option value="' . $this->getNumber($i) . '" ' . $selected . '>' . 
							$this->getString($i) . '</option>';
		}
		echo '
					</select>';
	}
	
	// Checkbox HTML (outputs seven check boxes for each day of the week)
	public function inputHTMLcheckbox($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		for($i=0; $i<7; $i++) {
			if ($this->getBoolean($i)) { // If that day of the week is flagged, then check the box
				$checkedTag = ' checked';
			}
			else {
				$checkedTag = '';
			}	//    Double array for names and lables (nameBase[dow][0] for Sunday, etc.)
			echo '
					<input type="checkbox" id="' . $idBase . '[dow][' . $i . ']" name="' . $nameBase . '[dow][' . $i . ']"' . $enabledTag . $checkedTag . '>' . 
					'<label for="' . $idBase . '[dow][' . $i . ']">' . $this->getString($i) . '</label><br>';
		}
	}
}

/* The gender class is a simple integer class.  0=all genders, 1=men only, 2=female only.  There's
not much more to say about it */
class gender extends number implements HTML {
	protected $defaultValue = 0;
	protected $className = 'gender';
	
	protected function format() {
		$this-> formatted = $this->getString($this->value);
	}
	
	// Format on constructor
	public static function getString($i) {
		switch($i) {
		case 0:
			return 'All genders';
			break;
		case 1:
			return 'Men';
			break;
		case 2:
			return 'Women';
			break;
		default:
			die('Invalid arguments for gender class!');
			break;
		}
	}
	
	// Label HTML
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[gender]">' . $label . '</label>';
	}

	// Use a pulldown menu to display each gender choice
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		echo '
					<select id="' . $idBase . '[gender]" name="' . $nameBase . '[gender]"' . $enabledTag . '>';			
		for ($i=0; $i<3; $i++) {
			if ($i == $this->getValue()) {
				$selectedTag = ' selected';
			}
			else {
				$selectedTag = '';
			}
			echo '
						<option value="' . $i . '"' . $selectedTag . '>' . $this->getString($i) . '</option>';
		}
		echo '
					</select>';
	}

}

// Initial functions just like name, except the input has a max length of 1
class initial extends text implements HTML {
	protected $className = 'initial';
	
	protected function format() {
		if (empty($this->value) || is_null($this->value)) {	// nullify if empty string, keep formatted null, too (modify to empty string!?)
			$this->value = null;
			$this->formatted = null;
		}
		else {
			$this->formatted = ' ' . htmlspecialchars($this->value) . '.';
		}
	}
	
	// Labl HTML
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[initial]">' . $label . '</label>';
	}
	
	// Input HTML with a maxlength of 1
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}	
		echo '
					<input class="initial" type="text" id="' . $idBase . '[initial]" name="' . $nameBase . 
						'[initial]" value="' . $this->getValue() . '"' . $enabledTag . ' maxlength="1" />';
	}
}

// Name class is a simple string input class.  The only thing beyond the text class is the HTML functions
class name extends text implements HTML {
	protected $className = 'name';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[name]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="name" type="text" id="' . $idBase . '[name]" name="' . $nameBase . 
						'[name]" value="' . $this->getValue() . '"' . $enabledTag . ' maxlength="128" required/>';
	}
}

// Notes is also simple.  Max length is 255 because I chose TinyText (which has a max length of 255 characters)
class notes extends text implements HTML {
	protected $className = 'notes';

	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[notes]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<textarea class="notes" id="' . $idBase . '[notes]" name="' . $nameBase . '[notes]"' . 
						' maxlength="255" rows="4"' . $enabledTag . '>' . $this->getValue() . '</textarea>';
	}	
}

// Notes public is to differentiate it from private notes for the meeting in the HTML form.  I wish I could have used the same
// notes class, but I would have to add an argument to inputHTML and I don't wish to do that.
class notesPublic extends text implements HTML {
	protected $className = 'notes';

	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[notesPublic]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<textarea class="notes" id="' . $idBase . '[notesPublic]" name="' . $nameBase . '[notesPublic]"' . $enabledTag . 
						' maxlength="255" rows="4">' . $this->getValue() . '</textarea>';
	}	
}

/* The phone class and the 10-digit number phone number and the three numbers that comprise it.
Note that they are all text numbers because they do not have any real computational value */
class phone extends text implements HTML {
	
	// Accepts ten-digit string or three 3/4 digit strings
	function __construct() {		// If no arguments, start with empty strings
		if (func_num_args() == 0) {
			$this->phone = new text();
			$this->phone1 = new text();
			$this->phone2 = new text();
			$this->phone3 = new text();
			$this->formatted = '';
		}
		else if (func_num_args() == 1) {	// If one argument, it's the 10-digit number.  Break it up
			$this->phone = new text(func_get_arg(0));
			$this->phone1to3();
			$this->format();
		}
		else if (func_num_args() == 3) { // 
			$this->phone1 = new text(func_get_arg(0));
			$this->phone2 = new text(func_get_arg(1));
			$this->phone3 = new text(func_get_arg(2));
			$this->phone3to1();
			$this->format();
		}
		else {
			die('Invalid number of arguments for phone class!');
		}		
	}
	
	// Convert three string phone number to one string
	private function phone3to1() {
		$this->phone = new text($this->phone1->getValue() . $this->phone2->getValue() . $this->phone3->getValue());
	}
	
	// Convert one string phone number to three strings;
	private function phone1to3() {
		$this->phone1 = new text(substr($this->phone->getValue(), 0, 3));
		$this->phone2 = new text(substr($this->phone->getValue(), 3, 3));
		$this->phone3 = new text(substr($this->phone->getValue(), 6, 4));
	}
	
	// Format number as (XXX) XXX-XXXX
	protected function format() {
		$this->formatted = '(' . $this->phone1->getFormatted() . ') ' . $this->phone2->getFormatted() . '-' . $this->phone3->getFormatted();
	}
	
	// Get functions
	public function getPhone1() {
		return $this->phone1->getValue();
	}
	public function getPhone2() {
		return $this->phone2->getValue();
	}
	public function getPhone3() {
		return $this->phone3->getValue();
	}
	public function getValue() {
		return $this->phone->getValue();
	}
	public function getFormatted() {
		return $this->formatted;
	}
	
	// Label HTML
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[phone1]">' . $label . '</label>';
	}
	
	// Input HTMl (note that the format is not (xxx) xxx-xxxx because the parentheses don't look good
	// next to text boxes.  (maybe use CSS?)
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabled = '';
		}
		else {
			$enabled = ' disabled';
		}
		echo '
					<input id="' . $idBase . '[phone][1]" type="text" name="' . $nameBase . '[phone][1]" value ="' . 
						$this->getPhone1() . '" maxlength="3" style="width: 30px;"' . $enabled . ' required/> - 
					<input id="' . $idBase . '[phone][2]" type="text" name="' . $nameBase . '[phone][2]" value ="' . 
						$this->getPhone2() . '" maxlength="3"  style="width: 30px;"' . $enabled . ' required/> - 
					<input id="' . $idBase . '[phone][3]" type="text" name="' . $nameBase . '[phone][3]" value ="' . 
						$this->getPhone3() . '" maxlength="4" style="width: 40px;"' . $enabled . ' required/>';
					
	}
}

// Address class is a simple text class, stores the address line of an institution
class address extends text implements HTML {
	protected $className = 'address';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[address]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="address" type="text" id="' . $idBase . '[address]" name="' . $nameBase . 
						'[address]" value="' . $this->getValue() . '"' . $enabledTag . ' maxlength="128" required/>';
	}
}

// City class is a simple text class, stores the city of an institution
class city extends text implements HTML {
	protected $className = 'city';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[city]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="city" type="text" id="' . $idBase . '[city]" name="' . $nameBase . 
						'[city]" value="' . $this->getValue() . '"' . $enabledTag . ' maxlength="64" required/>';
	}
}

//Zip code is a simple text class (not integer, but validated as an integer), stores the zip code of an institution
class zip extends text implements HTML {
	protected $className = 'zip';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[zip]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="zip" type="text" id="' . $idBase . '[zip]" name="' . $nameBase . 
						'[zip]" value="' . $this->getValue() . '"' . $enabledTag . ' maxlength="5" required/>';
	}
}

// Display ID is an ID used to identify a meeting, since it doesn't have a name.  It might be worth throwing away.
// The 1st digit of the ID identifies the day of the week
class displayID extends number implements HTML {
	protected $className = 'displayID';
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[displayID]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="id" type="number" min="100" max="799" id="' . $idBase . '[displayID]" name="' . $nameBase . 
						'[displayID]" value="' . $this->getValue() . '"' . $enabledTag . ' required/>';
	}
}
	
// mtime is a meeting time, stored as an integer (time is a protected keyword in PHP)
class mtime extends number {
	protected $className = 'mtime';
	protected $hour;
	protected $minute;
	protected $pm;
	
	
	function __construct() {
		if (func_num_args() == 0) {
			$this->value = null;
			$this->hour = null;
			$this->minute = null;
			$this->pm = null;
		}
		else if (func_num_args() == 1) {
			$this->value = func_get_arg(0);
			$this->decode();
		}
		else if (func_num_args() == 3) {
			$this->hour = func_get_arg(0);
			$this->minute = func_get_arg(1);
			$this->ampm = func_get_arg(2);
			$this->encode();
		}
		else {
			die('Invalid number of arguments for dow class!');
		}
	}
	
	// convert hour, minute, am/pm inputs into SQL and display formats
	protected function encode() {
		$this->value = date('H:i', strtotime($this->hour . ':' . $this->minute . ' ' . $this->ampm)); // SQL format
		$this->formatted = date('g:i A', strtotime($this->value)); // Display format
	}
	
	// Convert SQL format into hour, minute, am/pm, and display formats
	protected function decode() {
		$this->formatted = date('g:i A', strtotime($this->value));
		$this->hour = date('g', strtotime($this->value));
		$this->minute = date('i', strtotime($this->value));
		$this->ampm = date('A', strtotime($this->value));
	}
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[hour]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		
		// Hour input
		echo '
						<select id="' . $idBase . '[hour]" name="' . $nameBase . '[hour]" ' . $enabledTag . ' required/>
							<option value=""></option>';
			for ($i=1; $i<=12; $i++) {
				if ($i == $this->hour) {
					echo '
							<option value="' . $i . '" selected>' . $i . '</option>';
				}
				else {
					echo '
							<option value="' . $i . '">' . $i . '</option>';
				}
			}
			echo '
						</select>';
		
		// Minute input
		echo '
					<select id="' . $idBase . '[minute]" name="' . $nameBase . '[minute]" ' . $enabledTag . ' required/>
						<option value=""></option>';
		for ($i=0; $i<=45; $i=$i+15) {
			
			if (!is_null($this->minute) && ($i == $this->minute)) {
				echo '
						<option value="' . sprintf('%02d', $i) . '" selected>' . sprintf('%02d', $i) . '</option>';
			}
			else {
				echo '
						<option value="' . sprintf('%02d', $i) . '">' . sprintf('%02d', $i) . '</option>';
			}
		}
		echo '
					</select>';
		
		// AM/PM input
		echo '
					<select id="' . $idBase . '[ampm]" name="' . $nameBase . '[ampm]" ' . $enabledTag . ' required/>';
		switch($this->ampm) {
			case null:
				echo '
						<option value="" selected></option>
						<option value="AM">AM</option>
						<option value="PM">PM</option>';
				break;
			case 'AM':
				echo '
						<option value=""></option>
						<option value="AM" selected>AM</option>
						<option value="PM">PM</option>';
				break;
			case 'PM':
				echo '
						<option value=""></option>
						<option value="AM">AM</option>
						<option value="PM" selected>PM</option>';
				break;
		}
		echo '
					</select>';
	}

}

// mdate is an assignment date, stored as text.  This data type is kind of nuanced, because different date formats
// are in play (SQL, display, PHP, etc.)
class mdate extends text implements HTML {
	protected $className = 'mtime';
	
	protected function format() {
		if (empty($this->value) || is_null($this->value)) {
			$this->formatted = $this->defaultFormatted;
		}
		else {
			// If no date is specified...
			if (substr($this->value, -2) == '00') {
				$this->value = strtotime(substr($this->value,0,-3));	// Grab year and month only
				$this->value = date('Y-m', $this->value);	// SQL format
				$this->formatted = date('n/Y', strtotime($this->value)); // Display format
			}
			// If date is specified...
			else {
				$this->value = strtotime($this->value);
				$this->value = date('Y-m-d', $this->value);	// SQL format
				$this->formatted = date('n/j/Y', strtotime($this->value)); // Display format
				$this->formattedShort = date('n/j', strtotime($this->value)); // Display format (short is necessary
			}
		}
	}
	
	public function labelHTML($idBase, $label) {
		echo '
					<label for="' . $idBase . '[date]">' . $label . '</label>';
	}
	
	public function inputHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}		
		echo '
					<input class="time" type="date" id="' . $idBase . '[date]" name="' . $nameBase . 
						'[date]" value="' . $this->getValue() . '"' . $enabledTag . ' required/>';
	}
	
	// Get the short version of the date
	public function getFormattedShort() {
		return $this->formattedShort;
	}
}	
	
?>