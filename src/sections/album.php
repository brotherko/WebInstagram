<?php
  require_once 'lib/base.php';  
  $page = $_GET['page'] ?? 1;
  $offset = ($page-1) * CONFIG['items_per_page'];
  $total_items = $dal->count_images([VISIBILITY_PUBLIC]);
  $max_pager = ceil($total_items['count']/CONFIG['items_per_page']);
  $prev_pager = $page > 1 ? '<a href="?page='.($page-1).'">Prev</a>' : null;
  $next_pager = $page < $max_pager ? '<a href="?page='.($page+1).'">Next</a>' : null;
  $images = $dal->get_images_by_created([$current_user->id, $offset]);
?>
<style>
  .container {
    width: 1200px;
  }
  .container.album{
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }
  .album > div {
    width: 250px;
    padding: 5px;
  }
  .album > div > img{
    width: 100%;
  }
</style>
<h2>Photo Album</h2>
<div class="album container">
  <?php foreach($images as $image){ ?>
    <div><img src="<?=$image['link']?>"></div>
  <?php } ?>
</div>
<div class="container">
<?=$prev_pager?>
<?=$next_pager?>
</div>