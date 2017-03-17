<?php
$stats_list = '';
if ($statistics) {
  foreach($statistics as $row) {


  $product = $products[$row['product_id']];
  $creator = $users[$product['owner_id']];
  $user = $users[$row['user_id']];

  $stats_list .= '<div class="product clear" style="width: 905px;height: auto;">
    <div class="product_price fl_r"> Цена: '.$product['price'].'</div>
    <h3 class="product_title">'.$product['title'].' </h3>
    <div class="product_price "> Система получила: <b>'.$row['commission'].'</b></div>
    <div class="product_price "> Создал продукт: <b>'.$creator['first_name'].' '.$creator['last_name'].'</b></div>
    <div class="product_price "> Выполнил: <b>'.$user['first_name'].' '.$user['last_name'].'</b></div>
    </div>';
  }
} elseif(!$offset)  {
  $stats_list = '<div class="not_found">Ни одного продукта не найдено</div>';
}