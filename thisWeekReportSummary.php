<?php
	include "lib/db.php";	
	$link = DBConnect();
	$selectedPart = @addslashes($_GET['selectedPart']);
	$selectedName = @addslashes($_GET['selectedName']);
	$command = @addslashes($_GET['command']);
	

	$reportDate   = GetReportSummaryDate();

	$reportingMembers = "";
	$completeMembers = "";
	$inCompleteMembers = "";
	$partIdx=0;
	$memberNameArr = array();
	$projectNameArr = array();

	$projectsNameResult = @mysqli_query($link, "SELECT ProjectName, ProjectIdx FROM ECO_Project ORDER BY ProjectIdx");
	$allMemberResult = @mysqli_query($link, "SELECT * FROM ECO_Member WHERE MemberName != '관리자계정' AND Visible = 1 order by MemberName");
	$reportMemberResult = @mysqli_query($link, "SELECT * FROM ECO_Reports WHERE Date = '$reportDate' group by MemberIdx  ");
	$selectedPartMemberResult = "";
	$reportInfosResult = "";

	if( $_SESSION["report_login_level"] >= 3 ){	
 
		if($command=="showReport")
		{
			$selectedNameQuery = "";
			if($selectedPart && $selectedPart != "all")
			{
				$partIdxResult = mysqli_query($link, "SELECT PartIdx FROM ECO_Part WHERE Name = '$selectedPart'");
				//$partIdx = mysql_result($partIdxResult,0,0);
				$partIdx = mysqli_fetch_row($partIdxResult)[0];
				mysqli_free_result($partIdxResult);
			}
			if($selectedName)
			{
				$selectedNameQuery = "AND MemberIdx=(SELECT DISTINCT MemberIdx FROM ECO_Member WHERE MemberName = '$selectedName'  )";
			}	


			$reportInfosResult = @mysqli_query($link, "SELECT ReportIdx, MemberIdx, ProjectIdx,Report FROM ECO_Reports WHERE IsComplete = 1 
												AND Date = '$reportDate' 
												".$selectedNameQuery." order by MemberIdx");

		}

		if($selectedPart)
		{
			$selectedPartMemberResult = @mysqli_query($link, "SELECT * FROM ECO_Member WHERE MemberName != '관리자계정' 
										AND Visible = 1 AND PartIdx = (SELECT DISTINCT PartIdx FROM ECO_Part WHERE Name = '$selectedPart')
										order by MemberName");

		}
	}else if( $_SESSION["report_login_level"] == 2 ){

		$selectedNameQuery = "";
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

		$reportInfosResult = @mysqli_query($link, "SELECT ReportIdx, MemberIdx, ProjectIdx,Report FROM ECO_Reports WHERE IsComplete = 1 AND Date = '$reportDate'  order by MemberIdx");

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
			$inCompleteMembers .=  $row['MemberName'].", "; 
			$memberNameArr[$row['MemberIdx']] = $row['MemberName'];
		}
		$inCompleteMembers = rtrim($inCompleteMembers ,", ");
		mysqli_free_result($allMemberResult);
	}
	
	if($reportMemberResult)
	{
		while($row = mysqli_fetch_array($reportMemberResult) )
		{
			if($row['IsComplete'] == 1 && $row['MemberIdx'])
			{
				$completeMembers .= $memberNameArr[$row['MemberIdx']].", ";
			}
			else if($row['MemberIdx'])
			{
				$reportingMembers .= $memberNameArr[$row['MemberIdx']].", ";
			}

			if($row['MemberIdx'])
			{
				$inCompleteMembers = str_replace($memberNameArr[$row['MemberIdx']], "", $inCompleteMembers);
				$inCompleteMembers = str_replace(", ,", ",", $inCompleteMembers);
			}
		}
		mysqli_free_result($reportMemberResult);
	}


	$inCompleteMembers = rtrim($inCompleteMembers ,", ");
	$inCompleteMembers = ltrim($inCompleteMembers ,", ");
	$reportingMembers = rtrim($reportingMembers ,", ");
	$completeMembers = rtrim($completeMembers ,", ");
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
			frm.method = "GET";
			frm.reportIdx.value = reportIdx;
			frm.submit();
		}
    </script>

    <title>ECO팀 업무보고 - 업무 취합</title>
    <link rel="stylesheet" href="/css/base.css" type="text/css" />
    <link rel="stylesheet" href="/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="/css/button.css" type="text/css" />
</head>
<body onload="return resize()">
    <form id="form1" runat="server" method="GET">
	<input type="hidden" name="command" value="">
	<input type="hidden" name="reportIdx" value="">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 업무 취합
        </h1>
        <div class="divide">
            <div class="boardtype2">
                <div class="tabwrap">
                    <div class="tabmenu tabmenu2">
                        <a href="#tabcontents1">업무보고 상태</a> 
                    </div>
                    <!-- 게임프로젝트 리스트 탭-->
                    <div class="tabcontents" id="tabcontents1">
                        <p class="t2">
                            *리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>
                        <table cellpadding="0" cellspacing="0" width="520" border="0">
                            <div>
                                보고날짜 :
								<input type="text" value= "<?php echo GetReportSummaryDate() ?>" style="background-color:rgb(230,230,230);" readonly >
                               
                            </div>
							<br>
                            <div>
                            </div>
                                <table>
                                    <tr>
                                        <td width="50" align="center" style="background-color:#eeeeee">
                                            완 료
                                        </td>
                                        <td width="1000" align="left">
                                         <?php
											if($completeMembers)
												echo "$completeMembers";
										?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50" align="center" style="background-color:#eeeeee">
                                            작성중
                                        </td>
                                        <td width="1000" align="left">
                                        <?php
											if($reportingMembers)
												echo "$reportingMembers";
										?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50" align="center" style="background-color: #eeeeee">
                                            미작성				
                                        </td>
                                        <td width="1000" align="left">
										<?php
											 	if($inCompleteMembers)
													echo "$inCompleteMembers";
										?>
                                        </td>
                                    </tr>
                                </table>
                            <br />
							<br />
							<br />

							<p><font size="3">전체/파트별/개인별 업무취합</font></p><br>
		        			 <div>
							 <p class="t2">*프로젝트명 클릭시 세부내용 확인 가능합니다.</p> 	
							 </div>

							<?php if( $_SESSION["report_login_level"] == 3 ){ ?>	
                            <div class="left">파트 : 
							<?php
								echo "<select name='selectedPart' onchange='submit()'>";
								$memberPartResult = @mysqli_query($link, "SELECT * FROM ECO_Part");
								echo "<option value='all'>All</option>";
								while($row = mysqli_fetch_array($memberPartResult ))
								{
									echo "<option value='$row[Name]'".(($selectedPart==$row['Name']) ? "selected" : "").">".$row["Name"]."</option>";
								}
								echo "</select>";
								mysqli_free_result($memberPartResult);
							?>
							&nbsp이름 : 

							<?php
							 	echo "<select name='selectedName'>";
								echo "<option value=''>All</option>";
								if($selectedPartMemberResult)
								{
									while($row = mysqli_fetch_array($selectedPartMemberResult))
									{
										echo "<option value = '$row[MemberName]'".
											(($selectedName==$row['MemberName'])?"selected":"").
											">$row[MemberName]</option>";
									}
									mysqli_free_result($selectedPartMemberResult);
								}	

								echo "</select>";
							?>
							&nbsp<button type='button' class='button red' onclick='ShowReports()'>확인</button>
                            </div>

                            <?php } ?>

                    	<table cellpadding="0" cellspacing="0" width="520" border="0">
					
						<tr align="center" bgColor="#dddddd" >
						<td style="width:10%">이름</td>
						<td style="width:20%">프로젝트명</td>
						<td style="width:70%">내용</td>
					 	</tr>
						
						<?php
							$nameCounts=array();
							if($reportInfosResult)
							{
								$last_name="";
								
								while($arr = mysqli_fetch_array($reportInfosResult))
								{
								  $memberName = $memberNameArr[$arr['MemberIdx']];
								  $memberIdx = $arr['MemberIdx'];
								  $result = mysqli_query($link, "SELECT PartIdx,Visible FROM ECO_Member WHERE MemberIdx = '$memberIdx'");
								 
								  if($partIdx  && $partIdx == mysqli_result($result,0,0) && mysqli_result($result,0,1) ==  1||
									  mysqli_result($result,0,1) ==  1 &&  $selectedPart == "all")
								  {
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
								mysqli_free_result($reportInfosResult);
							}
						?>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="/js/ui.js"></script>
    <script type="text/javascript">
	<?php
		foreach($nameCounts as $key => $value) {
			 echo "document.getElementById('$key').rowSpan=$value;\n";
		}
	?>
		$('.tabmenu2').tabMenu(0);
  </script>


</body>
</html>

<?php @mysqli_close($link) ?>
