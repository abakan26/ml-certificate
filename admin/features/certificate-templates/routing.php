<?php
if (isset($_GET['add']) && $_GET['add'] = 'certificate') {
    include __DIR__ . '/add/controller.php';
} elseif (isset($_GET['download']) && !empty($_GET['download'])) {
    include __DIR__ . '/download/controller.php';
} elseif (isset($_GET['certificate_id']) && !empty($_GET['certificate_id'])) {
    include __DIR__ . '/edit/controller.php';
} else {
    include __DIR__ . '/table/controller.php';
}
