<?php
	include "lib/db.php";
	$link = DBConnect();
	$loginfalse = 0;

	#if(isset($_GET["logout"]))
		#SessionClose();

	$id = @trim(@addslashes($_POST["member_Idx"]));
	$pw = @trim(@addslashes($_POST["password"]));
	$isSaveId =@trim(@addslashes($_POST["saveId"]));

	if($isSaveId)
	{
		setcookie("save_login_id",$id, time() + (86400 * 30),"/"); 
	}
	else
	{
		setcookie("save_login_id", "", 0);
	}

	if($id == "" && isset($_COOKIE['save_login_id']) )
	{
		$id = @$_COOKIE['save_login_id'];
	}

	if($id && $pw) {
		if($result = @mysqli_query($link, "SELECT * FROM ECO_Member WHERE MemberIdx='$id' LIMIT 1")) {
			
			if($row = @mysqli_fetch_array($result)) {

				if( !strcmp($id,$row["MemberIdx"]) && !strcmp(sha1($pw),$row["Password"]) ){
					#echo "로그인 성공";
									
				$_SESSION["report_login_time"] = time();
				$_SESSION['report_login_userIdx'] = $row["MemberIdx"];
				$_SESSION["report_login_user"] = $row["MemberName"];
				$_SESSION["report_login_level"] = $row["LevelIdx"];	
				$_SESSION["report_login_partIdx"] = $row["PartIdx"];
				}
				else {
					$loginfalse = 1;
					/*
					echo "<script type='text/javascript'>\n
                        alert('ID 또는 비밀번호가 틀렸습니다');\n
                        </script>";
                        */

				}
			}
			@mysqli_free_result($result);
		}
	}

	if(SessionCheck()) {
		header("Location: /");
		# echo "<script type='text/javascript'> location='/'; </script>";
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ECO 업무보고 사이트 </title>
    <link rel="stylesheet" href="/css/base.css" type="text/css" />
    <link rel="stylesheet" href="/css/button.css" type="text/css" />

    <script type="text/javascript">
    function check_form(obj) {
		console.log(obj);
		var strid = obj.member_Idx.value;
		var strpw = obj.password.value;
        if (strid == "") {
            alert("사용자를 선택 하세요!");
            return false;
        }
        if (strpw == "") {
            alert("비밀번호를 입력 하세요!");
            return false;
        }
        return true;
    }
    function loginCheck()
    {
    	<?php
    	if($loginfalse == 1 ) 
    	{
    	 ?>
    	  alert('ID 또는 비밀번호가 틀렸습니다');
    	<?php
    	}
    	?>

    }
    </script>

</head>
<body onload="loginCheck()" >

    <form id="formDefault" onsubmit="return check_form(this);" action="login.php" method="post">
    <div id="C2Slogin">
        <div class="C2Slogin">
            <h1>
                <img src="img/com2us.png" alt="Exam" />
            </h1>
            <fieldset class="loginarea1"  style="height:66px" >
                <div class="field">
                    <label for="loginID">이름선택</label>

					<select name="member_Idx"  class = "inselect" >
					<option value="">Select...</option>
					<?php
						if($result = @mysqli_query($link,"SELECT * FROM ECO_Member WHERE Visible = 1 ORDER BY EN ASC")) {
							while($row = @mysqli_fetch_array($result)) {
								echo "<option value='$row[MemberIdx]'".
								(($id == $row["MemberIdx"]) ? "selected" : "").">$row[MemberName]</option>\r\n";
							}
							@mysqli_free_result($result);
						}
					?>
					</select>
                </div>

                <div class="field">
                    <label for="loginPW">비밀번호</label>
					<input type="password" name="password" class="intext" />
				</div>
			<div class="btns">
			<button type = "submit"  class="button red" value = "">로그인 </button>
			</div>
			<div>
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			<?php
			echo "<input type=\"checkbox\" name=\"saveId\" value=\"1\"".
					((!@$_COOKIE['save_login_id']) ? "":"checked").">Save my name</input>";
			?>
			</div>
			</fieldset>
            <div class="btns" style="width:370px">
            	 <span style="float:right"> 
				<input type="button" class="button blue" value="회원가입" style="width:60pt;height:20pt"onclick="location='signup.php';" />
				<input type="button" class="button blue" value="비밀번호 변경" style="width:80pt;height:20pt"onclick="location='editPassword.php';" />
				</span>
				<!--
				<center>
				<label for="signUPbtn">아이디가 없으신분은 회원가입 후 이용바랍니다.</label>
				<br>
				<label for="signUPbtn">&nbsp&nbsp&nbsp(기존 가입자는 관리자에게 문의해주세요.)&nbsp&nbsp&nbsp</label>
				</center> -->
			</div>
        </div>
        <!-- footer start -->
        <div class="C2Sfoot">
            <p class="copyright">
                Copyright &copy; com2us corp. All right reserved.  <br> 이 사이트는 <strong>맑은 고딕</strong>
                폰트로 제작되었습니다. 폰트를 다운 받으시려면 <a href="http://download.microsoft.com/download/0/3/e/03e8f61e-be04-4cbd-8007-85a544fec76b/VistaFont_KOR.EXE">
                    여기</a>를 눌러주세요.</p>
        </div>
        <!-- //footer end -->
    </div>
    </form>
</body>
</html>

<?php @mysqli_close($link) ?>
