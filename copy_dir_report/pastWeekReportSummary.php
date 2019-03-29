<?php
	include "db.php";
	$link = DBConnect();

	$selectedDate = @addslashes($_GET['selectedDate']);
	$selectedPart = @addslashes($_GET['selectedPart']);
	$command = @addslashes($_GET['command']);
	$partIdx=0;
	$memberNameArr = array();
	$projectNameArr = array();

	$projectsNameResult = @mysql_query("SELECT ProjectName, ProjectIdx FROM ECO_Project ORDER BY ProjectIdx", $link);
	$allMemberResult = @mysql_query("SELECT * FROM ECO_Member WHERE MemberName != '관리자계정' order by MemberName",$link);

	$selectedPartMemberResult = "";
	$reportInfoResult = "";
	
	if($command=="showReport")
	{
		if($selectedPart && $selectedPart != "all")
		{
			$partIdxResult = mysql_query("SELECT PartIdx FROM ECO_Part WHERE Name = '$selectedPart'",$link);
			$partIdx = mysql_result($partIdxResult,0,0);
			mysql_free_result($partIdxResult);
		}

		$reportInfoResult = @mysql_query("SELECT ReportIdx, MemberIdx, ProjectIdx,Report FROM ECO_Reports WHERE IsComplete = 1 
											AND Date = '$selectedDate' order by MemberIdx", $link);

	}

	if($projectsNameResult)
	{
		while($row = @mysql_fetch_array($projectsNameResult))
		{
			$projectNameArr[$row['ProjectIdx']] = $row['ProjectName'];
		}
		mysql_free_result($projectsNameResult);
	}

	if($allMemberResult)
	{
		while($row = mysql_fetch_array($allMemberResult) )
		{
			$memberNameArr[$row['MemberIdx']] = $row['MemberName'];
		}
		mysql_free_result($allMemberResult);
	}
	

	$reportDataResult= mysql_query("SELECT DISTINCT Date FROM ECO_Reports order by Date desc",$link);
	$partDataResult = mysql_query("SELECT Name FROM ECO_Part",$link);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
    <script type="text/javascript">
        function resize() {
            parent.fncResizeHeight(document);
        }
		function ShowReports()
		{
			var frm = document.forms.form1;
			frm.command.value = "showReport";
			frm.submit();
		}
		function ViewReportDetail(reportIdx)
		{
			var frm = document.forms.form1;
			frm.action="viewReport.php";
			frm.reportIdx.value = reportIdx;
			frm.submit();
		}
    </script>

    <title>ECO팀 업무보고 - 업무 취합</title>
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
</head>
<body onload="resize()">
    <form id="form1" runat="server" method="GET">
	<input type="hidden" name="command" value="">
	<input type="hidden" name="reportIdx" value="">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 업무 취합
        </h1>
        <div class="divide">
            <div class="boardtype2">
                <p class="t2">
                    *리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>
                <p class="t2">
                    *프로젝트명 클릭시 세부내용 확인 가능합니다.</p>
                <div>
                    보고날짜 선택
				<select name="selectedDate">
				<option></option>
				<?php 
					if($reportDataResult)
					{
						while($row = mysql_fetch_array($reportDataResult))
						{
							$date = date_format(date_create($row['Date']),'Y-m-d');
							echo "<option value='$date'".(($selectedDate == $date ) ? "selected" :"").">".$date." </option>";
						}
						mysql_free_result($reportDataResult);
					}
				?>
				</select>
				&nbsp
                	    파트 선택 
				<select name="selectedPart">
				<option value="all">All</option>
				<?php
					if($partDataResult)
					{
						while($row = mysql_fetch_array($partDataResult))
						{
							echo "<option value='$row[0]'".(($selectedPart == $row[0] ) ? "selected" :"").">".$row[0]."</option>";
						}
						mysql_free_result($partDataResult);
					}
				?>
				</select>
				&nbsp
				<button type="button" class="button red" onclick="ShowReports()">확인</button>
                </div>
				<div>
                <table cellpadding="0" cellspacing="0" width="520" border="0">
					<tr align="center" bgColor="#dddddd" >
					<td style="width:10%">이름</td>
					<td style="width:20%">프로젝트명</td>
					<td style="width:70%">내용</td>
					</tr>

					<?php
						$nameCounts=array();

						if($reportInfoResult)
						{
							$last_name="";

							while($arr = mysql_fetch_array($reportInfoResult))
							{
								$memberName = $memberNameArr[$arr['MemberIdx']];

								$result = mysql_query("SELECT PartIdx FROM ECO_Member WHERE MemberName = '$memberName'",$link);

								if($partIdx  && $partIdx == mysql_result($result,0,0) || $selectedPart == "all")
								{

									
									//$report = str_replace("\n","<br>",$arr['Report'] );
               						$report = str_replace(" ","&nbsp",$arr['Report'] );
               						$report = nl2br($report);
									$projectName = $projectNameArr[$arr['ProjectIdx']];

									echo "<tr align='center'>";
									if($last_name == $memberName)
									{
										$nameCounts[$memberName]++;
									}
									else if($last_name != $memberName)
									{
										$nameCounts[$memberName] = 1;
										echo "<td id='$memberName'>".$memberName."</td>";
									}
									echo "<td><a href='#' onclick='ViewReportDetail(".$arr['ReportIdx'].")'>".$projectName."</a></td>";
									echo "<td align='left'>".$report."</td>";
									echo "</tr>";
									$last_name = $memberName; 
								}
								mysql_free_result($result);
							}
							mysql_free_result($reportInfoResult);
						}
					?>
				</table>
				</div>
            </div>
        </div>
    </div>
    </div>
    </form>
	
    <script type="text/javascript">
	<?php
		foreach($nameCounts as $key => $value) {
			 echo "document.getElementById('$key').rowSpan=$value;\n";
		}
	?>

  </script>



</body>
</html>
<?php @mysql_close($link) ?>
