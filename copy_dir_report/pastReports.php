<?php
	include "db.php";
	$link = DBConnect();

	$selectedDate = @$_GET['selectedDate'];
	$selectedDate_1 = @$_GET['selectedDate_1'];
	$selectedDate_2 = @$_GET['selectedDate_2'];

	$reportDateResult = @mysql_query("SELECT DISTINCT Date FROM ECO_Reports order by Date desc",$link);

	$allItems="";

	$totalSpendTimeResult = "";
 	$projectsNameResult ="";
	$marketNameResult ="";
	$allSpendTimeResult="";

	$totalSpendTime = "";

	$selectDateQuery="";

	if($selectedDate)
	{
		$selectDateQuery = "AND Date='$selectedDate'";
	}
	else if($selectedDate_1 && $selectedDate_2)
	{
		$selectDateQuery = "AND Date >= '$selectedDate_1' AND Date <= '$selectedDate_2'";
	}
	
	if($selectDateQuery)
	{
	
		$allItems = mysql_query("SELECT DISTINCT Date, ProjectIdx, MarketIdx, Ver, ReportIdx FROM ECO_Reports 
								WHERE IsComplete=1 ".$selectDateQuery." AND MemberIdx = '$_SESSION[report_login_userIdx]' ", $link);

		$totalSpendTimeResult = mysql_query("SELECT SUM(SpendTime) FROM ECO_Reports 
											WHERE MemberIdx = '$_SESSION[report_login_userIdx]' AND IsComplete=1 ".$selectDateQuery." 
											Group by Date", $link);


		$projectsNameResult = mysql_query("SELECT ProjectName, ProjectIdx FROM ECO_Project ORDER BY ProjectIdx", $link);
		$marketNameResult = mysql_query("SELECT MarketName, MarketIdx FROM ECO_Market ORDER BY MarketIdx", $link);

		$allSpendTimeResult = mysql_query("SELECT SUM(SpendTime), ProjectIdx, MarketIdx, Date FROM ECO_Reports 
										WHERE IsComplete=1 ".$selectDateQuery."
										AND MemberIdx = '$_SESSION[report_login_userIdx]' Group by ProjectIdx, MarketIdx,Date order by Date ", $link);

	 	$totalSpendTime = @mysql_result($totalSpendTimeResult,0,0);	

	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />

    <script type="text/javascript">

       
        function resize() {
            parent.fncResizeHeight(document);
        }
        
        function ChangeDate()
        {
			var frm = document.forms.form1;
         	frm.submit();
        }



		function SelectDate()
		{
			var frm = document.forms.form1;
			if( frm.selectedDate_1.value > frm.selectedDate_2.value )
			{
				alert("기간 선택이 올바르지 않습니다.");
				frm.selectedDate_1.value = 0;
				frm.selectedDate_2.value = 0;
				return;
			}

			frm.selectedDate.value = "";
			frm.submit();
		}

        
		function ViewReport(projectIdx,marketIdx,reportIdx,reportDate)
		{
			var frm =  document.forms.form1;
			frm.selectedProjectIdx.value = projectIdx;
			frm.marketIdx.value = marketIdx;
			frm.reportDate.value = reportDate;
			frm.reportIdx.value = reportIdx;
			frm.command.value = "view";
			frm.action = "addReport.php";
			frm.method = "POST";
			frm.submit();
		}
		
    </script>

    <title>ECO팀 업무보고 - 지난 보고서</title>
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
</head>
<body onload="return resize()">
    <form id="form1" runat="server" method='GET'>
	<input type="hidden" name="selectedProjectIdx" value="" />
	<input type="hidden" name="marketIdx" value="" />
	<input type="hidden" name="reportDate" value=""/>
	<input type="hidden" name="reportIdx" value=""/>
	<input type="hidden" name="command" value=""/>
	<input type="hidden" name="weeklySpendTime" value="<?php echo $weeklySpendTime; ?>"/>

    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 지난 보고서
        </h1>
        <div class="left">
            <p class="t2">
                * 지난 보고서는 확인만 가능합니다.
            </p>
            <p class="t2">
                *프로젝트명 클릭시 세부내용 확인 가능합니다.
            </p>
        </div>
        <br><br>
        <div class="divide">
            <div class="boardtype2">
                <table cellpadding="0" cellspacing="0" width="520" border="0">
                    <tr>
                        <col style="width: 20%;" />
                        <col />
                        <td colspan="6">
                            <div>
                                			날짜선택
							<select name="selectedDate" onchange="ChangeDate()"> 
							<option></opton>
							<?php 
								if($reportDateResult)
								{
									while( $arr = mysql_fetch_array($reportDateResult) )
									{
										$date = date_format(date_create($arr[0]),'Y-m-d');
										echo "<option value='$date'".(($date && $selectedDate == $date) ? "selected":"")." >".$date."</option>";
									}
									mysql_data_seek( $reportDateResult, 0 );
								}
							?>
							</select>
                            </div>
							<hr style="height:2pt;" />
                            <div>
                                			기간선택          
							<select name='selectedDate_1'> 
							<option></option>
							<?php  
								if($reportDateResult)
								{
									while( $arr = mysql_fetch_array($reportDateResult) )
									{
										$date = date_format(date_create($arr[0]),'Y-m-d');
										echo "<option value='$date'".(($date && $selectedDate_1 == $date) ? "selected":"")." >".$date."</option>";
									}
									mysql_data_seek( $reportDateResult, 0 );
								}
							?>
							</select>
							&nbsp~&nbsp 
							<select name='selectedDate_2'> 
							<option></option>
							<?php  
								if($reportDateResult)
								{
									while( $arr = mysql_fetch_array($reportDateResult) )
									{
										$date = date_format(date_create($arr[0]),'Y-m-d');
										echo "<option value='$date'".(($date && $selectedDate_2 == $date) ? "selected":"")." >".$date."</option>";
									}
									mysql_free_result( $reportDateResult, 0 );
								}
							?>
							</select>
							<button type="button" onClick="SelectDate()" class="button red">확인</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="6">
							  <tr align="center" bgColor="#dddddd" >
								<td style="width:20%">보고날짜</td>
								<td style="width:20%">프로젝트명</td>
								<td style="width:15%">마켓</td>
								<td style="width:15%">버전</td>
								<td style="width:15%">프로젝트 투입시간(H)</td>
								<td style="width:15%">프로젝트 투입률(%)</td>
							  </tr>
                        </td>
                    </tr>
				    <?php
						$dateCounts=array();
					   if($allItems && $projectsNameResult && $marketNameResult)
					   {
							$projectNameArray = array();
							$marketNameArray = array();
							$allSpendTimeArray = array();

							for($i = 0; $arr = mysql_fetch_array($projectsNameResult); $i++)
							  $projectNameArray[$arr['ProjectIdx']] = $arr['ProjectName'];
							for($i = 0; $arr = mysql_fetch_array($marketNameResult); $i++)
							  $marketNameArray[$arr['MarketIdx']] = $arr['MarketName'];
							for($i = 0; $arr = mysql_fetch_array($allSpendTimeResult); $i++)
							   $allSpendTimeArray[$arr['ProjectIdx'].'_'.$arr['MarketIdx'].'_'.$arr['Date']] = $arr[0];

							$last_month = 0;

						   while($arr = mysql_fetch_array($allItems) )
            			   {
								$date = date_create($arr['Date']);
								$date = date_format($date,'Y-m-d');


							   $spend = $allSpendTimeArray[$arr['ProjectIdx'].'_'.$arr['MarketIdx'].'_'.$arr['Date']];
           					   $ratio =  round( $spend  / $totalSpendTime * 100,1);
								echo "<tr align='center' >";

								if($last_month != $date)
								{
									echo "<td id='$date'>".$date."</td>"; 
									@$dateCounts[$date]++;
								}
								else
								{
									@$dateCounts[$date]++;
								}
								
								echo"	
									<td><a href='#' onclick=ViewReport('".$arr['ProjectIdx']."','"
																		.$arr['MarketIdx']."','"
																		.$arr['ReportIdx']."','"
																		.$date."') >".$projectNameArray[$arr['ProjectIdx']]."</a></td>
									<td>".($arr['MarketIdx'] <= 0 ? "" : $marketNameArray[$arr['MarketIdx']])."</td>
									<td>".($arr['Ver'] <= 0 ? "" : $arr['Ver'])."</td>
									<td>".$spend."</td>
									<td>".$ratio."</td>
									</tr>";

							   $last_month = $date;

						   }

						if($allItems)
						  @mysql_free_result($allItems );
						if($projectsNameResult)
						  @mysql_free_result($projectsNameResult);
						if($marketNameResult)
						  @mysql_free_result($marketNameResult);
						if($allSpendTimeResult)
						  @mysql_free_result($allSpendTimeResult);
						if($totalSpendTimeResult)
						  @mysql_free_result($totalSpendTimeResult);

						}
						
					?>
                </table>
            </div>
        </div>
    </div>
    </form>


  <script type="text/javascript">
<?php
	foreach($dateCounts as $key => $value) {
		 echo "document.getElementById('$key').rowSpan=$value;\n";
	}

?>
    $('.tabmenu2').tabMenu(0);
  </script>



</body>
</html>

<?php @mysql_close($link) ?>
