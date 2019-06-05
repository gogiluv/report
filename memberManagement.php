<?php
  include "lib/db.php";
  include "lib/default.php";
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
  <script type="text/javascript" src="//image-glb.qpyou.cn/hiveone/shared_files/guide/js/ui.js"></script>
  <script src="js/report.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 팀원 관리</h1>
  <div class="boardtype2" style="width:auto; max-width:800px; max-height:1000px; overflow-y:auto;">
    <div class="member-create" style="width:620px;">
      <table>
        <thead>
          <tr>
            <th width="100px" scope="col">팀원 검색: </th>
            <td><input type="text" id="searchMember" class="w-100 input-style-01" onkeyup="MM.searchKeyUp()"/></td>
            <td><input type="checkbox" id="oldMember"/><label for="oldMember">이전 팀원 포함</label></td>
            <td width="220px" >
              <span><input type="button" class="button red mr-10" data-button="search" value="검색하기" onclick="MM.searchMember()"></span>/ 
              <span><input type="button" class="button green" data-button="addMemeber" value="추가하기" onclick="MM.addForm()"></span>
            </td>
            </tr>
        </thead>
      </table>
    </div>
    <div class="member-list mt-20 ta-c">
      <table>
        <colgroup width="10%"></colgroup>
        <colgroup span="3" width="20%"></colgroup>
        <colgroup width="20%"></colgroup>
        <thead>
          <tr>
            <th>ID</th>
            <th>이름</th>
            <th>직급</th>
            <th>파트</th>
            <th>비고</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
	</div>
</div>
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="MM.expandModalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>
<script type="text/javascript">
var MM = {
  parts: {},
  positions: {},

  addForm: function(){
    var content = '<table class="ta-c">\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <thead></thead>\
        <tbody>\
          <tr>\
            <th>이름</th>\
            <td><input type="text" id="memberName" class="input-style-01 w-100" placeholder="ex) 홍길동 "></td>\
            <th>직급</th>\
            <td>'+MM.getPositionSelectHTML()+'</td>\
          </tr>\
          <tr>\
            <th>파트</th>\
            <td>'+MM.getPartSelectHTML()+'</td>\
            <th>상태</th>\
            <td>'+MM.getVisibleHTML(1)+'</td>\
          </tr>\
          <tr>\
            <th>level(1,2,3)</th>\
            <td><input type="text". id="levelIdx" class="input-style-01 w-100" placeholder="ex) 1,2,3"></td>\
            <th>사번</th>\
            <td><input type="text" id="EN" class="input-style-01 w-100" placeholder="ex) 1812345"></td>\
          </tr>\
        </tbody>\
        </table>';

    var footer = '<div class="ta-c fs-15">\
                  <a class="button blue" onclick="MM.addConfirm()">추가</a>\
                  <a class="button" onclick="MM.expandModalClose()">취소</a>\
                  <div>';

    var modal_data = {
      header: '팀원 추가 (초기 비밀번호는 1234567)',
      content: content,
      footer: footer
    }

    MM.expandModalOpen(modal_data);                    
  },
  addConfirm: function(){
    Report.confirm('추가하시겠습니까?', function(b){
      //if cancel
      if(!b) return;

      var data = {
        memberName: $('#memberName').val(),
        partIdx: $('#part-select').val(),
        positionIdx: $('#position-select').val(),
        visible: $('.modal-div input[type="radio"]:eq(0)').prop('checked') ? 1 : 0,
        levelIdx: $('#levelIdx').val(),
        EN: $('#EN').val(),
      }

      Report.post('addMember', data).then(function(res){
        if(res.result) {
          MM.expandModalClose();
          MM.searchMember();
          Report.alert('추가되었습니다.');
        }else{
          Report.alert('생성 실패: '+ res.error);
        }
      });
    });
  },
  pwdReset: function(id){
    var msg = '비밀번호를 초기화 하시겠습니까?\n 1234567로 초기화 됩니다.'
    Report.confirm(msg, function(b){
      if(!b) return;

      var data = {
        memberIdx: id
      }
      Report.post('pwdReset', data).then(function(res){
        if(res.result){
          Report.alert('변경되었습니다');
        } else {
          Report.alert('error: 관리자에게 문의 해 주세요');
        }
      });
    })
  },
  modifyForm: function(id){
    var data = {
      memberIdx: id
    }
    Report.get("getMemberFromId", data).then(function(res){
      var content = '<table class="ta-c">\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <colgroup width="25%"></colgroup>\
        <thead></thead>\
        <tbody>\
          <tr>\
            <th>이름</th>\
            <td><input type="text" id="memberName" class="input-style-01 w-100" value="'+res.MemberName+'"></td>\
            <th>직급</th>\
            <td>'+MM.getPositionSelectHTML(res.PositionIdx)+'</td>\
          </tr>\
          <tr>\
            <th>파트</th>\
            <td>'+MM.getPartSelectHTML(res.PartIdx)+'</td>\
            <th>상태</th>\
            <td>'+MM.getVisibleHTML(res.Visible)+'</td>\
          </tr>\
          <tr>\
            <th>level(1,2,3)</th>\
            <td><input type="text". id="levelIdx" class="input-style-01 w-100" placeholder="ex) 1,2,3" value="'+res.LevelIdx+'"></td>\
            <th>사번</th>\
            <td><input type="text" id="EN" class="input-style-01 w-100" placeholder="ex) 1812345" value="'+res.EN+'"></td>\
          </tr>\
          <tr>\
            <th>패스워드 초기화</th>\
            <td><a class="button greed" onclick="MM.pwdReset('+res.MemberIdx+')" >초기화</a></td>\
            <th>기타</th>\
            <td></td>\
          </tr>\
        </tbody>\
        </table>';

      var footer = '<div class="ta-c fs-15">\
                    <a class="button blue" onclick="MM.modifyConfirm('+res.MemberIdx+')">수정</a>\
                    <a class="button" onclick="MM.expandModalClose()">취소</a>\
                    <div>';

      var modal_data = {
        header: '팀원 정보 수정',
        content: content,
        footer: footer
      }
      MM.expandModalOpen(modal_data);
    });
  },
  modifyConfirm: function(id){
    Report.confirm('수정하시겠습니까?', function(b){
      //if cancel
      if(!b) return;

      var data = {
        memberIdx: id,
        memberName: $('#memberName').val(),
        partIdx: $('#part-select').val(),
        positionIdx: $('#position-select').val(),
        visible: $('.modal-div input[type="radio"]:eq(0)').prop('checked') ? 1 : 0,
        levelIdx: $('#levelIdx').val(),
        EN: $('#EN').val(),
      }

      Report.post('modifyMember', data).then(function(res){
        if(res.result) {
          MM.expandModalClose();
          MM.searchMember();
          Report.alert('변경되었습니다.');
        }else{
          Report.alert('error: '+ res.error);
        }
      });
    });
  },
  reset: function(){
    console.log('reset');
  },
  getMembers: function(data){
    Report.get('getMembers', data).then(function(res){
      MM.renderRows(res);
    });
  },
  searchMember: function(){
    var data = {
      memberName: $('#searchMember').val(),
      visible: $('#oldMember').prop('checked') ? null : 1
    }
    MM.getMembers(data);
  },
  renderRows: function(rows){
    var html = [];
    for(var i in rows){
      row = rows[i];
      row_html = MM.createRow(row);
      html.push(row_html);
    }
    $('.member-list table tbody').html(html.join(''));
    parent.fncResizeHeight(document);
  },
  createRow: function(row){
    row_html = '<tr>\
      <td>'+row.MemberIdx+'</td>\
      <td>'+row.MemberName+'</td>\
      <td>'+row.Position+'</td>\
      <td>'+row.Part+'</td>\
      <td>\
      <input type="button" class="button blue mr-10" value="수정" onclick="MM.modifyForm('+row.MemberIdx+')">\
      <input type="button" class="button red mr-10" value="삭제" onclick="MM.deleteMember('+row.MemberIdx+')">\
      </td>\
      </tr>';
    return row_html;
  },
  deleteMember: function(id){
    Report.confirm('삭제하시겠습니까?', function(b){
      // cancel
      if(!b) return;

      Report.post('deleteMember', {memberIdx: id}).then(function(res){
        if(res.result==true){
          MM.searchMember();
        }else{
          Report.alert(res.result);
        }
      })
    });
  },
  searchKeyUp: function(e){
      // look for window.event in case event isn't passed in
      e = e || window.event;
      if (e.keyCode == 13)
      {
          $('input[type="button"][data-button="search"]').click();
          return false;
      }
      return true;
  },
  expandMember: function(id){
    var data = {
      memberIdx: id,
      
    }
    Report.get("getMemberFromId", data).then(function(res){
      PR.expandModalOpen(res);
    });
  },
  expandModalOpen: function(data){
    $('.modal-header').text(data.header);
    $('.modal-content').html(data.content);
    $('.modal-footer').html(data.footer);
    $('.overlay').show();
  },
  expandModalClose: function(){
    $('.modal-header').text('');
    $('.modal-content').html('');
    $('.modal-footer').html('');
    $('.overlay').hide();
  },
  setParts: function(){
    Report.get('getParts').then(function(res){
      MM.parts = res;
    });
  },
  setPositions: function(){
    Report.get('getPositions').then(function(res){
      MM.positions = res;
    });
  },
  getPartSelectHTML: function(selected_id){
    var html = [];
    html.push('<select class="input-style-01" id="part-select">');
    html.push('<option value="" disabled selected></option>');
    for(var i in MM.parts){
      if(MM.parts[i].PartIdx == selected_id){
        html.push('<option value='+MM.parts[i].PartIdx+' selected>');  
      }else{
        html.push('<option value='+MM.parts[i].PartIdx+'>');
      }
      html.push(MM.parts[i].Name);
      html.push('</option>');
    }
    html.push('</select>');
    return html.join('');
  },
  getPositionSelectHTML: function(selected_id){
    var html = [];
    html.push('<select class="input-style-01" id="position-select">');
    html.push('<option value="" disabled selected></option>');
    for(var i in MM.positions){
      if(MM.positions[i].PositionIdx == selected_id){
        html.push('<option value='+MM.positions[i].PositionIdx+' selected>');  
      }else{
        html.push('<option value='+MM.positions[i].PositionIdx+'>');
      }
      html.push(MM.positions[i].Name);
      html.push('</option>');
    }
    html.push('</select>');
    return html.join('');
  },
  getVisibleHTML: function(status){
    var html = [];
    switch (Number(status)) {
      case 0:
        html.push('<label for="radio-1">활성</label>');
        html.push('<input type="radio" name="radio-1" id="radio-1" style="margin:0px 3px 3px 3px;">');
        html.push('<label for="radio-2">비활성</label>');
        html.push('<input type="radio" name="radio-1" id="radio-2" style="margin:0px 3px 3px 3px;" checked>');
        break;
      case 1:
        html.push('<label for="radio-1">활성</label>');
        html.push('<input type="radio" name="radio-1" id="radio-1" style="margin:0px 3px 3px 3px;" checked>');
        html.push('<label for="radio-2">비활성</label>');
        html.push('<input type="radio" name="radio-1" id="radio-2" style="margin:0px 3px 3px 3px;">');
        break;    
      default:
        html.push('status code check plz');
        break;        
    }
    return html.join('');
  }
 }

 //init
 $(function(){
  console.log('init');
  MM.setParts();
  MM.setPositions();  
  MM.getMembers({visible: 1});
 }); 
</script>
</body>
</html>