<?php

include_once 'dbconnect.php';

//put table names you want backed up in this array.
//leave empty to do all
$tables = array();

backup_tables($db, $tables);



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

if (stripos($rows[1], '(')) {$type[$table][] = stristr($rows[1], '(', true);
} else $type[$table][] = $rows[1];

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
if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) $return.= $row[$j] ; else $return.= $DBH->quote($row[$j]); 

} else {
$return.= 'NULL';
}
if ($j<($num_fields-1)) {
$return.= ',';
}
}
$count++;
if ($count < ($result->rowCount())) {
$return.= "),";
} else {
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

date_default_timezone_set('US/Eastern');
$timeStamp = date('YmdHis');

echo '<script>alert("' . $timeStamp . '")';

header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="Balt AA Inst Comm Mtng Mngr -  MySQL Dump ' . $timeStamp . '.sql"');
echo($output);

}

?>