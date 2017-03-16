<?php
$products_list = '';
if ($products) {
  foreach($products as $row) {
  $products_list  .=  $products_list .= $products_list .= '<div class="product" id="product'.$row['id'].'">
    <h3>'.$row['title'].'</h3>
    <div class="product_descr">'.$row['descr'].'</div>
    <div class="product_price">Цена: '.$row['price'].'</div>
    </div>';
  }
} else {
  $products_list = '<div class="not_found">Ни одного продукта не найдено</div>';
}