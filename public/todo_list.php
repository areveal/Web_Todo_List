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
		function open_file() {
		    $list = [];
		    $file = 'todo_list.txt';
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
			foreach ($todos as $key => $todo) {
			 	if ($_GET['index'] == $key) {
			 	unset($todos[$key]);
			 	}
			 } 
		}
		
		save($todos,'todo_list.txt');
		
		//add todos
		if(!empty($_POST)){
			$todos[] = $_POST['new_items']; 
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
		<textarea name="new_items" autofocus></textarea>
		<p>
			<input type="submit">
		</p>
	</form>

	<?php	
		save($todos,'todo_list.txt');
	?>

</body>
</html>