<?php
$title = "Danh sách phiếu trả";
ob_start();
?>
<div class="">
    <!-- Header với các nút quản lý -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách phiếu trả</h2>
        
        <div>
            <a href="/borrows" class="btn btn-primary me-2">Quản lý Mượn Sách</a>
            <a href="/returns" class="btn btn-warning">Quản lý Trả Sách</a>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã phiếu trả</th>
                <th>Mã chi tiết phiếu mượn</th>
                <th>Ngày trả sách</th>
                <th>Tiền phạt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($returns as $return): ?>
                <tr>
                    <td><?php echo $return['ma_phieu_tra']; ?></td>
                    <td><?php echo $return['ma_ctpm']; ?></td>
                    <td><?php echo $return['ngay_tra_sach']; ?></td>
                    <td><?php echo number_format($return['tien_phat'], 2); ?> VNĐ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>