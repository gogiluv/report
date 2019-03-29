<?php
	include "db.php";
	$link = DBConnect();
	$tabIdx =@addslashes($_POST['tabIdx']);
	$selectState =@addslashes($_POST['addEditDelSelectPost']);
	$command =@addslashes($_POST['command']);
	$firstText =@addslashes($_POST['firstTextPost']);
	$middleText =@addslashes($_POST['middleTextPost']);
	$endText =@addslashes($_POST['endTextPost']);

	if($command == "apply")
	{
		if($selectState=="add")
		{
			if($tabIdx == "1" || $tabIdx == "4")
			{
				$isGame =  (($tabIdx == 1) ? 1:0);
				mysql_query("INSERT INTO ECO_Project (ProjectName,IsGame) Value('$endText',$isGame)",$link);
			}
			else if($tabIdx == "2")
			{
				mysql_query("INSERT INTO ECO_Platform (PlatformName) Value('$endText')",$link);
			}
			else if($tabIdx == "3")
			{
				$result = @mysql_query("SELECT PlatformIdx FROM ECO_Platform WHERE PlatformIdx = '$endText'",$link);
				if( @mysql_result($result,0,0) )
				{
					mysql_query("INSERT INTO ECO_Market (MarketName,PlatformIdx) Value('$middleText','$endText')",$link);
				}
				else
				{
					echo "<script javascript/text>alert('유효하지 않은 플랫폼번호 입니다. ')</script>";
				}
				@mysql_free_result($result);	
			}
		}
		else if($selectState=="edit")
		{
			if($tabIdx == "1" || $tabIdx == "4")
			{
				mysql_query("UPDATE ECO_Project SET ProjectName = '$endText' WHERE ProjectIdx = '$firstText'",$link);
			}
			else if($tabIdx == "2")
			{
				mysql_query("UPDATE ECO_Platform SET PlatformName = '$endText' WHERE PlatformIdx = '$firstText'",$link);
			}
			else if($tabIdx == "3")
			{
				$result = @mysql_query("SELECT PlatformIdx FROM ECO_Platform WHERE PlatformIdx = '$endText'",$link);
				if( @mysql_result($result,0,0) )
				{
					mysql_query("UPDATE ECO_Market SET MarketName = '$middleText', PlatformIdx = '$endText' WHERE MarketIdx = '$firstText'",$link);
				}
				else
				{
					echo "<script javascript/text>alert('유효하지 않은 플랫폼번호 입니다. ')</script>";
				}
				@mysql_free_result($result);				
			}
		}
		else if($selectState=="delete")
		{
			if($tabIdx == "1" || $tabIdx == "4")
			{
				mysql_query("DELETE FROM ECO_Project WHERE ProjectIdx = '$firstText'",$link);
			}
			else if($tabIdx == "2")
			{
				//mysql_query("DELETE FROM ECO_Platform WHERE PlatformIdx = '$firstText'",$link);	
			}
			else if($tabIdx == "3")
			{
				//mysql_query("DELETE FROM ECO_Market WHERE MarketIdx = '$firstText'",$link);			
			}
		}
	}
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
    <script type="text/javascript" src="http://manager.com2us.com/guide/js/ui.js"></script>
	<script type="text/javascript">
    function changetab(num) {

		document.forms.FormProjectList.tabIdx.value = num;
		AddEditDel();
        resize();

    }
	function AddEditDel()
	{
		var frm = document.forms.FormProjectList;	
		
		var select = document.getElementsByName("addEditDelSelect")[frm.tabIdx.value-1];
		var firstText = document.getElementsByName("firstText")[frm.tabIdx.value-1];
		var middleText = document.getElementsByName("middleText")[0];
		var endText = document.getElementsByName("endText")[frm.tabIdx.value-1];
		
		switch(select.value)
		{
		 	case "add":
			firstText.readOnly= true;
			firstText.value = "";
			firstText.style="background-color:#eeeeee";
			endText.readOnly= false;
			endText.style="background-color:#ffffff";

			if(frm.tabIdx.value == 3)
			{
				middleText.readOnly= false;
				middleText.style="background-color:#ffffff";
			}
			break;

			case "edit":
			firstText.readOnly= false;
			firstText.style="background-color:#ffffff";
			endText.readOnly= false;
			endText.style="background-color:#ffffff";
			
			if(frm.tabIdx.value == 3)
			{
				middleText.readOnly= false;
				middleText.style="background-color:#ffffff";
			}
			break;

			case "delete":
			endText.readOnly= true;
			endText.value = "";
			endText.style="background-color:#eeeeee";
			firstText.readOnly= false;
			firstText.style="background-color:#ffffff";

			if(frm.tabIdx.value == 3)
			{
				middleText.readOnly= true;
				middleText.value="";
				middleText.style="background-color:#eeeeee";
			}
			break;
		}


	}
	function Apply()
	{
		var frm = document.forms.FormProjectList;	
		var select = document.getElementsByName("addEditDelSelect")[frm.tabIdx.value-1];
		var firstText = document.getElementsByName("firstText")[frm.tabIdx.value-1];
		var middleText = document.getElementsByName("middleText")[0];
		var endText = document.getElementsByName("endText")[frm.tabIdx.value-1];
	
		if(select.value == "add")
		{	
			if(frm.tabIdx.value == 3)
			{
				if(middleText.value == "" || endText.value == "")
				{
					alert("빈칸이 있습니다. 빈칸을 채워주세요.");
					return;
				}
				else if(middleText.value && endText.value)
				{
					frm.endTextPost.value = endText.value;
					frm.middleTextPost.value = middleText.value;
				}
			}
			else if( frm.tabIdx.value == 1 || frm.tabIdx.value == 2 || frm.tabIdx.value == 4)
			{
				if(endText.value == "")
				{
					alert("빈칸이 있습니다.  빈칸을 채워주세요.");
					return;
				}
				else if(endText.value)
				{
					frm.endTextPost.value = endText.value;
				}
				
			}
			else
			{
				alert("에러! 관리자에게 문의 하세요.");
				return;
			}
			
		}
		else if(select.value == "edit")
		{
			if(frm.tabIdx.value == 3)
			{
				if(firstText.value == "" || middleText.value == "" || endText.value == "")
				{
					alert("빈칸이 있습니다. 빈칸을 채워주세요.");
					return;
				}
				else if(firstText.value && middleText.value && endText.value)
				{
					frm.firstTextPost.value = firstText.value;
					frm.middleTextPost.value = middleText.value;
					frm.endTextPost.value = endText.value;
				}
			}
			else if( frm.tabIdx.value == 1 || frm.tabIdx.value == 2 || frm.tabIdx.value == 4)
			{
				if(firstText.value == "" || endText.value == "")
				{
					alert("빈칸이 있습니다. 빈칸을 채워주세요.");
					return;
				}
				else if(firstText.value && endText.value)
				{
					frm.firstTextPost.value = firstText.value;
					frm.endTextPost.value = endText.value;
				}
			}
			else
			{
				alert("에러! 관리자에게 문의 하세요.");
				return;
			}
		}
		else if(select.value == "delete")
		{
			if(firstText.value == "")
			{
				alert("빈칸이 있습니다. 빈칸을 채워주세요.");
				return;
			}
			else if(firstText.value)
			{
				frm.firstTextPost.value = firstText.value;
			}
		}
		
		frm.command.value = "apply";
		frm.addEditDelSelectPost.value = select.value;
		frm.submit();	
	}
 
    function resize() {
		//AddEditDel();
        parent.fncResizeHeight(document);
    }
	function load()
	{
		var frm = document.forms.FormProjectList;
		frm.tabIdx.value = 1;

		for(var i = 0; i< 4; ++i)
		{	
			document.getElementsByName("addEditDelSelect")[i].value = "add";
		}
		AddEditDel();
		resize();
	}
    </script>

    <title>ECO 업무보고 - 프로젝트관리 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>

<!-- 프로젝트 관리 출력 부분-->
<body  onload="load()">
    <h1 class="location">관리메뉴 &gt; 프로젝트 관리 </h1>
    <div class="divide"></div>

    <div class="C2Scontent">
        <div class="boardtype2">
            <!-- 프로젝트 관리 탭 메뉴 -->
            <div class="tabwrap">
                <div class="tabmenu tabmenu2">
                    <a href="#tabcontents1" onclick="changetab(1)">게임 리스트</a>
                    <a href="#tabcontents2" onclick="changetab(2)">플랫폼 리스트</a>
                    <a href="#tabcontents3" onclick="changetab(3)">마켓 리스트</a>
                    <a href="#tabcontents4" onclick="changetab(4)">기타업무 리스트</a>
                </div>

                <!-- 게임프로젝트 리스트 탭-->
                <form id="FormProjectList" method="post">
				<input type="hidden" name="tabIdx" value="">
				<input type= "hidden" name="command" value="">
				<input type= "hidden" name="addEditDelSelectPost" value="">
				<input type= "hidden" name="firstTextPost" value="">
				<input type= "hidden" name="middleTextPost" value="">
				<input type= "hidden" name="endTextPost" value="">
                    <div class="tabcontents" id="tabcontents1">
                        <p class="t2">*게임리스트 수정은 신중하게 하시기 바랍니다.</p>
                        <p class="t2">*리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>

                        <!-- 리스트 표시부분 -->
                        <div class="boardtype2">
                            <table cellpadding="0" cellspacing="0" width="520" border="0">
							<tr align="right" style="background-color:#eeeeee">	
							<td colspan="2">
								<select name= "addEditDelSelect" onchange="AddEditDel()">
								<option value="add">추가</option><option value="edit">수정</option><option value="delete">삭제</option>
								</select>
								번호
								<input type="text" name="firstText"/>
								 프로젝트
								<input type="text" name="endText"/>
								<button type="button" class="button red" onclick="Apply()">확인</button>
							</td>
							</tr>
							<tr align="center" style="background-color:#eeeeee"><td  width='35%'>번호</td> <td>프로젝트</td></tr>
							<?php
								$result = mysql_query("SELECT * FROM ECO_Project WHERE IsGame = 1 order by ProjectIdx",$link);
								if($result)
								{
									while($row = mysql_fetch_array($result))
									{
										echo "<tr align='center'><td width='35%'>$row[ProjectIdx]</td>";
										echo "<td>$row[ProjectName]</td></tr>";
									}
									mysql_free_result($result);
								}
							?>     
                            </table>
                        </div>
                    </div>

                    <!-- 플랫폼 리스트 탭-->
                    <div class="tabcontents" id="tabcontents2">
                        <p class="t2">*플랫폼리스트 수정은 신중하게 하시기 바랍니다.</p>
                        <p class="t2">*리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>
                        <div class="boardtype2">
                         <table cellpadding="0" cellspacing="0" width="520" border="0">
							<tr align="right" style="background-color:#eeeeee">	
							<td colspan="2">
								<select name="addEditDelSelect" onchange="AddEditDel()">>
								<option value="add">추가</option><option value="edit">수정</option><option value="delete">삭제</option>
								</select>
								번호
								<input type="text" name="firstText"/>
								 플랫폼
								<input type="text" name="endText"/>
								<button type="button" class="button red" onclick="Apply()">확인</button>
							</td>
							</tr>
							<tr align="center" style="background-color:#eeeeee"><td  width='35%'>번호</td> <td>플랫폼</td></tr>
							<?php
								$result = mysql_query("SELECT * FROM ECO_Platform order by PlatformIdx",$link);
								if($result)
								{
									while($row = mysql_fetch_array($result))
									{
										echo "<tr align='center'><td width='35%'>$row[PlatformIdx]</td>";
										echo "<td>$row[PlatformName]</td></tr>";
									}
									mysql_free_result($result);
								}
							?>     
                            </table>
                        </div>
                    </div>


                    <!-- 마켓 리스트 탭-->
                    <div class="tabcontents" id="tabcontents3">
                        <p class="t2">*마켓리스트 수정은 신중하게 하시기 바랍니다.</p>
                        <p class="t2">*리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>
                        <div class="boardtype2">
                            <table cellpadding="0" cellspacing="0" width="520" border="0">
                             	<tr align="right" style="background-color:#eeeeee">	
								<td colspan="3">
									<select name= "addEditDelSelect" onchange="AddEditDel()">
									<option value="add">추가</option><option value="edit">수정</option><option value="delete">삭제</option>
									</select>
									번호<input type="text" name="firstText"/>
									마켓<input type="text" name="middleText"/>
									플랫폼 번호<input type="text" name="endText"/>
									<button type="button" class="button red" onclick="Apply()">확인</button>
								</td>
								</tr>
								<tr align="center" style="background-color:#eeeeee">
								<td  width='30%'>번호</td> <td width='30%'>마켓</td><td>플렛폼 번호</td></tr>
								<?php
									$result = mysql_query("SELECT * FROM ECO_Market order by MarketIdx",$link);
									if($result)
									{
										while($row = mysql_fetch_array($result))
										{
											echo "<tr align='center'><td>$row[MarketIdx]</td>";
											echo "<td>$row[MarketName]</td>";
											echo "<td>$row[PlatformIdx]</td></tr>";
										}
										mysql_free_result($result);
									}
								?>   
                            </table>
                        </div>
                    </div>

                    <!-- 게임외 기타업무 프로젝트 리스트 탭-->
                    <div class="tabcontents" id="tabcontents4">
                        <p class="t2">*기타업무 리스트 수정은 신중하게 하시기 바랍니다.</p>
                        <p class="t2">*리스트가 잘려서 표시된다면 상단 탭을 다시 한번 눌러주세요.</p>
                        <!-- 리스트 표시부분 -->
                        <div class="boardtype2">
                            <table cellpadding="0" cellspacing="0" width="520" border="0">
             				<tr align="right" style="background-color:#eeeeee">	
								<td colspan="3">
									<select name= "addEditDelSelect" onchange="AddEditDel()">
									<option value="add">추가</option><option value="edit">수정</option><option value="delete">삭제</option>
									</select>
									번호
									<input type="text" name="firstText"/>
									 업무
									<input type="text" name="endText"/>
									<button type="button" class="button red" onclick="Apply()">확인</button>
								</td>
								</tr>
								<tr align="center" style="background-color:#eeeeee">
								<td  width='30%'>번호</td> <td>업무</td>
								<?php
									$result = mysql_query("SELECT * FROM ECO_Project WHERE IsGame = 0 order by ProjectIdx",$link);
									if($result)
									{
										while($row = mysql_fetch_array($result))
										{
											echo "<tr align='center'><td>$row[ProjectIdx]</td>";
											echo "<td>$row[ProjectName]</td>";
										}
										mysql_free_result($result);
									}
								?> 
                            </table>

                        </div>
                    </div>
                </form>
                <script type="text/javascript">
                $('.tabmenu2').tabMenu(0);
                </script>

            </div>
        </div>
    </div>
</body>
</html>
<?php @mysql_close($link) ?>
