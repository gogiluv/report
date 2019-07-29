<?php
  include "lib/default.php";
  include "lib/db.php";
  if(SessionCheck() == false) {
    echo "<script>
            alert('세션이 만료되었습니다. 로그인 페이지로 이동합니다.');
            parent.location.href='index.php';
          </script>";
    return;
  }

  // $ch = curl_init();
  // $url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo'; /*URL*/
  // $queryParams = '?' . urlencode('ServiceKey') . '=uTHUN5ZrJcJvmr2dO%2FVhXt%2BEZf2bDxVJTh%2F%2B8fcE1qFFEbpRiBY%2BVEP2oPl%2FctiNJQorvG%2F6N%2FlY%2FmO%2FTA2csA%3D%3D'; /*Service Key*/
  // $queryParams .= '&' . urlencode('solYear') . '=' . urlencode('2019'); /*연*/
  // $queryParams .= '&' . urlencode('solMonth') . '=' . urlencode('05'); /*월*/
  
  // curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  // curl_setopt($ch, CURLOPT_HEADER, FALSE);
  // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  // $response = curl_exec($ch);
  // curl_close($ch);
  
  // $xml_data = simplexml_load_string($response);
  // $items = $xml_data->body->items->item;
  // $data_arr = array();
    
  // foreach ($items as $item) {
  //   $json = json_encode($item);
  //   $array = json_decode($json,TRUE);
  //   $data_arr[] = $array;
  // }
  // var_dump($xml_data->body->items->item);

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
  <script type="text/javascript" src="//image-glb.qpyou.cn/hiveone/shared_files/guide/js/ui.js"></script>
  <script src="js/report.js"></script>
</head>
<body>
<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 공휴일 관리</h1>   
  <div class="boardtype2" style="width:auto; max-width:1200px;">
    <div class="tabwrap">
			<div class="tabmenu tabmenu2">
				<a href="#tabcontents1" class="tab" data-tab="holiday">휴일 목록</a>
        <a href="#tabcontents2" class="tab" data-tab="public">공공 API 휴일</a>
			</div>
			<div class="tabcontents holiday" id="tabcontents1">
        <div class="search-area" style="width:800px;">
        <table>
            <colgroup width="10%"></colgroup>
            <colgroup width="10%"></colgroup>
            <colgroup width="12%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup></colgroup>
            <thead>
              <tr>
                <th scope="col">년 선택</th>
                <td>
                  <span id="year-area"></span>
                  <span id="month-area"></span>
                </td>
                <th scope="col">공휴일 검색</th>
                <td>
                  <span><input type="button" class="button red" value="검색하기" onclick="HD.getHolidaysWithAPI()"></span> / 
                  <span><input type="button" class="button green" value="추가하기" onclick="HD.addForm()"></span>
                  <!-- <span><input type="button" class="button red" value="임시저장" onclick="AddReport.draft()"></span> -->
                </td>
                <td>
                  공휴일은 업무보고취합, 미작성 알림에서 제외됩니다.                  
                </td>
              </tr>
            </thead>
          </table>
          <table id="holiday-list" class="mt-20">
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <thead>
              <th>날짜</th>
              <th>요일</th>
              <th>이름</th>
              <th>비고</th>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="tabcontents public" id="tabcontents2" style="display:none;">
        <div class="search-area" style="width:800px;">
          <table>
            <colgroup width="10%"></colgroup>
            <colgroup width="10%"></colgroup>
            <colgroup width="12%"></colgroup>
            <colgroup width="12%"></colgroup>
            <colgroup></colgroup>
            <thead>
              <tr>
                <th scope="col">년 선택</th>
                <td>
                  <span id="api-year-area"></span>
                  <span id="api-month-area"></span>
                </td>
                <th scope="col">공휴일 검색</th>
                <td>
                  <span><input type="button" class="button red" value="검색하기" onclick="HD.getHolidaysWithAPI()"></span>
                  <!-- <span><input type="button" class="button red" value="임시저장" onclick="AddReport.draft()"></span> -->
                </td>
                <td>
                  공공 api의 key 유효기간은 2년 입니다. (<a href="https://www.data.go.kr/" target="_blank">https://www.data.go.kr/</a>)
                  <br/>2021년 5월 이전에 키 갱신 필요합니다.
                  <br/>다음해의 휴일은 가을쯤 업데이트 된다고 합니다.
                </td>
              </tr>
            </thead>
          </table>
          <table id="api-holiday-list" class="mt-20">
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup width="25%"></colgroup>
            <thead>
              <th>날짜</th>
              <th>요일</th>
              <th>이름</th>
              <th>비고</th>
            </thead>
            <tbody></tbody>
          </table>

          <table>
            <tbody>
              <tr>
                <td>
                  <input type="button" class="button red" value="검색 결과를 휴일 DB에 추가하기" onclick="HD.addHolidayFromRows()">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="HD.modalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>
<script type="text/javascript">
    var HD = {
      weekday: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],

      getToday: function(){
        return Date.now();
      },

      getHolidays: function(){
        var data = {
          year: "2019"
        }

        //loading
        Report.loading();

        Report.get('getHolidays', data).then(function(res){
          $('#holiday-list tbody').html('');
          for(var i in res){
            var row = res[i];
            var row_date = new Date(row.loc_date);
           
            //관공서 휴일이 아닐경우 제외
            $('#holiday-list tbody').append('\
              <tr>\
              <td>'+row.loc_date+'</td>\
              <td>'+HD.weekday[row_date.getDay()]+'</td>\
              <td>'+row.date_name+'</td>\
              <td><input type="button" class="button red" value="삭제" onclick="HD.deleteHoliday('+row.id+')"></td>\
              </tr>\
            ');            
          }
          
          parent.fncResizeHeight(document);

          //loading close          
          Report.closeModal();

        });
      },
      
      getHolidaysWithAPI: function(){        
        var data = {
          year: $('#api-search-year').val(),
          //month: $('#search-month').val()
        }
        //loading
        Report.loading();

        Report.get('getHolidaysWithAPI', data).then(function(res){
          $('#api-holiday-list tbody').html('');
          for(var i in res){
            var row = res[i];
            var year = row.locdate.substr(0,4);
            var month = row.locdate.substr(4,2);
            var day = row.locdate.substr(6,2);
            var row_date = new Date(year, month, day);
            var isHoliday = row.isHoliday;

            //관공서 휴일이 아닐경우 제외
            if(isHoliday==='Y'){
              $('#api-holiday-list tbody').append('\
                <tr>\
                <td>'+year+'-'+month+'-'+day+'</td>\
                <td>'+HD.weekday[row_date.getDay()]+'</td>\
                <td>'+row.dateName+'</td>\
                <td><input type="button" class="button red" value="삭제" onclick="HD.removeRow(this)"></td>\
                </tr>\
                ');
            }
          }
          
          parent.fncResizeHeight(document);

          //loading close          
          Report.closeModal();
        });
      },
      setYearSelect: function(){
        var now = new Date();
        var currentYear = now.getFullYear();
        var html = [];
        html.push('<select id="search-year">');
        for(var i=2019; i<=2025; i++){
          if(currentYear===i){
            html.push('<option value="'+i+'" selected>'+i+'년</option>');
          }else{
            html.push('<option value="'+i+'">'+i+'년</option>');
          }
        }        
        html.push('</select>');
        $('#year-area').html(html.join(''));
      },
      setYearSelectForAPI: function(){
        var now = new Date();
        var currentYear = now.getFullYear();
        var html = [];
        html.push('<select id="api-search-year">');
        for(var i=2019; i<=2025; i++){
          if(currentYear===i){
            html.push('<option value="'+i+'" selected>'+i+'년</option>');
          }else{
            html.push('<option value="'+i+'">'+i+'년</option>');
          }
        }        
        html.push('</select>');
        $('#api-year-area').html(html.join(''));
      },
      setMonthSelect: function(){
        var now = new Date();
        var currentMonth = (now.getMonth()+1);
        var html = [];
        html.push('<select id="search-month">');
        for(var i=1; i<=12; i++){
          if(currentMonth===i){
            html.push('<option value="'+i+'" selected>'+i+'월</option>');
          }else{
            html.push('<option value="'+i+'">'+i+'월</option>');
          }
        }        
        html.push('</select>');
        $('#month-area').html(html.join(''));
      },

      removeRow: function(e){
        //행 삭제
        Report.confirm('해당 항목을 제외하시겠습니까?', function(b){
          if(!b) return;

          $(e).parents('tr').remove();
        })        
      },

      addHolidayFromRows: function(){
        var rows = $('#api-holiday-list tbody tr');
        var data = {};
        
        if(rows.length<1) {
          Report.alert('추가할 항목이 없습니다.');
          return;
        }

        for(var i=0; i<rows.length; i++){
          var row = rows[i];
          data[i] = {
            loc_date: $(row).find('td:eq(0)').text(),
            date_name: $(row).find('td:eq(2)').text()
          }
        }
        
        Report.post('insertHoliday', data).then(function(res){
          if(res.result==true){
            Report.alert('추가되었습니다.');
          }else{
            Report.alert(res.result);
          }
        });
      },
      deleteHoliday: function(id){
        var data = {
          id: id
        }
        //confirm
        Report.confirm('삭제하시겠습니까?', function(b){
          if(!b) return;
          //api
          Report.post('deleteHoliday', data).then(function(res){
            if(res.result==true){
              HD.getHolidays();
            }else{
              Report.alert('삭제 실패, 관리자에게 문의해주세요.' + res.result);
            }
          });
        });
      },
      addForm: function(){
        var content = '<table class="ta-c">\
            <colgroup width="15%"></colgroup>\
            <colgroup width="25%"></colgroup>\
            <colgroup width="15%"></colgroup>\
            <colgroup width="45%"></colgroup>\
            <thead></thead>\
            <tbody>\
              <tr>\
                <th>날짜</th>\
                <td><input type="text" id="locDate" class="input-style-01" readonly/></td>\
                <th>이름</th>\
                <td><input type="text" id="dateName" class="input-style-01 w-200"></td>\
              </tr>\
            </tbody>\
            </table>';

        var footer = '<div class="ta-c fs-15 mt-20">\
                      <a class="button blue" onclick="HD.addConfirm()">추가</a>\
                      <a class="button" onclick="HD.modalClose()">취소</a>\
                      <div>';

        var modal_data = {
          header: '휴일, 예외일 추가',
          content: content,
          footer: footer
        }

        HD.modalOpen(modal_data);
        $("#locDate").datepicker({
          showOtherMonths: true,
          selectOtherMonths: true,
          changeMonth: true,
          changeYear: true,
          dateFormat:"yy-mm-dd",      
        });
        $('#locDate').datepicker( "setDate" , HD.getToday() );
      },
      modalOpen: function(data){
        $('.modal-header').text(data.header);
        $('.modal-content').html(data.content);
        $('.modal-footer').html(data.footer);
        $('.overlay').show();
      },
      modalClose: function(){
        $('.modal-header').text('');
        $('.modal-content').html('');
        $('.modal-footer').html('');
        $('.overlay').hide();
      },
      addConfirm: function(){
        var date_name = $('#dateName').val().trim();
        var loc_date = $('#locDate').val();

        if(loc_date==null || loc_date.length < 1){
          Report.alert('날짜를 선택해 주세요.');
          return;
        }

        if(date_name==null || date_name.length < 1){
          Report.alert('날짜 명을 작성 해 주세요.');
          return;
        }

        Report.confirm('추가하시겠습니까?', function(b){
          //confirm check
          if(!b) return;
          var data = [{
            loc_date: loc_date,
            date_name: date_name
          }]
          
          Report.post('insertHoliday', data).then(function(res){
            if(res.result==true){
              HD.getHolidays();
              HD.modalClose();
            }else{
              Report.alert('추가 실패, 관리자에게 문의해주세요.' + res.result);
            }
          });
        })
      },
    }

    $(function(){
       console.log('holiday init');
       HD.setYearSelect();
       HD.setYearSelectForAPI();
       //HD.setMonthSelect();
       $('.tabmenu2').tabMenu(0);
       HD.getHolidays();
    });
</script>
