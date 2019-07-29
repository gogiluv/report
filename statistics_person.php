<?php
  	include "lib/db.php";
  	$link = DBConnect();

  	$memberInforResult="";
	if( $_SESSION["report_login_level"] == 3)		//팀장
	{
		$memberInforResult = mysqli_query($link, "SELECT * FROM ECO_Member WHERE MemberName != \"관리자계정\"  
										AND LevelIdx = '$_SESSION[report_login_level]'
										AND Visible = 1 order by EN");


	}
	else if ( $_SESSION["report_login_level"] == 2) //파트장
	{
		$memberNameResult = "";
	}
	else	//1 팀원
	{

	}

	 $allItems = mysqli_query($link, "SELECT DISTINCT MONTH(Date), ProjectIdx, MarketIdx FROM ECO_Reports 
							WHERE IsComplete=1 AND YEAR(DATE)='$year'$month_condition ORDER BY $month_sort ProjectIdx, MarketIdx");

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script type="text/javascript">
        function resize() {
            parent.fncResizeHeight(document);
        }

    </script>

    <title>ECO팀 업무보고 - 업무 취합</title>
    <link rel="stylesheet" href="/css/base.css" type="text/css" />
    <link rel="stylesheet" href="/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="/css/button.css" type="text/css" />
</head>
<body onload="return resize()">
    <form id="form1" runat="server">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 개인별 통계
        </h1>
        <div class="divide">
            <div class="boardtype2">
                <!-- 게임프로젝트 리스트 탭-->
                <div class="tabcontents" id="tabcontents2">
                    <p class="t2">
                        * 팀장 및 파트장은 해당 팀원/파트원 통계도 열람이 가능합니다.</p>
                    <table cellpadding="0" cellspacing="0" width="520" border="0">
                        이름 : 
                      <select>
						<?php
							if($_SESSION["report_login_level"]==1)
							{
								echo "<option>$_SESSION[report_login_user]</option>" ;
							}
							else
							{
								if($memberInforResult)
								{
										while($row = mysqli_fecth_array($memberInforResult) )
										{
											echo "<option>$row[MemberName]</option>" ;
										}
								}		
							}
						?>	
					  </select>
                        년 : 
                     <select>
						<?php
							$selectedYearResult = mysqli_query($link, "SELECT DISTINCT YEAR(Date) FROM ECO_Reports ORDER BY Date DESC");

							if($selectedYearResult)
							{
								while($row = mysqli_fetch_array($selectedYearResult))
								{
									echo "<option> $row[0] </option>";				
								}
								@mysqli_free_result($selectedYearResult);
							}
						?>
					</select>
                        월 : 
				      <select name="selected_month" >
				        <option value="0">All</option>
				      <?php
				      for($optionMonth = 1; $optionMonth <= 12; $optionMonth++)
				        echo "<option value='$optionMonth' ".(($month && $optionMonth == $month) ? "selected='selected'" : "").">$optionMonth</option>";
				      ?>
				      </select>

                         <tr>
                            <th scope="row" class="al" width="80">
                                전체 투입시간
                            </th>
                            <td width="500">
                                <asp:Label ID="TotalSpendTime" runat="server"></asp:Label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <asp:DataGrid ID="DataGrid1" runat="server" CssClass="txt01" AutoGenerateColumns="False"
                                    BorderColor="Black" BorderStyle="None" BorderWidth="1px" BackColor="White" CellPadding="4"
                                    GridLines="None">
                                    <SelectedItemStyle Font-Bold="True" ForeColor="White" BackColor="#669999"></SelectedItemStyle>
                                    <ItemStyle ForeColor="#000066"></ItemStyle>
                                    <HeaderStyle Font-Bold="True" ForeColor="Black" BackColor="#dddddd"></HeaderStyle>
                                    <FooterStyle ForeColor="#000066" BackColor="White"></FooterStyle>
                                    <Columns>
                                        <asp:BoundColumn DataField="Month" HeaderText="시간">
                                            <HeaderStyle HorizontalAlign="Center" Width="3px"></HeaderStyle>
                                            <ItemStyle HorizontalAlign="Center"></ItemStyle>
                                        </asp:BoundColumn>
                                        <asp:BoundColumn DataField="MatchProject" HeaderText="프로젝트명">
                                            <HeaderStyle HorizontalAlign="Center" Width="30px"></HeaderStyle>
                                            <ItemStyle HorizontalAlign="Center"></ItemStyle>
                                        </asp:BoundColumn>
                                        <asp:BoundColumn DataField="MatchMarket" HeaderText="마켓">
                                            <HeaderStyle HorizontalAlign="Center" Width="30px"></HeaderStyle>
                                            <ItemStyle HorizontalAlign="Center"></ItemStyle>
                                        </asp:BoundColumn>
                                        <asp:BoundColumn DataField="TotalSpendTime" HeaderText="프로젝트 투입시간(H)">
                                            <HeaderStyle HorizontalAlign="Center" Width="30px"></HeaderStyle>
                                            <ItemStyle HorizontalAlign="left"></ItemStyle>
                                        </asp:BoundColumn>
                                        <asp:BoundColumn DataField="TotalPercent" HeaderText="프로젝트 투입률(%)">
                                            <HeaderStyle HorizontalAlign="Center" Width="30px"></HeaderStyle>
                                            <ItemStyle HorizontalAlign="left"></ItemStyle>
                                        </asp:BoundColumn>
                                    </Columns>
                                </asp:DataGrid>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </form>

    <script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>

    <script type="text/javascript" src="/js/ui.js"></script>

    <script type="text/javascript">
                    //<![CDATA[
                    $('.tabmenu2').tabMenu(0);
                    //]]>
    </script>

</body>
</html>

<?php @mysqli_close($link) ?>
