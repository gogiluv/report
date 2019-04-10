<?php
  include "db.php";
  //get
  if(!empty($_GET)){
    $f_name = $_GET["f_name"];
    $data = isset($_GET["data"]) ? $_GET["data"] : null;
				
		echo call_user_func($f_name, $data);
    return;
  }
  
	//post
	if(!empty($_POST)){
    $f_name = $_POST["f_name"];
    $data = isset($_POST["data"]) ? $_POST["data"] : null;
    
		echo call_user_func($f_name, $data);
    return;
  }

  /*
    하단에 함수들을 정의해서 사용한다.
    $data 전달받아서 사용
  */
	function getReportFromId($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();

    $reportIdx = $data["reportIdx"];

		$sql = sprintf("SELECT er.ReportIdx, er.MemberIdx, er.work_d, er.work_h, er.ProjectIdx, er.create_dt, er.Report, ep.ProjectName FROM ECO_Reports as er
                    inner join ECO_Project as ep
                    on  er.projectidx = ep.projectidx
                    WHERE reportIdx=%d", $reportIdx);
    
    $result = mysqli_query($link, $sql);
    if(!$result) $result = mysqli_error($link);
    // id는 pk이기때문에 하나밖에 없다
    // fetch_assoc으로 한행만 뽑음
    $row = $result->fetch_assoc();

		mysqli_close($link);    
    return json_encode($row);
  }

  function getLastReportFromProject($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();

    $projectIdx = $data["projectIdx"];
    $memberIdx = $_SESSION['report_login_userIdx'];

		$sql = sprintf("SELECT * FROM ECO_Reports as er 
                    inner join ECO_Project as ep 
                    on er.projectidx = ep.projectidx
                    WHERE er.projectidx=%d and er.memberIdx=%d
                    order by er.work_d desc, er.create_dt desc
                    limit 1", $projectIdx, $memberIdx);
    
    $result = mysqli_query($link, $sql);
    $row = null;

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

    // 마지막 제출분만 필요함
    // fetch_assoc으로 한행만 뽑음
    $row = $result->fetch_assoc();
        

		mysqli_close($link);    
    return json_encode($row);
  }
  function getProjects(){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();

		$sql = "SELECT * FROM ECO_Project WHERE is_enabled=1
            order by isgame asc, projectname asc";
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return mysqli_result_to_json($result);
  }

  function getGameProjects(){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();

		$sql = "SELECT * FROM ECO_Project WHERE isgame=1
            order by projectname asc, projectidx desc";
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return mysqli_result_to_json($result);
  }

  function getEtcProjects(){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();

		$sql = "SELECT * FROM ECO_Project WHERE isgame=0
            order by projectname asc, projectidx desc";
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return mysqli_result_to_json($result);
  }
  function setProjectStatusChange($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();
    $is_enabled = $data["is_enabled"];
    $projectIdx = $data["projectIdx"];

		$sql = sprintf("UPDATE ECO_Project SET is_enabled=%d WHERE projectidx=%d", $is_enabled,$projectIdx);
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return json_encode(array("result"=>$result));
  }

  function createProject($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $link = DBConnect();
    $isGame = $data["isGame"];
    $projectName = $data["projectName"];

		$sql = sprintf("INSERT INTO ECO_Project(isGame, projectName) values (%d, '%s')", $isGame,$projectName);
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return json_encode(array("result"=>$result));
  }

  function updateReport($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

		$memberIdx = $_SESSION['report_login_userIdx'];
    
		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		*/		
		   
	  $sql = sprintf("UPDATE ECO_Reports SET work_h=%f, projectidx=%d, report='%s' 
                    where reportidx=%d and memberidx=%d", 
                    $data["work_hour"], $data["project_id"], 
                    @addslashes($data["content"]), $data["report_id"], $memberIdx);		
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }

  function getMembersForSummary(){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

		//레벨 3 미만의 멤버만 가져온다
    //관리자 = 99, 팀장님 = 3
    $memberIdx = $_SESSION['report_login_userIdx'];
    $partIdx = $_SESSION["report_login_partIdx"];
    
    $part_sql = '';
    if($partIdx!=1){ $part_sql = sprintf("and partIdx=%d", $partIdx); }

    $sql = sprintf("SELECT * FROM ECO_Member where visible=1 and levelidx < 3 %s order by memberidx", $part_sql);
    
		$link = DBConnect();
		$result = mysqli_query($link, $sql);

		mysqli_close($link);
		return mysqli_result_to_json($result);
  }
  
  function getReportStatus($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

    $year = $data["year"];
    $month = $data["month"];		
		
		$date_sql = '';		
		$level = $_SESSION["report_login_level"];
		
		if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
		}

		if(!empty($year) && !empty($month)) {
			$date_sql = "having year='$year' and month='$month'";
		} else if(!empty($year)) {
			$date_sql = "having year='$year'";
		} else if(!empty($month)) {			
			$date_sql = "having month='$month'";
		}

		$sql = "SELECT er.projectidx, year(er.work_d) as year, month(er.work_d) as month, 
		sum(er.work_h) as hour, ep.projectname 
		from ECO_Reports as er 
		inner join ECO_Project as ep on er.projectidx = ep.ProjectIdx 
		$member_sql 
		group by projectidx, year, month, projectname 
		$date_sql 
		order by year desc, month desc, projectidx asc";
    //having year='$year' and month='$month'
    $link = DBConnect();
		$result = mysqli_query($link, $sql);

		mysqli_close($link);		
		return mysqli_result_to_json($result);    
  }

  function getPastReports($data){
		if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }
    
    $memberIdx = $_SESSION['report_login_userIdx'];

    $sql = sprintf("SELECT er.reportidx, er.memberidx, er.work_d, er.work_h, er.report, ep.projectname 
                  FROM ECO_Reports as er INNER JOIN ECO_Project as ep
                  ON er.projectidx=ep.projectidx 
                  WHERE er.work_d between '%s' and '%s' and er.memberidx=%d 
                  order by er.work_d desc", 
                  $data['from'], $data['to'], $memberIdx);

    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return mysqli_result_to_json($result);
  }

  function deleteReport($data) {
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;
    }

    $memberIdx = $_SESSION['report_login_userIdx'];
    $sql = sprintf("DELETE FROM ECO_Reports where reportidx=%d and memberidx=%d", $data["reportIdx"], $memberIdx);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }
  
  function getStatisticsProject($data){
		if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }
    
    $memberIdx = $data["memberIdx"];
    $year = $data["year"];
    $month = $data["month"];
		
		$date_sql = '';
		$member_sql = '';
		$level = $_SESSION["report_login_level"];
		
		if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
		}

		if(!empty($year) && !empty($month)) {
			$date_sql = "having year='$year' and month='$month'";
		} else if(!empty($year)) {
			$date_sql = "having year='$year'";
		} else if(!empty($month)) {
			echo isset($month);
			$date_sql = "having year='$month'";
		}

		if(!empty($memberIdx)){
			$member_sql = "where memberidx=$memberIdx ";
		}

		$sql = "SELECT er.projectidx, year(er.work_d) as year, month(er.work_d) as month, 
		sum(er.work_h) as hour, ep.projectname 
		from ECO_Reports as er 
		inner join ECO_Project as ep on er.projectidx = ep.ProjectIdx 
		$member_sql 
		group by projectidx, year, month, projectname 
		$date_sql 
		order by year desc, month desc, projectidx asc";
    //having year='$year' and month='$month'  
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    
    if(!$result) $result = mysqli_error($link);

		mysqli_close($link);		
		return mysqli_result_to_json($result);
  }

	function insertReport($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

		$memberIdx = $_SESSION['report_login_userIdx'];    
    
		$sql = "INSERT INTO ECO_Reports (memberidx, work_d, work_h, projectidx, report, iscomplete) values ";
	
		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		row가 여러개니 벌크로 insert 한다
		위의 이유로 sprint로 sql을 만들어서 쿼리한다
		*/
		$str_format = "(%d, '%s', %f, %d, '%s', 1), ";
		foreach($data as $row){      
		  $sql .= sprintf($str_format,$memberIdx, $row["work_date"], $row["work_hour"], $row["project_id"], @addslashes($row["content"]));
		}
    $sql = substr($sql, 0, strlen($sql) - 2);
    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }
  function getReportedMember_bak($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }
    
		$sql = sprintf("SELECT em.MemberName, er.work_d, year(er.work_d) as year, month(er.work_d) as month 
    from ECO_Reports as er inner join ECO_Member as em on er.MemberIdx=em.MemberIdx 
    GROUP by em.MemberName, er.work_d having work_d BETWEEN '%s' and '%s' order by er.work_d desc", $data['from'], $data['to']);
    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return mysqli_result_to_json($result);
  }

  function getMembers(){
		//레벨 3 미만의 멤버만 가져온다
    //관리자 = 99, 팀장님 = 3		
    $link = DBConnect();
		$result = mysqli_query($link, "SELECT * FROM ECO_Member where visible=1 and levelidx < 3 order by memberidx");    
    //mysql error    
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    
		return mysqli_result_to_json($result);
  }
  
  function getReportedMember($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }
    
		$report_sql = sprintf("SELECT em.MemberName, er.work_d, year(er.work_d) as year, month(er.work_d) as month 
    from ECO_Reports as er inner join ECO_Member as em on er.MemberIdx=em.MemberIdx 
    GROUP by em.MemberName, er.work_d having work_d BETWEEN '%s' and '%s' order by er.work_d desc", $data['from'], $data['to']);
    
    $link = DBConnect();
    $report_result = mysqli_query($link, $report_sql);

    //mysql error
    if(!$report_result) {
      $report_result = mysqli_error($link);
      return mysqli_result_to_json($report_result);
    }

    //레벨 3 미만의 멤버만 가져온다
    //관리자 = 99, 팀장님 = 3		
		$member_result = mysqli_query($link, "SELECT membername FROM ECO_Member where visible=1 and levelidx < 3 order by memberidx");    
    //mysql error    
    if(!$member_result) {
      $member_result = mysqli_error($link);
      return mysqli_result_to_json($member_result);
    }

    $member_arr = array();
    while($member = $member_result->fetch_assoc()){
      $member_arr[] = $member['membername'];
    }

    $from = new DateTime($data['from']);
    $to = new DateTime($data['to']);
    $day = array("일","월","화","수","목","금","토");
    $reported_arr = array();

    // for($to; $to>=$from; date_modify($to, '-1 day')){
    //   $reported_arr[date_format($to, 'Y-m-d')] = "yeeeeeeeee";
    // }

    //날자 배열 세팅
    for($from; $from<=$to; date_modify($from, '+1 day')){
      $reported_arr[date_format($from, 'Y-m-d')] = array('reported'=>[], 'unreported'=>[], 'day'=> $day[date_format($from, 'w')]);
    }

    //정렬, 날짜 내림차순
    //uksort($reported_arr, 'dateSort');
    krsort($reported_arr);

    //set reported user
    while ( $row = $report_result->fetch_assoc()) {      
      $reported_arr[$row['work_d']]['reported'][] = $row['MemberName'];      
    }
    //set unreported user
    foreach($reported_arr as $key => $value){
      // 보고서 안쓴사람 = 전체 멤버 - 작성한 멤버
      // 아무도 안썼을 경우 체크해서 멤버 전체 배열 넣음
      if(isset($value['reported'])){
        $reported_arr[$key]['unreported'] = array_values(array_diff($member_arr, $value['reported']));
      }else{
        $reported_arr[$key]['unreported'] = $member_arr;
      }
    }

    mysqli_close($link);
    return json_encode($reported_arr);
  }

  function setDraft($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $memberIdx = $_SESSION['report_login_userIdx'];
    
		$sql = sprintf("INSERT INTO ECO_Draft (memberidx, draft) values (%d, '%s')", $memberIdx, $data);
	
    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }

  function getDraft(){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $memberIdx = $_SESSION['report_login_userIdx'];    
    
		$sql = sprintf("SELECT draft FROM ECO_Draft WHERE memberidx=%d order by created_dt desc limit 1", $memberIdx);
	
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);

    //return mysqli_result_to_json($result);

    $draft = $result->fetch_assoc();  //limit 1이기 때문에 한번만 수행하면 된다.
    return json_encode(json_decode($draft['draft'], true));
  }

  function deleteDraft(){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $memberIdx = $_SESSION['report_login_userIdx'];    
    
		$sql = sprintf("DELETE FROM ECO_Draft WHERE memberidx=%d", $memberIdx);
	
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }


?>