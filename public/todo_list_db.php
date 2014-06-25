<?	
// Get new instance of PDO object
$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_list', 'cole', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getPage() {
	if(!empty($_GET['page'])) {
		$page = ($_GET['page']);
	} else {
		$page = 1;
	}
	return $page;
}



function getTodos($dbc) {
	$page = getPage();
	$offset = ($page * 10) - 10;
	$stmt = $dbc->prepare('SELECT * FROM todos LIMIT 10 OFFSET :offset');
	$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function read_lines($filename)
{   

    $filesize = filesize($filename);
    //open file to read
    $read = fopen($filename, 'r');
    //read file into string
    $list_string = trim(fread($read, $filesize));
    //turn string into array
    $list = explode("\n", $list_string);
    //close the file
    fclose($read);

    return $list;    
}

$errormessage = '';

//remove todos
if(!empty($_POST) && isset($_POST['remove'])){

	//remove item at 'id'
	$stmt = $dbc->prepare("DELETE FROM todos WHERE id = :id");
	$stmt->bindValue(':id', $_POST['remove'], PDO::PARAM_STR);
	$stmt->execute();
	//reload page
	header("Location: /todo_list_db.php");
	exit(0);

}

// save($todos,'todo_list.txt');

//add todos
try{
	if(!empty($_POST) && isset($_POST['new_items'])){

		if(strlen($_POST['new_items']) > 240) {
			throw new Exception("We're sorry. The input provided was too long. Please try again.");
		}
		//take in new item added
		$stmt = $dbc->prepare("INSERT INTO todos (todo) VALUES (:todo)");
		$stmt->bindValue(':todo', $_POST['new_items'], PDO::PARAM_STR);
		$stmt->execute();
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
		$import = read_lines($saved_filename);

		$stmt = $dbc->prepare("INSERT INTO todos (todo) VALUES (:todo)");
		foreach ($import as $key => $item) {
			$stmt->bindValue(':todo', $item, PDO::PARAM_STR);
			$stmt->execute();
		}

		//read in file
		//add new items to todo list
		//save
	} else {
		//send error message if not a text file
		$errormessage = "File must be a text file... You jive turkey!!!";		
	}
}

$todos = getTodos($dbc);
$page = getPage();
$stmt = $dbc->query('SELECT * FROM todos');
$page_num = ceil(($stmt->rowCount())/10);


?>
	
<!DOCTYPE html>
<html>
<head>
	<title>TODO List</title>
	<!--stylesheet-->
	<link rel="stylesheet" href="/css/todo_list.css">
	<!--jquery-->	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<!--jquery-->	
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
</head>
<body>

	<h1>TODO List</h1>
	<!--this is all the stuff for the list-->	
	<ul>	
		<?	
			try {//write list
				if(count($todos) > 0):			
					//for each todo item, write it out and give it a remove link
					foreach($todos as $key => $todo):?>
						<li class="show_up"><?= $todo['todo']?><button id="btn<?=$todo['id']?>">Remove Item</button></li>
					<?endforeach;		
				endif; 
			}catch(InvalidInputException $e) {
				echo "We're sorry. The input provided was too long. Please try again.";
			}
		?>
	</ul>	

	<span id="pagination">
		<? if($page != 1): ?>
			<a href="?page=<?=($page-1)?>">Previous Page</a>
		<? endif; ?>
		<? if($page <= $page_num): ?>
			<a href="?page=<?=($page+1)?>">Next Page</a>
		<? endif; ?>
	</span>
	<!--add itmes here-->	
	<h2>Add New Items</h2>
	
	<?
		if(isset($msg)) : ?>
			<?= "<h2 style='color:red'>$msg</h2>" ?>
		<? endif; 
	?>

	<form method="POST" action="">
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
	<form method="POST" action="" enctype="multipart/form-data">
		<label for="file">File to Upload:</label>
		<input type="file" id="file" name="files">
		<p>
			<input class='button' type="submit" value="Upload">
		</p>
	</form>	
<div>
</div>

<form id="removeForm" action="todo_list_db.php" method="POST">
    <input id="removeId" type="hidden" name="remove" value="">
</form>

</body>
</html>

<script type="text/javascript">
	<? foreach($todos as $todo) : ?>
		$("#btn<?=$todo['id']?>").click(function() {
			varId = <?=$todo['id']?>;
			$('#removeId').val(varId);
			if(confirm("Are you sure you want to delete item <?=$todo['id']?>?")) {
				$('#removeForm').submit();
			}
		});
	<? endforeach;?>
</script>