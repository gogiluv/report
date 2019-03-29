
<?php
	include "db.php";

    $link = DBConnect();

    if(SessionCheck() == false) {
        header("Location: /login.php");
        exit;
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>ECO팀 업무보고 사이트</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/layout.css" type="text/css" />

    <script type="text/javascript">
        function fncResizeHeight(iframeWindow) {
            var iframeElement = document.getElementById("C2ScontentsFrame");
            iframeElement.height = 0;
            $("#C2ScontentsFrame").css("height", iframeWindow.body.scrollHeight);
            iframeElement.height = iframeWindow.body.scrollHeight;
        }

		function OpenURL(url) {
			var obj = document.getElementById('framediv');
			obj.innerHTML = "<iframe src='" + url + "' id='C2ScontentsFrame' width='100%' scrolling='no' frameborder='0' name='contents'></iframe>";
		}
    </script>

</head>
<body>
    <form id="formLayout" runat="server">
    <div id="C2Ssystem">
        <!-- header start -->
        <div class="C2Shead">
            <h1>
                <!-- <a href="javascript:void(document.getElementById('C2ScontentsFrame').src='main.php');"> -->
                <a href="layout.php">
                     <img src="img/toplogo.png" alt="ECO" /></a>
            </h1>
            <p class="greeting">
                
                <strong>
                    <?php 
						echo $_SESSION["report_login_user"] ?> </strong>님 환영합니다.</p>
            <h1>
             
            </h1>
            <div class="users">
                <a href="Logout.php" class="button red">로그아웃</a>
            </div>
            <!-- <div class="users">
                <a href="SignUpInfo.aspx" class="button blue">개인정보</a>
            </div> -->
        </div>
        <!-- //header end -->
        <div class="C2Swrap">
            <!-- navigation start -->
            <div class="C2Snav">
                <div class="depth2">
                    <ul>     
						<?php
							if (strcmp( $_SESSION["report_login_user"],"관리자계정") == 0)
							{
							echo "<li><a href=\"javascript:OpenURL('statistics_project.php');\">
									프로젝트별 통계</a></li>";
							}
						?>
				                   
                        <?php
                            if (strcmp( $_SESSION["report_login_user"],"관리자계정"))
                            {
                        ?>
                        <li><a href="javascript:OpenURL('introEco.php');">ECO 팀 ?!</a></li>
                        <li><a href="javascript:OpenURL('main.php');">공지 사항</a></li>
                        <li><a href="javascript:OpenURL('thisWeekReport.php');">이번 주 보고서</a></li>
                        <li><a href="javascript:OpenURL('pastReports.php');">지난 보고서</a></li>
                        <li><a href="javascript:OpenURL('statistics_project.php');"">프로젝트별 통계</a></li>
                        
                        <?php
                            }
					
                            if ($_SESSION["report_login_level"] == 3 || 
								$_SESSION["report_login_level"] == 2 )
                            {
                        ?>
                        <li><a href="javascript:OpenURL('thisWeekReportSummary.php');">이번주 업무 취합</a></li>
                        <li><a href="javascript:OpenURL('pastWeekReportSummary.php');">지난 업무 취합</a></li>
                        <li><a href="javascript:OpenURL('projectManagement.php');">프로젝트 관리</a></li>
                        <li><a href="javascript:OpenURL('memberManagement.php');">팀원 관리</a></li>
                        <li><a href="javascript:OpenURL('resetPassword.php');">비밀번호 초기화</a></li>
                        <li><a href="javascript:OpenURL('noticeManagement.php');">공지사항 관리</a></li>
                        
                        <?php
                            }   
                        ?>
                    </ul>
                </div>
            </div>
            <!-- //navigation end -->
            <div class="C2Scontents">
                <!-- 본문 아이프레임 영역 start -->
                <div id="framediv"><iframe src ="main.php"id="C2ScontentsFrame" width="100%" scrolling="no" frameborder="0" 
                    name="contents"></iframe></div>
                <!-- //본문 아이프레임 영역 end -->
            </div>
            <!-- 메뉴 열고닫기 -->
            <div class="C2SnavCtrl">
                <a href="#">
                    <img src="http://manager.com2us.com/guide/images/btn_close.gif" alt="" /></a>
            </div>
            <!-- //메뉴 열고닫기 -->
        </div>
        <!-- footer start -->
        <div class="C2Sfoot">
            <p class="copyright">
                Copyright &copy; com2us corp. All right reserved. 이 사이트는 <strong>맑은 고딕</strong>
                폰트로 제작되었습니다. 폰트를 다운 받으시려면 <a href="http://download.microsoft.com/download/0/3/e/03e8f61e-be04-4cbd-8007-85a544fec76b/VistaFont_KOR.EXE">
                    여기</a>를 눌러주세요.</p>
        </div>
        <!-- //footer end -->
    </div>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

    <script type="text/javascript" src="http://manager.com2us.com/guide/js/ui.js"></script>

    <script type="text/javascript">
            //<![CDATA[
            $('.C2Snav .depth2').Nav();
            $('.C2SnavCtrl').navCtrl();
            //]]>
    </script>

    </form>
</body>
</html>

