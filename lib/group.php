<?php
/* 
Group class.  This class is an extension of the class data and employs
all it's methods.  It overrides the default members to make it fit the 'groups'
table in the MySQL database
*/

include_once 'datatypes.php';
include_once 'data.php';
include_once 'person.php';

// Group class holds all the information and methods for a group.
class group extends data {

	// Define all default properties.  Data class uses this information to process SQL statements
	protected $db;
	protected $table = 'groups';
	protected $idField = 'ID';
	protected $fields = array('Name', 'DOW', 'Gender', 'BG', 'Rep', 'Rep2', 'Notes', 'Active', 'Probation');
	protected $fieldTypes = array(PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT, 
		PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_INT);
	protected $lookupFields = array(0);
	protected $sortFields = array(0);
	protected $params = array();
	protected $numParams = 15;

	// Store all inputs into members
	protected function parseInput($inputs) {
		if (is_null($inputs)) {	// If no inputs, then start with empty members
			$this->name = new name();
			$this->dow = new dow();
			$this->gender = new gender();
			$this->bg = new bg();
			$this->rep = new person($this->db);
			$this->rep2 = new person($this->db);
			$this->notes = new notes();
			$this->active = new active();
			$this->probation = new probation();
		}
		else {
			// Store inputs into members
			$this->name = new name($inputs[0]);
			$this->dow = new dow($inputs[1], $inputs[2], $inputs[3], 
				$inputs[4], $inputs[5], $inputs[6], $inputs[7]);
			$this->gender = new gender($inputs[8]);
			$this->bg = new bg($inputs[9]);
			$this->rep = new person($this->db, $inputs[10]);
			$this->rep2 = new person($this->db, $inputs[11]);
			$this->notes = new notes($inputs[12]);
			$this->active = new active($inputs[13]);
			$this->probation = new probation($inputs[14]);

			// Store the parameters used for SQL statements from the members
			$this->params[0] = $this->name->getValue();
			$this->params[1] = $this->dow->getValue();
			$this->params[2] = $this->gender->getValue();
			$this->params[3] = $this->bg->getValue();
			$this->params[4] = $this->rep->getID();
			$this->params[5] = $this->rep2->getID();
			$this->params[6] = $this->notes->getValue();
			$this->params[7] = $this->active->getValue();
			$this->params[8] = $this->probation->getValue();
		}
	}

	// Parse results from SQL view function into group members.  Note that this
	// will overwrite any members defined in the constructor
	protected function parseOutput() {
		$this->name = new name($this->params[0]);
		$this->dow = new dow ($this->params[1]);
		$this->gender = new gender($this->params[2]);
		$this->bg = new bg($this->params[3]);
		$this->rep = new person($this->db, $this->params[4]);
		$this->rep2 = new person($this->db, $this->params[5]);
		$this->notes = new notes($this->params[6]);
		$this->active = new active($this->params[7]);
		$this->probation = new probation($this->params[8]);
	}

	// Do the same with arrays for viewall.  Note that these have to be different
	// names from the members, otherwise it will overwrite them
	protected function parseOutputs() {
		$this->names[] = new name($this->params[0]);
		$this->dows[] = new dow ($this->params[1]);
		$this->genders[] = new gender($this->params[2]);
		$this->bgs[] = new bg($this->params[3]);
		$this->reps[] = new person($this->db, $this->params[4]);
		$this->rep2s[] = new person($this->db, $this->params[5]);
		$this->notess[] = new notes($this->params[6]);
		$this->actives[] = new active($this->params[7]);
		$this->probations[] = new probation($this->params[8]);
	}

	// Public 'get' functions to grab individual members
	public function getName() {
		return $this->name;
	}
	public function getDOW() {
		return $this->dow;
	}
	public function getGender() {
		return $this->gender;
	}
	public function getBG() {
		return $this->bg;
	}
	public function getRep() {
		return $this->rep;
	}
	public function getRep2() {
		return $this->rep2;
	}
	public function getNotes() {
		return $this->notes;
	}
	public function getActive() {
		return $this->active;
	}
	public function getProbation() {
		return $this->probation;
	}

	// Debugging code to output members.  Possibly depricated.
	public function output() {
		echo $this->id . '<br>';
		echo '&nbsp&nbsp' . $this->name->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->dow->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->gender->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->bg->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->rep->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->rep2->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->notes->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->active->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->probation->getFormatted() . '<br>';
	}

	// HTML for form page.  Uses pre-made HTML scripts of members from datatypes.php
	public function inputHTML($idBase, $nameBase, $enabled) {
		echo '
			<div class="' . $idBase . '">
			<center><h2>GROUP</h2></center>';	// Use CSS
		if (!(is_null($this->id))) {	// Only use hidden HTML ID field if not empty
			echo $this->idHTML($idBase, $nameBase);
		}

		echo '<p>';
		$this->name->labelHTML($idBase, 'Group Name:');
		echo '<br>';
		$this->name->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';

		echo '<p>';
		$this->dow->inputHTMLcheckbox($idBase, $nameBase, $enabled);
		echo '</p>';

		echo '<p>';
		$this->gender->labelHTML($idBase, 'Select a gender:');
		echo '<br>';
		$this->gender->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';

		echo '<p>';
		$this->bg->inputHTML($idBase, $nameBase, $enabled);
		$this->bg->labelHTML($idBase, 'Permits Background Checks');
		echo '</p>';

		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, $enabled);
		$this->active->labelHTML($idBase, 'Group Active');
		echo '</p>';

		echo '<p>';
		$this->probation->inputHTML($idBase, $nameBase, $enabled);
		$this->probation->labelHTML($idBase, 'Group On Probation');
		echo '</p>';

		echo '<p>';
		$this->notes->labelHTML($idBase, 'Notes:');
		echo '<br>';
		$this->notes->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';

		echo '

			</div>';
	}

	// Hidden HTML field for ID, for passing on to load.php
	private function idHTML($idBase, $nameBase) {
		echo '
				<input type="hidden" id="' . $idBase . '[id]" name="' . $nameBase . '[id]" value="' . $this->id . '">';
	}

	// Simple tabular code for displaying a group in view.php
	public function outputHTML($public) {
		// Display HTML
		if (!$public) {
			include('header.php');
			echo '
			<title>Institution Committee - "' . $this->name->getFormatted() . '" Group</title>
			</head>
			<body>
				<h1>Group Viewer</h1>
				<p>
				<a class="button" href="../">Home</a>
				<a class="button" href="viewall.php">Groups</a>
				</p>';
		}
		echo '
			<table cellspacing="5">
				<tr>
					<td colspan="3">
						<h2>Group:</h2>
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
						<b>Days of week:</b>
					</td>
					<td width="20">
					<td>
						' . $this->dow->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Gender:</b>
					</td>
					<td width="20">
					<td>
						' . $this->gender->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Permits background checks:</b>
					</td>
					<td width="20">
					<td>
						' . $this->bg->getFormatted() . '
					</td>
				</tr>';
		if (!$public) {
			echo '
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
						<b>On Probation:</b>
					</td>
					<td width="20">
					<td>
						' . $this->probation->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->notes->getFormatted() . '
					</td>
				</tr>';
		}
		echo '
				<tr>
					<td colspan="3">
						&nbsp
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<h2>Representative:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>';
		if ($public) {
			echo
						$this->rep->getName()->getFormatted() . $this->rep->getInitial()->getFormatted();
		}
		else {
			echo '
						<a href="../people/view.php?id=' . $this->rep->getID() . '">' . $this->rep->getName()->getFormatted() . $this->rep->getInitial()->getFormatted() . '</a>;';
		}
		echo '
					</td>
				</tr>
				<tr>
					<td>
						<b>Phone Number:</b>
					</td>
					<td width="20">
					<td>
						' . $this->rep->getPhone()->getFormatted() . '
					</td>
				</tr>';
		if (!$public) {
			echo '
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->rep->getNotes()->getFormatted() . '
					</td>
				</tr>';
		}

		// Second rep display

		// only display if rep is present:
		if(!is_null($this->rep2->id)) {
		echo '
					<tr>
					<td colspan="3">
						&nbsp
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<h2>Representative #2:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>';
			if ($public) {
				echo
						$this->rep2->getName()->getFormatted() . $this->rep2->getInitial()->getFormatted();
			}
			else {
				echo '
						<a href="../people/view.php?id=' . $this->rep2->getID() . '">' . $this->rep2->getName()->getFormatted() . $this->rep2->getInitial()->getFormatted() . '</a>';
			}
			echo '
					</td>
				</tr>
				<tr>
					<td>
						<b>Phone Number:</b>
					</td>
					<td width="20">
					<td>
						' . $this->rep2->getPhone()->getFormatted() . '
					</td>
				</tr>';
			if (!$public) {
				echo '
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->rep2->getNotes()->getFormatted() . '
					</td>
				</tr>';
			}
		}
				// Display navigation buttons at end
		echo '
			</table>
			<br>';
		if (!$public) {
			echo '
			<a class="button" href="form.php?id=' . $this->id . '">Edit</a>
			<a class="button" href="form.php">Add New</a>
			<a class="button" href="../assignments/viewall.php?group=' . $this->id . '">Assignments</a>
		</body>
		</html>';
		}
	}

	// Function to display outputs if using viewall.php in a table
	public function outputsHTML() {

		// Header HTML (include tables.css)
		include('header.php');
		echo '
			<link rel="stylesheet" type="text/css" href="' . $libloc . 'tables.css">
			<title>Institution Committee - View Groups</title>
		</head>
		<body>';

		// Start Body HTML, define table and table headers
		echo '
			<h1>AA Groups</h1>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
			<h2>Click a group to view it (including notes)</h2>
			<div id="container">
				<table class="scroll">
					<thead>
						<tr class="header">
							<th>Group Name</th>
							<th>Days of Week</th>
							<th>Gender</th>
							<th width="50">Background Checks OK</th>
							<th width="50">Active</th>
							<th width="50">On Probation</th>
							<th>Representative</th>
							<th>Phone Number</th>
							<th>Second Rep</th>
							<th>Phone Number</th>
						</tr>
					</thead>
					<tbody>';

		// For each group (note the title tag allows for mouse-over information)...
		for ($i=0; $i<$this->total; $i++) {
			$this->reps[$i]->view();
			$this->rep2s[$i]->view();
			if ($this->actives[$i]->getValue()) {
				echo '
						<tr class="row" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			else {
				echo '
						<tr class="row inactive" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			echo '
							<td nowrap>' . $this->names[$i]->getFormatted() .'</td>
							<td nowrap>' . $this->dows[$i]->getFormatted() . '</td>
							<td nowrap>' . $this->genders[$i]->getFormatted() . '</td>
							<td nowrap>' . ($this->bgs[$i]->getValue() ? 'BG checks OK' : 'No BG checks') . '</td>
							<td nowrap>' . $this->actives[$i]->getFormatted() . '</td>
							<td nowrap>' . $this->probations[$i]->getFormatted() . '</td>
							<td nowrap>' . (is_null($this->reps[$i]->getID()) ? '<i>Needs Representative</i>' : '<a href="../people/view.php?id=' . $this->reps[$i]->getID() . '">' . 
								$this->reps[$i]->getName()->getFormatted() . $this->reps[$i]->getInitial()->getFormatted()) . '</a></td>
							<td nowrap>' . (is_null($this->reps[$i]->getID()) ? '-' : $this->reps[$i]->getPhone()->getFormatted()) . '</td>
							<td nowrap>' . (is_null($this->rep2s[$i]->getID()) ? '-' : '<a href="../people/view.php?id=' . $this->rep2s[$i]->getID() . '">' . 
								$this->rep2s[$i]->getName()->getFormatted() . $this->rep2s[$i]->getInitial()->getFormatted()) . '</a></td>
							<td nowrap>' . (is_null($this->rep2s[$i]->getID()) ? '-' : $this->rep2s[$i]->getPhone()->getFormatted()) . '</td>
						</tr>';
		}

		echo '
					</tbody>
				</table>
			</div>
			<h2>Click a group to view it (including notes)</h2>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
		</body>
		</html>';
	}
}

?>
