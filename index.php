<?
header('content-type:text/html;charset=utf-8');
include './lang/ru.php';
?>

<!doctype html>
<html>
<head>
<title> </title>
<meta charset='utf-8'>
<style>
	*{
		font-family:arial, sans-serif;
		font-weight:bold;
	}
	html, body{
		height:100%;
		padding:0;
		margin:0;
		font-size:0.85em;
	}
	#container{
		width:98%;
		height:96%;
		margin:auto;
		position:absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
		background-color:#D4D0C8;
		border:2px outset #D4D0C8;
		padding:0.5%;

	}
	#toolbar{
		border:1px solid;
		border-color:#fff transparent #808080 transparent;
		height:5%;
		clear:both;
	}
	.panel{
		float:left;
		width:49.2%;
		border:2px inset #dddddd;
		height:90%;
		margin:0.1%;
		overflow:auto;
		background-color:#C0C0C0;
	}
	.panel > .current_dir{
		height:1.3em;
		background-color:#808080;
		color:#D4D0C8;
		outline:1px outset #dddddd;
	}
	.panel > .current_dir_active {
		height:1.3em;
		background-color:#0A246A;
		color:#ffffff;
		outline:1px outset #dddddd;
	}
	.panel > .current_dir_nonactive{
		height:1.3em;
		background-color:#808080;
		color:#D4D0C8;
		outline:1px outset #dddddd;
	}
	.panel > .list{
		width:100%;
		border-spacing:0;
	}
	.panel > * {
		cursor:pointer;
	}
	.list  td{
		border:1px solid transparent;
	}
	.list-header tr:first-child{
		background-color:#D4D0C8;
		height:1.5em;
		outline:1px outset #dddddd;
	}
	.cursor {
		
	}
	.cursor > td{
		border-bottom:1px dotted #3F3F3F;
		border-top:1px dotted #3F3F3F;
	}
	.cursor > td:first-child{
		border-left:1px dotted #3F3F3F;
	}
	.cursor > td:last-child{
		border-right:1px dotted #3F3F3F;
	}
	#bottom_panel{
		border:1px solid;
		border-color:#ffffff transparent transparent transparent;
		height:4%;
		clear:both;
	}
	thead , tbody{
		
	}
	.filename{
		text-indent:17px;
		background-position:center left;
		background-repeat:no-repeat;
	}
</style>
<script src='jquery-1.11.1.min.js'></script>
<script>
	
	$(document).ready(function(){
	
		var line;
		var activePanel = 0;

		renderPanel('.', 0);
		renderPanel('.', 1);
		
		
		function renderPanel(folderName, panel){
 			if (typeof (folderName) === "undefined") folderName = '.';
			
			if (typeof (panel) === "undefined")	panel = activePanel;
			
			$.ajax('filelist.php?folder=' + folderName).done(function(data) {
			
				$('.folder-content:eq(' + panel + ')').empty();
				
				var filelist = JSON.parse(data);
				
				var currentFolder = filelist[0].fullpath;
				if (folderName == '.'){
					folderName = currentFolder;
				}
				$('.current_dir:eq(' + panel + ')').html(folderName);
				
				for (var i = 0; i < filelist.length; i++){
					$('.folder-content:eq(' + panel + ')').append('<tr data-folder=' 
					+ filelist[i].fullpath + 
					' data-filename=' + 
					filelist[i].name +
					' data-is-folder = ' 
					+ filelist[i].folder + 
					'><td class="filename" style="background-image:url(\'' + filelist[i].icon + '\');">' +filelist[i].name +  
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
		
		$('body').keydown(function(){
			//alert(event.keyCode);
			event.preventDefault();
			switch (event.keyCode){
				case 35:
					//alert('end');
					//drawCursor(line.get(0));
					break;
				case 36:
					//alert('home');
					//drawCursor(line.last(), line);
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

			}
			
			
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
	
	</div>
</div>

</body>
</html>