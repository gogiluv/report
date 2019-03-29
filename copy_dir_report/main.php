<?php
	include "db.php";
	$link = DBConnect();

		if($result = @mysql_query("SELECT * FROM ECO_Notice LIMIT 1", $link)) {
			if($row = @mysql_fetch_array($result)) {

			    $noticeDate = $row["NoticeDate"];
                $noticeTitle = $row["NoticeTitle"];
                $noticeValue = $row["NoticeValue"];
				$noticeValue = str_replace("\n","<br><br>",$noticeValue );
                $noticeValue = str_replace(" ","&nbsp",$noticeValue );
									
			}
			@mysql_free_result($result);
		}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ECO 업무보고 사이트 </title>
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    
     <script type="text/javascript">

       
        function resize() {
            parent.fncResizeHeight(document);
        }
        
    </script>
</head>

<body onload="return resize()">
    <form id="formNotice" runat="server">
    <div class="C2Scontent">
        <div class="boardtype2">
            <h1 class="location">
                관리메뉴 &gt; 공지 사항
            </h1>
            <div class="divide">
            </div>
            <table>
                <col style="width: 20%;" />
                <col />
                <tbody>
                    <tr>
                        <th>
                            공지날짜
                        </th>
                        <td>
							<?php echo " $noticeDate " ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            공지사항
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="color: red; font-size: 15pt;">
							<div style="margin-top: 10px; margin-bottom: 10px;"><?php echo $noticeTitle ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            세부내용
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 13pt">
                            <br />
                            <?php echo $noticeValue; ?>
                            <br />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </form>
</body>
</html>
