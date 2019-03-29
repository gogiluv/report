
<?php
	include "lib/db.php";
	$link = DBConnect();

	$name = @trim(@addslashes($_POST["name"]));
    $number = @trim(@addslashes($_POST["number"]));
	$currentpassword = @trim(@addslashes($_POST["currentpassword"]));
	$newpassword = @trim(@addslashes($_POST["newpassword"]));
	$newpassword2 = @trim(@addslashes($_POST["newpassword2"]));

    $check = false;
		
	if($name &&  $number) {
		
		if($result = @mysqli_query($link, "SELECT * FROM ECO_Member	WHERE EN = '$number' And MemberName = '$name' And Password =sha1('$currentpassword') ")) {
			$num_rows = mysqli_num_rows($result);
			
            if($num_rows > 0)
			{
				@mysqli_query($link, "UPDATE ECO_Member SET Password = sha1('$newpassword') Where EN = '$number' And MemberName = '$name' And Password =sha1('$currentpassword')");
                
                echo "<script language=JavaScript>\n
                alert('비밀번호 변경 완료');\n
                location.href='/';
                </script>";
                //header("Location: /");
			}
			else
			{
                echo "<script language=JavaScript>\n
                alert('사용자 정보가 존재하지 않습니다.');\n  </script>";
			}

			@mysqli_free_result($result);
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
            if (document.getElementById("name").value == "") {
                alert("이름을 입력하세요!");
                document.getElementById("name").focus();
                return false;
            }
            else if (document.getElementById("number").value == "") {
                alert("사원번호를 입력하세요!");
                document.getElementById("number").focus();
                return false;
            }

            else if( document.getElementById("newpassword").value == "" ) {
                alert("비밀번호를 입력하세요!");
                document.getElementById("newpassword").focus();
                return false;
            }
             else if( document.getElementById("newpassword2").value == "") {
                alert("비밀번호를 입력하세요!");
                document.getElementById("newpassword2").focus();
                return false;
            }
            else if (document.getElementById("newpassword").value != document.getElementById("newpassword2").value) {
                alert("비밀번호가 일치하지 않습니다!");
                document.getElementById("newpassword2").focus();
                return false;
            }


            return true; 
        }

    </script>

    <title>ECO 업무보고 - 비밀번호 변경 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 가입화면 form 부분 -->
<body>
    <div class="C2Shead">
        <h1>
            <a href="index.php">
                <img src="img/toplogo.png" alt="ECO" /></a><strong>비밀번호 변경</strong></h1>
    </div>
    <form id="Form1" name="formSignup" method="post" onsubmit="return sendSignup();" enctype="multipart/form-data">
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
							<input type="text" name="name" id="name" class="intext"/> 이름은 공백없이 입력해주십시오. 
                        </td>
                    </tr>
                        <tr>
                        <th scope="row" class="al">
                            사원번호 
                        </th>
                        <td>
                            <input type="text" name="number" id="number" class="intext"/> 
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            기존 비밀번호 
                        </th>
                        <td>
							<input type="password" name="currentpassword" id="currentpassword" class="intext"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호
                        </th>
                        <td>
							<input type="password" name="newpassword" id="newpassword" class="intext"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호 확인
                        </th>
                        <td>
							<input type="password" name="newpassword2" id="newpassword2" class="intext"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="btns">
				<button type="submit" name="signUp" class="button red">비밀변호 변경하기 </button>

			</div>
        </div>
    </div>
    </form>
</body>
</html>
