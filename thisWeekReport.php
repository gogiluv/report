<?php
	include "db.php";
	$link = DBConnect();
	$reportDate = @addslashes($_POST['reportDate']);
	$weeklySpendTime= "";
	$reportInfoResult="";
	$marketInfoResult="";
	$projectInfoResult="";
	$command = @$_POST['command'];
	$checkCompleteReport="";
	$latestReportDate = "";

	if($reportDate =="")
	{
		$reportDate = GetReportDate();
	}

		$CurDate = DATE('Y-m-d');
		$time = DATE("H",time());
		$week = DATE("w",time());

	if($command == "withdrawal")
	{
		date_default_timezone_set('Asia/Seoul');
		$CurDate = DATE('Y-m-d');
		$time = DATE("H",time());
		$week = DATE("w",time());
		
		if( $week == 4 && $time < 16 && $CurDate <= $reportDate  || 
			$week < 4 && $CurDate <= $reportDate)
		{
			$result = @mysqli_query($link, "UPDATE ECO_Reports SET IsComplete = 0
			WHERE MemberIdx =  '$_SESSION[report_login_userIdx]'
			AND work_d =  '$reportDate'
			AND IsComplete = 1");

			if($result)
			{
				echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
			}
			else
			{
				echo "<script type='text/javascript'> alert(\"업무보고 에 실패했습니다. 관리자에게 문의하세요.\") </script>";
			}
			
		}
		else
		{
			echo "<script type='text/javascript'> alert(\"업무보고 철회는 월 ~ 목요일 오후 4시 이전에만 가능합니다. \")</script>";
			echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
			exit;
		}

	}

	 if( $checkReportCompleteResult = @mysqli_query($link, "SELECT COUNT(*) FROM ECO_Reports
											WHERE MemberIdx = '$_SESSION[report_login_userIdx]'
											AND work_d = '$reportDate' AND IsComplete = 0") )
	{
		 if($row = @mysqli_fetch_array($checkReportCompleteResult))
		{
			$countWaitingReport = $row[0];
		}
	}

	if($command=="complete")
	{
		//이전 보고 인지 확인
		$reportQuery = "";
		if( $reportDate < GetReportDate())
		{
			$reportQuery = ",work_d = '$reportDate'";
		}

		$thisWeekReportDate = GetReportDate();

		$result = @mysqli_query($link, "UPDATE ECO_Reports SET 
					IsComplete = 1 $reportQuery
					WHERE MemberIdx =  '$_SESSION[report_login_userIdx]'
					AND work_d =  '$thisWeekReportDate'
					AND IsComplete = 0");

		if($result)
		{
			$reportDate = GetReportDate();
			echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
		}
		else
		{
			echo "<script 'text/javascript'>alert(\"업무보고 완료에 실패했습니다. 관리자에게 문의하세요.\")</script>";
		}

		
	}

	 $reportInfoResult = @mysqli_query($link, "SELECT * FROM ECO_Reports
											WHERE MemberIdx =  '$_SESSION[report_login_userIdx]'
											AND work_d =  '$reportDate'
											order by ReportIdx ");


	$marketInfoResult = @mysqli_query($link, "SELECT MarketIdx, MarketName FROM ECO_Market order by MarketIdx") ;
	$projectInfoResult = @mysqli_query($link, "SELECT ProjectIdx, ProjectName FROM ECO_Project order by ProjectIdx") ;
	

	$marketInfoArr = array();
	$projectInfoArr = array();
	
	$marketInfoArr[0] = "";
	while($arr = @mysqli_fetch_array($marketInfoResult) )
	{
		$marketInfoArr[$arr['MarketIdx']] = $arr['MarketName'];
	}
	mysqli_free_result($marketInfoResult);
	
	while($arr = @mysqli_fetch_array($projectInfoResult) )
	{
		$projectInfoArr[$arr['ProjectIdx']] = $arr['ProjectName'];
	}
	mysqli_free_result($projectInfoResult);


	if( $result = @mysqli_query($link, "SELECT SUM(work_h)  FROM ECO_Reports 
								WHERE MemberIdx = '$_SESSION[report_login_userIdx]' 
								AND work_d = '$reportDate' ") )
	{
		//$weeklySpendTime = mysqli_result($result,0,0);
		//20190319 kbo
		$weeklySpendTime = mysqli_fetch_row($result)[0];
		mysqli_free_result($result);
	}


	 if( $latestReportDateResult = @mysqli_query($link, "SELECT MAX(work_d) FROM ECO_Reports 
									WHERE MemberIdx = '$_SESSION[report_login_userIdx]'") )
	{
		//$latestReportDate = mysqli_result($latestReportDateResult,0,0);
		//20190319 kbo
		$latestReportDate = mysqli_fetch_row($latestReportDateResult)[0];
		if($latestReportDate)
		{
			$latestReportDate = date_create($latestReportDate);
			$latestReportDate = date_format($latestReportDate,'Y-m-d');	
		}
		else
		{
			$latestReportDate = 0;
		}
		mysqli_free_result($latestReportDateResult);
	}

	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script type="text/javascript">

        function resize() {
            parent.fncResizeHeight(document);
        }

		function CompleteReport()
		{
			var frm =  document.forms.form1;
			if(frm.weeklySpendTime.value < 40)	
			{
				alert("주간 업무시간이 40시간 미만입니다. ");
				return;
			}	
			else if(frm.weeklySpendTime.value > 40)
			{
				alert("주간 업무시간이 40시간을 초과합니다.");
				return;
			}
	
			frm.command.value = "complete";
			frm.submit();
		}
		function ChangeReportDate()
		{
			var frm =  document.forms.form1;
			frm.reportDate.value = frm.reportDateSelect.value;
			
		}
		function EditReport(projectIdx,marketIdx,reportIdx)
		{
			var frm =  document.forms.form1;
			frm.selectedProjectIdx.value = projectIdx;
			frm.marketIdx.value = marketIdx;
			frm.reportIdx.value = reportIdx;
			frm.command.value = "edit";
			frm.action = "addReport.php";
			frm.submit();
		}
		
		function AddReport()
		{
			var frm =  document.forms.form1;

			if(<?php echo $countWaitingReport;  ?> <= 0 &&
				frm.latestReportDate.value >=  frm.reportDate.value)
			{
				alert("이미 보고를 완료했습니다. 추가 할 수 없습니다.");
				return;
			}

			frm.action="addReport.php";
			frm.command.value = "add";
			frm.submit();

		}

		function WithdrawalReport()
		{
			var frm =  document.forms.form1;
			frm.command.value = "withdrawal";
			frm.submit();
		}
    </script>

    <title>ECO팀 업무보고 - 이번주 보고서</title>
    <link rel="stylesheet" href="/css/base.css" type="text/css" />
    <link rel="stylesheet" href="/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="/css/button.css" type="text/css" />
</head>
<body onload="return resize()">
    <form name="form1" runat="server" method="post">
	<input type="hidden" name="selectedProjectIdx" value="" />
	<input type="hidden" name="marketIdx" value="" />
	<input type="hidden" name="reportIdx" value="" />
	<input type="hidden" name="command" value=""/>
	<input type="hidden" name="reportDate" value="<?php echo $reportDate; ?>"/>
	<input type="hidden" name="latestReportDate" value="<?php echo $latestReportDate; ?>"/>
	<input type="hidden" name="weeklySpendTime" value="<?php echo $weeklySpendTime; ?>"/>
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 이번주 보고서
        </h1>
        <div class="left">
            <p class="t2">
                * ECO팀 주간업무보고는 매주 목요일 입니다.
            </p>
            <p class="t2">
                * 프로젝트명 클릭시 세부내용 확인 가능합니다.
            </p>
            <p class="t2">
                * 업무철회는 월~목 오후 4시 이전까지만 가능합니다.
            </p>
        </div>
        <br>
        <div class="right">
			<?php 
				if($countWaitingReport == 0 && $latestReportDate >= $reportDate)
				{
					echo "<span style=\"font-weight: bold;\">주간업무보고가 완료되었습니다.</span>";
					echo " <button type=\"button\" class=\"button black\" onclick=\"WithdrawalReport()\">업무보고 철회하기</button>   "; 
				}
				else
				{
					$previousReportDate = date('Y-m-d',strtotime($reportDate.'-7 days'));
					echo "<select name='reportDateSelect' onchange='ChangeReportDate()'>
							<option value ='$reportDate' selected>".$reportDate."</option>
							<option value ='$previousReportDate'>$previousReportDate</option></select>";
					echo " <button type=\"button\" class=\"button red\" onclick=\"CompleteReport()\">주간업무보고 완료하기</button>   "; 
				}
			
			?>
			
        </div>
        <br><br><br>
        <div class="divide">
            <div class="boardtype2">
                <table cellpadding="0" cellspacing="0" width="520" border="0">
                    <tr>
                        <th style="width:20%">
                           			 이번주 업무투입시간(H)
                        </th>
                        <td colspan="4">
							<label style="color:rgb(0,0,230);"><?php echo "$weeklySpendTime" ?> </label>
                        </td>
                    </tr>
                    <tr>
						<tr align="center" bgColor="#dddddd" ";>
							<th style="width:30%">프로젝트명</th>
							<th style="width:30%">마켓 </th>
							<th style="width:15%">버전</th>
							<th style="width:15%">투입시간(H)</th>
							<th style="width:10%">주간 투입률(%)</th>
						</tr>
						<?php
							$selectedProject = "";
							while( $row = @mysqli_fetch_array($reportInfoResult) )
							{
								$selectedProject = $projectInfoArr[$row['ProjectIdx']];
								$selectedMarket = $marketInfoArr[$row['MarketIdx']];
								echo "<tr align='center'>";
								echo "<td> <a href='#' onclick=EditReport('".$row['ProjectIdx']. "','"
																		    .$row['MarketIdx'].  "','"
																		     .$row['ReportIdx']."') >".$selectedProject."</a></td>";
								echo "<td> ".$selectedMarket."</td>";
								printf("<td>%03d</td>",$row['Ver']);
								echo "<td> $row[work_h] </td>";
								echo "<td> $row[SpendPercent] </td>";	
								echo "</tr>";
							}	
							mysqli_free_result($reportInfoResult);
						?>                 
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="right">
                                <div class="btns">
									<button type="button" class="button red" onclick="AddReport()">추가</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    </form>
</body>
</html>

<?php @mysqli_close($link) ?>
