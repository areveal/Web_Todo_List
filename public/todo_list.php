<!DOCTYPE html>
<html>
<head>
	<title>TODO List</title>
</head>
<body>
	<h1>TODO List</h1>
	<ul>
		<?php 
			$todos = ['Take out the trash', 'Mow the lawn', 'Wash the dog', 'Run'];
			foreach ($todos as $todo) {
				echo "<li>$todo</li>\n";
			}
		?>
	</ul>	
	<h3>Add New Items</h3>
	<form method="POST" action="todo_list.html">
		<textarea name="new items"></textarea>
		<p>
			<input type="submit">
		</p>
	</form>

</body>
</html>