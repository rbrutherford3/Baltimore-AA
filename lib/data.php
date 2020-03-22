<?php
/* 
Data class

Core data object.  This object is extended by all the 'persons', 'group', and all the other
objects that require a database connection.  It is totally scalable and the number of arguments
determines the nature of the object.  It is the only object (other than the classes that extend
it) that connects with the database, which it accepts as an argument only.  All inputs and outputs
are handled by the extension classes.

 */

class data {
	// All of these members are defined by classes extending data class or in the constructor
	protected $db;					// database
	protected $table;				// table name
	protected $idField;				// sql field
	protected $id;					// sql value
	protected $idParamType = PDO::PARAM_INT;	// type of pdo parameter that the ID field is
	protected $fields;				// fields of the database
	protected $fieldTypes = array(PDO::PARAM_INT, PDO::PARAM_STR);	// pdo data type of each field
	protected $lookupFields = array();	// the fields used for finding an equivelant entry
	protected $sortFields = array();	// the fields used for sorting when querying all entries
	protected $params = array();	// paramaters, or arguments
	protected $numParams;			// number of parameters
	protected $total=0;				// count of rows for querying entire table

	function __construct() {
		date_default_timezone_set('America/New_York');	// Set time zone because Ampps requires it
		if (func_num_args() == 1) {		// db pdo only object -> initialize empty members
			$this->db = func_get_arg(0);
			$this->id = null;
			$this->parseInput(null);
		}
		else if (func_num_args() == 2) { // db and id are only arguments (seeking to query info)
			$this->db = func_get_arg(0);
			$this->id = func_get_arg(1);
			if (is_null($this->id)) {
				$this->parseInput(null);
			}
		// parameters passed without id (seeking to insert record)
		}
		else if (func_num_args() == ($this->numParams + 1)) {
			$this->db = func_get_arg(0);
			$this->parseInput(array_slice(func_get_args(), 1, $this->numParams));
			$this->id = null;
		}
		// parameters passed with id (seeking to update record)
		else if (func_num_args() == ($this->numParams + 2)) {
			$this->db = func_get_arg(0);
			$this->parseInput(array_slice(func_get_args(), 1, $this->numParams));
			$this->id = func_get_arg($this->numParams + 1);
		}
		else {
			die('Invalid number of arguments (' . func_num_args() . ') for data class!');
		}
	}

	protected function parseInput($inputs) {
		// empty function will be invoked by extending classes
	}

	protected function parseOutput() {
		// empty function will be invoked by extending classes
	}

	// Check to see if an entry exists matching the given set parameters
	// (i.e., don't insert a record if another with the same info already exists)
	public function checkExists() {
		try {
			// Build SQL statement
			$sql = "SELECT " . $this->idField . " FROM " . $this->table . " WHERE ";
			for ($i=0; $i<(count($this->lookupFields)-1); $i++) {
				$sql = $sql . $this->fields[$this->lookupFields[$i]] . "=:" . $this->fields[$this->lookupFields[$i]] . " AND ";
			}										// ^ lookup fields originated by extension class determine which fields can yield a "match"
			$sql = $sql . $this->fields[$this->lookupFields[count($this->lookupFields)-1]] . "=:" . $this->fields[$this->lookupFields[count($this->lookupFields)-1]] . ";";
			$stmt = $this->db->prepare($sql);
			// Bind values
			for ($i=0; $i<count($this->lookupFields); $i++) {
				$stmt->bindValue(':' . $this->fields[$this->lookupFields[$i]], $this->params[$this->lookupFields[$i]], $this->fieldTypes[$this->lookupFields[$i]]);
			}
			$stmt->execute();	// Run query
			// If there is a result, return true and grab the id of the matching record
			if ($row = $stmt->fetch()) {
				$this->id = $row[$this->idField];
				return true;
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			die('Attempting to check for ' . $this->table . ' record failed:<br>' . $e);
		}
	}
	// Check to see if an entry exists matching the given set parameters UNLESS it has the given ID
	// (i.e.: don't modify a record to match an existing one)
	public function checkExistsExcluding() {
		try {
			// Build SQL statement
			$sql = "SELECT " . $this->idField . " FROM " . $this->table . " WHERE ";
			for ($i=0; $i<count($this->lookupFields); $i++) {
				$sql = $sql . $this->fields[$this->lookupFields[$i]] . "=:" . $this->fields[$this->lookupFields[$i]] . " AND ";
			}	// ^ lookup fields originated by extension class determine which fields can yield a "match"
			$sql = $sql . $this->idField . "<>:" . $this->idField . ";";
			$stmt = $this->db->prepare($sql);
			// Bind values
			for ($i=0; $i<count($this->lookupFields); $i++) {
				$stmt->bindValue(':' . $this->fields[$this->lookupFields[$i]], $this->params[$this->lookupFields[$i]], $this->fieldTypes[$this->lookupFields[$i]]);
			}
			$stmt->bindValue(':' . $this->idField, $this->id, $this->idParamType);
			$stmt->execute();	// Run query
			// If there is a result, return true and grab the id of the matching record
			if ($row = $stmt->fetch()) {
				$this->id = $row[$this->idField];
				return true;
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			die('Attempting to check for ' . $this->table . ' record failed:<br>' . $e);
		}
	}
	// Insert a new record with the given parameters
	public function insert() {
		try {
			// Build SQL statement
			$sql = "INSERT INTO " . $this->table ." (";
			for ($i=0; $i<(count($this->fields)-1); $i++) {
				$sql = $sql . $this->fields[$i] . ", ";
			}
			$sql = $sql . $this->fields[count($this->fields)-1] . ") VALUES (";
			for ($i=0; $i<(count($this->fields)-1); $i++) {
				$sql = $sql . ":" . $this->fields[$i] . ", ";
			}
			$sql = $sql . ":" . $this->fields[count($this->fields)-1] . ");";
			$stmt = $this->db->prepare($sql);
			// Bind values
			for ($i=0; $i<count($this->fields); $i++) {
				$stmt->bindValue(":" . $this->fields[$i], $this->params[$i], $this->fieldTypes[$i]);
			}
			$stmt->execute();	// Run query

			$this->id = $this->db->lastInsertId();	// Grab the ID of the last inserted record and save it!
		}
		catch(PDOException $e) {
			die('Attempting to insert ' . $this->table . ' record failed:<br>' . $e);
		}
	}
	// Update an existing record with the given parameters and given ID
	public function update() {
		try {
			// Build SQL statement
			$sql = "UPDATE " . $this->table . " SET ";
			for ($i=0; $i<(count($this->fields)-1); $i++) {
				$sql = $sql . $this->fields[$i] . "=:" . $this->fields[$i] . ", ";
			}
			$sql = $sql . $this->fields[count($this->fields)-1] . "=:" . $this->fields[count($this->fields)-1] . " WHERE " . $this->idField . "=:" . $this->idField . ";";
			$stmt = $this->db->prepare($sql);
			// Bind values
			for ($i=0; $i<count($this->fields); $i++) {
				$stmt->bindValue(":" . $this->fields[$i], $this->params[$i], $this->fieldTypes[$i]);
			}
			$stmt->bindValue(":" . $this->idField, $this->id, $this->idParamType);
			$stmt->execute();	// Run query
		}
		catch(PDOException $e) {
			die('Attempting to update ' . $this->table . ' record failed:<br>' . $e->getMessage());
		}
	}
	// Query table with given ID
	public function view() {
		try {
			// Build SQL statement
			$stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE " . $this->idField . "=:" . $this->idField . ";");
			$stmt->bindValue(":" . $this->idField, $this->id, $this->idParamType);
			$stmt->execute();	// Run query
			if ($row = $stmt->fetch()) {	// Only grab first record (one would hope there's only one...)
				// Given the defined fields used, grab the data
				for ($i=0; $i<count($this->fields); $i++) {
					$this->params[$i] = $row[$this->fields[$i]];
				}
				$this->parseOutput();		// Parse the output (in the extension class)
				return true;
			}
			else {
				return false;				// Return false if no records found
			}
		}
		catch(PDOException $e) {
			die('Attempting to query ' . $this->table . ' record failed:<br>' . $e);
		}
	}
	// Query the entire database and store the results
	public function viewAll() {
		try {
			// Build SQL
			$sql = ("SELECT * FROM " . $this->table . " WHERE 1 ORDER BY ");
			for ($i=0; $i<(count($this->sortFields)-1); $i++) {
				$sql = $sql . $this->fields[$this->sortFields[$i]] . ", ";
			}
			$sql = $sql . $this->fields[$this->sortFields[count($this->sortFields)-1]] . " ASC;";
			$stmt = $this->db->prepare($sql);	// ^ Use predetermined sort fields to sort the query
			$stmt->execute();
			$this->total=0;		// Reset total
			while ($row = $stmt->fetch()) {	// For each row..
				$this->ids[] = $row[$this->idField];	// Grab id and store it in array
				for ($i=0; $i<count($this->fields); $i++) {		// Grab only fields we're interested in
					$this->params[$i] = $row[$this->fields[$i]];
				}
				$this->parseOutputs();	// Send to extension class's parser to store information internally
				$this->total++;
			}
			return ($this->total > 0);	// Return true only if there were results
		}
		catch(PDOException $e) {
			die('Attempting to query ' . $this->table . ' record failed:<br>' . $e);
		}
	}
	// Never used.  Only use if you want to recycle the same instance of data object
	public function setID($id) {
		$this->id = array($id);
	}
	// Return ID
	public function getID() {
		return $this->id;
	}
}

?>