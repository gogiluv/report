<?php
	include "db.php";
	$link = DBConnect();

	$selectedProject = @addslashes($_POST['selectedProject']);
	$selectedPlatform = @addslashes($_POST['selectedPlatform']);
	$selectedMarket = @addslashes($_POST['selectedMarket']);
	$spendTime = @addslashes($_POST['spendTime']);
	$spendPercent = @addslashes($_POST['spendPercent']);
	$thisWeekReport=  @addslashes($_POST['thisWeekReportText']);
	$nextWeekReport =  @addslashes($_POST['nextWeekReportText']);
	$disabledStr = "";
	$projectIdx = @addslashes($_POST['selectedProjectIdx']);
	$marketIdx = @addslashes($_POST['marketIdx']);
	$PlatformIdx = "";
	$projectVer = @addslashes($_POST['versionText']);
	$reportDate = @addslashes($_POST['reportDate']);
	$memberIdx ="";
	$isComplete = "";
	$command = @addslashes($_POST['command']);
	$previousPageCommand = @addslashes($_POST['previousPageCommand']);
	$reportIdx = @addslashes($_POST['reportIdx']);
	$viewSelectStr = "";
	$viewTextareaStr="";
	$isGame = @addslashes($_POST['isGame']);
	$isSaveProject = @addslashes($_POST['saveProject']);
			
	if($isSaveProject == 1)
	{
		@setcookie("save_addreport_projectInfo_project",$selectedProject, time() + (86400 * 30),"/"); 
		@setcookie("save_addreport_projectInfo_platform",$selectedPlatform, time() + (86400 * 30),"/"); 
		@setcookie("save_addreport_projectInfo_market",$selectedMarket, time() + (86400 * 30),"/"); 
	}
	else if( $isSaveProject == 2 ) // 해제 
	{
		@setcookie("save_addreport_projectInfo_project","", time() - (86400 * 30)); 
		@setcookie("save_addreport_projectInfo_platform","",time() - (86400 * 30)); 
		@setcookie("save_addreport_projectInfo_market","", time() - (86400 * 30)); 
	}

	if($isSaveProject != 2 && isset($_COOKIE['save_addreport_projectInfo_project']))
	{
		$isSaveProject = 1;
	}

	if($command=="add" && 
		 isset($_COOKIE['save_addreport_projectInfo_project'])  && $isSaveProject != 2)
	{
		$selectedProject = @$_COOKIE['save_addreport_projectInfo_project'];
		$selectedPlatform = @$_COOKIE['save_addreport_projectInfo_platform'];
		$selectedMarket = @$_COOKIE['save_addreport_projectInfo_market'];
	}


	if($command=="view")
	{
		$viewSelectStr ='disabled';
		$viewTextareaStr ='readOnly';
	}

	if($command=="add")
	{
		$previousPageCommand = $command;
	}
	
	if($command == "remove")
	{
		$query = "";
		if($selectedMarket)
		{		
			$query = "AND MarketIdx = (SELECT MarketIdx FROM ECO_Market WHERE MarketName = '$selectedMarket' )";
		}

		$Date = GetReportDate();

		if($result = @mysql_query("DELETE FROM ECO_Reports WHERE ReportIdx = '$reportIdx' ",$link) )
		{
			if($result)
			{
				echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
				@mysql_free_result($result);
				exit;
			}
		}
		
	}	

	if($projectIdx && $command == "edit" || 
		$projectIdx && $command == "view")
	{
		$previousPageCommand = $command;

		if($result = @mysql_query("SELECT ProjectName FROM ECO_Project WHERE ProjectIdx = '$projectIdx'",$link) )
		{
			if( mysql_result($result,0,0) )
			{
				$selectedProject = mysql_result($result,0,0);
			}
			mysql_free_result($result);
		}
		
		if($command == "edit")
		{	$Date = GetReportDate();
			$DateQuery= "AND Date = '$Date'";
		}
		else if($command == "view")
		{
			$DateQuery= "AND Date = '$reportDate'";
		}

		//보고서 수정일때는 디비에서 정보를 가져온다.
		if($result = @mysql_query("SELECT  Report, NextReport, MarketIdx, SpendTime, SpendPercent,ReportIdx,IsComplete, Ver FROM ECO_Reports
									WHERE ReportIdx = '$reportIdx' 
									AND MarketIdx ='$marketIdx' ".$DateQuery." 
									AND MemberIdx = '$_SESSION[report_login_userIdx]' ",$link) )
		{
			while( $row = mysql_fetch_array($result) )
			{
				$thisWeekReport = $row[0];
				$nextWeekReport = $row[1];
				$marketIdx = $row[2];
				$spendTime = $row[3];
				$spendPercent = $row[4];
				$reportIdx = $row[5];
				$isComplete = $row[6];
				$projectVer = $row[7];

			}
			mysql_free_result($result);	
		}

		if($isComplete)
		{
			$viewSelectStr ='disabled';
			$viewTextareaStr ='readOnly';
		}
		if( $marketIdx )
		{	
			if($result = @mysql_query("SELECT MarketName, PlatformIdx FROM ECO_Market WHERE MarketIdx = '$marketIdx'",$link) )
			{
				while( $row = mysql_fetch_array($result) )
				{
					$selectedMarket = $row[0];
					$PlatformIdx = $row[1];
				}
				mysql_free_result($result);	
			}
		}
		if($selectedMarket)
		{
			if($result = @mysql_query("SELECT PlatformName FROM ECO_Platform WHERE PlatformIdx = '$PlatformIdx'",$link) )
			{
				while( $row = mysql_fetch_array($result) )
				{
					$selectedPlatform = $row[0];
				}
				mysql_free_result($result);	
			}
		}
	}

	if($selectedProject)
	{			
		if($result = @mysql_query("SELECT IsGame, ProjectIdx FROM ECO_Project WHERE ProjectName = '$selectedProject' ", $link))
		{	
			if( mysql_result($result,0,0) )
			{
				$disabledStr = "";
				$isGame = 1;
			}
			else
			{
				$selectedPlatform="";
				$isGame = 0;
				$disabledStr = "disabled style=\"background-color:rgb(230,230,230)\"";
			}
			
			if( mysql_result($result,0,1) )
			{
				$projectIdx =  mysql_result($result,0,1);
			}		
			mysql_free_result($result);
		}
	}	

	if($selectedMarket)
	{
		if($result = @mysql_query("SELECT MarketIdx FROM ECO_Market WHERE MarketName = '$selectedMarket' ", $link))
		{
			if( mysql_result($result,0,0) )
				$marketIdx = mysql_result($result,0,0);

			mysql_free_result($result);
		}
		
	}

	if($projectIdx && $marketIdx && $command != "addClick" && $command != "editClick")
	{
		$reportDateQuery = "";
		if($reportDate && $command == "view")
		{
			$reportDateQuery = "AND Date = '$reportDate'";
		}

		if($result = @mysql_query("SELECT MAX(Ver) FROM ECO_Reports 
									WHERE ProjectIdx = '$projectIdx' 
									AND MarketIdx = '$marketIdx'
									'$reportDateQuery' ", $link))
		{
			if( mysql_result($result,0,0) )
			{
				$projectVer = mysql_result($result,0,0);

			}
			
			else
			{
				$projectVer = "000";
			}
			



			mysql_free_result($result);
		}
		
	}

	if($projectIdx && $spendTime && $spendPercent && $reportDate && $thisWeekReport && $command)
	{
		$memberIdx = $_SESSION['report_login_userIdx'];

		if($memberIdx == "")
		{
			echo "<script type='text/javascript'> alert('로그인 정보가 없습니다. 로그인 해주세요.') </script>";
			echo "<script 'text/javascript'>window.location.href='/login.php' </script>";
			exit;
		}

		if($command == "addClick")
		{

			$result = @mysql_query("INSERT INTO ECO_Reports 
			(MemberIdx,Date,ProjectIdx,MarketIdx,SpendTime,
			 Report,NextReport,SpendPercent,IsComplete,Ver)
			VALUES('$memberIdx','$reportDate','$projectIdx','$marketIdx','$spendTime',
					'$thisWeekReport','$nextWeekReport','$spendPercent',0,'$projectVer')",$link);

			if ($result)
			{
				echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
				exit;
			
			}
			else
			{
				echo "<script type='text/javascript'> alert('보고서 추가/수정에 실패 하였습니다. 관리자에게 문의 하세요.') </script>";
			}
		

		}
		else if($command == "editClick" && $reportIdx)
		{
			
			$result = @mysql_query("UPDATE ECO_Reports SET
							ProjectIdx='".$projectIdx."',
							MarketIdx='".$marketIdx."',
							SpendTime='".$spendTime."',
							Report='".$thisWeekReport."',
							NextReport='".$nextWeekReport."',
							SpendPercent='".$spendPercent."',
							Ver = '".$projectVer."' 
							WHERE ReportIdx = $reportIdx ",$link);


			if ($result)
			{
				echo "<script 'text/javascript'>window.location.href='thisWeekReport.php' </script>";
				exit;
			
			}
			else
			{
				echo "<script type='text/javascript'> alert('보고서 추가/수정에 실패 하였습니다. 관리자에게 문의 하세요.') </script>";
			}

		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />

    <script type="text/javascript">
		var frm = document.forms.formSignup;
        function resize() {
            parent.fncResizeHeight(document);
        }
        
        function changeForm() {
        	document.forms.formSignup.saveProject.value = 2;
			document.forms.formSignup.submit();
       }
      
        function changeSpendTime() {
            document.forms.formSignup.spendPercent.value = (document.forms.formSignup.spendTime.value/40)*100;

        }
        
        function changeVersion()
        {
			var frm = document.forms.formSignup;

            frm.versionText.value = "" +
			frm.selectVer_1.value + 
			frm.selectVer_2.value +
			frm.selectVer_3.value;

			resize();

        }

		function AutoInputReport(selectedWeek)
		{
			var frm = document.forms.formSignup;
			var userName ="<?php echo $_SESSION['report_login_user'] ?>";
			var selectedProject = frm.selectedProject.value;
			var selectedPlatform = frm.selectedPlatform.value;
			var selectedMarket = frm.selectedMarket.value;
			var spendPercent = frm.spendPercent.value;
			var isGame = frm.isGame.value;
			var projectVersion = "";

			if(selectedProject == "") 
			{
				alert("프로젝트를 선택해주세요.");
				
				return;
			}
			
			if( selectedPlatform != "" && isGame)			//게임이면
			{				
				if(selectedPlatform == "") {
					alert("플랫폼을 선택해주세요."); 
					return;
				}	 
				else if(selectedMarket == "") {
				 	alert("마켓을 선택해주세요.");
					return; 
				}
				else if(spendPercent == "") {
				 	alert("투입시간을 입력해주세요.");
					return;
				}

			  projectVersion = "(v." + frm.selectVer_1.value + "." +
				frm.selectVer_2.value + "." +  
				 frm.selectVer_3.value  + ") " ;

			}
			else if(selectedPlatform == "")	//게임이 아니면 
			{
				if(spendPercent == "") {
					 alert("투입시간을 입력해주세요."); 
					return;
				}
			}

			if(selectedWeek =='thisWeekReportText' && frm.thisWeekReportText.value == "")
			{
				frm.thisWeekReportText.value = "● "
						+ selectedProject + projectVersion
						+ selectedPlatform + " "
						+ selectedMarket + " ["
						+ userName         + "/주간투입률 " +
						spendPercent+"%]" +"\n   -";
			}
			else if(selectedWeek =='thisWeekReportText' && frm.thisWeekReportText.value != "")
			{
				var end = frm.thisWeekReportText.value.indexOf("%]");
				var firstString = frm.thisWeekReportText.value.substring(0, end);
				var secondString = frm.thisWeekReportText.value.substring(end+2, frm.thisWeekReportText.value.length);

				firstString = "● "
						+ selectedProject + projectVersion
						+ selectedPlatform + " "
						+ selectedMarket + " ["
						+ userName         + "/주간투입률 " +
						spendPercent+"%]";
				frm.thisWeekReportText.value = firstString + secondString;

			}
			else if( selectedWeek =='nextWeekReportText' )
			{
				frm.nextWeekReportText.value = "● " + selectedProject + " "
						+ selectedPlatform + " "
						+ selectedMarket+"\n   -";
			}
		}

		function thisWeekReportTextEnter(event)
		{
			if(event.keyCode === 13)
			{
				//document.getElementById("thisWeekReportText").value +="  -";
				var textarea = document.getElementById('thisWeekReportText');
				textarea.scrollTop = textarea.scrollHeight;
			}
		}
		function nextWeekReportTextEnter(event)
		{
			if(event.keyCode === 13)
			{
				//document.getElementById("nextWeekReportText").value +="";
				var textarea = document.getElementById('nextWeekReportText');
				textarea.scrollTop = textarea.scrollHeight;
			}
		}
		function enterKeyPress(event)
		{
			var frm = document.forms.formSignup;
			if(13 === event.keyCode )
			{
				frm.spendPercent.value = (frm.spendTime.value/40)*100;
				AutoInputReport('thisWeekReportText'); 
				
			}
		}
		function AddReport(state)
		{
			var frm = document.forms.formSignup;
			var projectVersion = frm.versionText.value;
			var selectedProject = frm.selectedProject.value;
			var selectedPlatform = frm.selectedPlatform.value;
			var selectedMarket = frm.selectedMarket.value;
			var spendPercent = frm.spendPercent.value;
			var spendTime = frm.spendTime.value;
			var thisWeekReport = frm.thisWeekReportText.value;
			var nextWeekReport = frm.nextWeekReportText.value;
			var isGame = frm.isGame.value;
			
			if(selectedProject == "")
			{
				alert("프로젝트가 선택되지 않았습니다.");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}
			if(selectedPlatform == "" && isGame == "1")
			{
				alert("플랫폼이 선택되지 않았습니다.");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}
			if(selectedMarket == "" && isGame == "1")
			{
				alert("마켓이 선택되지 않았습니다.");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}
			if(spendTime == "") 
			{
				alert("투입시간이 입력되지 않았습니다.");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}
			if(thisWeekReport == "")
			{
				 alert("보고서가 입력되지 않았습니다.");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}

			if( Number(projectVersion) < "<?php echo $projectVer ?>")
			{
				alert("버전이 낮습니다. 최종 버전은<?php echo $projectVer;?>입니다. ");
				frm.command.value = frm.previousPageCommand.value;
				return false;
			}
			frm.command.value = state;
			frm.submit();

		}

		function RemoveReport()
		{
			var frm = document.forms.formSignup;
			frm.command.value = "remove";
			frm.submit();
		}
		function SaveProject(chkbox)
		{
			var frm = document.forms.formSignup;

			if(chkbox.checked)
			{
				if(frm.selectedProject.value == "")
				{
					alert("저장 할 프로젝트가 없습니다. 프로젝트를 선택하세요.");
					chkbox.checked = false;
					return;
				}
				frm.saveProject.value = 1;
			}
				
			else
				frm.saveProject.value = 2;

			frm.submit();
		}
		function viewPastReport()
		{
  			open("/pastReports.php", "PastReports", "width=1200, height=1000, resizable=yes" );  
		}
    </script>

    <title>ECO팀 업무보고 - 이번주 보고서</title>
</head>
<body onload="changeVersion();">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 보고서 추가
        </h1>
        <p class="t2">
            * ECO팀 주간업무보고는 매주 목요일 입니다.</p>
        <div class="divide">
        </div>
    </div>
    <form id="FormAddReport" name="formSignup"  method="post"  enctype="multipart/form-data">
	<input type="hidden" name="command" value= "<?php echo $command; ?>"/>
	<input type="hidden" name="reportIdx" value= "<?php echo $reportIdx; ?>"/>
	<input type="hidden" name="isGame" value= "<?php echo $isGame; ?>"/>
	<input type="hidden" name="saveProject"/>

	<input type="hidden" name="previousPageCommand" value= "<?php echo $previousPageCommand; ?>"/>

    <div class="C2Scontent">
        <div class="boardtype2">
            <table class="mb10">
                <col style="width: 20%;" />
                <col />
                <tbody>
                    <tr>
                        <th>
                            현재날짜
                        </th>
                        <td>
                           <?php echo Date("Y-m-d") ?>
                        </td>
                        <th>
                            현재요일
                        </th>
                        <td>
							<?php 
							$week = Date('w');
							if($week == 1) echo "월요일";
							else if($week == 2) echo "화요일";
							else if($week == 3) echo "수요일";
							else if($week == 4) echo "목요일";
							else if($week == 5) echo "금요일";
							else if($week == 6) echo "토요일";
							else echo "일요일";
							?>

                        </td>
                        <th>
                            보고예정날짜
                        </th>
                        <td>
                            <?php
								if($command != "view")
									$reportDate = GetReportDate();
							echo "<input type='text' name='reportDate' style='background-color:transparent;border:0px solid white;' 
									value='$reportDate' readonly/>";
							?>

                        </td>
                    </tr>
                    <tr><td colspan="6"></td></tr>

                    <tr>
                        <th scope="col" colspan="6" class="al">
                            <div align="center">
                                업무내용</div>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            프로젝트명
                        </th>
                        <td colspan="5">
                            <div class="divide">
                                <div class="left">
									<select name="selectedProject" onchange="changeForm()" style="width: 200px" <?php echo $viewSelectStr ?> >
									<option value=''>---------------------</option>
									<?php

									//$sql = "SELECT ProjectIdx FROM ECO_Reports WHERE MemberIdx='$_SESSION[report_login_userIdx]' GROUP BY ProjectIdx";
									//$sql = "SELECT ProjectName FROM ECO_Project WHERE ProjectIdx IN ($sql)";

									$sql = "SELECT * FROM ECO_Project P LEFT JOIN ECO_Reports E ON P.ProjectIdx=E.ProjectIdx WHERE MemberIdx='$_SESSION[report_login_userIdx]' GROUP BY E.ProjectIdx ORDER BY COUNT(E.ProjectIdx) DESC";
									if ($result = mysql_query($sql, $link))
									{
										for($i = 0; $row = mysql_fetch_array($result); $i++)
										{
											echo "<option value='$row[ProjectName]'>$row[ProjectName]</option>";
										}
										echo "<option value=''>--------------------</option>";
										@mysql_free_result($result);
									}


									if($result = mysql_query("SELECT * FROM ECO_Project order by IsGame,ProjectName asc",$link ))
									{
										while($row = mysql_fetch_array($result))
										{
											if($selectedProject == $row["ProjectName"])
											echo "<option value='$row[ProjectName]' selected>$row[ProjectName] </option>";
											else
											echo "<option value='$row[ProjectName]'>$row[ProjectName] </option>";
										}
										@mysql_free_result($result);
									}
										
									?>
									</select>
                                </div>
                                <div class="right">
 
                                </div>
                            </div>
                            <div class="t2">
                                게임업무 외에 기타업무는 플랫폼,마켓을 선택할 수 없습니다.</div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            플랫폼명
                        </th>
                        <td colspan="5">
                            <div class="divide">
                                <div class="left">
									<?php	
										echo "<select $disabledStr name=\"selectedPlatform\" onchange= \"changeForm()\" style=\"width: 200px\" $viewSelectStr>";

										if($selectedPlatform == "" && $disabledStr  == "" )
											echo "<option value=\"\">Select Platform..</option>";
					
										
										if( $disabledStr  == "" && $selectedProject != "")
										{	
											if($result = mysql_query("SELECT * FROM ECO_Platform order by PlatformName asc",$link))
											{
												while($row = mysql_fetch_array($result))
												{
													if($selectedPlatform == $row["PlatformName"])
													echo"<option value='$row[PlatformName]' selected style=\"width:280px\"> $row[PlatformName] </option>";
													else
													echo"<option value='$row[PlatformName]'> $row[PlatformName] </option>";
												}
												mysql_free_result($result);
											}
										}
									?>
									</select>
                                </div>
                                <div class="right">
                                 
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            마켓명
                        </th>
                        <td colspan="5">
                            <div>
                                <?php	
										echo "<select $disabledStr name= \"selectedMarket\"  
												onchange=\"changeForm()\" style=\"width: 130px\" $viewSelectStr>";
										
										if($selectedPlatform == ""&& $disabledStr  == "" )
											echo "<option value=\"\">Select Platform..</option>";
										else if($selectedPlatform != ""&& $disabledStr  == "" )
											echo "<option value=\"\">Select Market..</option>";
										
										
											
																		
									if($result = mysql_query("SELECT MarketName From ECO_Market 
															WHERE PlatformIdx = (SELECT PlatformIdx FROM ECO_Platform 
															WHERE PlatformName = '$selectedPlatform')",$link)  )

									{	
										while ( $row = mysql_fetch_array($result) )
										{
											if( $selectedMarket == $row[MarketName])
												echo"<option value='$row[MarketName]' selected> $row[MarketName] </option>";
											else
												echo"<option value='$row[MarketName]'> $row[MarketName] </option>";
										}
										mysql_free_result($result);
									}
								?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            버전
                        </th>
                        <td colspan="5">
                            <div>
								<input type="text" value="0" name="versionText" style="width:150px;background-color:rgb(230,230,230)" readOnly/>
								<?php echo "<select name= \"selectVer_1\" $disabledStr class=\"inselect\" 
											onchange=\"changeVersion()\" $viewSelectStr> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == (int)($projectVer/100) )
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>
								
								<?php echo "<select name= \"selectVer_2\" $disabledStr class=\"inselect\" 
											onchange=\"changeVersion()\" $viewSelectStr> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == (int)(($projectVer%100)/10))
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>


								<?php echo "<select name= \"selectVer_3\" $disabledStr class=\"inselect\" 
											onchange=\"changeVersion()\" $viewSelectStr> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == $projectVer%10)
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>
								<?php if($projectVer == 0 && $disabledStr == "") $projectVer = "000"; ?>
								<?php 
									if($projectVer && $disabledStr == "" && $command != "view")
									{
										printf("<label> 최종 버전은 %03d 입니다.</label>",$projectVer); 
									}
								?>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            투입시간
                        </th>
                        <td colspan="3">
                            <div>
							<input type="text" name="spendTime" size="5"
							<?php echo " onkeypress=\"enterKeyPress(event)\" ";?>
							 onchange="changeSpendTime()" 
							<?php 
								if($spendTime&&$command=="edit" || spendTime&&$command=="add") 
								{
									echo "value='$spendTime'";
								}
								else if($spendTime && $command=="view" ) 
								{
									echo "value='$spendTime' readOnly";
								}
							?> >  H </input>
						
                            </div>
                        </td>
                        <th>
                            주간투입률
                        </th>
                        <td colspan="3">
							<input type="text" name="spendPercent" style="width:25px; background-color:rgb(230,230,230)" 
							<?php 
								if($spendPercent && $command=="edit" ||
									$spendPercent && $command=="view" ||
									$spendPercent && $command=="add") 
									echo "value='$spendPercent'" ?>readonly> % </input>
                        </td>
                    </tr>
					<?php 
					if($command!="view")
					{
						echo "<tr>
				                <th scope=\"row\" class=\"al\" colspan=\"6\">
				                    <div class=\"t2\">
				                 				   ※ 작성예시 (다음과 같이 작성하며, 프로젝트 자동입력버튼으로 간편하게 입력가능합니다.) ※
				                    </div>
				                    <div class=\"t3\">
				              						      ● 슬라이스잇 Android Google 작업중 [홍길동/주간투입률50%]<br />
				                     						- 이용약관 적용작업<br /><br />
												    ● 타워디펜스 Android Tstore 납품완료 [이상민/주간투입률20%]<br />
												     - 이용약관 적용작업완료<br />
												     - 납품완료(2013-01-03)<br />
				                    </div>
				                </th>
				            </tr>";
					}
					?>
                    <tr>
                        <th scope="row" class="al">
                         			   주간업무 요약<br />
							<button type="button" id="createProjectText" class="button green" 
							onclick="AutoInputReport('thisWeekReportText') ">프로젝트명 자동입력</input>
                        </th>
                        <td colspan="5" height="60">
							<textarea name="thisWeekReportText" style="width:800px;height:200px;"onkeypress="thisWeekReportTextEnter(event)"
							 <?php echo $viewTextareaStr?> ><?php if($thisWeekReport) {echo $thisWeekReport; }?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            다음주 업무일정<br />
							<button type="button" id="createProjectText" class="button green"
							 onclick="AutoInputReport('nextWeekReportText')">프로젝트명 자동입력</input>
                       
                        </th>
                        <td colspan="5">
                            <textarea name="nextWeekReportText"  style="width:800px;height:200px;"onkeypress="nextWeekReportTextEnter(event)"
							 <?php echo $viewTextareaStr?> ><?php if($nextWeekReport) {echo $nextWeekReport; }?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="right">
            	<?php
            		if($command != "view")
            		{
            			echo "<input type=\"checkBox\" onclick= \"SaveProject(this)\"";
            		
            			if( $isSaveProject == 1)
            				echo "checked";
    	
            			echo " >이 프로젝트 정보를 기억합니다.</intput>";
            		}
            	?>
            	&nbsp&nbsp
            	<button type="button" onclick="viewPastReport()" class="button red" <?php if($command == "view") { echo " style=\"visibility:hidden;\""; } ?> >지난보고서 보기</button>
				<?php 
					if($command=="edit"||$command=="add")
					{
						if($isComplete == 0)
							echo "<button type= \"button\" class=\"button red\"";
					}

					if($command=="edit" && $isComplete == 0)
					{
						echo "onclick='AddReport(\"editClick\")'>보고서 수정</button>";
					}
					else if($command=="add" && $isComplete == 0)
					{
						 echo "onclick='AddReport(\"addClick\")'>보고서 추가</button>"; 
					}
				?>
				<?php  if($command=="edit"  && $isComplete == 0) 
						{
							echo "<button type= 'button' class='button black' onclick='RemoveReport()'>보고서 삭제</button>"; 
						}
				?>
				
            </div>
        </div>
    </div>
    </form>
</body>
</html>

<?php @mysql_close($link) ?>
