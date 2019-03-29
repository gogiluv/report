<?php
	include "db.php";
	$link = DBConnect();
	$memberListResult="";
	$command = @$_POST["command"];
	$memberIdx = @$_POST["memberIdx"];

	$memberListResult = @mysql_query("SELECT * FROM ECO_Member");

	if($command == "reset")
	{
		mysql_query("UPDATE ECO_Member SET Password = sha1('1234567') WHERE MemberIdx ='$memberIdx'",$link);
		header("Location: /resetPassword.php");
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

		function resetPassword()
		{
			var frm =  document.forms.form1;
			frm.command.value="reset";
			frm.submit();
		}
    </script>

    <title>ECO 업무보고 - 비밀번호 초기화 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 프로젝트 관리 출력 부분-->
<body onload="return resize()">
    <h1 class="location">
        관리메뉴 &gt; 비밀번호 초기화
    </h1>
    <div class="divide">
    </div>
    <div class="C2Scontent">
        <form runat="server" id="form1" method="post">
		<input type="hidden" name="command" value=""/>
        <table class="mb10">
            <tr>
                <td align="center">현재 ECO 팀원</td>
            </tr>
            <tr>
                <td align="center">
					<select name="memberIdx" class = "inselect" >
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
					<button type="button" class="button black" style="height:30px;" onclick="resetPassword()">
						비밀번호 초기화
					</button>
                </td>
            </tr>
            <tr>
            	<td align="right">
					비밀번호가 1234567로 초기화 됩니다.
                </td>
            </tr>
        </table>
        </form>
    </div>
</body>
</html>

<?php @mysql_close($link) ?>

