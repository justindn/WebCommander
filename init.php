<?php
header('content-type:text/html;charset=utf-8');
include ('config.php');
$folder = dirname(__FILE__);
$files['folder'] = $folder;
echo json_encode($files);

?>