<?php

require_once __DIR__.'/vendor/autoload.php';
use Transactions\Modules\FileManagement;
use Transactions\Modules\FileComparison;

/**
 * Structures file data to display in the report table
 * @param array $indList
 * @param array $file
 * @param int $date
 * @param int $amount
 * @param int $wallet
 * @return array
*/
function structureData($indList, $file, $date, $amount, $wallet) {
    $data = [];
    foreach( $indList as $index){
        array_push($data,[
            'key' => $index,
            'date' => $file[$index][$date],
            'reference' => $file[$index][$wallet],
            'amount' => $file[$index][$amount]
            ]
        );
    }
    return $data;
}

/**
 * Catches the form POST and returns compared files
 */
if (isset($_FILES)){
    $fm1 = new FileManagement($_FILES['file1']);
    $fm2 = new FileManagement($_FILES['file2']);
    $file1 = $fm1->getArrayFile();
    $file2 = $fm2->getArrayFile();
    try{
        $fc = new FileComparison($file1, $file2);
        $unmatched = $fc->unmatchedRows();
        $unmatchedf1 = structureData($unmatched['file1'], $file1, $fc->getDateIndex(), $fc->getAmountIndex(), $fc->getWalletIndex());
        $unmatchedf2 = structureData($unmatched['file2'], $file2, $fc->getDateIndex(), $fc->getAmountIndex(), $fc->getWalletIndex());
        $closeMatch = $fc->closeMatch($fm1->getRowsById($unmatched['file1']), $fm2->getRowsById($unmatched['file2']));
        echo json_encode([
            'file1' => [
                'total' => sizeof($file1)-1,//remove header from total
                'unmatched' => sizeof($unmatched['file1']),
            ],
            'file2' => [
                'total' => sizeof($file2)-1,//remove header from total
                'unmatched' => sizeof($unmatched['file2']),
            ],
            'unmatched1' => $unmatchedf1,
            'unmatched2' => $unmatchedf2,
            'closematch' => $closeMatch
        ]);
    }catch(Exception $e){
        header('HTTP/1.1 400 Bad Request');
        die($e->getMessage());
    }
}