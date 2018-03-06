<?php
    header('Content-Type: application/json');
    $args = array(
        'post_type' => 'post'
    );

    $query = new WP_Query($args);

    echo json_encode($query);
?>
