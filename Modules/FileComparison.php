<?php

namespace Transactions\Modules;

class FileComparison {
    /**
     * File data 1 as array
     */
    protected Array $file1;
    /**
     * File data 2 as array
     */
    protected Array $file2;
    /**
     * Transaction date's column index
     */
    protected int $date;
    /**
     * Transaction amount's column index
     */
    protected int $amount;
    /**
     * Transaction id's column index
     */
    protected int $transaction_id;
    /**
     * Wallet reference's column index
     */
    protected int $wallet;

    /**
     * Initializes a new instance of the FileComparison class
     * @param array $file1
     * @param array $file2
     */
    public function __construct($file1, $file2) {
        if(empty($file1) || empty($file2)){
            throw new \Exception("Files can't be empty and should contain
            transaction date, amount, ID, and wallet reference.
            Check the README.md file for more information.");
        }
        $this->date = $this->getIndex('date', $file1[0], $file2[0]);
        $this->amount = $this->getIndex('amount', $file1[0], $file2[0]);
        $this->transaction_id = $this->getIndex('id', $file1[0], $file2[0]);
        $this->wallet = $this->getIndex('wallet', $file1[0], $file2[0]);
        $this->file1 = $file1;
        $this->file2 = $file2;
    }

    /**
     * Returns the index of the column for a given field
     * @param string $field
     * @param array $header1
     * @param array $header2
     * @return int
     */
    public function getIndex($field, $header1, $header2) {
        //Check field existence in file1
        $index = preg_grep("/$field/i", $header1);
        if(empty($index)) {
            throw new \Exception("Transaction header for {$field} missing in first file.
            Check the README.md file for more information.");
        }
        //Check field existence in file2
        $index = array_keys($index)[0];
        if(empty(preg_grep("/$field/i", [$header2[$index]]))) {
            throw new \Exception("Transaction header for {$field} missing in second file
            or is not in the same position as file 1.
            Check the README.md file for more information.");
        }
        return $index;
    }

    /**
     * Returns the index of the column TransactionDate
     * @return int
     */
    public function getDateIndex() {
        return $this->date;
    }

    /**
     * Returns the index of the column TransactionAmount
     * @return int
     */
    public function getAmountIndex() {
        return $this->amount;
    }

    /**
     * Returns the index of the column WalletReference
     * @return int
     */
    public function getWalletIndex() {
        return $this->wallet;
    }

    /**
     * Receives a file and returns a JSON encoded version
     * with the fields: date, amount, transaction id and wallet reference
     * @param array $file
     */
    protected function fileEncodeAll($file) {
        return (isset($file[$this->date]) && isset($file[$this->amount])
            && isset($file[$this->transaction_id]) && isset($file[$this->wallet])) ?
            json_encode([$file[$this->date],$file[$this->amount],$file[$this->transaction_id],$file[$this->wallet]]) :
            [];
    }

    /**
     * Receives a file and returns a JSON encoded version
     * with the fields: amount, transaction id and wallet reference
     * @param array $file
     */
    protected function fileExcludeDate($file) {
        return (isset($file[$this->amount]) && isset($file[$this->transaction_id]) && isset($file[$this->wallet])) ?
            json_encode([$file[$this->amount],$file[$this->transaction_id],$file[$this->wallet]]) :
            [];
    }

    /**
     * Receives a file and returns a JSON encoded version
     * with the fields: date, amount and wallet reference
     * @param array $file
     */
    protected function fileExcludeTransactionId($file) {
        return (isset($file[$this->date]) && isset($file[$this->amount]) && isset($file[$this->wallet])) ?
            json_encode([$file[$this->date],$file[$this->amount],$file[$this->wallet]]) :
            [];
    }

    /**
     * Returns an array with the unmatched transaction keys on both files
     * with structure ['file1'=>[0,1,2], 'file2'=>[3,4,5]]
     * @return array
     */
    public function unmatchedRows() {
        if(empty($this->file1)&&empty($this->file2)){
            return ['file1'=>[], 'file2'=>[]];
        }
        if(empty($this->file1)){
            return ['file1'=>[], 'file2'=>array_keys($this->file2)];
        }
        if(empty($this->file2)){
            return ['file1'=>array_keys($this->file1), 'file2'=>[]];
        }
        //Encode all rows as JSON
        $file1 = array_map([$this,'fileEncodeAll'], $this->file1);
        $file2 = array_map([$this,'fileEncodeAll'], $this->file2);
        //Find the rows in one file that are not present in the other file
        $diff1 = array_diff($file1, $file2);
        $diff2 = array_diff($file2, $file1);

        return ['file1'=>array_keys($diff1), 'file2'=>array_keys($diff2)];
    }

    /**
     * Returns the first corresponding matching pair of type key:value using a callback filter
     * A matching pair is a value that is present in both files
     * @param array $file1
     * @param array $file2
     * @param string $callback
     */
    public function findPair($file1, $file2, $callback) {
        if(empty($file1) || empty($file2)){
            return [];
        }
        $pair = [];
        $json1 = array_map([$this,$callback], $file1);
        $json2 = array_map([$this,$callback], $file2);
        foreach($json1 as $index=>$row){
            $key = array_search($row, $json2);
            if($key){
                $pair[$index]=$key;
            }
        }
        return $pair;
    }

    /**
     * Returns an array with the transactions that don't match perfectly but are a close match
     * where key=file1-key and value=file2-key
     * @param array $file1
     * @param array $file2
     * @return array
     */
    public function closeMatch($file1, $file2) {
        return
            $this->findPair($file1, $file2, 'fileExcludeTransactionId') +
            $this->findPair($file1, $file2, 'fileExcludeDate');
    }
}