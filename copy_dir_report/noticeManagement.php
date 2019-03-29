<?php
	include "db.php";
	$link = DBConnect();
	$command= @$_POST["command"];
	$contents= @$_POST["noticeContents"];
	$subject= @$_POST["noticeSubject"];
	$date = "";


	if($command=="update" && $contents && $subject)
	{
		$date = Date("Y-m-d");
		$result = mysql_query("UPDATE ECO_Notice 
								SET NoticeValue = '$contents', NoticeTitle='$subject',NoticeDate='$date'
								",$link);
	}
	else
	{
		if( $noticeResult = @mysql_query("SELECT * FROM ECO_Notice",$link) )
		{
			while($row = mysql_fetch_array($noticeResult) )
			{
				$contents = $row[0];
				$subject = $row[1];
				$date = $row[2];	
			}

			mysql_free_result($noticeResult);
		}
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
		function UpdateNotice()
		{
			var frm = document.forms.form1;
			frm.command.value="update";
			frm.submit();
		}
    </script>

    <title>ECO 업무보고 - 공지사항관리 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 프로젝트 관리 출력 부분-->
<body onload="return resize()">
    <h1 class="location">
        관리메뉴 &gt; 공지사항수정
    </h1>
    <div class="divide">
    </div>
    <div class="C2Scontent">
        <div class="boardtype2">
            <form runat="server" id="form1" method="post">
			<input type="hidden" name="command" value="">
            <table class="mb10">
                <col style="width: 20%;" />
                <col />
                <tbody>
                    <tr>
                        <th>
                            공지사항 제목
                        </th>
                        <td>
                          <input type="text" name="noticeSubject" style="width:500px;"
							<?php echo "value='$subject'" ?> >
                        </td>
                    </tr>
                    <tr>
                        <th>
                            공지사항 내용
                        </th>
                        <td>
                          <textarea name="noticeContents" style="width:500px;height:400px;"><?php echo $contents;?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                           <button type="button" name="noticeSubmit" OnClick="UpdateNotice()" class="button red" >공지사항 저장 </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
    </div>
</body>
</html>

<?php @mysql_close($link) ?>
