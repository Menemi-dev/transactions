<?php

use PHPUnit\Framework\TestCase;
use Transactions\Modules\FileComparison;

final class FileComparisonTest extends TestCase {

    public function testGetIndex() {
        //Same header
        $f1 = [['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference']];
        $f2 = [['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference']];
        $fc = new FileComparison($f1, $f2);
        $this->assertSame(1, $fc->getIndex('date', $f1[0], $f2[0]));

        //Field ID missing in first file
        try{
            $f1 = [['TransactionDate','TransactionAmount','WalletReference']];
            $f2 = [['TransactionDate','TransactionAmount','TransactionID','WalletReference']];
            $fc = new FileComparison($f1, $f2);
            $this->fail("Expected exception not thrown");
        }catch(Exception $e){
            $this->assertEquals("Transaction header for id missing in first file.
            Check the README.md file for more information.", $e->getMessage());
        }
    }

    public function testUnmatchedRows() {
        //One unmatching row
        $fc2 = new FileComparison(
            [
                ['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
                ['Card Campaign','2014-01-11 22:27:44','-20000','*MOLEPS ATM25  MOLEPOLOLE  BW','DEDUCT','0584011808649511','1','P_NzI2ODY2ODlfMTM4MjcwMTU2NS45MzA5'],
                ['Card Campaign','2014-01-12 12:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','0284012437519375','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
            ],
            [
                ['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
                ['Card Campaign','2014-01-11 22:27:44','-20000','*MOLEPS ATM25  MOLEPOLOLE  BW','DEDUCT','0584011808649511','1','P_NzI2ODY2ODlfMTM4MjcwMTU2NS45MzA5'],
                ['Card Campaign','2014-01-12 14:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','0284012437519375','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
            ]);
        $this->assertSame(['file1'=>[2],'file2'=>[2]], $fc2->unmatchedRows());

        //File with missing column
        try{
            new FileComparison(
                [
                    ['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
                    ['Card Campaign','2014-01-11 22:27:44','-20000','*MOLEPS ATM25  MOLEPOLOLE  BW','DEDUCT','0584011808649511','1','P_NzI2ODY2ODlfMTM4MjcwMTU2NS45MzA5'],
                    ['Card Campaign','2014-01-12 12:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','0284012437519375','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
                ],
                [
                    ['TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
                    ['2014-01-11 22:27:44','-20000','*MOLEPS ATM25  MOLEPOLOLE  BW','DEDUCT','0584011808649511','1','P_NzI2ODY2ODlfMTM4MjcwMTU2NS45MzA5'],
                    ['2014-01-12 14:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','0284012437519375','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
                ]);
            $this->fail("Expected exception not thrown");
        }catch(Exception $e){
            $this->assertEquals("Transaction header for date missing in second file
            or is not in the same position as file 1.
            Check the README.md file for more information.", $e->getMessage());
        }
    }

    public function testFindPair() {
        //One different value in row
        $file1 = [
            ['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
            ['Card Campaign','2014-01-12 14:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','111111111111111111','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
        ];
        $file2 = [
            ['ProfileName','TransactionDate','TransactionAmount','TransactionNarrative','TransactionDescription','TransactionID','TransactionType', 'WalletReference'],
            ['Card Campaign','2014-01-11 22:27:44','-20000','*MOLEPS ATM25  MOLEPOLOLE  BW','DEDUCT','0584011808649511','1','P_NzI2ODY2ODlfMTM4MjcwMTU2NS45MzA5'],
            ['Card Campaign','2014-01-12 14:06:11','-7440','Choppies Thamaga102339  Lobatse  BW','DEDUCT','0284012437519375','0','P_NzI2MDk4NTdfMTM4NzYxMjYyOS4wNDEy']
        ];

        $fc = new FileComparison($file1, $file2);
        $this->assertSame([1=>2], $fc->findPair($file1, $file2, 'fileExcludeTransactionId'));
    }
}