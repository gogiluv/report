var Report = {  
  // api 호출용
  // json 형식으로 response 받는걸 기본으로 한다
  // object 형식으로 request 보냄
  get: function(f, d) {
    return new Promise(function (resolve, reject) {
      var req_data = {
        f_name: f,
        data: d
      }
      $.get('lib/api.php', req_data, function (res) {
        // 데이터를 받으면 resolve() 호출        
        if (res) {
          var json_res = null;
          try {
            json_res = JSON.parse(res);
          } catch (e) {
            alert('관리자에게 문의 해 주세요:' + e);
            console.log(e);
            console.log(res);
            return;
          }
          //세션 만료 체크
          if(json_res && json_res.error==='SESSION_EXPIRED'){
            alert('세션이 만료되었습니다. 로그인 페이지로 이동합니다.');
            parent.location.href="index.php";
            return;
          }
          resolve(json_res);
        }
        reject(new Error("Request is failed"));
      });
    });
  },
  post: function(f, d) {
    return new Promise(function (resolve, reject) {
      var req_data = {
        f_name: f,
        data: d
      }
      $.post('lib/api.php', req_data, function (res) {
        // 데이터를 받으면 resolve() 호출        
        if (res) {
          var json_res = null;
          try {
            json_res = JSON.parse(res);
          } catch (e) {
            alert('관리자에게 문의 해 주세요:' + e);
            console.log(e);
            console.log(res);
            return;
          }
          //세션 만료 체크
          if(json_res.error && json_res.error==='SESSION_EXPIRED'){
            alert('세션이 만료되었습니다. 로그인 페이지로 이동합니다.');
            parent.location.href="index.php";
            return;
          }
          resolve(json_res);
        }
        reject(new Error("Request is failed"));
      });
    });
  },
  //alert 창
  alert: function(msg){
    var modal = $(`
    <div class="overlay boardtype2 alert">  
      <div class="modal-div">
        <div class="ui-icon ui-icon-closethick modal-close" onclick="Report.closeModal()"></div>
        <div class="modal-header">      
        </div>
        <div class="modal-content">`+msg+`</div>
        <div class="modal-footer mt-20">
          <a href="#" class="button blue" onclick="Report.closeModal()">확인</a>
        </div>
      </div>
    </div>
    `);                  
    $('body').append(modal);
    $(modal).show();
  },
  //confirm, 콜백 받아서 boolean 넣고 실행
  confirm: function(msg, callback){
    var modal = $(`
    <div class="overlay boardtype2 confirm">  
      <div class="modal-div">
        <div class="ui-icon ui-icon-closethick modal-close"></div>
        <div class="modal-header">      
        </div>
        <div class="modal-content">`+msg+`</div>
        <div class="modal-footer mt-20">
          <a href="#" class="button blue">확인</a>
          <a href="#" class="button red">취소</a>
        </div>
      </div>
    </div>
    `);
    $('body').append(modal);
    $(modal).show();
    
    //event
    $(modal).find('.modal-footer a:eq(0)').click(function(){
      callback(true);
      Report.closeModal();
    });
    $(modal).find('.modal-footer a:eq(1), .modal-close').click(function(){
      callback(false);
      Report.closeModal();
    });    
  },
  // 창닫기, 공통
  closeModal: function() {
    $('.alert').remove();
    $('.confirm').remove();
  },
}