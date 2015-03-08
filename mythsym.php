#!/usr/bin/php
<?php
require('config.php');

$xml = simplexml_load_file('http://'.$config['host'].':'.$config['port'].'/Dvr/GetRecordedList');

if (!is_dir($config['target_dir'])) {
  mkdir($config['target_dir']) or die("ERROR: The target_dir specified in config doesn't exist, and I cannot create it.\n");
}
chdir($config['target_dir']);

// First, delete everything!
exec("rm -rf ".$config['target_dir']."*");

foreach($xml->Programs->Program as $program) {
  $target = $config['recordings_dir'].$program->FileName;
  $link = $program->Title.'/'.$program->SubTitle.'.mpg';
  if (!is_dir($program->Title)) {
  	mkdir($program->Title);
  }
  if ($config['debug']) {
  	echo "Symlinking $target to $link\n";
  }

  symlink($target, $link);
}
