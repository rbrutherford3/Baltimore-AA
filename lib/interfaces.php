<?php
/* 
Interfaces

Dump for all the interfaces, which I used sparingly (probably should have used them more) that bind
the classes to certain public functions.

 */
 
// Defines interface for all the primitive objects (see primitives.php).  It makes sure that each
// primitive, which is extended by 'datatypes' has a getValue and getFormatted function.  They are
// how they sound: the getValue returns the raw data value whereas getFormatted is for output
interface primitive {

	function __construct();

	public function getValue();

	public function getFormatted();

}

// This is strictly for the datatypes classes and makes sure that there is HTML code for input controls
// such as radio buttons, etc., and associated labels for each of these input objects.  Note the arguments
// for each function.  idBase: root string for HTMNL element ids.  namBase: likewise with names.  label:
// output label for the element
interface HTML {

	public function labelHTML($idBase, $label);

	public function inputHTML($idBase, $nameBase, $enabled);

	//public function outputHTML();

}

// day of the week has different HTML based on whether multiple days of the week are allowed or not.  For instance:
// an institution meeting can only meet one day of the week, whereas a group could meet every day if they wanted.
interface dowHTML {

	public function labelHTMLpulldown($idBase, $label);

	public function inputHTMLpulldown($idBase, $nameBase, $enabled);

	public function inputHTMLcheckbox($idBase, $nameBase, $enabled);

	//public function outputHTML();

}

?>