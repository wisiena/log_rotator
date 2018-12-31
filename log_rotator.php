<?php
/*****************************************************************************
* wisiena.pl (github@wisiena.pl)
* (c) 2014 / 2018
****************************************************************************/

// this module is to be called by cron job
// it is for testing purposes of log rotating function
// it should be independent on includes, all functions and definitions are local
// this file has it's local log file with the same name and .log extension

//-------------------------------------------------------
// related files
// this script creates two files:
// _filename_.log - file with name taken form self nale of file.php.php
// this script requires file with settings
// _filename_settings.php - file which holds settings like $log_path and database credencials

$ll_path = basename(__FILE__, '.php') . ".ll";
$last_line = 'sample last line';  //any string different then regular logfile line
$log_level = 'i';
$script_version = '0.1';


//-------------------------------------------------------
// local functions

// writing local log to text file

function logAddLine ($log_line = 'log function called with empty line', $log_type = 'i')
{
  // log line types:
  // v - verbose (all events)
  // i - information
  // w - warning (warnings and errors)
  // e - error (errors only)
  // if log type is not set, it will be information

  if ($GLOBALS['log_level'] == 'i' && $log_type == 'v') return;
  if ($GLOBALS['log_level'] == 'w' && ($log_type == 'v' || $log_type == 'i')) return;
  if ($GLOBALS['log_level'] == 'e' && ($log_type == 'v' || $log_type == 'i' || $log_type == 'w')) return;

  $log_line = date("M d G:i:s ", time()) . " " . $log_line . "\n";

  // opening file for appending (adding lines)
  // if file don't exist it will be created
  $log_file = fopen(basename(__FILE__, '.php') . ".log", "a");

  // writing log line and closing file
  fwrite($log_file, $log_line);
  fclose($log_file);
}

//-------------------------------------------------------
// script body

// do we have parameter
  if (isset($argv[1]))
  {
    switch ($argv[1])
    {
      case '-v':
        $log_level = 'v';
        logAddLine('log level set to: verbose', 'v');
        break;
      case '-i':
        $log_level = 'i';
        logAddLine('log level set to: information', 'v');
        break;
      case '-w':
        $log_level = 'w';
        logAddLine('log level set to: warnings', 'v');
        break;
      case '-e':
        $log_level = 'e';
        logAddLine('log level set to: errors', 'v');
        break;
      case '-version':
        echo "log_rotator version: " . $script_version . "\n";
        die();
        break;
      case '-h':
        echo "log_rotator help\nacceptable parameters:\n-v - log on verbose level v+i+w+e\n-i - log on information level i+w+e\n-w - log on warning level w+e\n-e - log on error level\n-version - this script version print\n\n";
        die();
        break;
      default:
        logAddLine('unknown log level [' . $argv[1] . '] - set to default [i]', 'w');
        break;
    }
  }

// settings file

$settings_file = basename(__FILE__, '.php') . "_settings.php";

  if (file_exists($settings_file))
  {
    include($settings_file);
    logAddLine('All settings applied from ' . $settings_file, 'i');

    //So here we will check logfile(s) from settings if we have anything to rotate.

  }
  else
  {
    logAddLine('CRITICAL! No configuration file: ' . $settings_file, 'e');
    die();
  }


// EOF
?>
