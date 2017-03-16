<?php
$products_list = '';
if ($products) {
  foreach($products as $row) {
    $products_list .= '<div class="product">
    <div class="product_title">'.$row['title'].'</div>
    <div class="product_descr">'.$row['descr'].'</div>
    <div class="product_price">Цена: '.$row['price'].'</div>
    </div>';
  }
} else {
  $products_list = '<div class="not_found">Ни одного продукта не найдено</div>';
}