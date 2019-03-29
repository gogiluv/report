<?php
  include "db.php";
  $link = DBConnect();

  $year  = (int)@trim($_POST["selected_year"]);
  $month = (int)@trim($_POST["selected_month"]);
  $selectedName = @trim($_POST["selected_name"]);
	
  $totalSpendTime = 0;
  
  $memberIdxQuery = "";
  $selectedMemberIdx = 0;

  if($_SESSION['report_login_level'] == 3 && $selectedName)  	//팀장 
  {
	$memberNameResult = @mysql_query("SELECT MemberIdx FROM  ECO_Member WHERE MemberName = '$selectedName' ");
	$selectedMemberIdx = mysql_result($memberNameResult,0,0);
	mysql_free_result($memberNameResult);

	$memberIdxQuery = "AND MemberIdx = '$selectedMemberIdx'";
  }
  else if($_SESSION['report_login_level'] == 2)	//파트장 
  {
	//$memberNameQuery = "AND MemberLevel = '$_SESSION[report_login_level]'";
  }
  else  if($_SESSION['report_login_level'] == 1) //일반 사원 
  {
	$memberIdxQuery = "AND MemberIdx = '$_SESSION[report_login_userIdx]'";
  }


  	if($_SESSION['report_login_level'] == 2 ||
		$_SESSION['report_login_level'] == 3 )
	{ 
	$memberInfoResult = mysql_query("SELECT * FROM ECO_Member WHERE MemberName != \"관리자계정\"  
											".$memberIdxQuery."
											AND Visible = 1 order by EN", $link);
	}

  if($year > 2000)
  {
    $month_condition = $month_sort = "";
    if($month > 0)
      $month_condition = " AND MONTH(DATE)='$month'";
    else
      $month_sort = "MONTH(DATE) DESC,";

    $allItems = mysql_query("SELECT DISTINCT MONTH(Date), ProjectIdx, MarketIdx FROM ECO_Reports 
							WHERE IsComplete=1 
							AND YEAR(DATE)='$year'$month_condition $memberIdxQuery ORDER BY $month_sort ProjectIdx, MarketIdx", $link);

    $totalSpendTimeResult = mysql_query("SELECT SUM(SpendTime) FROM ECO_Reports 
							WHERE IsComplete=1 
							AND YEAR(Date)='$year'$month_condition $memberIdxQuery", $link);

    $projectsNameResult = mysql_query("SELECT ProjectName, ProjectIdx FROM ECO_Project ORDER BY ProjectIdx", $link);
    $marketNameResult = mysql_query("SELECT MarketName, MarketIdx FROM ECO_Market ORDER BY MarketIdx", $link);

    $allSpendTimeResult = mysql_query("SELECT SUM(SpendTime), ProjectIdx, MarketIdx, MONTH(Date) FROM ECO_Reports 
									WHERE IsComplete=1 
									AND YEAR(DATE)='$year' $month_condition  $memberIdxQuery
									GROUP BY ProjectIdx, MarketIdx, MONTH(Date)", $link);


    $totalSpendTime = mysql_result($totalSpendTimeResult,0,0);
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://manager.com2us.com/guide/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="http://manager.com2us.com/guide/js/ui.js"></script>
<script type="text/javascript">
function resize() {
  parent.fncResizeHeight(document);
}

</script>    
<title>ECO팀 업무보고 - 업무 취합</title>
<link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
<link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
<link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
</head>

<body onload="resize()">
  <div class="C2Scontent">
    <h1 class="location">관리메뉴 &gt; 프로젝트별 통계</h1>
    <div class="divide">
      <div class="boardtype2">
        <div class="tabcontents" id="tabcontents2">
          <form id="form1" runat="server" action="statistics_project.php" method="post" >
            <p class="t2">
              <?php

				if($_SESSION['report_login_level'] == 2 ||
						 $_SESSION['report_login_level'] == 3)
				{
				  echo "이름: <select name='selected_name'> <option value=\"\">All</option>";
				  $memberNameInfo = mysql_query("SELECT * FROM ECO_Member WHERE MemberName != \"관리자계정\" and Visible = 1",$link);
		         
			        while($row = @mysql_fetch_array($memberNameInfo))
					{
			          echo "<option value='$row[MemberName]' ".(($selectedName == $row['MemberName']) ? "selected" : "").">
						$row[MemberName]</option>\r\n";
					}
	          
					@mysql_free_result($memberInfoResult);
			  		echo " </select>";
				
            	}
              ?>
              년 :
              <select name="selected_year" class = "inselect" >
              <?php
              if($result = @mysql_query("SELECT DISTINCT SUBSTRING(Date,1,4) FROM ECO_Reports ORDER BY Date DESC", $link)) {
                while($row = @mysql_fetch_array($result))
                  echo "<option value='$row[0]' ".(($year && $row[0] == $year) ? "selected='selected'" : "").">$row[0]</option>\r\n";
                @mysql_free_result($result);
              }
              ?>
              </select>

              월 :
              <select name="selected_month"  class = "inselect" >
                <option value="0">All</option>
              <?php
              for($optionMonth = 1; $optionMonth <= 12; $optionMonth++)
                echo "<option value='$optionMonth' ".(($month && $optionMonth == $month) ? "selected='selected'" : "").">$optionMonth</option>";
              ?>
              </select>
              <button type="submit" name="showstatistics" class="button red" > 확인 </button>
            </p>
            <table cellpadding="0" cellspacing="0" width="520" border="0">
            <tr>
              <th scope="row" class="al" width="80">전체 투입시간</th>
              <td width ="500"><?php echo ($totalSpendTime > 0 ? $totalSpendTime : "") ?></td>
            </tr>
            </table>
          </form>

          <table align="center">
          <tr align="center" bgColor="#dddddd" >
            <td>시간</td>
            <td>프로젝트명</td>
            <td>마켓</td>
            <td>프로젝트 투입시간(H)</td>
            <td>프로젝트 투입률(%)</td>
          </tr>

          <?php
          $counts = array(0,0,0,0,0,0,0,0,0,0,0,0);
          if($year && $allItems && $totalSpendTime && $projectsNameResult && $marketNameResult && $allSpendTimeResult)
          {
            #$count = mysql_num_rows($allItems);
            $projectNameArray = array();
            $marketNameArray = array();
            $allSpendTimeArray = array();

            for($i = 0; $arr = mysql_fetch_array($projectsNameResult); $i++)
              $projectNameArray[$arr['ProjectIdx']] = $arr['ProjectName'];
            for($i = 0; $arr = mysql_fetch_array($marketNameResult); $i++)
              $marketNameArray[$arr['MarketIdx']] = $arr['MarketName'];
            for($i = 0; $arr = mysql_fetch_array($allSpendTimeResult); $i++)
			{
            	$allSpendTimeArray[$arr['ProjectIdx'].'_'.$arr['MarketIdx'].'_'.$arr['MONTH(Date)']] = ($arr[0]);		
			}

            $last_month = 0;

            while($arr = mysql_fetch_array($allItems))
            {
              if($arr[0] < 1 || $arr[0] > 12)
                continue;

              echo "<tr align='center'>";
              if($last_month == $arr[0]) {
                ++$counts[$last_month - 1];
              } else {
                $last_month = $arr[0];
                $counts[$last_month - 1] = 1;
                echo "<td id='month_$last_month'>".$arr[0]."월</td>"; // rowspan='$count'
              }
              $spend = $allSpendTimeArray[$arr['ProjectIdx'].'_'.$arr['MarketIdx'].'_'.$arr['MONTH(Date)']];
              $ratio =  round( $spend  / $totalSpendTime * 100,1);
              echo "<td>".$projectNameArray[$arr['ProjectIdx']]."</td>";
              echo "<td>".($arr['MarketIdx'] <= 0 ? "" : $marketNameArray[$arr['MarketIdx']])."</td>";
              echo "<td>$spend</td>";
              echo "<td>$ratio%</td>";
              echo "</tr>";
            }

            if($allItems)
              @mysql_free_result($allItems);
            if($totalSpendTimeResult)
              @mysql_free_result($totalSpendTimeResult);
            if($projectsNameResult)
              @mysql_free_result($projectsNameResult);
            if($marketNameResult)
              @mysql_free_result($marketNameResult);
            if($allSpendTimeResult)
              @mysql_free_result($allSpendTimeResult);
          }

          ?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
<?php
  $month_count = count($counts);
  for($i = 0; $i < $month_count; $i++) {
    if($counts[$i]) {
      $month = $i + 1;
      $count = $counts[$i];
      echo "document.getElementById('month_$month').rowSpan=$count;\n";
    }
  }
?>
    $('.tabmenu2').tabMenu(0);
  </script>

</body>
</html>
<?php @mysql_close($link) ?>
