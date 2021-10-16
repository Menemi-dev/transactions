# Transactions

The concept behind the project is to perform a financial reconciliation between two different sets of data.

## Description

The workflow starts by receiving two sets of data as CSV files. These files will be compared, and generate a report on how many transactions match perfectly, versus transactions that cannot be matched.
Those transactions which cannot be matched will be reported on so that a third party could refer to the report and investigate the exceptions.
If a transaction cannot be matched perfectly, the system will look for any close matches and suggest them as possibilities.

A perfect match is when the values transaction id, date, amount, and wallet reference are the same in both files.

A close match is when the amount AND wallet reference are the same in both files and the transaction id OR date also match.

### Notes
* Duplicate rows will be counted together as matched or unmatched
* Only CSV files are accepted
* Files should include a header with
**TransactionDate,TransactionAmount,TransactionID,WalletReference**
* If any of these fields are missing or the column order is different on both files, the files will be considered incorrect and will not be processed.

## Getting Started

### Dependencies

* PHP >= 7.4.3

### Executing program

* In the root folder run
```
php -S localhost:8000
```
* Go to http://localhost:8000/
* Select 2 CSV files with a header and the fields:
**TransactionDate,TransactionAmount,TransactionID,WalletReference**
* Click **Compare**

## Testing

* In the root folder run
```
composer update
```
* Run
```
./vendor/bin/phpunit tests
```