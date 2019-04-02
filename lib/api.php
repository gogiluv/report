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
    $f_name = $_GET["f_name"];
    $data = isset($_GET["data"]) ? $_GET["data"] : null;
				
		echo call_user_func($f_name, $data);
    return;
  }

  /*
    하단에 함수들을 정의해서 사용한다.
    $data 전달받아서 사용
  */
	function getReportFromId($data){
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
    $link = DBConnect();

		$sql = "SELECT * FROM ECO_Project WHERE is_enabled=1
            order by isgame desc, projectname asc";
    
    $result = mysqli_query($link, $sql);

    if(!$result) {
      $result = mysqli_error($link);
      return json_encode(array("error"=>$result));
    }

		mysqli_close($link);    
    return mysqli_result_to_json($result);
  }

  function getGameProjects(){
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
    $link = DBConnect();

		$memberIdx = $_SESSION['report_login_userIdx'];    
    
		/*
		싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
		*/		
		   
	  $sql = sprintf("UPDATE ECO_Reports SET work_h=%f, projectidx=%d, report='%s' 
                    where reportidx=%d and memberidx=%d", 
                    $data["work_hour"], $data["project_id"], 
                    @addslashes($data["content"]), $data["report_id"], $memberIdx);		
		
    $result = mysqli_query($link, $sql);

    //mysql error
    if(!$result) $result = mysqli_error($link);

    mysqli_close($link);
    return json_encode(array("result"=>$result));
  }
?>