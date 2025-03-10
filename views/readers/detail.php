<?php
$title = "Chi tiết Độc giả";
ob_start();
?>
<div class="container">
    <h2 class="text-center mb-4">Chi tiết Độc giả</h2>
    <div class="mt-3 mb-3">
        <a href="/readers" class="btn btn-secondary"> <- Quay lại</a>
    </div>
    <div class="card mb-4">
        <div class="card-header">Thông tin cá nhân</div>
        <div class="card-body">
            <p><strong>Mã độc giả:</strong> <?php echo $reader['ma_doc_gia']; ?></p>
            <p><strong>Tên độc giả:</strong> <?php echo $reader['ten_doc_gia']; ?></p>
            <p><strong>Ngày sinh:</strong> <?php echo $reader['ngay_sinh']; ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo $reader['so_dien_thoai']; ?></p>
            <p><strong>Email:</strong> <?php echo $reader['email']; ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lịch sử mượn sách</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả</th>
                        <th>Trạng thái</th>
                        <th>Tiền phạt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowHistory as $history): ?>
                    <tr>
                        <td><?php echo $history['ten_sach']; ?></td>
                        <td><?php echo $history['ngay_muon']; ?></td>
                        <td><?php echo $history['ngay_tra'] ?? 'Chưa trả'; ?></td>
                        <td><?php echo $history['ngay_tra'] ? 'Đã trả' : 'Chưa trả'; ?></td>
                        <td><?php echo $history['tien_phat'] > 0 ? number_format($history['tien_phat']) . ' VND' : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
