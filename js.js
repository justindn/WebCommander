	$(document).ready(function(){
		//Текущее местоположение курсора. Объект DOM со строкой таблицы.
		/* var line;
		//Активная панель
		var activePanel = 0;
		init();
		
		var panelLeft = {
			'isActive' : 1, 
			'folder' : '.', 
		}
		
		var panelRight = {
			'isActive' : 0, 
			'folder' : '.', 
		}
		
		function init(){
			$.ajax('init.php').done(function(data){
				var init = JSON.parse(data);
				renderPanel(init.folder, 0);
				renderPanel(init.folder, 1);
			});
		} */
		
		
		/* function renderPanel(folderName, panel){
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
					$('.folder-content:eq(' + panel + ')').append('<tr class="line" data-folder="' 
						+ filelist[i].fullpath + 
						'" data-filename="' + 
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
			
		} */
		
		/* function drawCursor(target, prev){

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
		} */
		
		
		/* $('.list').click(function(event){
			event.preventDefault();
			drawCursor($(event.target).parent());
		}); */
		
		/*$('.list').contextmenu(function(event){
			event.preventDefault();
			$(event.target).parent().toggleClass('selected');
			drawCursor($(event.target).parent());
		});*/
		/*
		$('.list').dblclick(function(event){
			event.preventDefault();
			drawCursor(line);
			line = $(event.target).parent();

			if (line.attr('data-is-folder') === 'true'){
				renderPanel(line.attr('data-folder'), activePanel);
			}
		});*/
		/*
		function panelScrollTop(){
			
			$('.panel:eq(' + Math.abs(activePanel) + ')').animate({scrollTop: 0}, 1);
		}
		function panelScrollBottom(){
			var height = $('.panel:eq(' + Math.abs(activePanel) + ')').height();
			$('.panel:eq(' + Math.abs(activePanel) + ')').animate({scrollTop: height}, 1);
		}*/
		
		/* function unlink(){
			if (line.attr('data-filename') == '..') return;
			if (line.attr('data-is-folder') == 'true'){
				var fileName = line.attr('data-filename');
			}
			else{
				var fileName = line.attr('data-filename') + '.' + line.attr('data-extension') ;
			}
			if (confirm ('Are you really want to delete ' + fileName + '?')){
					var currentDir = $('.current_dir:eq(' + activePanel + ')').html();
					$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=del&filename=' + fileName + '&path=' + currentDir,
					}).done(function (data){
						
						var result = JSON.parse(data);
						
						if (typeof(result.message) != "undefined"){
							alert(result.message);
						}
						renderPanel(currentDir);
					});
			}
		} */
		/* function newdir(){
			var dirName = prompt('Enter the new directory name');
			if (dirName !== null && dirName != '' && dirName != false && dirName !='.' && dirName !='..')
			var currentDir = $('.current_dir:eq(' + activePanel + ')').html();
				
			$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=newfolder&filename=' + dirName + '&path=' + currentDir,
					}).done(function (data){
						
						var result = JSON.parse(data);
						
						if (typeof(result.message) !== undefined){
							alert(typeof(result.message));
						}
						
			});
			renderPanel($('.current_dir:eq(' + activePanel + ')').html(), activePanel);
		} */
		
		/* function getDirSize(dir, writeTo){
			writeTo.html('...');
			$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=calcsize&path=' + dir,
					}).done(function (data){
						writeTo.html(data);
			});	
			
		} */
		
/* 		function rename(){
		
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
		} */
		
/* 		function show_viewer(){
			if (line.attr('data-is-folder') != 'true'){
						$('#viewer_header').html(line.attr('data-folder') + '/' + line.attr('data-filename'));
						$('#viewer_content').html('');
						$('#viewer').toggle();
					
						$.ajax({
							cache : false, 
							type  : 'POST',
							url   : 'operations.php',
							data  : 'action=getfile&folder=' + line.attr('data-folder'),
						}).done(function (data){
							$('#viewer_content').html('<pre>' + data + '</pre>');
						});	
			}
		}
		
		function hide_viewer(){
			$('#viewer').hide();
		} */
		
/* 		function copy(from, to){
			
		} */
		
		/* function show_progress(title){
			if(confirm('Do you really want to ' + title + ' these files?')){
				var copy_to = $('.current_dir:eq(' + Math.abs(activePanel-1) + ')').html() + '\\' + line.attr('data-filename') + '.' + line.attr('data-extension');
				var copy_from = line.attr('data-folder');
				
				if (copy_from == copy_to){
					alert('Cannot copy file to itself!');
					return;
				}
				if (typeof(title) == 'undefined') title='';
				$('#progress header').html(title);
				$('#progress').show();
					$.ajax({
					cache : false, 
					type  : 'POST',
					url   : 'copy.php',
					data  : 'from=' + copy_from + '&to=' + copy_to + '&overwrite=0',
					}).done(function (data){
						if (data == ''){
							alert('Copy error');
						}
						if (data == '2'){
							alert('File already exist!');
						}
						$('#progress').hide();
					});	
			}
		} */
		
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
				case 45:
					line.toggleClass ('selected');
					drawCursor(line.next());
					break;
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
		/* 		case 38:
					if (event.shiftKey){
						line.toggleClass ('selected');
					}
					drawCursor(line.prev());
					break;
					
				case 40:
					if (event.shiftKey){
						line.toggleClass ('selected');
					}
					drawCursor(line.next());
					break; */
/* 				case 9:
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
					break; */
				/* case 27:
					$('#viewer').hide();
					break;
				case 113: //F2
					rename();
					break;
				case 114: //F3
					show_viewer();
					break;
				case 116: //F5
					
					show_progress('Copy');
					break;
				case 117: //F6
					show_progress('Move');
					break;
				case 118: //F7
					newdir();
					break;
				case 119: //F8
				case 46:  //Delete
					unlink();
					break;
				
				case 82:  //Ctrl + R
					if (event.ctrlKey){
						renderPanel($('.current_dir:eq(' + activePanel + ')').html(), activePanel);
					}
					break;
				case 32:  //Space
					line.toggleClass('selected');
					if (line.attr('data-is-folder') == 'true'){
						getDirSize(line.attr('data-folder'), line.children('td:eq(2)'));
					}
				  break;
				case 27:
					hide_viewer();
					break; */
			}
			
			
		});
		
		/* $('#f2').click(function(){
			rename();
		});
		$('#f3').click(function(){
			show_viewer();
		});
		$('#f5').click(function(){
			show_progress('Copy');
		});
		$('#f6').click(function(){
			show_progress('Move');
		});
		$('#f7').click(function(){
			newdir();
		});
		$('#f8').click(function(){
			unlink();
		}); */

		/*VIEWER*/
/* 		$('#viewer_close').click(function(){
			hide_viewer();
		});
 */		
	});
