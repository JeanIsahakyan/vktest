<div class="nav clear">
  <ul>
    <li><a onclick="switchTab('login');" id="login_tab">Войти</a></li>
    <li><a onclick="switchTab('register');" id="register_tab">Регистрация</a></li>
  </ul>
</div>
<div class="nav_content" id="login_content">
  <div class="login_container">
    <h3>Вход</h3>
    <div class="input_wrap">
      <input type="text" placeholder="Email" id="login_email" class="inp">
    </div>
    <div class="input_wrap">
      <input type="password" placeholder="Пароль" id="login_pass" class="inp">
    </div>
    <div class="input_wrap">
      <button class="btn" onclick="submitLogin()" id="login_btn">Войти</button>
    </div>
  </div>
</div>
<div class="nav_content" id="register_content">
  <div class="login_container">
    <h3>Регистрация</h3>
    <div class="input_wrap">
      <input type="text" placeholder="Имя" id="first_name" class="inp">
    </div>
    <div class="input_wrap">
      <input type="text" placeholder="Фамилия" id="last_name" class="inp">
    </div>
    <div class="input_wrap">
      <select class="inp" id="reg_type">
        <option value="0">Тип</option>
        <option value="1">Исполнитель</option>
        <option value="2">Заказчик</option>
      </select>
    </div> 
    <div class="input_wrap" id="balance_wrap" style="display: none;">
      <input type="text" placeholder="Баланс" id="balance" class="inp" onkeyup="onlyInt(this);">
      <small>Например: 10.4</small>
    </div>
    <div class="input_wrap">
      <input type="text" placeholder="Email" id="reg_email" class="inp">
    </div>
    <div class="input_wrap">
      <input type="text" placeholder="Пароль" id="reg_pass" class="inp">
    </div>
    <div class="input_wrap">
      <button class="btn" onclick="submitRegister();" id="register_btn">Зарегистрироваться</button>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
      switchTab('login');
  });
</script>