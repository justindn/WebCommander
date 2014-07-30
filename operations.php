<?php
ini_set("max_execution_time","0");
header("Expires: Mon, 01 Jul 1997 05:00:00 GMT");
header("Last-Modified: Mon, 01 Jul 1997 05:00:00 GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'config.php';

$files_list = array();
$dir_list = array();

function getExtension($filename){
	if ((substr($filename, 0, 1) == '.') && (substr_count($filename, '.') == 1)){
		return '';
	}
	$expl = explode('.', $filename);
	if (count($expl) >1){
		return array_pop($expl);
	}
	else{
		return '';
	}
}


switch ($_GET['action']){
		case 'getfile':
			if (filesize($_GET['folder']) == 0){
				echo '';
				return;
			}
			$handle = fopen ($_GET['folder'], 'r');
			$file = fread($handle, filesize($_GET['folder']));
			fclose($handle);
			
			switch (strtolower(getExtension($_GET['folder']))){
				case 'jpg':
				case 'jpeg':
					header ('Content-type:image/jpeg');
					echo $file;
					break;
				case 'png':
					header ('Content-type:image/jpeg');
					echo $file;
					break;
				case 'gif':
					header ('Content-type:image/gif');
					echo $file;
					break;
				case 'bmp':
					header ('Content-type:image/bmp');
					echo $file;
					break;
				default:
					header('Content-Type: text/html; charset= utf-8'); 
					$file = iconv('UTF-8', mb_detect_encoding (file_get_contents($_GET['folder'])), $file);
					echo '<pre>' . htmlentities($file, ENT_QUOTES, 'UTF-8') . '</pre>';
			}
}

switch ($_POST['action']){
	case 'ren':
		echo rename ($_POST['path'] . SEP . $_POST['oldname'], $_POST['path'] . SEP . $_POST['newname']);

		break;
	case 'del':
		$path = $_POST['filename'];
		if (!is_dir($path)){
			if(!@unlink($path)){
				$file['message'] = 'Cannot delete file. It may be because you have not enough rights or file does not exists';
				echo json_encode($file);
			}
			else{
				echo json_encode('1');
			}
		}
		else{
			if (!@rmdir($path)){
				$file['message'] = 'Directory not empty';
				echo json_encode($file);
			}
			else{
				echo json_encode('1');
			}
		}
		break;
	case 'newfolder':
		$path = $_POST['path'] . SEP . $_POST['filename'];
		if (!mkdir($path)){
			$file['message'] = 'Cannot create directory';
			echo json_encode($file);
		}
		else{
			echo json_encode('1');
		}
		break;
	case 'calcsize':
		$dir_list = array();
		$files_list = array();
		$total = 0;
		getFullFolderFilesList($_POST['path']);
		foreach ($files_list as $item){
			$total += filesize($item);
		}
		echo $total;
		break;
	
	default:
		break;

}



function getFullFolderFilesList($folder='.'){
	global $files_list;
	global $dir_list;
	$folder_content = scandir($folder);
	
		foreach($folder_content as $item){
		if ($item != '.' && $item != '..'){
			$item = realpath($folder.SEP.$item);
			
			if (is_dir($item)){
				$dir_list[] = $item;
				getFullFolderFilesList(realpath($item));
			}
			else{
				$files_list[] = $item;
			}
		}
	}
	return array_merge($files_list, $dir_list);
}
//file_put_contents('errors.txt',$php_errormsg . "\n". $path);
?>