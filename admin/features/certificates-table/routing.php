<?php
if (isset($_GET['mode']) && $_GET['mode'] === 'view'
    && isset($_GET['certificate_id']) && !empty($_GET['certificate_id'])) {
    include 'preview/controller.php';
} else {
    include 'table/controller.php';
}

