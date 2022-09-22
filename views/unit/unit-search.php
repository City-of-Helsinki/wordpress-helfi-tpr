<?php use CityOfHelsinki\WordPress\TPR as Plugin; ?>
<div class="wrap">
    <h1><?php _e('Add new units', 'helsinki-tpr')?></h1>
    <?php
        require_once Plugin\views_path( 'unit' ) . 'unit-config.php';
        require_once Plugin\views_path( 'unit' ) . 'unit-list.php';
    ?>
</div>