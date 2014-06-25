<?	

require_once('class_todo.php');
// Get new instance of PDO object
$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_list', 'cole', 'password');
// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//declare database
$database = new Db($dbc);
//remove todos
$database->removeItem();
//add todos
$database->addItem();
// Verify there were uploaded files and no errors
$database->uploadItem();
//create todo list from database
$todos = $database->getTodos();

?>
	
<!DOCTYPE html>
<html>
<head>
	<title>TODO List DB</title>
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

	<div id="pagebox">
			<? if($database->page != 1): ?>
				<a href="?page=<?=($database->page-1)?>" class="pagination">Previous Page</a>
			<? endif; ?>
			<? if($database->page < $database->page_num): ?>
				<a href="?page=<?=($database->page+1)?>" class="pagination">Next Page</a>
			<? endif; ?>
	</div>
	<!--add itmes here-->	
	<h2>Add New Items</h2>
	
	<?
		if(isset($database->msg)) : ?>
			<?= "<h2 style='color:red'>$database->msg</h2>" ?>
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
		if(isset($database->errormessage)) : ?>
			<?= "<h2 style='color:red'>$database->errormessage</h2>" ?>
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
<div id="container">
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