<div class="nav clear">
  <ul>
    <li><a href="/" class="<?php if (!$_GET['finished']) {
      echo 'active';
      } ?>" onclick="navGo(this, event); btnLoader(this);">Все продукты</a></li>
    <li><a href="/?finished=1" class="<?php if ($_GET['finished']) {
      echo 'active';
      } ?>" onclick="navGo(this, event); btnLoader(this);">Выполненные</a></li>
    <?php
    if ($owner['type'] == 2) {
      echo '<li><button class="btn" onclick="addProduct(\''.genHash('addProduct').'\');" id="add_product">Добавить заказ</button></li>';
    }
    ?>
  </ul>
</div>
<div id="products_list" class="clear">
  <?php echo $products_list;?>
</div>
<div class="show_more">
  <button onclick="showMoreProducts(<?php echo (int)$new_offset; ?>);" class="btn" id="show_more" style="<?php if ($new_offset < $limit) { echo 'display: none'; } ?>">Показать еще</button>
</div>