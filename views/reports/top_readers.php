<?php
$title = "Độc giả mượn nhiều sách nhất";
ob_start();
?>

<div class="container">
    <h2>Độc giả mượn nhiều sách nhất từ <?php echo $startDate; ?> đến <?php echo $endDate; ?></h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã độc giả</th>
                <th>Tên độc giả</th>
                <th>Số lượt mượn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($readers as $reader): ?>
            <tr>
                <td><?php echo $reader['ma_doc_gia']; ?></td>
                <td><?php echo $reader['ten_doc_gia']; ?></td>
                <td><?php echo $reader['so_luot_muon']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>