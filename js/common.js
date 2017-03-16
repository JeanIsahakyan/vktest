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
      type       = $('#last_name').val().trim(),
      balance    = $('#balance').val().trim(),
      email      = $('#reg_email').val().trim(),
      pass       = $('#reg_pass').val().trim();

     if (email.length == 0) {
       showError('Введите Email');
       return inputError('#login_email');
     }
     if (pass.length == 0) {
     	showError('Введите пароль');
      return inputError('#login_pass');
     }

}