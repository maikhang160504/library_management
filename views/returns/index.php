<?php
$title = "Danh sách phiếu trả";
ob_start();
?>
<div class="container">
    <!-- Header với các nút quản lý -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách phiếu trả</h2>
        
        <div>
            <a href="/borrows" class="btn btn-primary me-2">Quản lý Mượn Sách</a>
            <a href="/returns" class="btn btn-warning">Quản lý Trả Sách</a>
        </div>
    </div>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Mã phiếu trả</th>
                <th>Mã chi tiết phiếu mượn</th>
                <th>Ngày mượn</th>
                <th>Ngày trả dự kiến</th>
                <th>Ngày trả thực tế</th>
                <th>Tiền phạt</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($returns as $return): ?>
                <tr>
                    <td><?php echo $return['ma_phieu_tra']; ?></td>
                    <td><?php echo $return['ma_ctpm']; ?></td>
                    <td><?php echo $return['ngay_muon']; ?></td>
                    <td><?php echo $return['ngay_tra_du_kien']; ?></td>
                    <td><?php echo $return['ngay_tra_thuc_te']; ?></td>
                    <td><?php echo number_format($return['tien_phat'], 2); ?> VNĐ</td>
                    <td>
                        <a href="/returns/detail/<?php echo $return['ma_phieu_tra']; ?>" class="btn btn-info btn-sm">
                            Xem chi tiết
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
