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

	//get
  if(!empty($_GET)){
    $data = $_GET;
		$result = getReportsFromMemberWithDate(null, $data);
		
		echo $result;
    return;
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
      <div class="boardtype2" style="width:auto; max-width:1500px;">
        <div class="select-area">
          <table style="width:530px;">
            <thead>
              <tr>
                <td>
                  <select id="name">
                  </select>
                </td>
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
                  <span><input type="button" id="export-excel" class="button green ml-10" value="내보내기" onclick="tableToExcel('rs-table', 'export table')"></span>
                </td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="rs-list mt-20">
          <table id="rs-table">
            <colgroup>
              <col span="4" align="center">
              <col align="left">
            </colgroup>
            <thead>
              <tr>
                <th width="10%">이름</th>
                <th width="10%">업무일자</th>
                <th width="10%">근무 시간</th>
                <th width="15%">유형</th>
                <th width="*">내용</th>
                <th width="10%">비고</th>
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
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="RS.expandModalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
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

			$.get('reportSummary.php', data, function(res){
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
			})
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
        //pre 태그내에 mso-data-placement:same-cell 설정을 줄 수 없어서 다음과 같이 처리한다
        html.push('<div>'+row.report.replace(/</gi, '&lt').replace(/ /gi, '&nbsp').replace(/\n/gi, '<br style="mso-data-placement:same-cell;" />')+'</div>');
        //html.push('<div><pre class="plain-text" style="mso-data-placement:same-cell;">'+row.report+'</pre></div>');
        html.push('</div>')
        html.push('</td>');
        html.push('<td>');
				//html.push('<input type="button" class="button red" value="삭제" onclick="RS.deleteRow('+row.reportidx+')">');
        html.push('</td>');
        html.push('</tr>');        
        list.append(html.join(''));
      }		
      RS.sumWorkHour();	
      $('#rs-table td, #rs-table th').css('border', '1px solid #ddd');
      $('#rs-table th').css('background-color', '#f4f4f4');
      parent.fncResizeHeight(document);	//resize
    },
    expandReport: function(id){
      var data = {
        reportIdx: id        
      }
      Report.get("getReportFromId", data).then(function(res){
        RS.expandModalOpen(res);
      });
    },
    expandModalOpen: function(report){
      $('.modal-header').text('보고일: '+ report.work_d);
      $('.modal-content').text(report.Report);
      $('.overlay').show();
    },
    expandModalClose: function(){
      $('.overlay').hide();
    },
    sumWorkHour: function(){
      //console.log($('tr[data-member-name]').attr('data-member-name'),' : ',$('tr[data-member-name]').length);
      var rows = $('tr[data-member-name]');
      var member_data = {};
      //console.log($(rows[0]).attr('data-member-name'));
      for(var i=0; i<rows.length; i++) {
        var row = rows[i];
        if(member_data[$(row).attr('data-member-name')]){
          member_data[$(row).attr('data-member-name')] += Number($(row).find('td[data-work-h]').text());
        }else{
          member_data[$(row).attr('data-member-name')] = Number($(row).find('td[data-work-h]').text());
        }
      }
      
      var members = Object.keys(member_data);
      for(var i in members) {        
        var html = [];        
        html.push('<tr class="sum-row" data-member-name='+members[i]+'>');
        html.push('<td>근무시간 합계</td>');
        html.push('<td>');
        html.push(member_data[members[i]].toFixed(2));
        html.push('</td>');
        html.push('<td colspan=3></td>')
        html.push('</tr>');
        
        $('tr[data-member-name='+members[i]+']:last').after(html.join(''));
        var row_count = $('tr[data-member-name='+members[i]+']').length;
        $('tr[data-member-name='+members[i]+']:first th').attr('rowspan', row_count);
      }
      // for excel export
      $('.sum-row td').css('background-color', 'lightyellow');
    },
    
    setMemeberSelect: function(){
      Report.get("getMembersForSummary").then(function(res){
        return res;        
      }).then(function(data){
        var html = [];
        html.push('<option value="">All</option>');
        for(var i in data){
          html.push('<option value='+data[i].MemberIdx+'>'+data[i].MemberName+'</option>');
        }
        $('select[id="name"]').html(html.join(''));        
      });
    },

	}
  
	$(function(){
    // $("#datepicker").datepicker({
    //   showOtherMonths: true,
    //   selectOtherMonths: true,
    //   changeMonth: true,
    //   changeYear: true,
    //   dateFormat:"yy-mm-dd",      
    // });
		// $('#datepicker').datepicker( "setDate" , RS.getToday() );
		
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
    RS.setMemeberSelect();
    RS.search();
  });

  var tableToExcel = (function () {
            var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
            , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
            return function (table, name) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
                var blob = new Blob([format(template, ctx)]);
                var blobURL = window.URL.createObjectURL(blob);

                if (ifIE()) {
                    csvData = table.innerHTML;
                    if (window.navigator.msSaveBlob) {
                        var blob = new Blob([format(template, ctx)], {
                            type: "text/html"
                        });
                        navigator.msSaveBlob(blob, '' + name + '.xls');
                    }
                }
                else
                window.location.href = uri + base64(format(template, ctx))
            }
        })();

  function ifIE() {
    var isIE11 = navigator.userAgent.indexOf(".NET CLR") > -1;
    var isIE11orLess = isIE11 || navigator.appVersion.indexOf("MSIE") != -1;
      return isIE11orLess;
  }
</script>
</body>
</html>
