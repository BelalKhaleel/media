<div class="pages">
  <?php
  $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
  if ($current_page > 1) {
    ?>
    <span><a style="text-decoration:none;" href="<?= $webpage ?>.php?page=<?= $current_page - 1 ?>"><</a></span>
    <?php
  } else {
    ?>
    <span><a style="text-decoration:none; pointer-events:none;"><</a></span>
    <?php
  }
  for($page = 1; $page <= $number_of_pages; $page++) {
    ?>
      <a style="text-decoration:none;" href="<?= $webpage ?>.php?page=<?= $page ?>"><?= $page ?></a>
    <?php
  }
  if ($current_page < $number_of_pages) {
    ?>
    <span><a style="text-decoration:none;" href="<?= $webpage ?>.php?page=<?= $current_page + 1 ?>">></a></span>
    <?php
  } else {
    ?>
    <span><a style="text-decoration:none; pointer-events:none;">></a></span>
    <?php
  }
  ?>
</div>