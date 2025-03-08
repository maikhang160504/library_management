<?php
$title = "Thống kê sách mượn trong tháng";
ob_start();
?>

<div class="container">
    <h2>Thống kê sách mượn trong tháng</h2>
    <p>Số lượng sách mượn: <?php echo $stats['SoSachMuon']; ?></p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>