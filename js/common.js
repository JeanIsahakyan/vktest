var cur = {};

function switchTab(tab) {
  if (cur.tab == tab) return;
  $('.nav ul li a, .nav_content').removeClass('active');
  $('#'+tab+'_content, #'+tab+'_tab').addClass('active');
  cur.tab = tab;
}
function inputError(inp, color) {
   $(inp).css('background-color', color || '#ffe9e9').focus();
   setTimeout(function(){
        $(inp).css('background-color', '');
   }, 1000);
}
function showError(text, focus){
    if(!this.cnt) this.cnt = 0;
    var cnt = this.cnt;
    $('#main_errors').append('<div class="main_error" id="main_error_'+cnt+'">'+text+'</div>');
    setTimeout(function() {
      $('#main_error_'+cnt).addClass('active');
    },100); 
    setTimeout(function(){
      $('#main_error_'+cnt).remove();
      cnt -= 1;
    },5000);
    this.cnt += 1;
}
function submitLogin() {
  var email = $('#login_email').val().trim(), 
      pass = $('#login_pass').val().trim();
     if (email.length == 0) {
       showError('Введите Email');
       return inputError('#login_email');
     }
     if (pass.length == 0) {
       showError('Введите пароль');
      return inputError('#login_pass');
     }
     $.post('/index.php?act=login', {email: email, pass: pass}, function(res){
       if (res == 'ok') return location.reload();
       if (res == 'email') {
         showError('Неправильный Email');
         return inputError('#login_email'); 
       }
       else if (res == 'pass'){
         showError('Неправильный пароль');
          return inputError('#login_pass');
       }else if (res == 'unknown') {
         return showError('Email или пароль был введен неправильно.');
       } 
     });
}

function submitRegister() {
  var first_name = $('#first_name').val().trim(), 
      last_name  = $('#last_name').val().trim(),
      type       = $('#reg_type').val().trim(),
      balance    = $('#balance').val().trim(),
      email      = $('#reg_email').val().trim(),
      pass       = $('#reg_pass').val().trim();

     if (first_name.length == 0) {
       showError('Введите имя');
       return inputError('#first_name');
     }
     if (last_name.length == 0) {
       showError('Введите фамилию');
       return inputError('#last_name');
     }
     if (parseInt(type) == 0) {
       showError('Необходимо выбрать тип');
       return inputError('#reg_type');
     } else if (type == 2 && balance.length == 0) {
        showError('Введите текущий баланс заказчика');
        $('#balance_wrap').slideDown();
       return inputError('#balance');
     }
     if (email.length == 0) {
       showError('Введите Email');
       return inputError('#reg_email');
     }
     if (pass.length == 0) {
       showError('Введите пароль');
      return inputError('#reg_pass');
     }

     var query = {
       first_name: first_name,
       last_name: last_name, 
       type: type,
       balance: balance,
       email: email,
       pass: pass
     }
     $.post('index.php?act=register', query, function(res) {

       switch (res) {
           case 'ok':
              return location.reload();
             break;

           case 'first_name': 
             showError('Имя слишком короткое');
              return inputError('#first_name');
             break;

           case 'last_name':
              showError('Фамилия слишком короткая');
             return inputError('#last_name'); 
             break;

           case 'type':
             showError('Необходимо выбрать тип');
                 return inputError('#reg_type');
             break;

           case 'balance':
            	 showError('Баланс дожен быть в цифравом формате');
            	  $('#balance_wrap').slideDown();
                 return inputError('#balance');
             break;

           case 'email':
              showError('Неправильный формат Email адреса');
             return inputError('#reg_email'); 
             break;

           case 'used_email':
          	   showError('Этот Email адрес уже использован');
      		   return inputError('#reg_email');
             break;

           case 'pass':
           		showError('Пароль слишком короткий');
         		 return inputError('#reg_pass');
             break;
       }

     });
}
function logOut() {
  $.post('/index.php?act=logout', function() {
    location.reload();
  })
}