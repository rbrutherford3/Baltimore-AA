<?php
/* 
Institution class.  This class is an extension of the class data and employs
all it's methods.  It overrides the default members to make it fit the 'institutions'
table in the MySQL database
*/
include_once 'datatypes.php';
include_once 'data.php';

// Institution class holds all the information and methods for an institution
class institution extends data {

	// Define all default properties.  Data class uses this information to process SQL statements
	protected $db;
	protected $table = 'institutions';
	protected $idField = 'ID';
	protected $fields = array('Name', 'Address', 'City', 'Zip', 'BG', 'NotesPublic', 'NotesPrivate', 'Active');
	protected $fieldTypes = array(PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT);
	protected $lookupFields = array(0);
	protected $sortFields = array(0);
	protected $params = array();
	protected $numParams = 8;

	// Store all inputs into members
	protected function parseInput($inputs) {
		if (is_null($inputs)) { // If no inputs, then start with empty members
			$this->name = new name();
			$this->address = new address();
			$this->city = new city();
			$this->zip = new zip();
			$this->bg = new bg();
			$this->notesPublic = new notesPublic();
			$this->notesPrivate = new notes();
			$this->active = new active();
		}
		else {
			// Store inputs into members
			$this->name = new name($inputs[0]);
			$this->address = new address($inputs[1]);
			$this->city = new city($inputs[2]);
			$this->zip = new zip($inputs[3]);
			$this->bg = new bg($inputs[4]);
			$this->notesPublic = new notesPublic($inputs[5]);
			$this->notesPrivate = new notes($inputs[6]);
			$this->active = new active($inputs[7]);

			// Store the parameters used for SQL statements from the members
			$this->params[0] = $this->name->getValue();
			$this->params[1] = $this->address->getValue();
			$this->params[2] = $this->city->getValue();
			$this->params[3] = $this->zip->getValue();
			$this->params[4] = $this->bg->getValue();
			$this->params[5] = $this->notesPublic->getValue();
			$this->params[6] = $this->notesPrivate->getValue();
			$this->params[7] = $this->active->getValue();
		}
	}

	// Parse results from SQL view function into institution members.  Note that this
	// will overwrite any members defined in the constructor
	protected function parseOutput() {
		$this->name = new name($this->params[0]);
		$this->address = new address($this->params[1]);
		$this->city = new city($this->params[2]);
		$this->zip = new zip($this->params[3]);
		$this->bg = new bg($this->params[4]);
		$this->notesPublic = new notesPublic($this->params[5]);
		$this->notesPrivate = new notes($this->params[6]);
		$this->active = new active($this->params[7]);
	}

	// Do the same with arrays for viewall.  Note that these have to be different
	// names from the members, otherwise it will overwrite them
	protected function parseOutputs() {
		$this->names[] = new name($this->params[0]);
		$this->addresss[] = new address($this->params[1]);
		$this->citys[] = new city($this->params[2]);
		$this->zips[] = new zip($this->params[3]);
		$this->bgs[] = new bg($this->params[4]);
		$this->notesPublics[] = new notesPublic($this->params[5]);
		$this->notesPrivates[] = new notes($this->params[6]);
		$this->actives[] = new active($this->params[7]);
	}

	// Public 'get' functions to grab individual members
	public function getName() {
		return $this->name;
	}
	public function getAddress() {
		return $this->address;
	}
	public function getCity() {
		return $this->city;
	}
	public function getZip() {
		return $this->zip;
	}
	public function getBG() {
		return $this->bg;
	}
	public function getNotesPublic() {
		return $this->notesPublic;
	}
	public function getNotesPrivate() {
		return $this->notesPrivate;
	}
	public function getActive() {
		return $this->active;
	}

	// Debugging code to output members.  Possibly depricated.
	public function output() {
		echo $this->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->getName()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getAddress()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getCity()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getZip()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getBG()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getNotesPublic()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getNotesPrivate()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getActive()->getFormatted() . '<br>';
	}

	// Debugging code to output array members.  Possibly depricated.
	public function outputs() {
		for ($i=0; $i<$this->total; $i++) {
			echo $this->ids[$i] . '<br>';
			echo '&nbsp&nbsp' . $this->names[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->addresss[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->citys[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->zips[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->bgs[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->notesPublics[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->notesPrivates[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->actives[$i]->getFormatted() . '<br>';
		}
	}

	// HTML for form page.  Uses pre-made HTML scripts of members from datatypes.php
	// Note that this class has three different options: add, edit, and select, as it is
	// always used in conjunction with another class, so it must have its input methods
	// on one page.  The radiobuttons.js file handles the selection of the input methods
	public function inputHTML($idBase, $nameBase, $visible) {
		if ($visible) {
			$visibleTag = '';
		}
		else {
			$visibleTag = ' style="display: none;"';
		}
		echo '
			<div class="institution" ' . $visibleTag . ' id=' . $idBase . '>
			<center><h2>INSTITUTION</h2></center>';

		if (is_null($this->id)) {
			echo '<p>';
			$this->addButton($idBase, $nameBase, true);
			echo '</p>';
		}
		else {	// Only add an edit button and ID HTML if this is an existing entry
			echo $this->idHTML($idBase, $nameBase);
			echo '<p>';
			$this->addButton($idBase, $nameBase, false);
			echo '<br>';
			$this->editButton($idBase, $nameBase, true);
			echo '</p>';
		}

		echo '<p>';
		$this->name->labelHTML($idBase, 'Name:');
		echo '<br>';
		$this->name->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		$this->address->labelHTML($idBase, 'Address:');
		echo '<br>';
		$this->address->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		echo '<div class="citycontainer">';
		$this->city->labelHTML($idBase, 'City:');
		$this->city->inputHTML($idBase, $nameBase, true);
		echo '</div>';
		echo '<div class="zipcontainer">';
		$this->zip->labelHTML($idBase, 'Zip Code:');
		$this->zip->inputHTML($idBase, $nameBase, true);
		echo '</div>';
		echo '</p><br><br>';

		echo '<p>';
		$this->bg->inputHTML($idBase, $nameBase, true);
		$this->bg->labelHTML($idBase, 'Requires Background Check');
		echo '</p>';

		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, true);
		$this->active->labelHTML($idBase, 'Institution Active');
		echo '</p>';

		echo '<p>';
		$this->notesPublic->labelHTML($idBase, 'Public Notes (printed):');
		echo '<br>';
		$this->notesPublic->inputHTML($idBase, $nameBase, true);
		echo '</p>';
		echo '<p>';
		$this->notesPrivate->labelHTML($idBase, 'Private Notes (not printed):');
		echo '<br>';
		$this->notesPrivate->inputHTML($idBase, $nameBase, true);
		echo '</p>';
		echo '<hr>';
		echo '<p>';
		$this->selectButton($idBase, $nameBase, false);
		echo '</p>';
		echo '<p>';
		$this->pulldownHTML($idBase, $nameBase, false);
		echo '</p>';
		echo '
			</div>';
		echo '
			<script>
			institutionbuttons("' . $idBase . '");
			</script>';
	}

	// Simpler version of inputHTML above, omits add/remove buttons
	public function inputHTMLSimple($idBase, $nameBase) {

		echo '
			<div class="institution" id=' . $idBase . '>
			<center><h2>INSTITUTION</h2></center>';

		if (!(is_null($this->id))) {	// Only use hidden HTML ID field if not empty
			echo $this->idHTML($idBase, $nameBase);
		}

		echo '<p>';
		$this->name->labelHTML($idBase, 'Name:');
		echo '<br>';
		$this->name->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		$this->address->labelHTML($idBase, 'Address:');
		echo '<br>';
		$this->address->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		echo '<div class="citycontainer">';
		$this->city->labelHTML($idBase, 'City:');
		$this->city->inputHTML($idBase, $nameBase, true);
		echo '</div>';
		echo '<div class="zipcontainer">';
		$this->zip->labelHTML($idBase, 'Zip Code:');
		$this->zip->inputHTML($idBase, $nameBase, true);
		echo '</div>';
		echo '</p><br><br>';

		echo '<p>';
		$this->bg->inputHTML($idBase, $nameBase, true);
		$this->bg->labelHTML($idBase, 'Requires Background Check');
		echo '</p>';

		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, true);
		$this->active->labelHTML($idBase, 'Institution Active');
		echo '</p>';

		echo '<p>';
		$this->notesPublic->labelHTML($idBase, 'Public Notes (printed):');
		echo '<br>';
		$this->notesPublic->inputHTML($idBase, $nameBase, true);
		echo '</p>';
		echo '<p>';
		$this->notesPrivate->labelHTML($idBase, 'Private Notes (not printed):');
		echo '<br>';
		$this->notesPrivate->inputHTML($idBase, $nameBase, true);
		echo '</p>';
		echo '
			</div>';
	}

	// Hidden HTML field for ID, for passing on to load.php
	private function idHTML($idBase, $nameBase) {
		echo '
					<input type="hidden" id="' . $idBase . '[id]" name="' . $idBase . '[id]" value="' . $this->id . '">';
	}

	// HTML for button to add new institution
	private function addButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selected = ' checked';
		}
		else {
			$selected = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[add]" value="1" name="' . $nameBase . '[method]"' . $selected . '>
					<b><label for="' . $idBase . '[add]">Add new institution</label></b>';
	}

	// HTML for button to edit existing institution
	private function editButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selected = ' checked';
		}
		else {
			$selected = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[edit]" value="2" name="' . $nameBase . '[method]"' . $selected . '>
					<b><label for="' . $idBase . '[edit]">Edit existing institution</label></b>';
	}

	// HTML for option to select existing institution from a pulldown menu
	private function selectButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selected = ' checked';
		}
		else {
			$selected = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[select]" value="3" name="' . $nameBase . '[method]"' . $selected . '>
					<b><label for="' . $idBase . '[select]">Select existing institution</label></b>';
	}

	// HTML for the pulldown menu to select an existing entry
	public function pulldownHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabled = '';
		}
		else {
			$enabled = ' disabled';
		}
		echo '
					<select class="pulldown" id="' . $idBase . '[pulldown]" name="' . $nameBase . '[pulldown]"' . $enabled . '>';
		if ($this->total > 0) {	// Only display "****Select institution from list****" if institutions in database
			echo '
						<option value="none" selected="selected">***Select institution from list***</option>';
			for ($i=0; $i<$this->total; $i++) { // Populate list
				echo '
						<option value="' . $this->ids[$i] . '">' . 
							$this->names[$i]->getFormatted() . '</option>';
			}
		}
		else {	// Display 'no institutions to select from' if database table is empty
			echo '
						<option selected="selected">***No institutions to select from!***</option>';
		}
		echo '
					</select>';
	}

	// Simple tabular code for displaying an institution in view.php
	public function outputHTML() {

		// Check for the meetings this institution is attached to and record them
		$sqlMeetings = "SELECT `ID`, `DisplayID`, `Active` FROM meetings WHERE `Institution`=:Institution;";
		$stmtMeetings = $this->db->prepare($sqlMeetings);
		$stmtMeetings->bindValue(":Institution", $this->id, PDO::PARAM_INT);
		if ($stmtMeetings->execute()) {
			while ($rowMeetings = $stmtMeetings->fetch(PDO::FETCH_ASSOC)) {
				$rowsMeetings[] = $rowMeetings;
			}
		}

		// Display HTML
		include('header.php');
		echo '
		<title>Institution Committee - ' . $this->name->getFormatted() . '</title>
		</head>
		<body>
			<h1>Institution Viewer</h1>
			<p>
			<a class="button" href="../">Home</a>
			<a class="button" href="viewall.php">Institutions</a>
			</p>
			<table cellspacing="5">
				<tr>
					<td colspan="3">
						<h2>Institution:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>
						' . $this->name->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Address:</b>
					</td>
					<td width="20">
					<td>
						' . $this->address->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>City:</b>
					</td>
					<td width="20">
					<td>
						' . $this->city->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Zip Code:</b>
					</td>
					<td width="20">
					<td>
						' . $this->zip->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Background Check Required:</b>
					</td>
					<td width="20">
					<td>
						' . $this->bg->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Active:</b>
					</td>
					<td width="20">
					<td>
						' . $this->active->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Public Notes (printed):</b>
					</td>
					<td width="20">
					<td>
						' . $this->notesPublic->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Private Notes (not printed):</b>
					</td>
					<td width="20">
					<td>
						' . $this->notesPrivate->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Meetings:</b>
					</td>
					<td width="20">
					<td>';

		foreach($rowsMeetings as $row) {
			echo '<a href="../meetings/view.php?id=' . $row['ID'] . '">' . $row['DisplayID'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
		}
		echo '
					</td>
				</tr>';
				// Display navigation buttons at end
		echo '
			</table>
			<br>
			<a class="button" href="form.php?id=' . $this->id . '">Edit</a>
			<a class="button" href="form.php">Add New</a>
		</body>
		</html>';
	}

	// Function to display outputs if using viewall.php in a table
	public function outputsHTML() {

		// Header HTML (include tables.css)
		include('header.php');
		echo '
			<link rel="stylesheet" type="text/css" href="' . $libloc . 'tables.css">
			<title>Institution Committee - View Institutions</title>
		</head>
		<body>';

		// Start Body HTML, define table and table headers
		echo '
			<h1>Institutions</h1>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
			<h2>Click an institution to view it (including notes)</h2>
			<div id="container">
				<table class="scroll">
					<thead>
						<tr class="header">
							<th>Institution Name</th>
							<th>Address</th>
							<th>City</th>
							<th>Zip</th>
							<th width="50">Background Checks</th>
							<th>Active</th>
						</tr>
					</thead>
					<tbody>';

		// For each institution...
		for ($i=0; $i<$this->total; $i++) {
			if ($this->actives[$i]->getValue()) {
				echo '
						<tr class="row" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			else {
				echo '
						<tr class="row inactive" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			echo '
							<td nowrap>' . $this->names[$i]->getFormatted() . '</td>
							<td nowrap>' . $this->addresss[$i]->getFormatted() . '</td>
							<td nowrap>' . $this->citys[$i]->getFormatted() . '</td>
							<td nowrap>' . $this->zips[$i]->getFormatted() . '</td>
							<td nowrap>' . ($this->bgs[$i]->getValue() ? 'BG check req.' : '-') . '</td>
							<td nowrap>' . $this->actives[$i]->getFormatted() . '</td>
						</tr>';
		}

		echo '
					</tbody>
				</table>
			</div>
			<h2>Click an institution to view it (including notes)</h2>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
		</body>
		</html>';
	}

}


?>
