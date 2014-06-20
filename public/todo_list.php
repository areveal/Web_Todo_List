<?	
$errormessage = '';
require_once('filestore.php');

$open = new Filestore('todo_list.txt');

$todos = $open->read();

//remove todos
if(!empty($_GET)){
	if ($_GET['action'] == 'remove'){
		//remove item at 'index'
		unset($todos[$_GET['index']]);
		//reload page
		header("Location: /todo_list.php");
		//save 
		$open->write($todos);
		// save($todos,'todo_list.txt');
		//get out!
		exit(0);
	}
}

$open->write($todos);	
// save($todos,'todo_list.txt');

//add todos
try{
	if(!empty($_POST)){

		if(strlen($_POST['new_items']) > 240) {
			throw new Exception("We're sorry. The input provided was too long. Please try again.");
		}
		//take in new item added
		$todos[] = $_POST['new_items'];
		//save bc we can
		$open->write($todos);
	}
}catch(Exception $e){
	$msg = $e->getMessage();
}

	

// Verify there were uploaded files and no errors
if (count($_FILES) > 0 && $_FILES['files']['error'] == 0) {
	if($_FILES['files']['type'] == 'text/plain'){	
		// Set the destination directory for uploads
		$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
		// Grab the filename from the uploaded file by using basename
		$filename = basename($_FILES['files']['name'] . time());
		// Create the saved filename using the file's original name and our upload directory
		$saved_filename = $upload_dir . $filename;
		// Move the file from the temp location to our uploads directory
		move_uploaded_file($_FILES['files']['tmp_name'], $saved_filename);
		//time to import the list
		$import = new Filestore("uploads/$filename");
		//read in file
		$imported_list = $import->read();
		//add new items to todo list
		$todos = array_merge($todos, $imported_list);
		//save
		$open->write($todos);
	} else {
		//send error message if not a text file
		$errormessage = "File must be a text file... You jive turkey!!!";		
	}
}


?>
	
<!DOCTYPE html>
<html>
<head>
	<title>TODO List</title>
	<link rel="stylesheet" href="/css/todo_list.css">
</head>
<body>

	<h1>TODO List</h1>
	<ul>	
		<?	
			try {//write list
				if(!empty(implode($todos))):			
					//for each todo item, write it out and give it a remove link
					foreach ($todos as $key => $todo):?>
						<li class="show_up"><?= htmlspecialchars(strip_tags(($todo)))?> <a class="complete"href=<?="?action=remove&index=$key"?>>Mark Complete</a></li>
					<?endforeach;		
				endif; 
			}catch(InvalidInputException $e) {
				echo "We're sorry. The input provided was too long. Please try again.";
			}
		?>
	</ul>	
	<!--this is all the stuff for the list-->	

			
	<!--add itmes here-->	
	<h2>Add New Items</h2>
	
	<?
		if(isset($msg)) : ?>
			<?= "<h2 style='color:red'>$msg</h2>" ?>
		<? endif; 
	?>

	<form method="POST" action="todo_list.php">
		<input id="add_item_box"type="text" name="new_items">
		<p>
			<input class='button' type="submit" value='Add Item'>
		</p>

	</form>

	<h2>File Upload</h2>

	<?
		if(isset($errormessage)) : ?>
			<?= "<h2 style='color:red'>$errormessage</h2>" ?>
		<? endif; 
	?>


	<!--accepts file uplaod input-->
	<form method="POST" action="/todo_list.php" enctype="multipart/form-data">
		<label for="file">File to Upload:</label>
		<input type="file" id="file" name="files">
		<p>
			<input class='button' type="submit" value="Upload">
		</p>
	</form>	
<div>
</div>

</body>
</html>