<?php
$products_list = '';
if ($products) {
  foreach($products as $row) {

   if ($owner['type'] == 1) { 
    $prc = $row['price'] * ($SYSTEM_COMMISSION / 100);
    $submit = '<small >С учетом комиссии: '.($row['price'] - $prc).'</small>';
    if (!$row['user_id']) {
      $submit .= '<button class="btn fl_r" onclick="submitProduct('.$row['id'].', \''.genHash('product'.$row['id']).'\')" id="product_btn'.$row['id'].'">Выполнить</button>';
    }
   } elseif ($owner['type'] == 2 && $row['user_id']) {
    $u  = $user[$row['user_id']];
    $submit = '<small style="color: #2fba5e">Выполнил: '. $u['first_name'].' '.$u['last_name'].'</small>';
   }

  $products_list .= '<div class="product clear" id="product'.$row['id'].'">
    <div class="product_price fl_r"> Цена: '.$row['price'].'</div>
    <h3 class="product_title">'.$row['title'].' </h3>
    <div class="product_descr">'.$row['descr'].'</div>
    <div class="clear product_submit">'.$submit.'</div>
    </div>';
  }
} else {
  $products_list = '<div class="not_found">Ни одного продукта не найдено</div>';
}