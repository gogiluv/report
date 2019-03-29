
<?php
	include "lib/db.php";
	$link = DBConnect();
	$disabledStr = "";
	$projectVer = "";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />

    <script type="text/javascript">
        var EditReport_thisWeek_Height = 300;
     
 		  function add() {
            EditReport_thisWeek_Height += 100;
            document.getElementById("EditReport_thisWeek").style.height = EditReport_thisWeek_Height + "px";
            resize();
        }

       
        function resize() {
            parent.fncResizeHeight(document);
        }
        
        function changeForm() {
			//var frm = document.forms.formSignup;
			//frm.projectName.value;
			 var selectedProject = document.getElementById('selectedProject').value;
			 var selectedPlatform = document.getElementById('selectedPlatform').value;
			 var selectedMarket = document.getElementById('selectedMarket').value;
              
				window.location.href = "addReport.php?selectedProject="+
									selectedProject+"&selectedPlatform="+
									selectedPlatform+"&selectedMarket="+
									selectedMarket;
       }

        function changeSpendTime() {
            document.getElementById("spendPercent").value = (document.getElementById("spendTime").value/40)*100;
        }
        
        function changeVersion()
        {
            resize();

            if( document.getElementById("selectVer_1").value == "0" && 
				document.getElementById("selectVer_2").value == "0" &&
				document.getElementById("selectVer_3").value == "0")
            {
                document.getElementById("versionText").value = "0";
            }
            else
            {
                document.getElementById("versionText").value = "" +
				document.getElementById("selectVer_1").value + 
				document.getElementById("selectVer_2").value +
				document.getElementById("selectVer_3").value;
            }
        }

		function AutoInputReport(selectedWeek)
		{
			var userName ="<?php echo $_SESSION['report_login_user'] ?>";
			var selectedProject = document.getElementById("selectedProject").value;
			var selectedPlatform = document.getElementById("selectedPlatform").value;
			var selectedMarket = document.getElementById("selectedMarket").value;
			var spendPercent = document.getElementById("spendPercent").value;
			var projectVersion = "";


			if(selectedProject == "") 
			{
				alert("프로젝트를 선택해주세요.");
				return;
			}
			
			if( selectedPlatform != "")			//게임이면
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

			  projectVersion = "(v." + document.getElementById("selectVer_1").value + "." +
				 document.getElementById("selectVer_2").value + "." +  
				 document.getElementById("selectVer_3").value  + ") " ;

			}
			else if(selectedPlatform == "")	//게임이 아니면 
			{
				if(spendPercent == "") {
					 alert("투입시간을 입력해주세요."); 
					return;
				}
			}
			
			if(selectedWeek=='thisWeekReportText')
			{
				document.getElementById(selectedWeek).value = "● "
						+ selectedProject + projectVersion
						+ selectedPlatform + " "
						+ selectedMarket + " ["
						+userName         + "/주간투입률 " +
						Math.floor(spendPercent) +"%]" +"\n   -";;	
			}
			else
			{
				document.getElementById(selectedWeek).value = "● " + selectedProject + " "
						+ selectedPlatform + " "
						+ selectedMarket+"\n   -";
			}
		}
		function CheckReport()
		{
			var userName ="<?php echo $_SESSION['report_login_user'] ?>";
			var selectedProject = document.getElementById("selectedProject").value;
			var selectedPlatform = document.getElementById("selectedPlatform").value;
			var selectedMarket = document.getElementById("selectedMarket").value;
			var spendPercent = document.getElementById("spendPercent").value;
			var thisWeekReport = document.getElementById("thisWeekReportText").value;			
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
			if(13 === event.keyCode)
			{
				document.getElementById("spendPercent").value = (document.getElementById("spendTime").value/40)*100;
				AutoInputReport('thisWeekReportText'); 
				document.getElementById("thisWeekReportText").focus(); 
			}
		}
		function AddReport()
		{
			var projectVersion = document.getElementById("selectVer_1").value +
				 document.getElementById("selectVer_2").value +
				 document.getElementById("selectVer_3").value;

			if( projectVersion >= "<?php echo $projectVer ?>")
			{
				
			}
			else
			{
				alert("버전이 낮습니다. 최종 버전은<?php echo $projectVer;?>입니다. ");
				return
			}

			var selectedProject = document.getElementById("selectedProject").value;
			var selectedPlatform = document.getElementById("selectedPlatform").value;
			var selectedMarket = document.getElementById("selectedMarket").value;
			var spendPercent = document.getElementById("spendPercent").value;
			var spendTime = document.getElementById("spendTime").value;
			var thisWeekReport = document.getElementById("thisWeekReportText").value;
			var nextWeekReport = document.getElementById("nextWeekReportText").value;
			var reportDate = document.getElementById("reportDate").value;
			

			window.location.href = "addReport.php?selectedProject="+ selectedProject+
									"&selectedPlatform="+selectedPlatform+
									"&selectedMarket="+selectedMarket + 
									"&spendPercent="+spendPercent +
									"&spendTime="+spendTime+
									"&thisWeekReport="+ thisWeekReport+
									"&nextWeekReport="+nextWeekReport+
									"&ver="+projectVersion+
									"&reportDate="+reportDate+
									"&addReport=1";
		}
        
    </script>

    <title>ECO팀 업무보고 - 이번주 보고서</title>
</head>
<body onload="return changeVersion()">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 보고서 추가
        </h1>
        <p class="t2">
            * ECO팀 주간업무보고는 매주 목요일 입니다.</p>
        <div class="divide">
        </div>
    </div>
    <form id="FormEditReport" name="formSignup" runat="server" method="post" enctype="multipart/form-data">
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
								$reportDate = GetReportDate();
							echo "<input type='text' id='reportDate' style='background-color:transparent;border:0px solid white;' value='$reportDate'/>";
							?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        </td>
                    </tr>
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
                        <td colspan="6">
                            <div class="divide">
                                <div class="left">
                                   <select name="projectName"  id="selectedProject" onchange="changeForm()" style="width: 200px">
									<option vale=""></option>
									<?php
									if($result = mysqli_query($link, "SELECT * FROM ECO_Project order by IsGame,ProjectName asc"))
									{
										while($row = mysqli_fetch_array($result))
										{	
											if($selectedProject == $row["ProjectName"])
											echo "<option value='$row[ProjectName]' selected> $row[ProjectName] </option>";
											else
											echo "<option value='$row[ProjectName]'> $row[ProjectName] </option>";
										}
										@mysqli_free_result($result);
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
                            플랫폼명
                        </th>
                        <td colspan="6">
                            <div class="divide">
                                <div class="left">
                                    <?php	
										echo "<select $disabledStr id=\"selectedPlatform\" onchange= \"changeForm()\" style=\"width: 200px\">";

										if($selectedPlatform == "" && $disabledStr  == "" )
											echo "<option value=\"\">Select Platform..</option>";
					
										
										if( $disabledStr  == "")
										{	
											if($result = mysqli_query($link, "SELECT * FROM ECO_Platform order by PlatformName asc"))
											{
												while($row = mysqli_fetch_array($result))
												{
													if($selectedPlatform == $row["PlatformName"])
													echo"<option value='$row[PlatformName]' selected style=\"width:280px\"> $row[PlatformName] </option>";
													else
													echo"<option value='$row[PlatformName]'> $row[PlatformName] </option>";
												}
												mysqli_free_result($result);
											}
										}
									?>
									</select>
                                </div>
                                <div class="right">
                                    <asp:Button ID="ButtonPlatform" runat="server" Text="플랫폼선택" OnClick="LOAD_MarketLIST"
                                        class="button red" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            마켓명
                        </th>
                        <td colspan="6">
                            <div>
                                <?php	
										echo "<select $disabledStr id= \"selectedMarket\"  onchange=\"changeForm()\" style=\"width: 130px\">";
										
										if($selectedPlatform == ""&& $disabledStr  == "" )
											echo "<option value=\"\">Select Platform..</option>";
										else if($selectedPlatform != ""&& $disabledStr  == "" )
											echo "<option value=\"\">Select Market..</option>";
										
										
											
																		
									if($result = mysqli_query($link, "SELECT MarketName From ECO_Market 
															WHERE PlatformIdx = (SELECT PlatformIdx FROM ECO_Platform 
															WHERE PlatformName = '$selectedPlatform')")  )

									{	
										while ( $row = mysqli_fetch_array($result) )
										{
											if( $selectedMarket == $row[MarketName])
												echo"<option value='$row[MarketName]' selected> $row[MarketName] </option>";
											else
												echo"<option value='$row[MarketName]'> $row[MarketName] </option>";
										}
										mysqli_free_result($result);
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
								<input type="text" value="0" id="versionText" style="width:150px;background-color:rgb(230,230,230)" readOnly/>
								<?php echo "<select id= \"selectVer_1\" $disabledStr class=\"inselect\" onchange=\"changeVersion()\"> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == (int)($projectVer/100) )
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>
								
								<?php echo "<select id= \"selectVer_2\" $disabledStr class=\"inselect\" onchange=\"changeVersion()\"> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == (int)(($projectVer%100)/10))
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>

								<?php echo "<select id= \"selectVer_3\" $disabledStr class=\"inselect\" onchange=\"changeVersion()\"> "?>
								<?php  for($i = 0; $i < 10; $i++) {
										if ( $projectVer && $disabledStr == "" && $i == $projectVer%10)
											echo "<option value='$i' selected >$i</option>"; 
										else
						 					echo "<option value='$i' >$i</option>";
									
										} ?>
								</select>
								<?php if($projectVer && $disabledStr == "") echo "<label> 최종 버전은 $projectVer 입니다.</label>" ;  ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
            			                투입시간
                        </th>
                        <td colspan="3">
                            <div>
							<input type="text" id="spendTime" style="width:25px;" 
							onkeypress="enterKeyPress(event)" onchange="changeSpendTime()"  > H </input>
						
                            </div>
                        </td>
                        <th>
                            			주간투입률
                        </th>
                        <td colspan="3">
							<input type="text" id="spendPercent" style="width:25px; background-color:rgb(230,230,230)" readonly> % </input>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                         			   주간업무 요약<br />
							<button type="button" id="createProjectText" class="button green" 
							onclick="AutoInputReport('thisWeekReportText') ">프로젝트명 자동입력</input>
                        </th>
                        <td colspan="5" height="60">
							<textarea name="thisWeekReportText" id="thisWeekReportText" style="width:800px;height:200px;"
							onkeypress="thisWeekReportTextEnter(event)"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                           				 다음주 업무일정<br />
							<button type="button" id="createProjectText" class="button green"
							 onclick="AutoInputReport('nextWeekReportText')">프로젝트명 자동입력</input>
                       
                        </th>
                        <td colspan="5">
                            <textarea name="nextWeekReportText" id="nextWeekReportText" style="width:800px;height:200px;"
							onkeypress="nextWeekReportTextEnter(event)" class="intext"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="right">
                <asp:Button ID="Button_EditReport" runat="server" Text="보고서 수정" OnClick="checkEditReport"
                    class="button red" />
                <asp:Button ID="Button_DeleteReport" runat="server" Text="보고서 삭제" OnClick="checkDeleteReport"
                    class="button black" />
            </div>
        </div>
    </div>
    </form>
</body>
</html>

<?php @mysqli_close($link) ?>
