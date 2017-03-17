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
    $('#main_errors').append('<div class="main_error '+(focus ? 'green' : '')+'" id="main_error_'+cnt+'">'+text+'</div>');
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
  if(cur.loginLoading) return;
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
     cur.loginLoading = true;
     btnLoader('#login_btn');
     $.post('/index.php?act=login', {email: email, pass: pass}, function(res){
       
       delete cur.loginLoading;
       btnLoader('#login_btn');

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
  if(cur.registerLoading) return;
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
     cur.registerLoading = true;
     btnLoader('#register_btn');
     $.post('index.php?act=register', query, function(res) {
      
       delete cur.registerLoading;
       btnLoader('#register_btn');

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
function logOut(hash) {
  $.post('/index.php?act=logout', {hash: hash}, function(res) {
    if (res == 'error')  return showError('ОШИБКА ДОСТУПА');
    location.reload();
  })
}

function btnLoader(btn) {
  var btn = $(btn), dbtn = btn.get(0);
  if (dbtn.loading) {
    $(btn).html(dbtn.text).css('width', '');
    dbtn.loading = false;
  } else {
    dbtn.loading = true;
    dbtn.text = $(btn).html();
    var width = parseInt(btn.css('width').replace('px','') + (18 * 2));
    btn.html('<img src="/images/loader_inv.gif">').css('width', width + 'px');
  }
}

function showMoreProducts(offset, stats) {
  if (cur.showMoreLoading) return;

 var btn = $('#show_more'), dbtn = btn.get(0);
 if (dbtn.offset){
   offset = dbtn.offset;
 }
  cur.showMoreLoading = true;
  btnLoader('#show_more');
 $.post(location.href, {offset: offset}, function(res) {
 
  delete cur.showMoreLoading;
  btnLoader('#show_more');
  
  res = JSON.parse(res);
  if (res.content.length != 0) {
      dbtn.offset = res.offset;
      $('#'+ (stats ? 'stats' : 'products') +'_list').append(res.content);
  } else {
    $('#show_more').hide();
  }
 });
}

function addProductFinish(hash) {
  if (cur.productAddLoading) return;

  var title = $('#product_title').val().trim(),
      descr = $('#product_descr').val().trim(),
      price = $('#product_price').val().trim();

    if (title.length == 0) {
      showError('Необходимо ввести название продукта');
      return inputError('#product_title'); 
    } else if (descr.length == 0) {
      showError('Необходимо ввести описание продукта');
      return inputError('#product_descr'); 
    } else if (price.length == 0) {
      showError('Необходимо указать цену продукта');
      return inputError('#product_price'); 
    }

   var query = {
    title: title,
    descr: descr,
    price: price,
    hash: hash
   };

   cur.productAddLoading = true;
   btnLoader('#add_product_box_btn');

   $.post('/index.php?act=add_product', query, function(res) { 
     delete cur.productAddLoading;
     btnLoader('#add_product_box_btn');
     res = res.split('|');
     switch(res[0]) {
      case 'ok':
         addProductClose();
         $('#balance').text(res[1]);
         navGo('/'); 
        break;
      case 'title':
          showError('Название слишком короткое');
          return inputError('#product_title'); 
        break;
       case 'descr':
          showError('Описание слишком короткое');
          return inputError('#product_descr'); 
        break;
        case 'price':
          showError('Неправильный формат цены');
          return inputError('#product_price'); 
         break;
        case 'low_price':
          return showError('У Вас недостаточно средств');
          break;
     }
   })

}
function onlyInt(el) {
  if(el.timeout) clearTimeout(el.timeout);

  el.timeout = setTimeout(function(){
    var val =  Number(el.value.replace(/[^0-9.]/g, ""));
    if(!isNaN(val)) {
      el.value = val;
    }
  }, 400);
}

function addProduct(hash) {
  if (cur.productBoxLoading) return;

  btnLoader('#add_product');
  cur.productBoxLoading = true;
  $.post('/index.php?act=a_product_box', {hash: hash}, function(res){
    delete cur.productBoxLoading;
    btnLoader('#add_product');
    if (res == 'error') {
      showError('У вас недостаточно прав для этого действия');
    } else {
      $('body').append(res);
      $('body, html').css('overflow','hidden');
    }
  });
}

function addProductClose(){
 $('#add_product_box').remove();
 $('body, html').css('overflow','');
}

function submitProduct(id, hash) {
  if(cur.submitProductLoading) return;
  
  cur.submitProductLoading = true;
  btnLoader('#product_btn' + id);
  $.post('/index.php?act=submit_product', {id: id, hash: hash}, function(res){
    delete cur.submitProductLoading;
    btnLoader('#product_btn' + id);
    if (res == 'error') {
      return showError('Не удалось выполнить действие');
    } else {
      res =  JSON.parse(res);
      $('#balance').text(res.balance);
      showError('За выполнение Вам было зачислено '+res.price , true);
      $('#product'+id).slideUp();
    } 
  });
}

window.onload = function(){
  window.addEventListener('popstate', function(e){
    e.preventDefault();
    if(e.state){
      navGo(e.state, false, {noHistory:true});
    }
  });
  history.pushState(location.href, null, location.href);
}

function navGo(h,e,opts) {
  if(cur.navGoLoading) return;

  if (h.href) {
     h = h.href;
  }
  if (e) {
     e.preventDefault();
  }
  opts =  opts || {};
  
  if (opts.loader) {
     $(opts.loader).addClass('active');
  }
  cur.navGoLoading = true;
  $.post(h, {ajax: 1}, function(res){
    delete cur.navGoLoading;
    
    $('body').scrollTop(0);

    if(!opts.noHistory){
      history.pushState(h, null, h);
    }

    if (opts.loader) {
       $(opts.loader).removeClass('active');
    }
    if (opts.cb && typeof opts.cb == 'function') {
      opts.cb();
    }

    res = JSON.parse(res);
    
    if (res.title) {
       document.title = res.title;
    }

    $('#content').html(res.content);

  })
}