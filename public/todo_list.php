<!DOCTYPE html>
<html>
<head>
	<title>TODO List</title>
	<?php
		//save the list to a file
		function save($list, $file) {
		    //open the file for writing
		    $write = fopen($file, 'w');
		    //turn the array into a string
		    $string = implode("\n", $list);
		    // write the string onto the file
		    fwrite($write, $string . "\n");
		    //close the file
		    fclose($write);
		}
				

		// open in a file
		function open_file($file = 'todo_list.txt') {
		    $list = [];
		    if (is_readable($file)) {
		        
		        $filesize = filesize($file);
		        //open file to read
		        $read = fopen($file, 'r');
		        //read file into string
		        $list_string = trim(fread($read, $filesize));
		        //turn string into array
		        $list = explode("\n", $list_string);
		        //close the file
		        fclose($read);
		    } else {
		        echo 'File is not readable.' . PHP_EOL;
		    } 
		    //dump the array
		    return $list;
		}
	?>
</head>
<body>


	<h1>TODO List</h1>
	<!--this is all the stuff for the list-->	
	<?php 

		$todos = open_file();
		
		//remove todos
		if(!empty($_GET)){
			if ($_GET['action'] == 'remove'){
				unset($todos[$_GET['index']]);
				header("Location: /todo_list.php");
				save($todos,'todo_list.txt');
				exit(0);
			}
		}
		
		save($todos,'todo_list.txt');
		
		//add todos
		if(!empty($_POST)){
			$todos[] = $_POST['new_items'];
			header("Location: /todo_list.php");
			save($todos,'todo_list.txt');
			exit(0); 
		}

		

		// Verify there were uploaded files and no errors
		if (count($_FILES) > 0 && $_FILES['files']['error'] == 0 && $_FILES['files']['type'] == 'text/plain') {
			
			// Set the destination directory for uploads
			$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
			// Grab the filename from the uploaded file by using basename
			$filename = basename($_FILES['files']['name'] . time());
			// Create the saved filename using the file's original name and our upload directory
			$saved_filename = $upload_dir . $filename;
			// Move the file from the temp location to our uploads directory
			move_uploaded_file($_FILES['files']['tmp_name'], $saved_filename);
		}

		if(isset($saved_filename)){
			$imported_list = [];	
			$imported_list = open_file("uploads/$filename");
			$todos = array_merge($todos, $imported_list);
			save($todos,'todo_list.txt');
		}


		
		
		//write list
		if(!empty(implode($todos))){			
			foreach ($todos as $key => $todo) {
				echo "<li>$todo <a href='todo_list.php?action=remove&index=$key'>Mark Complete</a></li>";
			}			
		}
	?>






			
	
	<!--add itmes here-->	
	<h2>Add New Items</h2>
	<form method="POST" action="todo_list.php">
		<input type="text" name="new_items" autofocus>
		<p>
			<input type="submit">
		</p>
	</form>

	<?php	
		save($todos,'todo_list.txt');
	?>

	<?php

	if (isset($_FILES['files']['type']) && $_FILES['files']['type'] != 'text/plain') {
		echo "<h1 style='color: red'>File must be a text file... You jive turkey!!!</h1>";
	}
	?>


	<h2>File Upload</h2>

	<form method="POST" action="/todo_list.php" enctype="multipart/form-data">
		<label for="file">File to Upload:</label>
		<input type="file" id="file" name="files">
		<p>
			<input type="submit" vaule="Upload">
		</p>
	</form>	

</body>
</html>