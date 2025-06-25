<?php

session_start();

$is_logged_in = isset($_SESSION['username']);

$donate_link = $is_logged_in ? 'donasi_form.html' : '../login.html';
?>