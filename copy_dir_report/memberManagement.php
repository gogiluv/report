<?php
	include "db.php";
	$link = DBConnect();
	$memberListResult="";
	$command = @$_POST["command"];
	$curMemberIdx = @$_POST["curMemberIdx"];
	$preMemberIdx = @$_POST["preMemberIdx"];

	$memberListResult = @mysql_query("SELECT * FROM ECO_Member");

	if($command == "add")
	{
		mysql_query("UPDATE ECO_Member SET Visible = 1 WHERE MemberIdx='$preMemberIdx'",$link);
		header("Location: /memberManagement.php");
	}
	else if($command == "delete")
	{
		$result = mysql_query("UPDATE ECO_Member SET Visible = 0 WHERE MemberIdx='$curMemberIdx'",$link);
		
		if($result)
			header("Location: /memberManagement.php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
    <script type="text/javascript">
        function resize() {
            parent.fncResizeHeight(document);
        }

		function movetoPreECO()
		{
			var frm =  document.forms.form1;
			frm.command.value="delete";
			frm.submit();
		}
		function movetoCurECO()
		{
			var frm = document.forms.form1;
			frm.command.value="add";
			frm.submit();
		}

    </script>

    <title>ECO 업무보고 - 팀원관리 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 프로젝트 관리 출력 부분-->
<body onload="return resize()">
    <h1 class="location">
        관리메뉴 &gt; 팀원 관리
    </h1>
    <div class="divide">
    </div>
    <div class="C2Scontent">
        <form runat="server" id="form1" method="post">
		<input type="hidden" name="command" value=""/>
        <table class="mb10">
            <tr>
                <td align="center">현재 ECO 팀원</td>
                <td align="center"></td>
                <td align="center">이전 ECO 팀원</td>
            </tr>
            <tr>
                <td align="center">
					<select name="curMemberIdx" size = 2 style="width:200px; height:400px; ">
					<?php
						while($row = @mysql_fetch_array($memberListResult ))
						{
							if($row[Visible])
								echo"<option value='$row[MemberIdx]'>$row[MemberName]</option>";
						}
						@mysql_data_seek($memberListResult,0)
					?>
					</select>
				</td>					
                <td align="center">
					<button type="button" class="button black" style="height:30px;" onclick="movetoPreECO()">
					<p style="font-size:15px;"> >>> </p>
					</button> <br><br><br><br>
	   				<button type="button" class="button red" style="height:30px;" onclick="movetoCurECO()">
					<p style="font-size:15px;"> <<< </p> 
					</button>
                </td>
                <td align="center">
					<select name="preMemberIdx" size = 2 style="width:200px; height:400px; ">
					<?php
						while($row = @mysql_fetch_array($memberListResult))
						{
							if($row[Visible] == 0)
								echo"<option value='$row[MemberIdx]'>$row[MemberName]</option>";
						}
					?>
					  </select>
                </td>
            </tr>
        </table>
        </form>
    </div>
</body>
</html>

<?php @mysql_close($link) ?>

