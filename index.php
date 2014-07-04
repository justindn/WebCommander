<?
header('content-type:text/html;charset=utf-8');
include './lang/ru.php';
include 'config.php';
?>
<!doctype html>
<html>
<head>
<title> </title>
<meta charset='utf-8'>
<link rel='stylesheet' href='<?='./themes/' . THEME . '/styles.css'?>'>
<style>
button{
	width:14%;
	margin:0.8% 0;
	border:1px solid;
	border-color:#ddd #888 #888 #ddd;
	background-color:#cccccc;
	height:80%;
}

</style>
<script src='jquery-1.11.1.min.js'></script>
<script>
	
	$(document).ready(function(){
	
		var line;
		var activePanel = 0;
		init();
		
		function init(){
			$.ajax('init.php').done(function(data){
				var init = JSON.parse(data);
				renderPanel(init.folder, 0);
				renderPanel(init.folder, 1);
			});
		}
		
		
		function renderPanel(folderName, panel){
		//checking if the name of current forder is exists
 			if (typeof (folderName) === "undefined") folderName = '.';
		//checking if the 'panel' object is defined	
			if (typeof (panel) === "undefined")	panel = activePanel;
			
			$.ajax('filelist.php?folder=' + folderName).done(function(data) {
			
				$('.folder-content:eq(' + panel + ')').empty();
				
				var filelist = JSON.parse(data);
				
				var currentFolder = filelist[0].fullpath;
				
				
				if (folderName == '.') {
					folderName = currentFolder;
				}
				$('.current_dir:eq(' + panel + ')').html(folderName);
				
				for (var i = 0; i < filelist.length; i++){
					$('.folder-content:eq(' + panel + ')').append('<tr data-folder=' 
						+ filelist[i].fullpath + 
						' data-filename="' + 
						filelist[i].name +
						'" data-extension="'+ 
						filelist[i].extension +
						'" data-is-folder = "' 
						+ filelist[i].folder + 
						'"><td class="filename" style="background-image:url(\'' + filelist[i].icon + '\');">' +filelist[i].name +  
						'</td> <td>' + filelist[i].extension + 
						'</td> <td align="right">' + filelist[i].size + 
						'</td><td>' + filelist[i].datetime + 
						'</td></tr>');
				}
				
				if (typeof (line) === "undefined"){
					line = $('.folder-content:eq(0) tr:eq(0)');
				}
				else{
					line = $('.folder-content:eq(' + panel + ') tr:eq(0)');
					drawCursor(line);
				}
				
			});
			
		}
		
		function drawCursor(target, prev){

			if (target.index() == -1) return;
			
			panel = $(target.parent().parent().parent());
			
			activePanel = panel.index()-1;
			
			$('.panel:eq(' + Math.abs(activePanel - 1) + ')').children('.current_dir').removeClass('current_dir_active');
			
			panel.children('.current_dir').addClass('current_dir_active');			
			
			if (line != ''){
				line.removeClass('cursor');
			}
			if (typeof (prev) !== "undefined"){
				prev.removeClass('cursor');
			}
			line = target;
			target.addClass('cursor');
		}
		
		$('.list').click(function(){
			drawCursor($(event.target).parent());
		});
		
		$('.list').dblclick(function(event){

			drawCursor(line);
			line = $(event.target).parent();

			if (line.attr('data-is-folder') === 'true'){
				renderPanel(line.attr('data-folder'), activePanel);
			}
		});
		
		function panelScrollTop(){
			
			$('.panel:eq(' + Math.abs(activePanel) + ')').animate({scrollTop: 0}, 1);
		}
		function panelScrollBottom(){
			var height = $('.panel:eq(' + Math.abs(activePanel) + ')').height();
			$('.panel:eq(' + Math.abs(activePanel) + ')').animate({scrollTop: height}, 1);
		}
		
		function unlink(){
			if (line.attr('data-filename') == '..') return;
			if (line.attr('data-is-folder') == 'true'){
				var fileName = line.attr('data-filename');
			}
			else{
				var fileName = line.attr('data-filename') + '.' + line.attr('data-extension') ;
			}
			if (confirm ('Are you really want to delete ' + fileName + '?')){
					var currentDir = $('.current_dir:eq(' + activePanel + ')').html();
					alert('action=del&filename=' + fileName + '&path=' + currentDir);
					$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=del&filename=' + fileName + '&path=' + currentDir,
					}).done(function (){
						renderPanel(currentDir);
					});
			}
		}
		
		
		function rename(){
		
			if (line.attr('data-filename') == '..') return;
			
			if (line.attr('data-is-folder') == 'true'){
				var oldName = line.attr('data-filename');
			}
			else{
				var oldName = line.attr('data-filename') + '.' + line.attr('data-extension') ;
			}
			
			var newName = prompt('Enter the new name', oldName);
			
			var currentDir = $('.current_dir:eq(' + activePanel + ')').html();

			if (newName !== null && newName != false && newName != oldName && line.attr('data-filename') != '..'){
					$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=ren&oldname=' + oldName + '&newname=' + newName + '&path=' + currentDir,
					}).done(function (){
						renderPanel(currentDir);
					});
			}
		}
		
		$('body').keydown(function(){
			
			//alert(event.keyCode);
			/*
			F3-114
			4-115
			5-116
			6-117
			7-118
			R - 82
			*/
			event.preventDefault();
			switch (event.keyCode){
				case 35:
					var previousLine = line;
					var quant = $('.folder-content:eq(' + activePanel  + ') tr').length;
					line = $('.folder-content:eq(' + activePanel  + ') tr:eq(' + (quant-1) + ')');
					drawCursor(line, previousLine);
					panelScrollBottom();
					break;
				case 36:
					var previousLine = line;
					line = $('.folder-content:eq(' + activePanel  + ') tr:eq(0)');
					drawCursor(line, previousLine);
					panelScrollTop();
					break;
				case 38:
					drawCursor(line.prev());
					break;
					
				case 40:
					drawCursor(line.next());
					break;
				case 9:
					//alert('tab');
					activePanel = Math.abs(panel.index()-2);
					var previousLine = line;
					line = $('.folder-content:eq(' + activePanel  + ') tr:eq(0)');
					drawCursor(line, previousLine);
					break;
				case 13:
					drawCursor(line);
					if (line.attr('data-is-folder') === 'true'){
						renderPanel(line.attr('data-folder'), activePanel);
					}
					break;
				case 113:
					rename();
					break;
				case 119:
				case 46:
					unlink();
					break;
				case 82:
					if (event.ctrlKey){
						renderPanel($('.current_dir:eq(' + activePanel + ')').html(), activePanel);
					}
			}
			
			
		});
		
		$('#f2').click(function(){
			rename();
		});
		$('#f8').click(function(){
			unlink();
		});
		
	});
</script>
</head>

<body>

<div id = 'container'>
	<div id = 'toolbar'></div>
		<div class = 'panel'>
			<div class = 'current_dir'>Current dir</div>
			<table class='list'>
				<thead class='list-header'>
					<tr>
						<td class='filename'><?=$lang['file']?></td>
						<td><?=$lang['type']?></td>
						<td><?=$lang['size']?></td>
						<td><?=$lang['date']?></td>
					</tr>
				</thead>
				<tbody class='folder-content'>
				
				</tbody>
			</table>
		</div>
		<div class = 'panel'>
		<div class = 'current_dir'>Current dir</div>
			<table class='list'>
				<thead class='list-header'>
				<tr>
					<td><?=$lang['file']?></td>
					<td><?=$lang['type']?></td>
					<td><?=$lang['size']?></td>
					<td><?=$lang['date']?></td>
				</tr>
				</thead>
				<tbody class='folder-content'>
				
				</tbody>
			</table>
		</div>
	<div id = 'bottom_panel'>
	<button id='f2'>F2 Rename</button>
	<button id='f3'>F3 View</button>
	<button id='f4'>F4 Edit</button>
	<button id='f5'>F5 Copy</button>
	<button id='f6'>F6 Move</button>
	<button id='f7'>F7 NewDir</button>
	<button id='f8'>F8 Delete</button>
	</div>
</div>

</body>
</html>