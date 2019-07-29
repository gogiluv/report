<?php
  include "lib/default.php";
  include "lib/db.php";

  //로그인 체크
  if (!SessionCheck()) {
    echo "<script type='text/javascript'>
          top.window.location='login.php';
          </script>";
  }

  //페이지 권한 체크
  if ( $_SESSION["report_login_level"] < 2 ) {
    header('Location: main.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Page Title</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/ui.css" type="text/css" />
  <link rel="stylesheet" href="/css/button.css" type="text/css" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="css/common.css">    
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="js/report.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 업무 보고 취합</h1>
  <div>
    <div>
      <div class="boardtype2" style="width:auto; max-width:1000px;">
        <div class="select-area">
          <table style="width:400px;">
            <thead>
              <tr>
                <td>
                  <span>
                    <label for="from">업무일</label>
                    <input type="text" id="from" name="from">
                  </span>
                  <span>
                    <label for="to">~</label>
                    <input type="text" id="to" name="to">
                  </span>
                  <span><input type="button" class="button red" value="검색하기" onclick="RS.search()"></span>
                </td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="unreported mt-20">
          <table>
            <tr>
              <th width="15%">미완료 팀원</th>
              <td></td>
            </tr>
          </table>
        </div>
        <div class="status-list mt-20">
          <table>
            <thead>
              <tr>
                <th width="15%">근무일</th>
                <th width="42%">작성자</th>
                <th width="42%">미작성자</th>
              </tr>            
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>      
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	var RS = {
    members: {},

    getToday: function(){
      return Date.now();
		},
		search: function() {
			var data = {
				from: $('#from').val(),
				to: $('#to').val()
      }

			Report.get('getReportedMember', data).then(function(res){
        RS.renderRows(res);
      });
    },
    setMembers: function() {
      Report.get('getMembers').then(function(res){
        RS.members = res;
			});
    },

		renderRows: function(rows) {
      var list = $('.status-list tbody');
      var unreported = [];
      var html = '';
      
      for(var date in rows) {
        if(rows[date].day==='토' || rows[date].day==='일' || rows[date].isHoliday==="Y"){
          // 토, 일요일은 display:none 이 기본이다
          // 주말 보고서를 제출한 사람이 있을경우에는 보이게한다
          // rows[date].reported.length 가 1보다 작으면 제출된 보고서가 없는것으로 판단한다.
          html +='<tr class="bg-lightpink weekend '+ (rows[date].reported.length<1 ? 'd-n' : '') +'">\
                    <td class="ta-c">'+ date + ' ' +rows[date].day +'요일</td>\
                    <td>'+ rows[date].reported +'</td>\
                    <td>'+ (rows[date].dateName ? rows[date].dateName : '주말') +'</td>\
                  </tr>';
        }else{
          html +='<tr>\
                    <th>'+ date + ' ' +rows[date].day +'요일</th>\
                    <td>'+ rows[date].reported.join(', ') +'</td>\
                    <td>'+ rows[date].unreported.join(', ') +'</td>\
                  </tr>';
          //검색 기간내에 보고 미완료 건이 있는 팀원을 추린다. 중복 제거
          unreported = unreported.concat(rows[date].unreported);          
          unreported = unreported.reduce(function(a,b){
            if(a.indexOf(b)<0)a.push(b);return a;
          },[]);
          
          $('.unreported table tr td').text(unreported.join(', '));
        }
      }

      list.html(''); //초기화
      list.html(html);
      
      parent.fncResizeHeight(document);	//resize
    },

    formatDate: function(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();

      if (month.length < 2) month = '0' + month;
      if (day.length < 2) day = '0' + day;

      return [year, month, day].join('-');
    },

	}
  
	$(function(){		
    var dateFormat = "yy-mm-dd",
      from = $( "#from" )
        .datepicker({
					showOtherMonths: true,
          defaultDate: "0",
					changeMonth: true,
					changeYear: true,
    			dateFormat:"yy-mm-dd",      
          //numberOfMonths: 3
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
				})
			from.datepicker( "setDate" , -6);

      to = $( "#to" ).datepicker({
				showOtherMonths: true,
        defaultDate: "0",
				changeMonth: true,
				changeYear: true,
    		dateFormat:"yy-mm-dd",      
        //numberOfMonths: 3
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
			});

			to.datepicker( "setDate" , RS.getToday() );
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      } 
      return date;
    }
    //init
    //RS.setMembers();
    RS.search();
  });
</script>
</body>
</html>
