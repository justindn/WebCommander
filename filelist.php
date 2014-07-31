<?php
header('Content-Type: text/html; charset= utf-8'); 
include './lang/ru.php';
include 'config.php';

$folder = dirname(__FILE__);

$theme_path_files = '.' . SEP . 'images' . SEP . 'themes' . SEP . THEME .  SEP . 'icons' . SEP . 'files' . SEP;
//file_put_contents('test', $theme_path_files . ' ' . SEP);

$folder_icon = $theme_path_files . 'folder.png';

$up_icon = $theme_path_files . 'up.png';

$file_icon = $theme_path_files . 'file.png';


if (isset($_GET['folder']) OR $_GET['folder']!='') {
	$folder = $_GET['folder'];
}

chdir($folder);
$folder .= '/';
$files = getFiles($folder);

echo json_encode($files);

/* Functions */
function getFiles($folder) {
	global $lang;
	global $theme_path_files;
	global $folder_icon;
	global $up_icon;
	global $file_icon;

	clearstatcache();
	
	$list = scandir($folder);
	
	$dir_array = array();
	$files_array = array();
	
	foreach ($list as $filename){

		if ($filename != '.') {
			$file_array = array();
			$file_array['name'] = removeExtension($filename);
			$file_array['fullpath'] = realpath($filename);
			$file_array['datetime'] = date('d.m.Y h:m:s', filemtime($folder . $filename));

			if (is_dir($filename)) {
				
				$file_array['icon'] = $folder_icon;
				$file_array['name'] = $filename;
				if ($filename == '..'){
					
					$file_array['icon'] = $up_icon;
				}
				$file_array['extension'] = '';
				$file_array['size'] = $lang['folder'];
				$file_array['folder'] = 'true';
				$dir_array[] = $file_array;
			} else {
				$file_array['extension'] = getExtension($filename);
				$icon_name = $theme_path_files . $file_array['extension'] . '.png';
				
					if (file_exists(dirname(__FILE__) . $icon_name)) {
						$file_array['icon'] = $icon_name;
					} else {
						$file_array['icon'] = $file_icon;
					}
				
				$file_array['size'] = filesize($folder . $filename);
				$file_array['folder'] = 'false';
				$files_array[] = $file_array;
			}
			
		}
	}
	
	
	$dir_array = array_merge($dir_array, $files_array);
	
	if ($dir_array[0]['name'] != '..' ){
		for ($i=0; $i<count($dir_array); $i++){
			if ($dir_array[$i]['name'] == '..'){
				list($dir_array[0], $dir_array[$i]) = array($dir_array[$i], $dir_array[0]);
				break;
			}
		}
	}
	return $dir_array;
	
}

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

function removeExtension($filename){
	if ((substr($filename, 0, 1) == '.') && (substr_count($filename, '.') == 1)){
		return $filename;
	}
	$arr = explode('.', $filename);
	if (count($arr)>1){
		array_pop($arr);
	}
	return implode('.', $arr);
	
}

?>