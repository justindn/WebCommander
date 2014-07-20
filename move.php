<?php
//include 'config.php';
define ('SEP', "\\");
if (file_exists($_POST['to'])){
	echo '2';
	return;
}
echo copy ($_POST['from'], $_POST['to']);
unlink ($_POST['from']);
//file_put_contents('1.txt', serialize($_POST));

?>