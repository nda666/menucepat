<?php

exec('chmod 777 ' . __DIR__ . ' -R', $output);
var_dump($output);
echo "Done\n";

define('_PATH', dirname(__FILE__));
$filename = _PATH . '/app.zip';
exec('unzip -o  ' . $filename . ' -d ' . _PATH, $output, $result);
echo 'unzip -o  ' . $filename . ' -d ' . _PATH;
var_dump($output);
echo "Done\n";
var_dump($result);

exec('chmod 777 ' . __DIR__ . ' -R', $output);
var_dump($output);
echo "Done\n";

// $zip = new ZipArchive;
// $res = $zip->open($filename);
// if ($res === TRUE) {
//     $path = _PATH . '/tmp';

//     // var_dump($zip->numFiles);
//     $extract = $zip->extractTo($path);
//     $zip->close();
//     var_dump($extract);
//     var_dump($path);
// }
