<?php
define ('SEP', "\\");

file_put_contents('text.txt', serialize($_POST));
switch ($_POST['action']){
	case 'ren':
		echo rename ($_POST['path'] . SEP . $_POST['oldname'], $_POST['path'] . SEP . $_POST['newname']);
		//file_put_contents('text.txt', file_exists($_POST['path'] . SEP . $_POST['oldname']));
		break;
	case 'del':
		$path = $_POST['path'] . SEP . $_POST['filename'];
		if (!is_dir($path)){
			echo unlink ($path);
		}
		else{
			echo rmdir ($path);
		}
	default:
		break;

}
file_put_contents('errors.txt',$php_errormsg . "\n". $path);
?>