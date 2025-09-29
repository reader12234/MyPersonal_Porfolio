<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $res = mysqli_query($conn, "SELECT file_path FROM projects WHERE id=" . $id);
    if ($row = mysqli_fetch_assoc($res)) {
        mysqli_query($conn, "DELETE FROM projects WHERE id=" . $id);
        if (!empty($row['file_path'])) {
            $p = __DIR__ . '/..' . $row['file_path'];
            if (is_file($p)) { @unlink($p); }
        }
    }
}

header('Location: /MyPersonal_Porfolio/admin/index.php');
exit;


