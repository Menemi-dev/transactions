<?php

namespace Transactions\Modules;

class FileManagement {
    /**
     * Uploaded file data
     */
    protected Array $data;
    /**
     * File data in array as rows per line
     */
    protected Array $rows;

    /**
     * Initializes a new instance of the FileManagement class
     * @param array $file
     */
    public function __construct($file) {
        $this->data = $file;
        $rows = $this->csvToArray($file);
        $this->rows = $rows;
    }

    /**
     * Returns the instance's rows property
     * @return array
     */
    public function getArrayFile() {
        return $this->rows;
    }

    /**
     * Receives a file in CSV format and returns an array of elements per line
     * @param array $file
     * @return array
    */
    public function csvToArray($file) {
        $rows = [];
        $type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if($type != "csv") { //Only allow csv extension
            return $rows;
        }
        ini_set('auto_detect_line_endings', TRUE);
        if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) { //Check the resource is valid
            while (($data = fgetcsv($handle)) !== FALSE) { //Check opening the file is OK
                if(!empty($data[0])){
                    array_push($rows, $data);
                }
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Receives an array of indexes and returns an array with the file rows on those indexes
     * @param array $arrKeys
     * @return array
    */
    public function getRowsById($arrKeys) {
        $newArray = [];
        foreach($arrKeys as $key){
            if(isset($this->rows[$key])){
                $newArray[$key] = $this->rows[$key];
            }
        }
        return $newArray;
    }
}