<?php
	@session_start();

	function DBConnect()
	{
		
		// $link = @mysqli_connect('localhost', 'eco', '36Mw@c=vfg');		
		$link = @mysqli_connect('localhost', 'report_admin', 'q1w2e3r4!@');
		if (!$link)
			// die('Could not connect: ' . mysqli_error());
			die('Could not connect: ' . mysqli_connect_error());
		@mysqli_select_db($link, 'report');
		//@mysqli_query("set names utf8");
		mysqli_set_charset($link, 'utf8');
		return $link;
	}

	function SessionCheck()
	{
		global $_SESSION;
		$time = time();

		if(@$_SESSION["report_login_user"]) {
			if($_SESSION["report_login_time"] + 7200 < $time)
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
		$_SESSION["report_login_partIdx"] =0;
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
	function getProjectAll($link){
		if(is_null($link)){ $link = DBConnect();}
		$result = mysqli_query($link, "SELECT * FROM ECO_Project where is_enabled=1 order by IsGame desc, ProjectName asc") or die(mysqli_error($link));

		mysqli_close($link);
		return $result;
	}

	function getMemberAll($link){
		//레벨 3 미만의 멤버만 가져온다
		//관리자 = 99, 팀장님 = 3
		if(is_null($link)){ $link = DBConnect();}
		$result = mysqli_query($link, "SELECT * FROM ECO_Member where visible=1 and levelidx < 3 order by memberidx") or die(mysqli_error($link));

		mysqli_close($link);
		return $result;
	}
	
	// function getStatisticsProject($link, $memberIdx, $year, $month){
	// 	if(SessionCheck() == false) {
	// 		echo json_encode(array("error"=>"SESSION_EXPIRED"));
	// 		return;
	// 	}
		
	// 	if(is_null($link)){ $link = DBConnect();}

	// 	$date_sql = '';
	// 	$member_sql = '';
	// 	$level = $_SESSION["report_login_level"];
		
	// 	if($level<3) {
	// 		$memberIdx = $_SESSION['report_login_userIdx'];
	// 	}

	// 	if(!empty($year) && !empty($month)) {
	// 		$date_sql = "having year='$year' and month='$month'";
	// 	} else if(!empty($year)) {
	// 		$date_sql = "having year='$year'";
	// 	} else if(!empty($month)) {
	// 		echo isset($month);
	// 		$date_sql = "having year='$month'";
	// 	}

	// 	if(!empty($memberIdx)){
	// 		$member_sql = "where memberidx=$memberIdx ";
	// 	}

	// 	$sql = "SELECT er.projectidx, year(er.work_d) as year, month(er.work_d) as month, 
	// 	sum(er.work_h) as hour, ep.projectname 
	// 	from ECO_Reports as er 
	// 	inner join ECO_Project as ep on er.projectidx = ep.ProjectIdx 
	// 	$member_sql 
	// 	group by projectidx, year, month, projectname 
	// 	$date_sql 
	// 	order by year desc, month desc, projectidx asc";
	// 	//having year='$year' and month='$month'  
	// 	$result = mysqli_query($link, $sql) or die(mysqli_error($link));

	// 	mysqli_close($link);		
	// 	return mysqli_result_to_json($result);
  // }
  
  // function getStatisticsProject_bak($link, $memberIdx, $year, $month){
	// 	if(is_null($link)){ $link = DBConnect();}

	// 	$date_sql = '';
	// 	$member_sql = '';

	// 	if(!empty($year) && !empty($month)) {
	// 		$date_sql = "having year='$year' and month='$month'";
	// 	} else if(!empty($year)) {
	// 		$date_sql = "having year='$year'";
	// 	} else if(!empty($month)) {
	// 		echo isset($month);
	// 		$date_sql = "having year='$month'";
	// 	}

	// 	if(!empty($memberIdx)){
	// 		$member_sql = "where memberidx=$memberIdx ";
	// 	}

	// 	$sql = "SELECT projectidx, year, month, sum(work_h) as hour, projectname from
	// 	(SELECT ern.projectidx, year(ern.work_d) as year, month(ern.work_d) as month, 
	// 	sum(ern.work_h) as work_h, ep.projectname 
	// 	from ECO_Reports_New as ern 
	// 	inner join ECO_Project as ep on ern.projectidx = ep.ProjectIdx 
	// 	$member_sql 
	// 	group by projectidx, year, month, projectname 
	// 	union all 
	// 	SELECT er.projectidx, year(er.work_d) as year, month(er.work_d) as month, 
	// 	sum(er.work_h) as work_h, ep.projectname 
	// 	from ECO_Reports as er 
	// 	inner join ECO_Project as ep on er.projectidx = ep.ProjectIdx 
	// 	$member_sql 
	// 	group by projectidx, year, month, projectname) as t 
	// 	group by projectidx, year, month, projectname 
	// 	$date_sql 
	// 	order by year desc, month desc, projectidx asc";
	// 	//having year='$year' and month='$month'  
	// 	$result = mysqli_query($link, $sql) or die(mysqli_error($link));

	// 	mysqli_close($link);		
	// 	return mysqli_result_to_json($result);
	// }
	
	function mysqli_result_to_json($result){
		try {
			$rows = array();
			while ( $row = $result->fetch_assoc()) {
				$rows[]=$row;
			}
			return json_encode($rows);
		} catch (Throwable $e) {
			//$msg = $e->getMessage();
			return json_encode(array("error"=>$e->getMessage(), "result"=>$result));
		}		
	}

	// function insertReport($link, $data){
  //   if(is_null($link)){ $link = DBConnect();}

	// 	$memberIdx = $_SESSION['report_login_userIdx'];    
    
	// 	$sql = "INSERT INTO ECO_Reports (memberidx, work_d, work_h, projectidx, report, iscomplete) values ";
	
	// 	/*
	// 	싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
	// 	row가 여러개니 벌크로 insert 한다
	// 	위의 이유로 sprint로 sql을 만들어서 쿼리한다
	// 	*/
	// 	$str_format = "(%d, '%s', %f, %d, '%s', 1), ";
	// 	foreach($data as $row){      
	// 	  $sql .= sprintf($str_format,$memberIdx, $row["work_date"], $row["work_hour"], $row["project_id"], @addslashes($row["content"]));
	// 	}
	// 	$sql = substr($sql, 0, strlen($sql) - 2);  
  //   $result = mysqli_query($link, $sql);

  //   //mysql error
  //   if(!$result) $result = mysqli_error($link);

  //   mysqli_close($link);
  //   return json_encode(array("result"=>$result));
  // }

  function insertReport_bak($link, $data){
    if(is_null($link)){ $link = DBConnect();}

		$memberIdx = $_SESSION['report_login_userIdx'];
    
		$sql = "INSERT INTO ECO_Reports_New (memberidx, work_d, work_h, projectidx, content) values ";
	
		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		row가 여러개니 벌크로 insert 한다
		위의 이유로 sprint로 sql을 만들어서 쿼리한다
		*/
		$str_format = "(%d, '%s', %f, %d, '%s'), ";        
		foreach($data as $row){      
		  $sql .= sprintf($str_format,$memberIdx, $row["work_date"], $row["work_hour"], $row["project_id"], @addslashes($row["content"]));
		}
		$sql = substr($sql, 0, strlen($sql) - 2);  
		$result = mysqli_query($link, $sql) or die(mysqli_error($link));
		
    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }
  
  // function getReportsFromDate($link, $data){
	// 	if(empty($_SESSION['report_login_user'])) {
	// 		echo json_encode(array("error"=>"SESSION_EXPIRED"));			
	// 		return;
	// 		exit();
	// 	}

  //   if(is_null($link)){ $link = DBConnect();}

  //   $memberIdx = $_SESSION['report_login_userIdx'];

  //   $sql = sprintf("SELECT er.reportidx, er.memberidx, er.work_d, er.work_h, er.report, ep.projectname 
  //                 FROM ECO_Reports as er INNER JOIN ECO_Project as ep
  //                 ON er.projectidx=ep.projectidx 
  //                 WHERE er.work_d between '%s' and '%s' and er.memberidx=%d 
  //                 order by er.work_d desc", 
  //                 $data['from'], $data['to'], $memberIdx);

  //   $result = mysqli_query($link, $sql);

  //   if(!$result) $result = mysqli_error($link);

  //   mysqli_close($link);
  //   return mysqli_result_to_json($result);
  // }

  // function deleteReport($link, $data) {
  //   if(is_null($link)){ $link = DBConnect();}

  //   $memberIdx = $_SESSION['report_login_userIdx'];

  //   $sql = sprintf("DELETE FROM ECO_Reports where reportidx=%d and memberidx=%d", $data["reportIdx"], $memberIdx);
    
  //   $result = mysqli_query($link, $sql);
  //   if(!$result) $result = mysqli_error($link);

  //   mysqli_close($link);
  //   return json_encode(array("result"=>$result));
	// }
	
	function getReportsFromMemberWithDate($link, $data) {
		if(is_null($link)){ $link = DBConnect();}

		$member_sql = '';
		if(!empty($data["memberIdx"])){
			$member_sql = "and er.memberidx=$data[memberIdx] ";
		}

		// 파트장님들은 해당 파트만 볼수있음
		$partIdx = $_SESSION["report_login_partIdx"];
		$part_sql = '';
		if($partIdx!=1){ 
			$part_sql = "and em.partIdx=$partIdx"; 
		}

		$sql = sprintf("SELECT er.reportidx, er.memberidx, er.work_d, er.work_h, er.report, 
										ep.projectname, em.membername
										FROM ECO_Reports as er 
										INNER JOIN ECO_Project as ep ON er.projectidx=ep.projectidx 
										INNER JOIN ECO_Member as em ON er.memberidx=em.memberidx
										WHERE er.work_d between '%s' and '%s' %s %s
										order by em.membername asc, er.work_d asc", 
										$data['from'], $data['to'], $member_sql, $part_sql);
    
    $result = mysqli_query($link, $sql);
		if(!$result) $result = mysqli_error($link);
		
		mysqli_close($link);
    return mysqli_result_to_json($result);
	}
	
?>


