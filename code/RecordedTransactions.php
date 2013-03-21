<?php
/*
 * Allows to store additional DPS specifc data in a dedicated table
 */
class RecordedTransactions extends DataObject {
	static $db = array(
		'Success'			=> 'Int',
		'MerchantTxnID'		=> 'Varchar(255)',
		'UserID'			=> 'Varchar(255)',
		'MerchantReference'	=> 'Varchar(255)',
		'CurrencyInput'		=> 'Varchar(255)',
		'CardName'			=> 'Varchar(255)',
		'Settlement'		=> 'Money',
		'Fee'				=> 'Money',
		'AuthCode'			=> 'Int',
		'TxnData1'			=> 'Int',
		'PersonalDetails'	=> 'Varchar(255)',
		'EmailAddress'		=> 'Varchar(255)',
		'BillingId'			=> 'Varchar(255)',
		'DpsBillingId'		=> 'Varchar(255)',
		'DpsTxnRef'			=> 'Varchar(255)',
		'ReceivedAsFPRN'	=> 'Boolean',
	);

	static $indexes = array(
		'MerchantReference'=>true,
		'PersonalDetails'=>true
	);
}
?>