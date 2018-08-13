<?php
require_once('./base.php');
?>


<script>
  var Data = {
    users: <?php echo json_encode(get_all_users()) ?>
  }
</script>
<script src="dist/js/index.js?v=<?php echo VERSION ?>"></script>