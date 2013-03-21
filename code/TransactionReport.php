<?php
/**
 * Show all payment transaction data - specific to DPS
 * @package payment
 */
class TransactionReport extends SS_Report {
	function title() {
		return 'Transaction Report';
	}
	
	function columns() {
		$fields = array(
			'TxnData1'				=> array(
					'title'		=> 'ID#'					
					),
			'LastEdited'		=> array(
					'title'		=> 'Date/Time',
					'casting'	=> 'SS_Datetime->Nice24'
					),
			'MerchantReference' => 'Reference',
			'SettlementAmount'	=> array(
					'title'		=> "Amount",
					'casting'	=> 'Currency->Nice'
					),
			'FeeAmount'			=> array(
					'title'		=> "Fee",
					'casting'	=> 'Currency->Nice'
					),
			'CardName'			=> 'Card',
			'PersonalDetails'	=> 'PersonalDetails',
			'EmailAddress'		=> array(
					'title'		=> 'Email',
					'formatting'=> '<a target=\"_blank\" href=\"mailto:$value\">$value</a>'
					),
			'DpsTxnRef'			=> 'DpsRef',
			'ReceivedAsFPRN'	=>	array(
					'title'		=> 'Received as FPRN?',
					'casting'	=> 'Boolean->Nice',
					),
			);
		return $fields;
	}
	
	/*
	 * @todo use DataList()->filter() expressions - they're neater.
	 */
	function sourceRecords($params, $sort, $limit) {

		$records = RecordedTransactions::get();

		$where = array();
		$where[] = "\"RecordedTransactions\".\"Success\" = 1";
		if (!empty($params['SearchName'])) {
			$where[] = "\"RecordedTransactions\".\"PersonalDetails\" LIKE '%".$params['SearchName']."%'";
		}
		
		$startDate = isset($params['StartDate']) ? $params['StartDate'] : null;
		$endDate = isset($params['EndDate']) ? $params['EndDate'] : null;
		$startTime = isset($params['StartTime']) ? $params['StartTime'] : null;
		$endTime = isset($params['EndTime']) ? $params['EndTime'] : null;

		if($startDate) {
			if(count(explode('/', $startDate)) == 3) {
				list($d, $m, $y) = explode('/', $startDate);
				$startTime = $startTime ? $startTime : '00:00:00';
				$startDate = date('Y-m-d H:i:s', strtotime("$y-$m-$d {$startTime}"));
			}
			else {
				$startDate = null;
			}
		}
		if($endDate) {
			if(count(explode('/', $endDate)) == 3) {
				list($d, $m, $y) = explode('/', $endDate);
				$endTime = $endTime ? $endTime : '23:59:59';
				$endDate = date('Y-m-d H:i:s', strtotime("$y-$m-$d {$endTime}"));
			}
			else {
				$endDate = null;
			}
		}

		if ($startDate && $endDate) {
			$where[] = "\"RecordedTransactions\".\"LastEdited\" >= '".Convert::raw2sql($startDate)."' AND \"RecordedTransactions\".\"LastEdited\" <= '".Convert::raw2sql($endDate)."'";
		}
		else if ($startDate && !$endDate) {
			$where[] = "\"RecordedTransactions\".\"LastEdited\" >= '".Convert::raw2sql($startDate)."'";
		}
		else if (!$startDate && $endDate) {
			$where[] = "\"RecordedTransactions\".\"LastEdited\" <= '".Convert::raw2sql($endDate)."'";
		}
		else {
			$where[] = "\"RecordedTransactions\".\"LastEdited\" >= '".SS_Datetime::now()->Rfc2822()."'";
		}
		
		// Default sort to Date published descending
		$sort = (!$sort) ? 'LastEdited DESC' : $sort;
		
		// Turn a query into records
		if($sort) {
			$parts = explode(' ', $sort);
			$field = $parts[0];
			$direction = $parts[1];
			
			if($field == 'AbsoluteLink') {
				$sort = '"URLSegment" ' . $direction;
			}
			if($field == '"Subsite"."Title"') {
				$records->leftJoin('Subsite', '"Subsite"."ID" = "SiteTree"."SubsiteID"');
			}
		}

		$records->where($where);
		$records->sort($sort);
		$records->limit($limit['limit'],$limit['start']);
		return $records;
	}
	
	function parameterFields() {
		$fields = new FieldList();
		
		$fields->push(new TextField("SearchName", "Name"));
		$fields->push($startDate = new DateField('StartDate', 'Start date', date('d/m/Y')));
		$fields->push($startTime = new TimeField('StartTime', 'Start time', date('H:i:s')));
		$fields->push($endDate = new DateField('EndDate', 'End date', date('d/m/Y')));
		$fields->push($endTime = new TimeField('EndTime', 'End time', date('H:i:s')));

		$startDate->setConfig('showcalendar', 1);
		$endDate->setConfig('showcalendar', 1);
		$startDate->setConfig('dateformat', 'dd/mm/YYYY');
		$endDate->setConfig('dateformat', 'dd/mm/YYYY');
		
		return $fields;
	}
}
?>