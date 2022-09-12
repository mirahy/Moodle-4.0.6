<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = getenv('DB_TYPE');
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('DB_HOST');
$CFG->dbname    = getenv('DB_NAME');
$CFG->dbuser    = getenv('DB_USER');
$CFG->dbpass    = getenv('DB_PASSWORD');
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

if (getenv('APP_ENV') && getenv('APP_ENV') == "production") {
  $CFG->wwwroot   = 'https://' . $_SERVER['HTTP_HOST'];
  $CFG->sslproxy = 1;
} else {
  $CFG->wwwroot   = 'http://' . $_SERVER['HTTP_HOST'];
}

$CFG->dataroot  = '/var/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

//Configurações para upgrade de http em https
header("Upgrade-Insecure-Requests: 1");
header("Content-Security-Policy: upgrade-insecure-requests;");

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
