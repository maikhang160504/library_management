<?php
$title = "Chi tiết Độc giả";
ob_start();
?>
<?php
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
?>

<div class="container">
    <h2 class="text-center mb-4">Chi tiết Độc giả</h2>
    <div class="mt-3 mb-3">
        <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>


    <!-- Thông tin cá nhân độc giả -->
    <div class="card mb-4">
        <div class="card-header bg-light  text-primary">Thông tin cá nhân</div>
        <div class="card-body">
            <p><strong>Mã độc giả:</strong> <?php echo $reader['ma_doc_gia']; ?></p>
            <p><strong>Tên độc giả:</strong> <?php echo $reader['ten_doc_gia']; ?></p>
            <p><strong>Ngày sinh:</strong> <?php echo $reader['ngay_sinh']; ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo $reader['so_dien_thoai']; ?></p>
        </div>
    </div>

    <!-- Lịch sử mượn sách -->
    <div class="card">
        <div class="card-header bg-light text-primary">Lịch sử mượn sách</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã phiếu mượn</th>
                        <th>Tên sách</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả dự kiến</th>
                        <th>Ngày trả thực tế</th>
                        <th>Trạng thái trả sách</th>
                        <th>Tiền phạt</th>
                        <th>Trạng thái thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowHistory)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có thông tin lịch sử mượn sách.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $groupedHistory = [];
                        foreach ($borrowHistory as $history) {
                            $maPhieuMuon = $history['ma_phieu_muon'];
                            if (!isset($groupedHistory[$maPhieuMuon])) {
                                $groupedHistory[$maPhieuMuon] = [
                                    'ngay_muon' => $history['ngay_muon'],
                                    'ngay_tra_du_kien' => $history['ngay_tra_du_kien'],
                                    'ngay_tra_thuc_te' => $history['ngay_tra_thuc_te'],
                                    'trang_thai_tra_sach' => $history['ngay_tra_thuc_te'] ? '<span class="text-success"><i class="fa fa-check-circle"></i> Đã trả</span>' : '<span class="text-danger"><i class="fa fa-times-circle"></i> Chưa trả</span>',
                                    'tien_phat' => isset($history['tien_phat']) && $history['tien_phat'] > 0 ? $history['tien_phat'] . ' VND' : '0 VND',
                                    'trang_thai_thanh_toan' => ($history['ngay_tra_thuc_te'] !== null) ?
                                        (($history['tien_phat'] > 0) ? '<span class="text-success">Đã thanh toán</span>' : '<span class="text-muted">Không cần thanh toán</span>')
                                        : '<span class="text-warning">Chưa thanh toán</span>',

                                    'ten_sach' => [] // Danh sách sách
                                ];
                            }
                            $groupedHistory[$maPhieuMuon]['ten_sach'][] = $history['ten_sach'];
                        }
                        ?>
                        <?php foreach ($groupedHistory as $maPhieuMuon => $history): ?>
                            <tr>
                                <td><?php echo $maPhieuMuon; ?></td>
                                <td><?php echo implode("<br>", $history['ten_sach']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($history['ngay_muon'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($history['ngay_tra_du_kien'])); ?></td>
                                <td><?php echo $history['ngay_tra_thuc_te'] ? date('d/m/Y', strtotime($history['ngay_tra_thuc_te'])) : 'Chưa trả'; ?></td>
                                <td><?php echo $history['trang_thai_tra_sach']; ?></td>
                                <td><?php echo $history['tien_phat']; ?></td>
                                <td><?php echo $history['trang_thai_thanh_toan']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>