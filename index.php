<?php

if (isset($_GET['page'])) {
    $page = $_GET['page'];

    if ($page === 'home' || $page === '') {
        include './view/home/home.view.php';
    }
    if ($page === 'changePassword') {
        include './view/changePassword/changePassword.view.php';
    }
} else {
    include './view/home/home.view.php';
}