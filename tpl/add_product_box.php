<div class="box_back" id="add_product_box">
  <div class="box_main">
    <div class="box_title">Добавление нового продукта <div class="box_close" onclick="addProductClose();">&times;</div></div>
     <div class="clear box_content">
      <div class="input_wrap">
      	<label>Название:</label>
        <input type="text" class="inp" id="product_title">
      </div>
      <div class="input_wrap">
        <label>Описание:</label>
        <textarea class="inp" style="resize: none;min-height:100px;" id="product_descr"></textarea>
      </div>
      <div class="input_wrap">
      	<label>Цена:</label>
        <input type="text" class="inp" id="product_price">
        <small>Например: 10.2</small>
      </div>
      <div class="input_wrap">
      	<button class="btn" onclick="addProductFinish('<?php echo $_POST['hash']; ?>');" id="add_product_box_btn">Добавить продукт</button>
      </div>
    </div>
  </div>
</div>