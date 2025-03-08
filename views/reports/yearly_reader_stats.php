<?php
$title = "Thống kê độc giả mượn sách trong năm";
ob_start();
?>

<div class="container">
    <h2>Thống kê độc giả mượn sách trong năm</h2>
    <p>Số lượng độc giả mượn sách: <?php echo $stats['SoDocGiaMuon']; ?></p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>