#!/usr/bin/php
<?php
require('config.php');

$xml = simplexml_load_file('http://'.$config['host'].':'.$config['port'].'/Dvr/GetRecordedList');

if (!is_dir($config['target_dir'])) {
  mkdir($config['target_dir']) or die("ERROR: The target_dir specified in config doesn't exist, and I cannot create it.\n");
}
chdir($config['target_dir']);

// First, delete everything!
// @TODO: more incremental management of the links.
exec("rm -rf ".$config['target_dir']."*");

// Iterate through all the recordings the API gives us.
$unique_episode_counter = ($config['unknown_episode_start'] ? $config['unknown_episode_start'] : 1);
foreach($xml->Programs->Program as $program) {
  // Find the filename of the program.
  $target = $config['recordings_dir'].$program->FileName;

  // Correct for S0E0 conflicts so they each get a unique episode number. Otherwise even with separate titles,
  // Plex will glom them together. 
  if ($program->Season == 0) $program->Season = ($config['unknown_season'] ? $config['unknown_season'] : 100);
  if ($program->Episode == 0) {
    $program->Episode = $unique_episode_counter++;
  }

  // Calculate the name of the symlink to be created.
  $link = $program->Title.'/Season '.$program->Season.'/'.
          $program->Title. ' - S'.$program->Season.'E'.$program->Episode.' - ' . $program->SubTitle.'.mpg';

  if (!is_file($target)) {
    // This is weird because it means the API gave us a program with no file.
    trigger_error("$target does not exist ($program->Title S $program->Season E $program->Episode)", E_USER_WARNING);
    continue;
  }

  // Get the recording date.
  $recordingStarted = array_shift(explode('T', $program->Recording->StartTs));

  // Make the Program folder if it doesn't exist.
  if (!is_dir($program->Title)) {
  	mkdir($program->Title);
  }
  // Inside the program folder, make the Season folder if it doesn't exist.
  if (!is_dir($program->Title.'/Season '.$program->Season)) {
        mkdir($program->Title.'/Season '.$program->Season);
  }
  // DEBUG: symlink what to what.
  if ($config['debug']) {
  	echo "Symlinking $target to $link\n";
  }

  // If the symlink doesn't already exist, create it.
  if (!is_link($link)) {
    symlink($target, $link);
  }
  // If it does exist, there may be a collision from bad info (S00E00, etc),
  // So make the filename using some uniq id as a fallback, so it still shows up.
  else {
    symlink($target, str_replace('.mpg', uniqid().'.mpg', $link));
  }
}

