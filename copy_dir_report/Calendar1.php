<!--  <%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Calendar1.aspx.cs" Inherits="NewWeeklyReport.Calendar1" %>  -->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <script language="javascript">

        function resize() {
            parent.fncResizeHeight(document);
        }


    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>날짜 선택 </title>
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
</head>
<body>
        <form id="form2" runat="server">

            <div align="center">
                <asp:Calendar ID="Calendar" runat="server" BackColor="#FFFFCC" BorderColor="#FFCC66"
                    BorderWidth="1px" Font-Names="Verdana" Font-Size="8pt" ForeColor="#663399"
                    Height="200px" Width="220px" DayNameFormat="Shortest" ShowGridLines="true" OnSelectionChanged="selectCalendar">
                    <SelectedDayStyle BackColor="#CCCCFF" Font-Bold="true" />
                    <SelectorStyle BackColor="#FFCC66" />
                    <TodayDayStyle BackColor="#FFCC66" ForeColor="White" />
                    <OtherMonthDayStyle ForeColor="#CC9966" />
                    <NextPrevStyle Font-Size="9pt" ForeColor="#FFFFCC" />
                    <DayHeaderStyle BackColor="#FFCC66" Font-Bold="true" Height="1px" />
                    <TitleStyle BackColor="#990000" Font-Bold="true" Font-Size="9pt" ForeColor="#FFFFCC" />
                </asp:Calendar>
            </div>
        </form>
</body>
</html>
