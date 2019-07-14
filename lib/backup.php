<?php

// Check to see if there is a second "dummy" database connection file
if (file_exists('dbconnect2.php')) {
	$dummyDB = true;
}
else  {
	$dummyDB = false;
}

// Connect to the one, possibly two, databases
include_once 'dbconnect.php';
if ($dummyDB) {
	include_once 'dbconnect2.php';
}

// Call functions...

//put table names you want backed up in this array.
//leave empty to do all
$tables = array();
$sqlDump = backup_tables($db, $tables);
if ($dummyDB) {
	mirrorDB($db2, $sqlDump);
}
saveFile($sqlDump);

// Function to create sql dump string from a database connection
function backup_tables($DBH, $tables) {

	$DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL );

	//Script Variables
	$compression = false;
	$BACKUP_PATH = "";
	$nowtimename = time();
	$output = '';

	//create/open files
	/* if ($compression) {
	$zp = gzopen($BACKUP_PATH.$nowtimename.'.sql.gz', "a9");
	} else {
	$handle = fopen($BACKUP_PATH.$nowtimename.'.sql','a+');
	} */

	//array of all database field types which just take numbers 
	$numtypes=array('tinyint','smallint','mediumint','int','bigint','float','double','decimal','real');

	//get all of the tables
	if(empty($tables)) {
		$pstm1 = $DBH->query('SHOW TABLES');
		while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
			$tables[] = $row[0];
		}
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through the table(s)

	foreach($tables as $table) {
		$result = $DBH->query("SELECT * FROM $table");
		$num_fields = $result->columnCount();
		$num_rows = $result->rowCount();

		$return="";
		//uncomment below if you want 'DROP TABLE IF EXISTS' displayed
		//$return.= 'DROP TABLE IF EXISTS `'.$table.'`;'; 

		//table structure
		$pstm2 = $DBH->query("SHOW CREATE TABLE $table");
		$row2 = $pstm2->fetch(PDO::FETCH_NUM);
		$ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
		$return.= "\n\n".$ifnotexists.";\n\n";

		$output.=$return;
		/* if ($compression) {
		gzwrite($zp, $return);
		} else {
		fwrite($handle,$return);
		} */
		$return = "";

		//insert values
		if ($num_rows){
			$return= 'INSERT INTO `'."$table"."` (";
			$pstm3 = $DBH->query("SHOW COLUMNS FROM $table");
			$count = 0;
			$type = array();

			while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {

				if (stripos($rows[1], '(')) {
					$type[$table][] = stristr($rows[1], '(', true);
				} 
				else {
					$type[$table][] = $rows[1];
				}

				$return.= "`".$rows[0]."`";
				$count++;
				if ($count < ($pstm3->rowCount())) {
					$return.= ", ";
				}
			}

			$return.= ")".' VALUES';

			$output.=$return;
			/* if ($compression) {
			gzwrite($zp, $return);
			} else {
			fwrite($handle,$return);
			} */
			$return = "";
		}
		$count =0;
		while($row = $result->fetch(PDO::FETCH_NUM)) {
			$return= "\n\t(";

			for($j=0; $j<$num_fields; $j++) {

				//$row[$j] = preg_replace("\n","\\n",$row[$j]);

				if (isset($row[$j])) {

					//if number, take away "". else leave as string
					if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
						$return.= $row[$j]; 
					}
					else {
						$return.= $DBH->quote($row[$j]);
					}

				} 
				else {
					$return.= 'NULL';
				}
				if ($j<($num_fields-1)) {
					$return.= ',';
				}
			}
			$count++;
			if ($count < ($result->rowCount())) {
				$return.= "),";
			} 
			else {
				$return.= ");";
			}

			$output.=$return;
			/* if ($compression) {
			gzwrite($zp, $return);
			} else {
			fwrite($handle,$return);
			} */
			$return = "";
		}
		$return="\n\n-- ------------------------------------------------ \n\n";
		$output.=$return;
		/* if ($compression) {
		gzwrite($zp, $return);
		} else {
		fwrite($handle,$return);
		} */
		$return = "";
	}

	$error1= $pstm2->errorInfo();
	$error2= $pstm3->errorInfo();
	$error3= $result->errorInfo();
	echo $error1[2];
	echo $error2[2];
	echo $error3[2];

	/* if ($compression) {
	gzclose($zp);
	} else {
	fclose($handle);
	} */
	return $output;
}

// Mirror database into 'dummy' database, modifying personal information only
function mirrorDB($db, $sqlDump) {

	$sqlList = "SHOW TABLES;";
	 
	//Prepare our SQL statement,
	$stmtList = $db->prepare($sqlList);
	 
	//Execute the statement.
	$stmtList->execute();
	 
	//Fetch the rows from our statement.
	$tables = $stmtList->fetchAll(PDO::FETCH_NUM);
	 
	//Loop through our table names.
	foreach($tables as $table){
		//echo '<script>alert("' . $table . '");</script>?';
		//Print the table name out onto the page.
		$sqlDrops[] =  "DROP TABLE IF EXISTS " . $table[0] . ";";
	}
	
	// Execute table drops
	foreach($sqlDrops as $sqlDrop) {
		$stmtDrop = $db->prepare($sqlDrop);
		$stmtDrop->execute();
		$stmtDrop->closeCursor();
	}

	// Execute sql dump, effectively copying information into dummy databse
	$sqlTransfer = $sqlDump;
	$stmtTransfer = $db->prepare($sqlTransfer);
	$stmtTransfer->execute();
	$stmtTransfer->closeCursor();
	
	// Censor personal information	
	$sqlPeoples[] = "UPDATE `people` SET `Name`='John' WHERE `ID`%2=0;";
	$sqlPeoples[] = "UPDATE `people` SET `Name`='Jane' WHERE `ID`%2=1;";
	$sqlPeoples[] = "UPDATE `people` SET `Initial`='D';";
	$sqlPeoples[] = "UPDATE `people` SET `Phone`=4105555555;";
	$sqlPeoples[] = "UPDATE `people` SET `Notes`=NULL;";
	
	// Execute censoring
	foreach($sqlPeoples as $sqlPeople) {
		$stmtPeople = $db->prepare($sqlPeople);
		$stmtPeople->execute();
		$stmtPeople->closeCursor();
	}
}

// Save output file
function saveFile($output) {
	date_default_timezone_set('US/Eastern');
	$timeStamp = date('YmdHis');
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="Balt AA Inst Comm Mtng Mngr -  MySQL Dump ' . $timeStamp . '.sql"');
	echo($output);
}

?>