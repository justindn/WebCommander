<?php
include 'config.php';
if (file_exists($_POST['to'])){
	echo '2';
	return;
}
echo copy ($_POST['from'], $_POST['to']);
unlink ($_POST['from']);


?>