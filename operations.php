<?php
ini_set("max_execution_time","0");

include 'config.php';

$files_list = array();
$dir_list = array();

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
	case 'getfile':
		//file_put_contents('f.txt', mb_detect_encoding (file_get_contents($_POST['folder'])));
		$file = iconv('UTF-8', mb_detect_encoding (file_get_contents($_POST['folder'])), file_get_contents($_POST['folder']));
		echo htmlspecialchars($file);
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