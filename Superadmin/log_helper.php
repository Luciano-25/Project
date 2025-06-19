<?php
function log_admin_action($conn, $admin_id, $action) {
    $stmt = $conn->prepare("INSERT INTO admin_log (admin_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $admin_id, $action);
    $stmt->execute();
}
?>
