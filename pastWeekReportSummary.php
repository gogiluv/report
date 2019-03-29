<?php
	include "lib/db.php";
	include "lib/default.php";	
	$link = DBConnect();

	$selectedDate = @addslashes($_GET['selectedDate']);
	$selectedPart = @addslashes($_GET['selectedPart']);
	$command = @addslashes($_GET['command']);
	$partIdx=0;
	$memberNameArr = array();
	$projectNameArr = array();

	$projectsNameResult = @mysqli_query($link, "SELECT ProjectName, ProjectIdx FROM ECO_Project ORDER BY ProjectIdx");
	$allMemberResult = @mysqli_query($link, "SELECT * FROM ECO_Member WHERE MemberName != '관리자계정' order by MemberName");

	$selectedPartMemberResult = "";
	$reportInfoResult = "";
	

	if( $_SESSION["report_login_level"] >= 3 ){		
		if($command=="showReport")
		{
			if($selectedPart && $selectedPart != "all")
			{
				$partIdxResult = mysqli_query($link, "SELECT PartIdx FROM ECO_Part WHERE Name = '$selectedPart'");
				$partIdx = mysqli_fetch_row($partIdxResult)[0];
				mysqli_free_result($partIdxResult);
			}

			$reportInfoResult = @mysqli_query($link, "SELECT ReportIdx, MemberIdx, ProjectIdx, Report FROM ECO_Reports WHERE IsComplete = 1 
												AND work_d = '$selectedDate' order by MemberIdx");

		}

	}else if( $_SESSION["report_login_level"] == 2 ){

		if($command=="showReport")
		{
			$selectedPart = "";
			switch ($_SESSION['report_login_partIdx']) {
				case 1:
					$selectedPart = "EDU";
					break;
				case 2:
					$selectedPart = "SE";
					break;
				case 3:
					$selectedPart = "ART";
					break;
				case 4:
					$selectedPart = "GS";
					break;
				case 5:
					$selectedPart = "TS";
					break;
				default:
					# code...
					break;
			}

			$partIdxResult = mysqli_query($link, "SELECT PartIdx FROM ECO_Part WHERE Name = '$selectedPart'");
				$partIdx = mysqli_result($partIdxResult,0,0);
				mysqli_free_result($partIdxResult);

			$reportInfoResult = @mysqli_query($link, "SELECT ReportIdx, MemberIdx, ProjectIdx, Report FROM ECO_Reports WHERE IsComplete = 1 
												AND work_d = '$selectedDate' order by MemberIdx");
		}

	
	}




	if($projectsNameResult)
	{
		while($row = @mysqli_fetch_array($projectsNameResult))
		{
			$projectNameArr[$row['ProjectIdx']] = $row['ProjectName'];
		}
		mysqli_free_result($projectsNameResult);
	}

	if($allMemberResult)
	{
		while($row = mysqli_fetch_array($allMemberResult) )
		{
			$memberNameArr[$row['MemberIdx']] = $row['MemberName'];
		}
		mysqli_free_result($allMemberResult);
	}
	

	$reportDataResult= mysqli_query($link, "SELECT DISTINCT work_d FROM ECO_Reports order by work_d desc");
	$partDataResult = mysqli_query($link, "SELECT Name FROM ECO_Part");
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
						while($row = mysqli_fetch_array($reportDataResult))
						{
							$date = date_format(date_create($row['work_d']),'Y-m-d');
							echo "<option value='$date'".(($selectedDate == $date ) ? "selected" :"").">".$date." </option>";
						}
						mysqli_free_result($reportDataResult);
					}
				?>
				</select>

				<?php if( $_SESSION["report_login_level"] >= 3 ){ ?>

				&nbsp
                	    파트 선택 
				<select name="selectedPart">
				<option value="all">All</option>
				<?php
					if($partDataResult)
					{
						while($row = mysqli_fetch_array($partDataResult))
						{
							echo "<option value='$row[0]'".(($selectedPart == $row[0] ) ? "selected" :"").">".$row[0]."</option>";
						}
						mysqli_free_result($partDataResult);
					}
				?>
				</select>

				<?php } ?>

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

							while($arr = mysqli_fetch_array($reportInfoResult))
							{
								$memberName = $memberNameArr[$arr['MemberIdx']];

								$result = mysqli_query($link, "SELECT PartIdx FROM ECO_Member WHERE MemberName = '$memberName'");

								if($partIdx  && $partIdx == mysqli_fetch_row($result)[0] || $selectedPart == "all")
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
								mysqli_free_result($result);
							}
							mysqli_free_result($reportInfoResult);
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
<?php @mysqli_close($link) ?>
