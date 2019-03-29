<!-- <%@ Page Language="C#" AutoEventWireup="true" CodeBehind="SignUpInfo.aspx.cs" Inherits="NewWeeklyReport.SignUPInfo" %> -->

<%--회원가입 부분 Sangmin 2012.12.27--%>
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
            if (document.getElementById("SignupName").value == "") {
                alert("이름을 입력하세요!");
                document.getElementById("SignupName").focus();
                return;
            }
            else if (document.getElementById("SignupEN").value == "") {
                alert("사원번호를 입력하세요!");
                document.getElementById("SignupEN").focus();
                return;
            }
            else if( document.getElementById("SignupPW1").value == "" ||document.getElementById("SignupPW2").value == "") {
                alert("비밀번호를 입력하세요!");
                document.getElementById("SignupPW1").focus();
                return;
            }
            else if (document.getElementById("SignupPW1").value != document.getElementById("SignupPW2").value) {
                alert("비밀번호가 일치하지 않습니다!");
                document.getElementById("SignupPW1").focus();
                return;
            }
            else if (document.getElementById("SignupPart").value == "") {
                alert("소속파트를 선택해주세요!");
                document.getElementById("SignupPart").focus();
                return;
            }
            else if (document.getElementById("SignupPosition").value == "") {
                alert("본인직급을 선택해주세요!");
                document.getElementById("SignupPosition").focus();
                return;
            }
            return;
        }

        function changePart() {
            document.getElementById("SignupPart").value = document.getElementById("SelectPart").value;
        }

        function changePosition() {
            document.getElementById("SignupPosition").value = document.getElementById("SelectPosition").value;
        }

    </script>

    <title>ECO 업무보고 - 정보수정 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
</head>
<!-- 가입화면 form 부분 -->
<body>
    <div class="C2Shead">
        <h1>
            <a href="Default.aspx">
                <img src="/img/toplogo.png" alt="ECO" /></a><strong>ECO Report - 정보수정</strong></h1>
    </div>
    <form id="Form1" name="formSignup" runat="server" method="post" enctype="multipart/form-data">
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
                            <asp:TextBox ID="SignupName" runat="server" TextMode="SingleLine" class="intext" />이름은
                            공백없이 입력해주십시오. ex)홍길동
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            사원번호
                        </th>
                        <td>
                            <asp:TextBox ID="SignupEN" runat="server" TextMode="SingleLine" class="intext" />
                            ex) 1201123
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호
                        </th>
                        <td>
                            <asp:TextBox ID="SignupPW1" runat="server" TextMode="Password" class="intext" />정확하게
                            입력해주십시오.
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            비밀번호확인
                        </th>
                        <td>
                            <asp:TextBox ID="SignupPW2" runat="server" TextMode="Password" class="intext" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            소속파트
                        </th>
                        <td>
                            <div>
                                <asp:TextBox ID="SignupPart" runat="server" TextMode="SingleLine" class="readonly"/>
                                <asp:DropDownList ID="SelectPart" runat="server" class="inselect" onchange="return changePart()">
                                </asp:DropDownList>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="al">
                            본인직급
                        </th>
                        <td>
                            <div>
                                <asp:TextBox ID="SignupPosition" runat="server" TextMode="SingleLine" class="readonly"/>
                                <asp:DropDownList ID="SelectPosition" runat="server" class="inselect" onchange="return changePosition()">
                                </asp:DropDownList>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="btns">
                <asp:Button ID="ButtonSignup" runat="server" Text="개인정보수정" OnClientClick="return sendSignup()"
                    OnClick="SubmitSignUpEdit" class="button red"/></div>
        </div>
    </div>
    </form>
</body>
</html>
