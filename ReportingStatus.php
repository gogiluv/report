<?php
  include "lib/default.php";
  include "lib/db.php";

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
  <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
  <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
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
      <div class="boardtype2" style="width:auto; max-width:1500px;">
        <div class="select-area">
          <table style="width:500px;">
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
        <div class="rs-list mt-20">
          <table>
            <thead>
              <tr>
                <th width="20%">근무일</th>
                <th width="40%">작성자</th>
                <th width="40%">미작성자</th>
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
    getToday: function(){
      return Date.now();
		},
		search: function() {
			var data = {
        memberIdx: $('#name').val(),
				from: $('#from').val(),
				to: $('#to').val()
      }

			Report.get('getReportStatus', data).then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res.error){
					console.log(json_res.error);
					return;
				}
        RS.renderRows(json_res);
			});
		},
		renderRows: function(rows) {
      var list = $('.rs-list tbody');

      list.html(''); //초기화
      for(var num in rows){
        var html = [];        
        row = rows[num];
        var rendered_member = $('tr[data-member-name='+row.membername+']');
        var rendered_date = $('tr[data-member-name='+row.membername+'][data-date='+row.work_d+']');

        html.push('<tr data-member-name='+row.membername+' data-date='+row.work_d+' data-report-id='+row.reportidx+'>');
        if(rendered_member.length == 0) {
          html.push('<th>');
          html.push(row.membername);
          html.push('</th>');
        } else {
          rendered_member.first().find('th:first').attr('rowspan', rendered_member.length + 1);
        }
        if(rendered_date.length == 0) {
          html.push('<td>');
          html.push(row.work_d);
          html.push('</td>');
        } else {
          rendered_date.first().find('td:first').attr('rowspan', rendered_date.length + 1);
				}				        
        html.push('<td data-work-h='+row.work_h+'>');
        html.push(Number(row.work_h));
        html.push('</td>');
        html.push('<td>');
				html.push(row.projectname);
        html.push('</td>');
        html.push('<td>');        
        html.push('<div class="preview-scroll-hidden mh-200">');
        html.push('<div class="ui-icon ui-icon-arrow-4-diag btn expand-btn" onclick="RS.expandReport('+row.reportidx+')"></div>');
        html.push(row.report);
        html.push('</td>');
        html.push('<td>');
				//html.push('<input type="button" class="button red" value="삭제" onclick="RS.deleteRow('+row.reportidx+')">');
        html.push('</td>');
        html.push('</tr>');        
        list.append(html.join(''));
      }		
      
      parent.fncResizeHeight(document);	//resize
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
    RS.search();
  });
</script>
</body>
</html>