<?php
$title = "Báo cáo mượn - trả sách";
ob_start();
?>

<div class="container">
    <h2>Báo cáo mượn - trả sách tháng <?php echo $thang; ?> năm <?php echo $nam; ?></h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã phiếu mượn</th>
                <th>Mã độc giả</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Tiền phạt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report as $row): ?>
            <tr>
                <td><?php echo $row['ma_phieu_muon']; ?></td>
                <td><?php echo $row['ma_doc_gia']; ?></td>
                <td><?php echo $row['ngay_muon']; ?></td>
                <td><?php echo $row['ngay_tra']; ?></td>
                <td><?php echo $row['trang_thai']; ?></td>
                <td><?php echo number_format($row['tien_phat'], 2); ?> VNĐ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>