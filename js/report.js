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
          resolve(res);
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
          resolve(res);
        }
        reject(new Error("Request is failed"));
      });
    });
  }
}