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

		$sql = sprintf("SELECT er.ReportIdx, er.MemberIdx, er.work_d, er.work_h, 
                    er.ProjectIdx, er.create_dt, er.Report, ep.ProjectName FROM ECO_Reports as er
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

    $level = $_SESSION["report_login_level"];
    $memberIdx = $_SESSION['report_login_userIdx'];

    if($level>2) {
			$member_sql = '';
    } else {
      $member_sql = sprintf('and memberidx=%d', $memberIdx);
    }

		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		*/		
		   
	  $sql = sprintf("UPDATE ECO_Reports SET work_h=%f, projectidx=%d, report='%s' 
                    where reportidx=%d %s", 
                    $data["work_hour"], $data["project_id"], 
                    @addslashes($data["content"]), $data["report_id"], $member_sql);		
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

    // 2레벨 이상이어야한다, 파트장님 = 2레벨
    $level = $_SESSION["report_login_level"];
		
		if($level<2) {
      echo json_encode(array("error"=>"authentification error"));
			return;
		}

		//레벨 3 미만의 멤버만 가져온다
    //관리자 = 99, 팀장님 = 3
    $memberIdx = $_SESSION['report_login_userIdx'];
    $partIdx = $_SESSION["report_login_partIdx"];
    
    $part_sql = '';
    if($partIdx!=1 && $partIdx!=5){ $part_sql = sprintf("and partIdx=%d", $partIdx); }

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

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }

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

    $level = $_SESSION["report_login_level"];
    $memberIdx = $_SESSION['report_login_userIdx'];

    if($level>2) {
			$member_sql = '';
    } else {
      $member_sql = sprintf('and memberidx=%d', $memberIdx);
    }

    $sql = sprintf("DELETE FROM ECO_Reports where reportidx=%d", $data["reportIdx"], $member_sql);
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

  function getMembers($data){
    $visible_sql = "";
    
    if(isset($data['visible']) && $data['visible']!=null){
      $visible_sql = sprintf("and visible=%d", $data['visible']);
    }

    $memberName_sql = "";
    if(isset($data['memberName'])){
      $memberName_sql = sprintf("and memberName like '%%%s%%'", addslashes($data['memberName']));
    }
    // 본인 레벨 이하만 가져온다, 관리자만 관리자 정보 확인, 수정할 수 있게 하기 위함
    // 관리자 = 99, 팀장님 = 3
    $level = $_SESSION["report_login_level"];

    $link = DBConnect();
    $sql = sprintf("SELECT MemberIdx, MemberName, LevelIdx, Visible, pos.name as Position, par.name as Part
                    FROM ECO_Member as em 
                    inner join ECO_Position as pos on em.positionidx = pos.positionidx
                    inner join ECO_Part as par on em.partidx = par.partidx 
                    where em.levelidx<=%d %s %s 
                    order by em.visible desc, em.membername asc", $level, $visible_sql, $memberName_sql);

		$result = mysqli_query($link, $sql);
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

    $link = DBConnect();
    
    //get report
		$report_sql = sprintf("SELECT em.MemberName, er.work_d, year(er.work_d) as year, month(er.work_d) as month 
    from ECO_Reports as er 
    inner join ECO_Member as em on er.MemberIdx=em.MemberIdx
    GROUP by em.MemberName, er.work_d having work_d BETWEEN '%s' and '%s' order by er.work_d desc", $data['from'], $data['to']);
    
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

    //get holiday
    $holiday_sql = sprintf("SELECT loc_date, date_name from ECO_Holiday 
                            where loc_date BETWEEN '%s' and '%s' 
                            order by loc_date asc", $data['from'], $data['to']);
    $holiday_result = mysqli_query($link, $holiday_sql);

    //mysql error
    if(!$holiday_result) {
      $holiday_result = mysqli_error($link);
      return mysqli_result_to_json($holiday_result);
    }


    $from = new DateTime($data['from']);
    $to = new DateTime($data['to']);
    $day = array("일","월","화","수","목","금","토");
    $reported_arr = array();

    // for($to; $to>=$from; date_modify($to, '-1 day')){
    //   $reported_arr[date_format($to, 'Y-m-d')] = "yeeeeeeeee";
    // }

    //날자 배열 세팅
    /*
    ex) 2019-05-01: {
          reported:[홍길동, 철수, 영희],
          unreported:[소닉, 피카츄, 라이츄],
          day: '수요일',
          isHoliday: Y
          dateName: '근로자의날'
        }
    */
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
    //set holiday
    while ( $row = $holiday_result->fetch_assoc()) {      
      $reported_arr[$row['loc_date']]['isHoliday'] = "Y";
      $reported_arr[$row['loc_date']]['dateName'] = $row['date_name'];
    }

    mysqli_close($link);
    return json_encode($reported_arr);
  }

  //자동저장
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

  // 자동저장 불러오기
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
  // 자동저장 삭제
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

  // 월 근무시간 합계 - 월 지정
  function getSumWorkHourFromMonth($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];
		
    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }
    //$memberIdx = $_SESSION['report_login_userIdx'];
    
    $year = $data['year'];
    $month = $data['month'];

    $sql = sprintf("SELECT year(work_d) as year, month(work_d) as month, sum(work_h) as sum_work_h
                    FROM ECO_Reports where MemberIdx=%d and year(work_d)=%d and month(work_d)=%d 
                    group by year(work_d), month(work_d)", $memberIdx, $year, $month);
    $link = DBConnect();    
    $result = mysqli_query($link, $sql);    
    //mysql error
    if(!$result) $result = mysqli_error($link);
    $row = $result->fetch_assoc();  // 년/월로 조회하기때문에 한번만 수행하면됨

    mysqli_close($link);
    return json_encode($row);
    //return mysqli_result_to_json($result);
  }

  // 월 근무시간 합계 - 최근 n 개월
  function getSumWorkHourFromRecentMonth($data) {
    if(SessionCheck() == false) {
      echo json_encode(array("error"=>"로그인 필요"));
      return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }
    //$memberIdx = $_SESSION['report_login_userIdx'];
    $limit = $data['limit'];

    $sql = sprintf("SELECT * from (SELECT year(work_d) as year, 
                    month(work_d) as month, sum(work_h) as sum_work_h
                    FROM ECO_Reports where MemberIdx=%d
                    group by year(work_d), month(work_d) 
                    order by year(work_d) desc, month(work_d) limit %d) as A
                    order by year, month asc", $memberIdx, $limit);
    
    $link = DBConnect();    
    $result = mysqli_query($link, $sql);    
    //mysql error
    if(!$result) $result = mysqli_error($link);
  
    mysqli_close($link);     
    return mysqli_result_to_json($result);
  }

  //근무일(보고서가 제출된 날) count
  function getWorkDayCount($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }

    //$memberIdx = $_SESSION['report_login_userIdx'];
    $year = $data['year'];
    $month = $data['month'];

    $sql = sprintf("SELECT count(work_d) as count_work_d from (SELECT work_d from ECO_Reports 
                    where MemberIdx=%d and year(work_d)=%d and month(work_d)=%d 
                    group by work_d) as report_date", $memberIdx, $year, $month);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    $row = $result->fetch_assoc();  // 년/월로 조회하기때문에 한번만 수행하면됨

    mysqli_close($link);
    return json_encode($row);
    //return mysqli_result_to_json($result);
  }

  //주말 근무일 count
  function getWorkDayCountFromWeekend($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }
    //$memberIdx = $_SESSION['report_login_userIdx'];
    $year = $data['year'];
    $month = $data['month'];
		/*
    $sql = sprintf("SELECT count(work_d) as count_work_d from (SELECT work_d from ECO_Reports 
                    where MemberIdx=%d and year(work_d)=%d and month(work_d)=%d and dayofweek(work_d) in (1,7) 
                    group by work_d) as report_date", $memberIdx, $year, $month);
		*/
		// 기존에는 주말만 카운트 했었음. 공휴일을 추가한다
    $sql = sprintf("SELECT count(work_d) as count_work_d from 
									(SELECT work_d from ECO_Reports where MemberIdx=%d and 
									(year(work_d)=%d and month(work_d)=%d and dayofweek(work_d) in (1,7) or work_d in 
									(SELECT loc_date FROM ECO_Holiday WHERE year(loc_date)=%d and month(loc_date)=%d)) 
									group by work_d) as report_date", $memberIdx, $year, $month, $year, $month);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    $row = $result->fetch_assoc();  // 년/월로 조회하기때문에 한번만 수행하면됨

    mysqli_close($link);
    return json_encode($row);
    //return mysqli_result_to_json($result);
  }

  // 최근 n일간의 하루당 근무시간을 가져온다
  function getWorkHourPerDay($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }
    //$memberIdx = $_SESSION['report_login_userIdx'];
    $limit = $data['limit'];

    $sql = sprintf("SELECT * from (SELECT work_d, sum(work_h) as work_h from ECO_Reports 
                    where memberidx = %d group by work_d 
                    order by work_d desc limit %d) as A order by work_d asc", $memberIdx, $limit);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);
  }

  //최근한달간 프로젝트별 업무시간 합계
  function getWorkHourPerProject($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }
    //$memberIdx = $_SESSION['report_login_userIdx'];

    $sql = sprintf("SELECT er.projectidx, ep.ProjectName, sum(er.work_h) as sum_work_h 
                    FROM ECO_Reports as er inner join ECO_Project as ep on er.ProjectIdx=ep.ProjectIdx 
                    where er.memberidx=%d and work_d>=now()-INTERVAL 1 month 
                    group by er.ProjectIdx",$memberIdx);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);    
  }
  //최근 n일간 일별 프로젝트 시간
  function getWorkHourPerDayAndProject($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"로그인 필요"));
			return;
    }

    $level = $_SESSION["report_login_level"];

    if($level<3) {
			$memberIdx = $_SESSION['report_login_userIdx'];
    } else if(isset($data['memberIdx'])){
      $memberIdx = $data['memberIdx'];
    } else {
      echo json_encode(array("error"=>"memberIdx none"));
      return;
    }

    //마지막 보고서 제출날짜
    $link = DBConnect();

    $work_d_sql = sprintf("SELECT work_d from ECO_Reports where memberidx=%d order by work_d desc limit 1", $memberIdx);
    $work_d_result = mysqli_query($link, $work_d_sql);

    $latest_work_d = $work_d_result->fetch_assoc()['work_d'];

    $day = $data['day'];

    $sql = sprintf("SELECT er.work_d, er.work_h, ep.ProjectName
                    FROM ECO_Reports as er 
                    inner join ECO_Project as ep on er.ProjectIdx = ep.ProjectIdx 
                    WHERE er.memberidx=%d and er.work_d >= (DATE('%s')-INTERVAL %d DAY)  
                    group by er.ProjectIdx, er.work_d, er.work_h
                    order by er.work_d asc",$memberIdx, $latest_work_d, $day);

        
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);
  }

  //프로젝트 관리
  function getProjectFromId($data) {
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }
    $link = DBConnect();

    $projectIdx = $data["projectIdx"];

		$sql = sprintf("SELECT * from ECO_Project WHERE projectIdx=%d", $projectIdx);
    
    $result = mysqli_query($link, $sql);
    if(!$result) $result = mysqli_error($link);
    // id는 pk이기때문에 하나밖에 없다
    // fetch_assoc으로 한행만 뽑음
    $row = $result->fetch_assoc();

		mysqli_close($link);    
    return json_encode($row);
  }
  function updateProject($data){
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;			
    }

		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		*/		
		   
	  $sql = sprintf("UPDATE ECO_Project SET projectname='%s' where projectidx=%d", 
                    $data["projectName"], $data["projectIdx"]);		
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    //return json_encode(array("result"=>$result));
    return json_encode(array("result"=>$result));
  }
  function deleteProject($data) {
    if(empty($_SESSION['report_login_user'])) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));			
			return;
    }

    // 해당 프로젝트 id로 작성된 보고서가 있는지 체크한다
    // 해당 프로젝트로 작성된 보고서가 있을시 삭제하지 않는다
    // 보고서가 해당 프로젝트 id를 참조하기때문에 프로젝트를 삭제하면 보고서 조회가 정상적으로 되지 않게된다
    $projectIdx = $data['projectIdx'];

    $link = DBConnect();
    // 보고서 있는지 체크
    $check_sql = sprintf("SELECT count(*) as count FROM ECO_Reports where projectidx=%d", $projectIdx);
    $check_result = mysqli_query($link, $check_sql);
    $row = $check_result->fetch_assoc();
    //count가 0보다 클경우 삭제하지 않는다
    if($row['count'] > 0){
      return json_encode(array("result"=>'has report'));
    }

    $delete_sql = sprintf("DELETE FROM ECO_Project where projectidx=%d", $projectIdx);
    
    $delete_result = mysqli_query($link, $delete_sql);
    if(!$delete_result) $delete_result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$delete_result));
  }

  function getLevel(){
    $level = $_SESSION["report_login_level"];
    return json_encode(array("level"=>$level));
  }

  function getMemberFromId($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }
    $link = DBConnect();

    $memberIdx = $data["memberIdx"];

		$sql = sprintf("SELECT MemberIdx, MemberName, LevelIdx, Visible, PartIdx, PositionIdx, EN 
                    from ECO_Member WHERE memberIdx=%d", $memberIdx);
    
    $result = mysqli_query($link, $sql);
    if(!$result) $result = mysqli_error($link);
    // id는 pk이기때문에 하나밖에 없다
    // fetch_assoc으로 한행만 뽑음
    $row = $result->fetch_assoc();

		mysqli_close($link);    
    return json_encode($row);
  }
  
  function getParts(){
    $sql = sprintf("SELECT * from ECO_Part order by partidx asc");
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);
  }
  function getPositions(){
    $sql = sprintf("SELECT * from ECO_Position order by positionidx asc");
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);
  }

  function pwdReset($data){
    $memberIdx = $data['memberIdx'];

    $sql = sprintf("UPDATE ECO_Member SET Password = sha1('1234567') WHERE MemberIdx =%d", $memberIdx);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return json_encode(array("result"=>$result));
  }

  function modifyMember($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    if(empty($data['memberName'])){
      return json_encode(array("error"=>"이름 누락"));
    }else if(empty($data['partIdx'])){
      return json_encode(array("error"=>"파트 누락"));
    }else if(empty($data['positionIdx'])){
      return json_encode(array("error"=>"직급 누락"));
    }else if(is_null($data['visible'])){
      return json_encode(array("error"=>"상태 누락 "));
    }else if(empty($data['levelIdx'])){
      return json_encode(array("error"=>"레벨 누락"));
    }else if(empty($data['EN'])){
      return json_encode(array("error"=>"사번 누락"));
    }
    
    $sql = sprintf("UPDATE ECO_Member 
                    Set membername='%s', partidx=%d, 
                        positionidx=%d, visible=%d,
                        levelIdx=%d, en='%s'
                    where memberidx=%d", $data['memberName'], $data['partIdx'], 
                    $data['positionIdx'], $data['visible'], $data['levelIdx'],
                    $data['EN'], $data['memberIdx']);
                    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return json_encode(array("result"=>$result));                    
  }

  function addMember($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    if(empty($data['memberName'])){
      return json_encode(array("error"=>"이름 누락"));
    }else if(empty($data['partIdx'])){
      return json_encode(array("error"=>"파트 누락"));
    }else if(empty($data['positionIdx'])){
      return json_encode(array("error"=>"직급 누락"));
    }else if(is_null($data['visible'])){
      return json_encode(array("error"=>"상태 누락"));
    }else if(empty($data['levelIdx'])){
      return json_encode(array("error"=>"레벨 누락"));
    }else if(empty($data['EN'])){
      return json_encode(array("error"=>"사번 누락"));
    }
    
    $sql = sprintf("INSERT ECO_Member (membername, partidx, positionidx, visible, levelidx, en, password) 
                    values ('%s', %d, %d, %d, %d, '%s', sha1('1234567'))",
                    $data['memberName'], $data['partIdx'], $data['positionIdx'], 
                    $data['visible'], $data['levelIdx'], $data['EN']);
                    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return json_encode(array("result"=>$result));
  }

  function deleteMember($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $level = $_SESSION["report_login_level"];
    $memberIdx = $data["memberIdx"];
    
    //level 3 이상만 삭제 커맨드를 사용 할 수 있음
    if($level<3) return json_encode(array("error"=>"permission denied"));

    // 해당 멤버가 제출한 보고서가 있는지 확인한다
    // 제출한 보고서가 있을경우 삭제하지 않는다.
    $link = DBConnect();

    $check_sql = sprintf("SELECT count(*) AS report_count 
                            FROM ECO_Reports WHERE memberIdx=%d", $memberIdx);
    
    $check_result = mysqli_query($link, $check_sql);     
    $row = $check_result->fetch_assoc();

    //count가 0보다 클경우 삭제하지 않는다
    if($row['report_count'] > 0){
      return json_encode(array("result"=>'has report'));
    }

    $delete_sql = sprintf("DELETE FROM ECO_Member where memberidx=%d and membername not like '관리자계정'", $memberIdx);
    $delete_result = mysqli_query($link, $delete_sql);

    if(!$delete_result) $delete_result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$delete_result));
  }

  function getHolidays_bak($data){
    $ch = curl_init();
    $year = $data['year'];
    $month = $data['month'];
    $url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo'; /*URL*/
    $queryParams = '?' . urlencode('ServiceKey') . '=uTHUN5ZrJcJvmr2dO%2FVhXt%2BEZf2bDxVJTh%2F%2B8fcE1qFFEbpRiBY%2BVEP2oPl%2FctiNJQorvG%2F6N%2FlY%2FmO%2FTA2csA%3D%3D'; /*Service Key*/
    $queryParams .= '&' . urlencode('solYear') . '=' . urlencode(2019); /*연*/
    $queryParams .= '&' . urlencode('solMonth') . '=' . urlencode('05'); /*월*/
    
    curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $response = curl_exec($ch);
    curl_close($ch);
    
    //var_dump($response);
    $xml_data = simplexml_load_string($response);

    /*
    $items = $xml_data->body->items->item;
    $data_arr = array();
    
    foreach ($items as $item) {
      $json = json_encode($item);
      $array = json_decode($json,TRUE);
      $data_arr[] = $array;
    }
    return json_encode($data_arr);
    */
    $items = $xml_data->body->items;
    return json_encode($items);
  }

  function getHolidaysWithAPI($data){
    $ch = curl_init();
    $year = $data['year'];
    $month = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    $holiday_list = [];
    //1~12월까지 반복
    foreach($month as $mon){
      $url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo'; /*URL*/
      $queryParams = '?' . urlencode('ServiceKey') . '=uTHUN5ZrJcJvmr2dO%2FVhXt%2BEZf2bDxVJTh%2F%2B8fcE1qFFEbpRiBY%2BVEP2oPl%2FctiNJQorvG%2F6N%2FlY%2FmO%2FTA2csA%3D%3D'; /*Service Key*/
      $queryParams .= '&' . urlencode('solYear') . '=' . urlencode($year); /*연*/
      $queryParams .= '&' . urlencode('solMonth') . '=' . urlencode($mon); /*월*/
      
      curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      $response = curl_exec($ch);
      $xml_data = simplexml_load_string($response);
      //$items = $xml_data->body->items;
      $items = $xml_data->body->items->item;

      //각월의 공휴일을 $result_list에 넣는다.
      foreach ($items as $item) {
        $holiday_list[] = $item;
      }
    }
    curl_close($ch);

    return json_encode($holiday_list);
  }

  function insertHoliday($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

		$sql = "INSERT INTO ECO_Holiday (loc_date, date_name) values ";
	
		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		row가 여러개니 벌크로 insert 한다
		위의 이유로 sprint로 sql을 만들어서 쿼리한다
		*/
		$str_format = "('%s', '%s'), ";
		foreach($data as $row){      
		  $sql .= sprintf($str_format, $row["loc_date"], $row["date_name"]);
		}
    $sql = substr($sql, 0, strlen($sql) - 2);
    
    $link = DBConnect();
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }

  function getHolidays($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $sql = sprintf("SELECT * from ECO_Holiday where year(loc_date)='%s' order by loc_date asc", $data['year']);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return mysqli_result_to_json($result);
  }

  function deleteHoliday($data){
    if(SessionCheck() == false) {
			echo json_encode(array("error"=>"SESSION_EXPIRED"));
			return;
    }

    $sql = sprintf("DELETE from ECO_Holiday where id=%d", $data['id']);
    $link = DBConnect();
    $result = mysqli_query($link, $sql);
    //mysql error
    if(!$result) $result = mysqli_error($link);
    mysqli_close($link);

    return json_encode(array("result"=>$result));
  }

?>
