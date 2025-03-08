<?php
$title = "Sách được mượn nhiều nhất";
ob_start();
?>

<div class="container">
    <h2>Sách được mượn nhiều nhất từ <?php echo $startDate; ?> đến <?php echo $endDate; ?></h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th>Số lượt mượn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['ma_sach']; ?></td>
                <td><?php echo $book['ten_sach']; ?></td>
                <td><?php echo $book['so_luot_muon']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>