<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysql';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '10.2.74.50';
$CFG->dbname    = 'bdCapVirtual';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'n1md0gm11i3p5';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://10.2.74.100/aulavirtualieps';
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!