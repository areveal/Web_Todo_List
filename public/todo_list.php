<!DOCTYPE html>
<html>
<head>
	<title>TODO List</title>
</head>
<body>
	<h1>TODO List</h1>
	<ul>
		<?php 
			var_dump($_POST);	


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


			$todos = open_file();

			if(!empty($_POST)){
				$todos[] = $_POST['new_items']; 
			}
			
			foreach ($todos as $todo) {
				echo "<li>$todo</li>\n";
			}




		?>
	</ul>	
	

	<h3>Add New Items</h3>
	<form method="POST" action="todo_list.php">
		<textarea name="new_items"></textarea>
		<p>
			<input type="submit">
		</p>
	</form>

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
		
		save($todos,'todo_list.txt');
	?>

</body>
</html>