<?php
/* Restore Default Custom Fields Panel */
add_filter('acf/settings/remove_wp_meta_box', '__return_false', 20);
