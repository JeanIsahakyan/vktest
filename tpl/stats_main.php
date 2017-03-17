<div id="products_list" class="clear">
  <?php echo $stats_list;?>
</div>
<div class="show_more">
  <button onclick="showMoreProducts(<?php echo (int)$new_offset; ?>);" class="btn" id="show_more" style="<?php if ($new_offset < $limit) { echo 'display: none'; } ?>">Показать еще</button>
</div>