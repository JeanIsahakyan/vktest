var cur = {};

function switchTab(tab) {
  if (cur.tab == tab) return;
  $('.nav ul li a, .nav_content').removeClass('active');
  $('#'+tab+'_content, #'+tab+'_tab').addClass('active');
  cur.tab = tab;
}
function submitLogin() {
  var first_name = $('#login_email').val().trim(), 
      last_name = $('#login_pass').val().trim();

}
function submitRegister() {
  var first_name = $('#first_name').val().trim(), 
      last_name  = $('#last_name').val().trim(),
      type       = $('#last_name').val().trim(),
      balance    = $('#balance').val().trim(),
      email      = $('#reg_email').val().trim(),
      pass       = $('#reg_pass').val().trim();

}