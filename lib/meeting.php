<?php
/* 
Meeting class.  This class is an extension of the class data and employs
all it's methods.  It overrides the default members to make it fit the 'meetings'
table in the MySQL database
*/

include_once 'datatypes.php';
include_once 'data.php';
include_once 'institution.php';
include_once 'person.php';

// Meeting class holds all the information and methods for a meeting.
class meeting extends data {
	
	// Define all default properties.  Data class uses this information to process SQL statements
	protected $db;
	protected $table = 'meetings';
	protected $idField = 'ID';
	protected $fields = array('DisplayID', 'Institution', 'DOW', 'Time', 
		'Gender', 'Sponsor', 'CoSponsor', 'CoSponsor2', 'NotesPublic', 'NotesPrivate', 'Active');
	protected $fieldTypes = array(PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT, 
		PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT);
	protected $lookupFields = array(0);
	protected $sortFields = array(0);
	protected $params = array();
	protected $numParams = 13;
	
	// Store all inputs into members
	protected function parseInput($inputs) {
		if (is_null($inputs)) {	// If no inputs, then start with empty members
			$this->displayID = new displayID();
			$this->institution = new institution($this->db);
			$this->dow = new dow();
			$this->mtime = new mtime();
			$this->gender = new gender();
			$this->sponsor = new person($this->db);
			$this->cosponsor = new person($this->db);
			$this->cosponsor2 = new person($this->db);
			$this->notesPublic = new notesPublic();
			$this->notesPrivate = new notes();
			$this->active = new active();
		}
		else {
			// Store inputs into members
			$this->displayID = new displayID($inputs[0]);
			$this->institution = new institution($this->db, $inputs[1]);
			$this->dow = new dow($inputs[2]);
			$this->mtime = new mtime($inputs[3], $inputs[4], $inputs[5]);
			$this->gender = new gender($inputs[6]);
			$this->sponsor = new person($this->db, $inputs[7]);
			$this->cosponsor = new person($this->db, $inputs[8]);
			$this->cosponsor2 = new person($this->db, $inputs[9]);
			$this->notesPublic = new notesPublic($inputs[10]);
			$this->notesPrivate = new notes($inputs[11]);
			$this->active = new active($inputs[12]);			
			
			// Store the parameters used for SQL statements from the members
			$this->params[0] = $this->displayID->getValue();
			$this->params[1] = $this->institution->getID();
			$this->params[2] = $this->dow->getValue();
			$this->params[3] = $this->mtime->getValue();
			$this->params[4] = $this->gender->getValue();
			$this->params[5] = $this->sponsor->getID();
			$this->params[6] = $this->cosponsor->getID();
			$this->params[7] = $this->cosponsor2->getID();
			$this->params[8] = $this->notesPublic->getValue();
			$this->params[9] = $this->notesPrivate->getValue();
			$this->params[10] = $this->active->getValue();
		}
	}
	
	// Parse results from SQL view function into meeting members.  Note that this
	// will overwrite any members defined in the constructor
	protected function parseOutput() {
		$this->displayID = new displayID($this->params[0]);
		$this->institution = new institution($this->db, $this->params[1]);
		$this->dow = new dow($this->params[2]);
		$this->mtime = new mtime($this->params[3]);
		$this->gender = new gender($this->params[4]);
		$this->sponsor = new person($this->db, $this->params[5]);
		$this->cosponsor = new person($this->db, $this->params[6]);
		$this->cosponsor2 = new person($this->db, $this->params[7]);
		$this->notesPublic = new notesPublic($this->params[8]);
		$this->notesPrivate = new notes($this->params[9]);
		$this->active = new active($this->params[10]);	
	}
	
	// Do the same with arrays for viewall.  Note that these have to be different
	// names from the members, otherwise it will overwrite them
	protected function parseOutputs() {
		$this->displayIDs[] = new displayID($this->params[0]);
		$this->institutions[] = new institution($this->db, $this->params[1]);
		$this->dows[] = new dow($this->params[2]);
		$this->mtimes[] = new mtime($this->params[3]);
		$this->genders[] = new gender($this->params[4]);
		$this->sponsors[] = new person($this->db, $this->params[5]);
		$this->cosponsors[] = new person($this->db, $this->params[6]);
		$this->cosponsor2s[] = new person($this->db, $this->params[7]);
		$this->notesPublics[] = new notesPublic($this->params[8]);
		$this->notesPrivates[] = new notes($this->params[9]);
		$this->actives[] = new active($this->params[10]);	
	}
	
	// Public 'get' functions to grab individual members
	public function getDisplayID() {
		return $this->displayID;
	}
	public function getInstitution() {
		return $this->institution;
	}
	public function getDOW() {
		return $this->dow;
	}
	public function getMTime() {
		return $this->mtime();
	}
	public function getGender() {
		return $this->gender;
	}
	public function getSponsor() {
		return $this->sponsor;
	}	
	public function getCoSponsor() {
		return $this->cosponsor;
	}
	public function getCoSponsor2() {
		return $this->cosponsor2;
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
		echo $this->id . '<br>';
		echo '&nbsp&nbsp' . $this->displayID->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->institution->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->dow->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->mtime->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->gender->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->sponsor->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->cosponsor->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->cosponsor2->getID() . '<br>';
		echo '&nbsp&nbsp' . $this->notesPublic->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->notesPrivate->getFormatted() . '<br>';
		echo '&nbsp&nbsp' . $this->active->getFormatted() . '<br>';
	}
	
	// HTML for form page.  Uses pre-made HTML scripts of members from datatypes.php
	public function inputHTML($idBase, $nameBase, $enabled) {
		echo '
			<div class="' . $idBase . '">
			<center><h2>MEETING</h2></center>';	// Use CSS
		if (!(is_null($this->id))) {	// Only use hidden HTML ID field if not empty
			echo $this->idHTML($idBase, $nameBase);
		}

		echo '<p>';
		$this->displayID->labelHTML($idBase, 'Meeting ID:');
		echo '<br>';
		$this->displayID->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';
		
		echo '<p>';
		$this->dow->labelHTMLpulldown($idBase, 'Day of the week:');
		echo '<br>';
		$this->dow->inputHTMLpulldown($idBase, $nameBase, $enabled);
		echo '</p>';
		
		echo '<p>';
		$this->mtime->labelHTML($idBase, 'Meeting time:');
		echo '<br>';
		$this->mtime->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';
		
		echo '<p>';
		$this->gender->labelHTML($idBase, 'Gender:');
		echo '<br>';
		$this->gender->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';
		
		echo '<p>';
		$this->active->inputHTML($idBase, $nameBase, $enabled);
		$this->active->labelHTML($idBase, 'Meeting Active');
		echo '</p>';
		
		echo '<p>';
		$this->notesPublic->labelHTML($idBase, 'Public notes (printed):');
		echo '<br>';
		$this->notesPublic->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';

		echo '<p>';
		$this->notesPrivate->labelHTML($idBase, 'Public notes (not printed):');
		echo '<br>';
		$this->notesPrivate->inputHTML($idBase, $nameBase, $enabled);
		echo '</p>';
		
		echo '
		
			</div>';
	}
	
	// Hidden HTML field for ID, for passing on to load.php
	private function idHTML($idBase, $nameBase) {
		echo '
				<input type="hidden" id="' . $idBase . '[id]" name="' . $nameBase . '[id]" value="' . $this->id . '">';
	}
	
	// Simple tabular code for displaying a meeting in view.php
	// "Public" option allows for hiding of private notes and hyperlinks for public view
	public function outputHTML($public) {
		// Display HTML
		if (!$public) {
			include('header.php');
			echo '
		<title>Institution Committee - Meeting ' . $this->displayID->getFormatted() . '</title>
		</head>
		<body>
			<h1>Meeting Viewer</h1>
			<a class="button" href="../">Home</a>
			<a class="button" href="viewall.php">Meetings</a>
			<br>
			<br>';
		}
		echo '
			<table cellspacing="5">
				<tr>
					<td colspan="3">
						<h2>Meeting:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>ID:</b>
					</td>
					<td width="20">
					<td>
						' . $this->displayID->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Day of week:</b>
					</td>
					<td width="20">
					<td>
						' . $this->dow->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Time:</b>
					</td>
					<td width="20">
					<td>
						' . $this->mtime->getFormatted() . '
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
				</tr>';
		}
		if($public) {
			echo '
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->notesPublic->getFormatted() . '
					</td>
				</tr>';
		}
		else {
			echo '
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
						<h2>Institution:</h2>
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
						$this->institution->name->getFormatted();
		}
		else {
			echo '
						<a href="../institutions/view.php?id=' . $this->institution->getID() . '">' . $this->institution->name->getFormatted() . '</a>';
		}
		echo '
					</td>
				</tr>
				<tr>
					<td>
						<b>Address:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->address->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>City:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->city->getFormatted() . '
					</td>
				</tr>		
				<tr>
					<td>
						<b>Zip Code:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->zip->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Background Check Required:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->bg->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Active:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->active->getFormatted() . '
					</td>
				</tr>';
		if ($public) {
			echo '
				<tr>
					<td>
						<b>Notes:</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->notesPublic->getFormatted() . '
					</td>
				</tr>';
		}
		else {
			echo '
				<tr>
					<td>
						<b>Public Notes (printed):</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->notesPublic->getFormatted() . '
					</td>
				</tr>
				<tr>
					<td>
						<b>Private Notes (not printed):</b>
					</td>
					<td width="20">
					<td>
						' . $this->institution->notesPrivate->getFormatted() . '
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
						<h2>Sponsor:</h2>
					</td>
				</tr>
				<tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>';
		if ($public) {
					echo $this->sponsor->getName()->getFormatted();
		}
		else {
			echo '
						<a href="../people/view.php?id=' . $this->sponsor->getID() . '">' . $this->sponsor->getName()->getFormatted() . $this->sponsor->getInitial()->getFormatted() . '</a>';
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
						' . $this->sponsor->getPhone()->getFormatted() . '
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
						' . $this->sponsor->getNotes()->getFormatted() . '
					</td>
				</tr>';
		}
		echo '
				<tr>
					<td colspan="3">
						&nbsp
					</td>
				</tr>';
		// only display if rep is present:
		if (is_null($this->cosponsor->id)) {
			echo '
				<tr>
					<td colspan="3">
						<h2>Needs Co-Sponsor</h2>
					</td>
				</tr>';
		}
		else {
			echo '
				<tr>
					<td colspan="3">
						<h2>Co-Sponsor:</h2>
					</td>
				</tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>';
			if ($public) {
				echo
					$this->cosponsor->getName()->getFormatted();
			}
			else {
				echo '
					<a href="../people/view.php?id=' . $this->cosponsor->getID() . '">' . $this->cosponsor->getName()->getFormatted() . $this->cosponsor->getInitial()->getFormatted() . '</a>';
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
						' . $this->cosponsor->getPhone()->getFormatted() . '
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
						' . $this->cosponsor->getNotes()->getFormatted() . '
					</td>
				</tr>';
			}
		}
		// Second rep display

		// only display if rep is present:
		if (!is_null($this->cosponsor2->id)) {
			echo '
					<tr>
					<td colspan="3">
						&nbsp
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<h2>Co-Sponsor #2:</h2>
					</td>
				</tr>
					<td>
						<b>Name:</b>
					</td>
					<td width="20">
					<td>';
			if ($public) {
				echo
					$this->cosponsor2->getName()->getFormatted();
			}
			else {
				echo '
					<a href="../people/view.php?id=' . $this->cosponsor2->getID() . '">' . $this->cosponsor2->getName()->getFormatted() . $this->cosponsor2->getInitial()->getFormatted() . '</a>';	
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
						' . $this->cosponsor2->getPhone()->getFormatted() . '
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
						' . $this->sponsor->getNotes()->getFormatted() . '
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
			<a class="button" href="../assignments/viewall.php?meeting=' . $this->id . '">Assignments</a>
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
			<title>Institution Committee - View Meetings</title>
		</head>
		<body>';

		// Start Body HTML, define table and table headers
		echo '
			<h1>Institution Meetings</h1>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
			<h2>Click a meeting to view it (including notes)</h2>
			<div id="container">
				<table class="scroll">
					<thead>
						<tr class="header">
							<th>Meeting ID</th>
							<th>Day of Week</th>
							<th>Time</th>
							<th>Institution</th>
							<th>Gender</th>
							<th width="50">Background Checks Required</th>
							<th width="50">Active</th>
							<th>Sponsor</th>
							<th>Phone Number</th>
							<th>Co-Sponsor</th>
							<th>Phone Number</th>
							<th>Second Co-Sponsor</th>
							<th>Phone Number</th>						
						</tr>
					</thead>
					<tbody>';

		// For each meeting (note the title tag allows for mouse-over information)...
		for ($i=0; $i<$this->total; $i++) {
			$this->institutions[$i]->view();
			$this->sponsors[$i]->view();
			$this->cosponsors[$i]->view();
			$this->cosponsor2s[$i]->view();
			if ($this->actives[$i]->getValue()) {
				echo '
						<tr class="row" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			else {
				echo '
						<tr class="row inactive" onclick="window.document.location=\'view.php?id=' . $this->ids[$i] . '\';">';
			}
			echo '
							<td nowrap>' . $this->displayIDs[$i]->getFormatted() .'</td>
							<td nowrap>' . $this->dows[$i]->getFormatted() .'</td>
							<td nowrap>' . $this->mtimes[$i]->getFormatted() . '</td>					
							<td nowrap><a href="../institutions/view.php?id=' . $this->institutions[$i]->getID() . '">' . $this->institutions[$i]->name->getFormatted() . '</a></td>
							<td nowrap>' . $this->genders[$i]->getFormatted() . '</td>
							<td nowrap>' . ($this->institutions[$i]->bg->getValue() ? 'BG check req.' : '-') . '</td>
							<td nowrap>' . $this->actives[$i]->getFormatted() . '</td>
							<td nowrap>' . (is_null($this->sponsors[$i]->getID()) ? '<i>Needs Spons.</i>' : '<a href="../people/view.php?id=' . $this->sponsors[$i]->getID() . '">' . 
								$this->sponsors[$i]->getName()->getFormatted() . $this->sponsors[$i]->getInitial()->getFormatted()) . '</a></td>
							<td nowrap>' . (is_null($this->sponsors[$i]->getID()) ? '-' : $this->sponsors[$i]->getPhone()->getFormatted()) . '</td>
							<td nowrap>' . (is_null($this->cosponsors[$i]->getID()) ? '<i>Needs Co-Spons.</i>' : '<a href="../people/view.php?id=' . $this->cosponsors[$i]->getID() . '">' . 
								$this->cosponsors[$i]->getName()->getFormatted() . $this->cosponsors[$i]->getInitial()->getFormatted()) . '</a></td>
							<td nowrap>' . (is_null($this->cosponsors[$i]->getID()) ? '-' : $this->cosponsors[$i]->getPhone()->getFormatted()) . '</td>
							<td nowrap>' . (is_null($this->cosponsor2s[$i]->getID()) ? '-' : '<a href="../people/view.php?id=' . $this->cosponsor2s[$i]->getID() . '">' . 
								$this->cosponsor2s[$i]->getName()->getFormatted() . $this->cosponsor2s[$i]->getInitial()->getFormatted()) . '</a></td>
							<td nowrap>' . (is_null($this->cosponsor2s[$i]->getID()) ? '-' : $this->cosponsor2s[$i]->getPhone()->getFormatted()) . '</td>						
						</tr>';
		}

		echo '
					</tbody>
				</table>
			</div>
			<h2>Click a meeting to view it (including notes)</h2>
			<a class="button" href="../">Home</a>
			<a class="button" href="form.php">Add New</a>
		</body>
		</html>';
	}
}

?>
