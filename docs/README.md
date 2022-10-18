# Baltimore Alcoholics Anonymous (AA) Institution Committee Meeting Assignment Tool

[![standard-readme compliant](https://img.shields.io/badge/readme%20style-standard-brightgreen.svg?style=flat-square)](https://github.com/RichardLitt/standard-readme)

This is a PHP/MySQL database management system for the Baltimore AA Institution Commmittee to maintain records of:
- Institutions that allow AA meetings to be brought into their facility on a weekly basis
- Details of the weekly meetings
- AA groups in the area participating in this program that are willing to bring such meetings in once a month
- AA members that serve as contacts and organizers

There is also a tool for optionally matching AA groups to the institution meetings automatically, with the ability to manually edit such assignments after the fact.

See the [background section](#background) for a detailed description of the solution.  See [https://baltaa.spiffindustries.com/](https://baltaa.spiffindustries.com/) for a working demo.

## Table of Contents

- [Security](#security)
- [Background](#background)
- [Install](#install)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Security

Out of respect of the privacy and anonymity of AA members, no names or phone numbers from the original database are provided in the project files.  Regarding security, the PDO PHP object was used to prevent SQL injection, and a Google Recaptcha v3 was incorporated to prevent non-human interaction with the site.  `.htaccess` and `.htpasswd` files were used during the life of the tool to prevent unauthorized access.  The site was taken down after the responsibility was passed on to prevent the possibility of a future data breach.

Following the tradition of anonymity, the last names of members are omitted and their last initials used instead.  No personal data is provided in this repository or is available online at all anymore.

## Background

The Baltimore [Alcoholics Anonymous (AA)](https://www.aa.org/what-is-aa) Institution Committee brings AA meetings into hospitals and institutions (jails, drug/alcohol treatment centers) throughout the greater Baltimore area.  To do so, local AA groups provide one or two members to facilitate the weekly meetings on a monthly basis.  Such scheduling has proven difficult and time consuming, especially when restrictions such as gender and availability are considered.

This PHP/MySQL tool attempts to alleviate the pressure of manually assigning local AA groups to institution meetings.  It is basically a database management tool with the extra capability of making random assignments that fit the following criteria:
- Gender restrictions are respected (i.e., male only institutions would not be assigned an all-women's A.A. group)
- AA groups that object to submitting personal information would not be considered for institutions that require background checks, such as jails
- AA groups would not be assigned an institution meeting on the same night their own group meets
- AA groups would not be assinged the same institution meeting within the span of a year, if possible, to maintain variety

The tool was used for over two years before being retired when the torch was passed on to another AA member to maintain the institution meeting schedule.

## Install

Summary:
1. Server requirements:
    - PHP
    - MySQL or MariaDB
    - PHP-FPM (to interact with the MySQL database)
1. Clone this project into a folder on the server
1. Set up the MySQL database
1. Create `lib/credentials.php` and `lib/credentialsview.php` files
1. Create a `lib/credentialsrecaptchav3.php` file to prevent non-human interactions with the site
1. Password protect the site using `.htaccess` and `.htpasswd` files (optional and not discussed)

Because of the wide variety of options for servers, the instructions will begin with the assumption that the user has already has access to a server of their choice.  The PHP server must have the PDO object available for the site to function.  The remainder of the instructions assume a Linux system and root access.

Clone the project:
```
cd /path/to/site/root
git clone https://github.com/rbrutherford3/Baltimore-AA.git
```
Set up the database:
```
mysql < setup.sql
```
If necessary, set up a database user to interact with the database and, optionally, a read-only user.  Such a setup might look like the following SQL statements:
```
CREATE USER baltaa_viewer@localhost IDENTIFIED BY 'password1';
GRANT SELECT ON baltaa.* TO baltaa_viewer@localhost;
CREATE USER baltaa_admin@localhost IDENTIFIED BY 'password2';
GRANT ALL PRIVILEGES ON baltaa.* TO baltaa_admin@localhost;
FLUSH PRIVILEGES;
```

Create a new file `credentials.php` in the `lib` directory of the project root:
```
<?php

function credentials() {
	$host = 'localhost';
	$database = 'baltaa';
	$username = 'username';
	$password = 'password';
	return array('HOST' => $host, 'DATABASE' => $database, 'USERNAME' => $username, 'PASSWORD' => $password);
}

?>
```
Replace the username and password fields with the appropriate values.

If you wish to have a separate user that has read-only access to the database, then repeat the previous instructions and create a separate `credentialsview.php` file in the `lib` directory and replace the username and password fields with a read-only user.  If this is not an issue, simply copy `credentials.php` to `credentialsview.php`.  Either way, both files must exist for the site to work properly and the credentials must exist in the MySQL database.  To better understand the relational database, an [Entity Relationship Diagram (ERD)](ERD.pdf) has been provided. 

Finally, you can create a `credentialsrecaptchav3.php` in the `lib` directory that looks like:

```
<?php
	const RECAPTCHA_SITE_KEY_V3="03JDlkSD09CIKE,Kkd04klsK9FL4L0kldle0";
	const RECAPTCHA_SECRET_KEY_V3="IMkd94mh9JJdf9wkk(JMfkl54r0";
?>
```
Replace the values of the keys with your own values (these above keys will not work).  See [https://developers.google.com/recaptcha/docs/v3](https://developers.google.com/recaptcha/docs/v3) for more information.

## Usage

Visiting the installed site reveals three roles:

- Group Representative
- Institution Meeting Sponsor
- Institution Committee Scheduler 

The first two roles are the AA group representatives that are assigned to a single institution meeting for the month, or the AA members that facilitate the meetings in the institution and host multiple AA groups that month.  These users should have read-only access to the database and the links are for viewing, not editing.

The third role is the AA member who creates the schedule and is the core of the program.  That scheduler can manage the information in the database, which consists of:

- AA groups
- AA members who represent the groups
- Institutions that host AA meetings
- AA meetings hosted at these institutions
- AA members who sponsor those meetings

The meat of the program is the creation of meeting assignments, where the scheduler can automatically or manually match AA groups to AA meetings at institutions each month.  Automatic assignments can also be edited.  The assignments are based on the following constraints:

- **Gender:** assigning men to host meetings at a male-only institution, etc
- **Background check:** AA groups that oppose background checks aren't assigned to meetings at institutions that require them
- **Day of week:** an AA meeting that meets on a Wednesday is not assigned to an institution meeting that also meets that day
- **Variety:** Making sure that the same AA group is not assigned to the same institution meeting repeatedly

Other factors play into scheduling that are beyond the scope of this document.  The scheduler can export the schedule to a `.tsv` (tab-separated value) file or download the entire database in `.sql` file.  Historically, the `.tsv` file was used to import data into a formatted Excel file for printing and distribution.

## Contributing

This is an archived project.  No further contribution will be made.  A demonstration is available at [https://baltaa.spiffindustries.com/](https://baltaa.spiffindustries.com/).

## License

[MIT © Robert Rutherford](../LICENSE)
