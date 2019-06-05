
<?php
	include "lib/db.php";

    $link = DBConnect();

    if(SessionCheck() == false) {
        header("Location: /login.php");
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head runat="server">
    <title>ECO팀 업무보고 사이트</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/layout.css" type="text/css" />

    <script type="text/javascript">
        function fncResizeHeight(iframeWindow) {
            var iframeElement = document.getElementById("C2ScontentsFrame");
            iframeElement.height = 0;
            iframeElement.height = iframeWindow.body.scrollHeight;
            //$("#C2ScontentsFrame").css("height", iframeWindow.body.scrollHeight);
            //document.querySelector('#C2ScontentsFrame').style.height =iframeWindow.body.scrollHeight+'px';
            //document.querySelector('#C2ScontentsFrame').style.border = "10px solid red";
        }

		function OpenURL(url) {
			var obj = document.getElementById('framediv');
            obj.innerHTML = "<iframe src='" + url + "' id='C2ScontentsFrame' width='100%' scrolling='no'"
                            + "frameborder='0' name='contents' style='min-height:800px;'></iframe>";
        }
    </script>

</head>
<body>
    <form id="formLayout" runat="server" style="max-width:1500px; margin:auto;">
    <div id="C2Ssystem">
        <!-- header start -->
        <div class="C2Shead">
            <h1>
                <!-- <a href="javascript:void(document.getElementById('C2ScontentsFrame').src='main.php');"> -->
                <a href="layout.php"><img src="img/toplogo.png" alt="ECO" /></a>
            </h1>
            <p class="greeting">                
                <strong><?php echo $_SESSION["report_login_user"] ?> </strong>님 환영합니다.
            </p>
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
                            if ( $_SESSION["report_login_level"] < 3 )
                            {
                        ?>
                        <li><a href="javascript:OpenURL('introEco.php');">ECO 팀 ?!</a></li>
                        <li><a href="javascript:OpenURL('main.php');">공지 사항</a></li>           
                        <li><a href="javascript:OpenURL('addReport.php');">주간 보고(일별)</a></li>
                        <li><a href="javascript:OpenURL('pastReports.php');">보고서 조회</a></li>
                        <li><a href="javascript:OpenURL('statistics_report.php');">업무보고 통계</a></li>
                        <li><a href="javascript:OpenURL('statistics_project.php');">프로젝트별 통계</a></li>
                        <?php
                            }

                            if ( $_SESSION["report_login_level"] == 2 )
                            {
                        ?>

                        <!-- <li><a href="javascript:OpenURL('thisWeekReportSummary.php');">이번주 업무 취합</a></li>         
                        <li><a href="javascript:OpenURL('pastWeekReportSummary.php');">지난 업무 취합</a></li>                        -->
                        <li><a href="javascript:OpenURL('reportSummary.php');">업무 보고 취합</a></li>

			            <?php
                            }                       
                            
                            if ( $_SESSION["report_login_level"] >= 3 )
                            {
                        ?>
                        <!-- <li><a href="javascript:OpenURL('thisWeekReportSummary.php');">이번주 업무 취합</a></li>
                        <li><a href="javascript:OpenURL('pastWeekReportSummary.php');">지난 업무 취합</a></li> -->
                        <li><a href="javascript:OpenURL('statistics_project.php');">프로젝트별 통계</a></li>
                        <li><a href="javascript:OpenURL('statistics_report.php');">업무보고 통계</a></li>    
                        <li><a href="javascript:OpenURL('reportSummary.php');">업무 보고 취합</a></li>
                        <li><a href="javascript:OpenURL('ReportingStatus.php');">보고 현황</a></li>
                        <li><a href="javascript:OpenURL('projectManagement.php');">프로젝트 관리</a></li>                        
                        <li><a href="javascript:OpenURL('memberManagement.php');">팀원 관리</a></li>
                        <li><a href="javascript:OpenURL('noticeManagement.php');">공지사항 관리</a></li>                        
                        
                        <?php
                            }   
                            
                            if( $_SESSION["report_login_level"] == 99 )
                            {
                        ?>
                        <li><a href="javascript:OpenURL('pastReports.php');">보고서 조회(수정용)</a></li>
                        <li><a href="javascript:OpenURL('introEco.php');">ECO 팀 ?!</a></li>
                        <li><a href="javascript:OpenURL('main.php');">공지 사항</a></li>
                        <li><a href="javascript:OpenURL('addReport.php');">주간 보고(일별)</a></li>
                        <li><a href="javascript:OpenURL('holiday.php');">공휴일 관리</a></li>
                        <?php 
                            }
                        ?>
                        
                    </ul>
                </div>
            </div>
            <!-- //navigation end -->
            <div class="C2Scontents" style="min-width:1200px">
                <!-- 본문 아이프레임 영역 start -->
                <div id="framediv"><iframe src ="main.php" id="C2ScontentsFrame" width="100%" height="100%" scrolling="no" frameborder="0" 
                    name="contents"></iframe></div>
                <!-- //본문 아이프레임 영역 end -->
            </div>
            <!-- 메뉴 열고닫기 -->
            <div class="C2SnavCtrl">
                <a href="#"><img src="http://manager.com2us.com/guide/images/btn_close.gif" alt="" /></a>
            </div>
            <!-- //메뉴 열고닫기 -->
        </div>
        <!-- footer start -->
        <div class="C2Sfoot" style="padding-bottom:50px;">
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
            $(window).bind('load', function() {  
                console.log($('#C2ScontentsFrame'));
            $('#C2ScontentsFrame').frameResize();
            });
            //]]>
    </script>

    </form>
</body>
</html>

