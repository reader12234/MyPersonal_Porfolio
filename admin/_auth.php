<?php
// simple session check (student-style)
session_start();

function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /MyPersonal_Porfolio/admin/login.php');
        exit;
    }
}


