<?

class Db {
	public $page = 0;
	public $page_num = 0;
	private $dbc = '';
	public $errormessage = '';
	public $msg = '';

    function __construct($dbc = '') 
    {
        $this->dbc = $dbc;

    }

	private function getPage() {

		if(!empty($_GET['page'])) {
			$this->page = ($_GET['page']);
		} else {
			$this->page = 1;
		}

	}

	public function getTodos() {
		//set page
		$this->getPage();
		//set page_num
		$stmt = $this->dbc->query('SELECT * FROM todos');
		$this->page_num = ceil(($stmt->rowCount())/10);
		//set offset
		$offset = ($this->page * 10) - 10;
		//get todo list from database limit 10
		$stmt = $this->dbc->prepare('SELECT * FROM todos LIMIT 10 OFFSET :offset');
		$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);

	}

	public function read_lines($filename) {

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

	public function removeItem() {
		if(!empty($_POST) && isset($_POST['remove'])){
			//remove item at 'id'
			$stmt = $this->dbc->prepare("DELETE FROM todos WHERE id = :id");
			$stmt->bindValue(':id', $_POST['remove'], PDO::PARAM_STR);
			$stmt->execute();
			//reload page
			header("Location: /todo_list_db.php");
			exit(0);
		}		
	}

	public function addItem() {
		try{
			if(!empty($_POST) && isset($_POST['new_items'])){

				if(strlen($_POST['new_items']) > 240) {
					throw new Exception("We're sorry. The input provided was too long. Please try again.");
				}
				//take in new item added

				$stmt = $this->dbc->prepare("INSERT INTO todos (todo) VALUES (:todo)");
				$stmt->bindValue(':todo', $_POST['new_items'], PDO::PARAM_STR);
				$stmt->execute();
			}
		}catch(Exception $e){
			$this->msg = $e->getMessage();
		}		
	}

	public function uploadItem() {

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
				$import = $this->read_lines($saved_filename);

				$stmt = $this->dbc->prepare("INSERT INTO todos (todo) VALUES (:todo)");
				foreach ($import as $item) {
					$stmt->bindValue(':todo', $item, PDO::PARAM_STR);
					$stmt->execute();
				}
			} else {
				//send error message if not a text file
				$this->errormessage = "File must be a text file... You jive turkey!!!";		
			}
		}
		
	}


}


?>