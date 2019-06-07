<?php
require_once('./base.php');
?>

<div id="mobile-alert">
</div>

<script>
  Data = window.Data || {};
  Data.users= <?php echo json_encode(get_all_users()) ?>;
</script>
<script src="dist/js/index.js?v=<?php echo VERSION ?>"></script>