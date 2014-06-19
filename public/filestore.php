<?

class Filestore {

    public $filename = '';

    function __construct($filename = '') 
    {
        // Sets $this->filename
        $this->filename = $filename;
    }

    /**
     * Returns array of lines in $this->filename
     */
    function read_lines()
    {   

        if (is_readable($this->filename) && filesize($this->filename) > 0) {
            $filesize = filesize($this->filename);
            //open file to read
            $read = fopen($this->filename, 'r');
            //read file into string
            $list_string = trim(fread($read, $filesize));
            //turn string into array
            $list = explode("\n", $list_string);
            //close the file
            fclose($read);
        }    

        return $list;    
    }

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    function write_lines($array)
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
    function read_csv()
    {
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
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    function write_csv($array)
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

}


?>