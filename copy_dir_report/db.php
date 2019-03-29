<?php
	@session_start();

	function DBConnect()
	{
		
		$link = @mysql_connect('localhost', 'eco', '36Mw@c=vfg');
		if (!$link)
			die('Could not connect: ' . mysql_error());
		@mysql_select_db('report', $link);
		@mysql_query("set names utf8");
		return $link;
	}

	function SessionCheck()
	{
		global $_SESSION;
		$time = time();

		if(@$_SESSION["report_login_user"]) {
			if($_SESSION["report_login_time"] + 3600 < $time)
				return false;
			$_SESSION["report_login_time"] = $time;
			return true;
		}
		return false;
	}

	function SessionClose()
	{
		global $_SESSION;
		$_SESSION["report_login_user"] = FALSE;
		$_SESSION["report_login_time"] = 0;
		$_SESSION["report_login_level"] = 0;
		$_SESSION["report_login_userIdx"] =0;
	}

	function GetReportDate()				//팀원이 보고하는 날
	{
		date_default_timezone_set('Asia/Seoul');
		$now = Date("y-m-d"); 
		$week = Date('w',strtotime($now));
		if($week == 1)   $reportDate = date('Y-m-d',strtotime($now.'+3 days'));
		else if($week == 2) $reportDate = date('Y-m-d',strtotime($now.'+2 days'));
		else if($week == 3) $reportDate = date('Y-m-d',strtotime($now.'+1 days'));
		else if($week == 4) $reportDate = date('Y-m-d',strtotime($now.'+0 days'));
		else if($week == 5) $reportDate = date('Y-m-d',strtotime($now.'+6 days'));
		else if($week == 6) $reportDate = date('Y-m-d',strtotime($now.'+5 days'));
		else $reportDate = date('Y-m-d',strtotime($now.'+4 days'));

		return $reportDate;
	}

	function GetReportSummaryDate() 		//팀장님이 취합하는 날
	{
		date_default_timezone_set('Asia/Seoul');
		$now = Date("y-m-d"); 
		$week = Date('w',strtotime($now));
		if($week == 1)   $reportDate = date('Y-m-d',strtotime($now.'+3 days'));
		else if($week == 2) $reportDate = date('Y-m-d',strtotime($now.'+2 days'));
		else if($week == 3) $reportDate = date('Y-m-d',strtotime($now.'+1 days'));
		else if($week == 4) $reportDate = date('Y-m-d',strtotime($now.'+0 days'));
		else if($week == 5) $reportDate = date('Y-m-d',strtotime($now.'-1 days'));
		else if($week == 6) $reportDate = date('Y-m-d',strtotime($now.'-2 days'));
		else $reportDate = date('Y-m-d',strtotime($now.'-3 days'));

		return $reportDate;
	}
?>


