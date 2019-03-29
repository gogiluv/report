
<?php
	include "db.php";
	$link = DBConnect();

	$name = @trim(@addslashes($_POST["signupname"]));
	$number = @trim(@addslashes($_POST["signupnumber"]));
	$password = @trim(@addslashes($_POST["signuppassword"]));
	$part = @trim(@addslashes($_POST["memberpart"]));
	$position = @trim(@addslashes($_POST["memberposition"]));

		
	if($number) {
		
		if($result = @mysql_query("SELECT * FROM ECO_Member	WHERE EN = '$number' ", $link)) {
			$num_rows = mysql_num_rows($result);
			if($num_rows > 0)
			{
				echo "<script language=JavaScript>\n
                alert('가입된 사원번호가 이미 존재합니다. 관리자에게 문의해주세요.');\n  </script>";
			}
			else
			{
				@mysql_query("INSERT INTO ECO_Member (MemberName, EN, PartIdx,LevelIdx, PositionIdx, Password, Visible,MemberImage)
				VALUES('$name', '$number', '$part', 1,'$position', sha1('$password'),1, NULL)",$link);
				echo "<script language=JavaScript>\n
                alert('가입 완료');\n  </script>";
				header("Location: /");
			}

			@mysql_free_result($result);
		}
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <script src="/inc/jquery-1.6.2.min.js"></script>

    <script type="text/javascript">
        function fncResizeHeight(iframeWindow) {
            var iframeElement = document.getElementById("C2ScontentsFrame");
            iframeElement.height = 0;
            $("#C2ScontentsFrame").css("height", iframeWindow.body.scrollHeight);
            iframeElement.height = iframeWindow.body.scrollHeight;
        }
        //   <!-- 회원가입 입력 확인 부분-->
        function sendSignup() {
            if (document.getElementById("signupname").value == "") {
                alert("이름을 입력하세요!");
                document.getElementById("signupname").focus();
                return false;
            }
            else if (document.getElementById("signupnumber").value == "") {
                alert("사원번호를 입력하세요!");
                document.getElementById("signupnumber").focus();
                return false;
            }
            else if( document.getElementById("signuppassword").value == "" ||document.getElementById("signuppassword2").value == "") {
                alert("비밀번호를 입력하세요!");
                document.getElementById("signuppassword").focus();
                return false;
            }
            else if (document.getElementById("signuppassword").value != document.getElementById("signuppassword2").value) {
                alert("비밀번호가 일치하지 않습니다!");
                document.getElementById("signuppassword2").focus();
                return false;
            }
            else if (document.getElementById("memberpart").value == "") {
                alert("소속파트를 선택해주세요!");
                document.getElementById("memberpart").focus();
                return false;
            }
            else if (document.getElementById("memberposition").value == "") {
                alert("본인직급을 선택해주세요!");
                document.getElementById("memberposition").focus();
                return false;
            }
            return true; 
        }

        function setForm() {
            changePart();
            changePosition();
        }

        function changePart() {
            document.getElementById("SignupPart").value = document.getElementById("SelectPart").value;
        }

        function changePosition() {
            document.getElementById("SignupPosition").value = document.getElementById("SelectPosition").value;
        }

    </script>

    <title>ECO 업무보고 - 회원가입 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 가입화면 form 부분 -->
<body onload="return setForm()">
    <div class="C2Shead">
        <h1>
            <a href="index.php">
                <img src="img/toplogo.png" alt="ECO" /></a><strong>ECO Report - 회원가입</strong></h1>
    </div>
    <form id="Form1" name="formSignup" runat="server" method="post" onsubmit="return sendSignup();" enctype="multipart/form-data">
    <div class="C2Scontent">
        <div class="boardtype2">
            <table class="mb10">
                <col style="width: 20%;" />
                <col />
                <tbody>
                    <tr>
                        <p class="t2">
                            *모든 사항은 필수 입력입니다.</p>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            이 름
                        </th>
                        <td>
							<input type="text" name="signupname" id="signupname" class="intext"/>이름은 공백없이 입력해주십시오. 
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            사원번호
                        </th>
                        <td>
							<input type="text" name="signupnumber" id="signupnumber" class="intext"/>ex) 1201123
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호
                        </th>
                        <td>
							<input type="password" name="signuppassword" id="signuppassword" class="intext"/>정확하게 입력해주십시오.
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호확인
                        </th>
                        <td>
							<input type="password" name="signuppassword2" id="signuppassword2" class="intext"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            소속파트
                        </th>
                        <td>
                            <div>
								<select name="memberpart" id="memberpart"  class = "inselect" >
								<option value="">Select...</option>	
								<?php
								if($result = @mysql_query("SELECT * FROM ECO_Part", $link)) {
								while($row = @mysql_fetch_array($result)) {
									echo "<option value='$row[PartIdx]'>$row[Name]</option>\r\n";
									}
									@mysql_free_result($result);
								}
								?>
								</select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            본인직급
                        </th>
                        <td>
                            <div>
								<select name="memberposition" id="memberposition" class = "inselect" >
								<option value="">Select...</option>	
								<?php
								if($result = @mysql_query("SELECT * FROM ECO_Position", $link)) {
								while($row = @mysql_fetch_array($result)) {
									echo "<option value='$row[PositionIdx]'>$row[Name]</option>\r\n";
									}
									@mysql_free_result($result);
								}
								?>
								</select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="btns">
				<button type="submit" name="signUp" class="button red">회원가입 하기 </button>

			</div>
        </div>
    </div>
    </form>
</body>
</html>
