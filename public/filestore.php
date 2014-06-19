<?

class Filestore {

    public $filename = '';
    public $is_csv = false;

    function __construct($filename = '') 
    {
        // Sets $this->filename
        $this->filename = $filename;
        $str = substr($filename, -3, 3);
        if($str == 'csv') {
            $this->is_csv = true;
        }
    }

    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines()
    {   

        $filesize = filesize($this->filename);
        //open file to read
        $read = fopen($this->filename, 'r');
        //read file into string
        $list_string = trim(fread($read, $filesize));
        //turn string into array
        $list = explode("\n", $list_string);
        //close the file
        fclose($read);

        return $list;    
    }

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    private function write_lines($array)
    {
        //open the file for writing
        $write = fopen($this->filename, 'w');
        //turn the array into a string
        $string = implode("\n", $array);
        // write the string onto the file
        fwrite($write, $string . "\n");
        //close the file
        fclose($write);
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    private function read_csv()
    {
        $array = [];
        //open the file for reading
        $read = fopen($this->filename, 'r');
        //while not at the end of file, add each contact to the array
        while(!feof($read)) {
            $contact = fgetcsv($read);
            //only if it is an array
            if(is_array($contact)) {
                $array[] = $contact;
            }
        }
        //close the handle
        fclose($read);
        return $array;
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    private function write_csv($array)
    {
        //open the file for writing
        $write = fopen($this->filename, 'w');
        //write contact to the file
        foreach ($array as $address) {
            fputcsv($write, $address);
        }
        //close the handle
        fclose($write);
    }


    public function read()
    {
        if($this->is_csv == true) {
            $array = $this->read_csv();
        } else {
            $array = $this->read_lines();
        }
        return $array;
    }

    public function write($array)
    {
          if($this->is_csv == true) {
            $this->write_csv($array);
        } else {
            $this->write_lines($array);
        }  
    }
}


?>