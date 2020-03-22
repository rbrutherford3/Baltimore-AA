<?php
/* 
Person class.  This class is an extension of the class data and employs
all it's methods.  It overrides the default members to make it fit the 'person'
table in the MySQL database
*/
include_once 'datatypes.php';
include_once 'data.php';

// Person class holds all the information and methods for a group
class person extends data {

	// Define all default properties.  Data class uses this information to process SQL statements
	protected $db;
	protected $table = 'people';
	protected $idField = 'ID';
	protected $fields = array('Name', 'Initial', 'Phone', 'Notes', 'Active');
	protected $fieldTypes = array(PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT);
	protected $lookupFields = array(0, 1, 2);
	protected $sortFields = array(0, 1);
	protected $params = array();
	protected $numParams = 7;

	// Store all inputs into members
	protected function parseInput($inputs) {
		if (is_null($inputs)) { // If no inputs, then start with empty members
			$this->name = new name();
			$this->initial = new initial();
			$this->phone = new phone();
			$this->notes = new notes();
			$this->active = new active();
		}
		else {
			// Store inputs into members
			$this->name = new name($inputs[0]);
			$this->initial = new initial($inputs[1]);
			$this->phone = new phone($inputs[2], $inputs[3], $inputs[4]);
			$this->notes = new notes($inputs[5]);
			$this->active = new active($inputs[6]);

			// Store the parameters used for SQL statements from the members
			$this->params[0] = $this->name->getValue();
			$this->params[1] = $this->initial->getValue();
			$this->params[2] = $this->phone->getValue();
			$this->params[3] = $this->notes->getValue();
			$this->params[4] = $this->active->getValue();
		}
	}

	// Parse results from SQL view function into person members.  Note that this
	// will overwrite any members defined in the constructor
	protected function parseOutput() {
		$this->name = new name($this->params[0]);
		$this->initial = new initial($this->params[1]);
		$this->phone = new phone($this->params[2]);
		$this->notes = new notes($this->params[3]);
		$this->active = new active($this->params[4]);
	}

	// Do the same with arrays for viewall.  Note that these have to be different
	// names from the members, otherwise it will overwrite them
	protected function parseOutputs() {
		$this->names[] = new name($this->params[0]);
		$this->initials[] = new initial($this->params[1]);
		$this->phones[] = new phone($this->params[2]);
		$this->notess[] = new notes($this->params[3]);
		$this->actives[] = new active($this->params[4]);
	}

	// Public 'get' functions to grab individual members
	public function getName() {
		return $this->name;
	}
	public function getInitial() {
		return $this->initial;
	}
	public function getPhone() {
		return $this->phone;
	}
	public function getNotes() {
		return $this->notes;
	}
	public function getActive() {
		return $this->active;
	}

	// Debugging code to output members.  Possibly depricated.
	public function output() {
		echo $this->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->getName()->getFormatted() . $this->getInitial()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getPhone()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getNotes()->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->getActive()->getFormatted() . '<br>';
	}

	// Debugging code to output array members.  Possibly depricated.
	public function outputs() {
		for ($i=0; $i<$this->total; $i++) {
			echo $this->ids[$i] . '<br>';
			echo '&nbsp&nbsp' . $this->names[$i]->getFormatted() . $this->initials[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->phones[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->notess[$i]->getFormatted() . '<br>';
			echo '&nbsp&nbsp' . $this->actives[$i]->getFormatted() . '<br>';
		}
	}

	// HTML for form page.  Uses pre-made HTML scripts of members from datatypes.php
	// Note that this class has three different options: add, edit, and select, as it is
	// always used in conjunction with another class, so it must have its input methods
	// on one page.  The radiobuttons.js file handles the selection of the input methods
	public function inputHTML($idBase, $nameBase, $visible, $title) {
		if ($visible) {
			$visibleTag = '';
		}
		else {
			$visibleTag = ' style="display: none;"';
		}
		echo '
			<div class="person" ' . $visibleTag . ' id="' . $idBase . '">
			<center><h2>' . strtoupper($title) . '</h2></center>';

			echo $this->titleHTML($idBase, $nameBase, $title);

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
		$this->name->inputHTML($idBase, $nameBase, $visible);
		echo '</p>';

		echo '<p>';
		$this->initial->labelHTML($idBase, 'Last Initial:');
		echo '<br>';
		$this->initial->inputHTML($idBase, $nameBase, $visible);
		echo '</p>';

		echo '<p>';
		$this->phone->labelHTML($idBase, 'Phone Number:');
		echo '<br>';
		$this->phone->inputHTML($idBase, $nameBase, $visible);
		echo '</p>';

		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, $visible);
		$this->active->labelHTML($idBase, 'Person Active');
		echo '</p>';

		echo '<p>';
		$this->notes->labelHTML($idBase, 'Notes:');
		echo '<br>';
		$this->notes->inputHTML($idBase, $nameBase, $visible);
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
			personbuttons("' . $idBase . '");
			</script>';
	}

	// Simple version of the input HTML (complex version above)
	public function inputHTMLSimple($idBase, $nameBase, $title) {
		echo '
			<div class="person" id="' . $idBase . '">
			<center><h2>AA MEMBER</h2></center>';

		echo $this->titleHTML($idBase, $nameBase, $title);

		if (!(is_null($this->id))) {	// Only use hidden HTML ID field if not empty
			echo $this->idHTML($idBase, $nameBase);
		}

		echo '<p>';
		$this->name->labelHTML($idBase, 'Name:');
		echo '<br>';
		$this->name->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		$this->initial->labelHTML($idBase, 'Last Initial:');
		echo '<br>';
		$this->initial->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		$this->phone->labelHTML($idBase, 'Phone Number:');
		echo '<br>';
		$this->phone->inputHTML($idBase, $nameBase, true);
		echo '</p>';

		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, true);
		$this->active->labelHTML($idBase, 'Person Active');
		echo '</p>';

		echo '<p>';
		$this->notes->labelHTML($idBase, 'Notes:');
		echo '<br>';
		$this->notes->inputHTML($idBase, $nameBase, true);
		echo '</p>';
		echo '
			</div>';
	}

	// Hidden HTML field for ID, for passing on to load.php
	private function idHTML($idBase, $nameBase) {
		echo '
					<input type="hidden" id="' . $idBase . '[id]" name="' . $idBase . '[id]" value="' . $this->id . '">';
	}

	// Hidden HTML field for title, not used, might be eliminated
	private function titleHTML($idBase, $nameBase, $title) {
		echo '
					<input type="hidden" id="' . $idBase . '[title]" name="' . $idBase . '[title]" value="' . $title . '">';
	}

	// HTML for button to add new person
	private function addButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selectedTag = ' checked';
		}
		else {
			$selectedTag = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[add]" value="1" name="' . $nameBase . '[method]"' . $selectedTag . '>
					<b><label for="' . $idBase . '[add]">Add new person</label></b>';
	}

	// HTML for button to edit existing person
	private function editButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selectedTag = ' checked';
		}
		else {
			$selectedTag = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[edit]" value="2" name="' . $nameBase . '[method]"' . $selectedTag . '>
					<b><label for="' . $idBase . '[edit]">Edit existing person</label></b>';
	}

	// HTML for option to select existing person from a pulldown menu
	private function selectButton($idBase, $nameBase, $selected) {
		if ($selected) {
			$selectedTag = ' checked';
		}
		else {
			$selectedTag = '';
		}
		echo '
					<input type="radio" id="' . $idBase . '[select]" value="3" name="' . $nameBase . '[method]"' . $selectedTag . '>
					<b><label for="' . $idBase . '[select]">Select existing person</label></b>';
	}

	// HTML for the pulldown menu to select an existing entry
	private function pulldownHTML($idBase, $nameBase, $enabled) {
		if ($enabled) {
			$enabledTag = '';
		}
		else {
			$enabledTag = ' disabled';
		}
		echo '
					<select class="pulldown" id="' . $idBase . '[pulldown]" name="' . $nameBase . '[pulldown]"' . $enabledTag . ' required>';
		if ($this->total > 0) {	// Only display "****Select person from list****" if people in database
			echo '
						<option value="" selected="selected">***Select person from list***</option>';
			for ($i=0; $i<$this->total; $i++) { // Populate list
				echo '
						<option value="' . $this->ids[$i] . '">' . 
							$this->names[$i]->getFormatted() . 
							$this->initials[$i]->getFormatted() . ' - ' . 
							$this->phones[$i]->getFormatted() . '</option>';
			}
		}
		else {	// Display 'no people to select from' if database table is empty
			echo '
						<option selected="selected">***No people to select from!***</option>';
		}
		echo '
					</select>';
	}

	// In some rare cases, there is a second group rep or meeting co-sponsor.  Add a button to add or delete them
	public function toggleButton($idBase, $nameBase, $state, $type) {
		// Set initial values based on whether there's an existing second rep or second co-sponsor

		// Select initial state of button (add/remove/none)
		switch ($state) {
			case 0: 	// This person does not exist and is the second person: no buttons
				$showHidden = ' style="display: none"';
				$hideHidden = ' style="display: none"';
				$exists = '0';
				break;
			case 1:		// This person does not initially exist and is NOT the second person: need "add" button
				$showHidden = '';
				$hideHidden = ' style="display: none"';
				$exists = '0';
				break;
			case 2:		// This person does initially exist and there is no second version of the person, need "remove" button
						// Or: this person does initially exist and is the second version of the person
				$showHidden = ' style="display: none"';
				$hideHidden = '';
				$exists = '1';
				break;
			case 3:		// This person does initially exist and there is a second version of the person, show nothing
				$showHidden = ' style="display: none"';
				$hideHidden = ' style="display: none"';
				$exists = '1';
				break;
		}

		 // Use two different buttons: "add" and "delete" and hide or show them accordingly (handled by secondbuttons.js)
		echo '
				<input type="hidden" id="' . $idBase . '[exists]" name="' . $nameBase . '[exists]" value="' . $exists . '">
				<button id="' . $idBase . '[show]" name="' . $nameBase . '[show]" type="button"' . $showHidden . '>Add ' . $type . '</button>
				<button id="' . $idBase . '[hide]" name="' . $nameBase . '[hide]" type="button"' . $hideHidden . '>Remove ' . $type . '</button>';
/* 		echo '  // this function was moved to the form.php files to be more versatile
					<script>
					secondbuttons("' . $idBase . '");
					</script>'; */
	}

	// Simple tabular code for displaying a person in view.php
	public function outputHTML() {

		// Check to see if this person is rep at any group
		$sqlRep = "SELECT `ID`, `Name`, `Active` FROM groups  WHERE `Rep`=:Rep;";
		$stmtRep = $this->db->prepare($sqlRep);
		$stmtRep->bindValue(":Rep", $this->id, PDO::PARAM_INT);
		$foundRep = false;
		if ($stmtRep->execute()) {
			while ($rowRep = $stmtRep->fetch(PDO::FETCH_ASSOC)) {
				$foundRep = true;
				$rowsRep[] = $rowRep;
			}
		}

		// Check to see if this person is second rep at any group
		$sqlRep2 = "SELECT `ID`, `Name`, `Active` FROM groups  WHERE `Rep2`=:Rep2;";
		$stmtRep2 = $this->db->prepare($sqlRep2);
		$stmtRep2->bindValue(":Rep2", $this->id, PDO::PARAM_INT);
		$foundRep2 = false;
		if ($stmtRep2->execute()) {
			while ($rowRep2 = $stmtRep2->fetch(PDO::FETCH_ASSOC)) {
				$foundRep2 = true;
				$rowsRep2[] = $rowRep2;
			}
		}

		// Check to see if this person is sponsor of any meeting
		$sqlSponsor = "SELECT `ID`, `DisplayID`, `Active` FROM meetings  WHERE `Sponsor`=:Sponsor;";
		$stmtSponsor = $this->db->prepare($sqlSponsor);
		$stmtSponsor->bindValue(":Sponsor", $this->id, PDO::PARAM_INT);
		$foundSponsor= false;
		if ($stmtSponsor->execute()) {
			while ($rowSponsor = $stmtSponsor->fetch(PDO::FETCH_ASSOC)) {
				$foundSponsor = true;
				$rowsSponsor[] = $rowSponsor;
			}
		}

		// Check to see if this person is co-sponsor of any meeting
		$sqlCoSponsor = "SELECT `ID`, `DisplayID`, `Active` FROM meetings  WHERE `CoSponsor`=:CoSponsor;";
		$stmtCoSponsor = $this->db->prepare($sqlCoSponsor);
		$stmtCoSponsor->bindValue(":CoSponsor", $this->id, PDO::PARAM_INT);
		$foundCoSponsor = false;
		if ($stmtCoSponsor->execute()) {
			while ($rowCoSponsor = $stmtCoSponsor->fetch(PDO::FETCH_ASSOC)) {
				$foundCoSponsor = true;
				$rowsCoSponsor[] = $rowCoSponsor;
			}
		}

		// Check to see if this person is second co-sponsor of any meeting
		$sqlCoSponsor2 = "SELECT `ID`, `DisplayID`, `Active` FROM meetings  WHERE `CoSponsor2`=:CoSponsor2;";
		$stmtCoSponsor2 = $this->db->prepare($sqlCoSponsor2);
		$stmtCoSponsor2->bindValue(":CoSponsor2", $this->id, PDO::PARAM_INT);
		$foundCoSponsor2 = false;
		if ($stmtCoSponsor2->execute()) {
			while ($rowCoSponsor2 = $stmtCoSponsor2->fetch(PDO::FETCH_ASSOC)) {
				$foundCoSponsor2 = true;
				$rowsCoSponsor2[] = $rowCoSponsor2;
			}
		}

		// Display HTML
		include('header.php');
		echo '
		<title>Institution Committee - ' . $this->name->getFormatted() . $this->initial->getFormatted() . '</title>
		</head>
		<body>
			<h1>Person Viewer</h1>
			<p>
			<a class="button" href="../">Home</a>
			<a class="button" href="viewall.php">People</a>
			</p>
			<table cellspacing="5">
				<tr>
					<td colspan="3">
						<h2>Person:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>
						' . $this->getName()->getFormatted() . $this->getInitial()->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Phone Number:</b>
					</td>
					<td width="20">
					<td>
						' . $this->getPhone()->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->getNotes()->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Roles:</b>
					</td>
					<td width="20">
					<td>';

		// Output roles from queries above
		if ($foundRep) {
			foreach($rowsRep as $row) {
				echo 'Representative at <a href="../groups/view.php?id=' . $row['ID'] . '">' . $row['Name'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
			}
		}
		if ($foundRep2) {
			foreach($rowsRep2 as $row) {
				echo 'Second Representative at <a href="../groups/view.php?id=' . $row['ID'] . '">' . $row['Name'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
			}
		}
		if ($foundSponsor) {
			foreach($rowsSponsor as $row) {
				echo 'Sponsor of meeting <a href="../meetings/view.php?id=' . $row['ID'] . '">' . $row['DisplayID'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
			}
		}
		if ($foundCoSponsor) {
			foreach($rowsCoSponsor as $row) {
				echo 'Co-Sponsor of meeting <a href="../meetings/view.php?id=' . $row['ID'] . '">' . $row['DisplayID'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
			}
		}
		if ($foundCoSponsor2) {
			foreach($rowsCoSponsor2 as $row) {
				echo 'Second Co-Sponsor of meeting <a href="../meetings/view.php?id=' . $row['ID'] . '">' . $row['DisplayID'] . '</a> (' . ($row['Active'] ? 'Active' : 'Not Active') . ')<br>';
			}
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
			<title>Institution Committee - View People</title>
		</head>
		<body>';

		// Start Body HTML, define table and table headers
		echo '
			<h1>AA Members</h1>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
			<h2>Click a person to view it</h2>
			<div id="container">
				<table class="scroll">
					<thead>
						<tr class="header">
							<th>Member Name</th>
							<th>Phone Number</th>
							<th width="50">Notes</th>
							<th>Active</th>
						</tr>
					</thead>
					<tbody>';

		// For each person (note the title tag allows for mouse-over information)...
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
							<td nowrap>' . $this->names[$i]->getFormatted() . $this->initials[$i]->getFormatted() .'</td>
							<td nowrap>' . $this->phones[$i]->getFormatted() . '</td>
							<td nowrap title="' . $this->notess[$i]->getFormatted() . '">' . (is_null($this->notess[$i]->getValue()) ? '-' : 'Notes') . '</td>
							<td nowrap>' . $this->actives[$i]->getFormatted() . '</td>
						</tr>';
		}

		echo '
					</tbody>
				</table>
			</div>
			<h2>Click a person to view it</h2>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
		</body>
		</html>';
	}

}


?>
