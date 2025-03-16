<?php
$title = "Chi tiết Độc giả";
ob_start();
?>
<?php
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
?>

<div class="container">

    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center"><i class="bi bi-person-lines-fill"></i> Chi tiết Độc giả</h2>
    </div>

    <!-- Thông tin cá nhân độc giả -->
    <div class="card mb-4">
        <div class="card-header bg-light text-primary">
            <i class="bi bi-person-vcard"></i> Thông tin cá nhân
        </div>
        <div class="card-body">
            <p><strong><i class="bi bi-hash text-danger"></i> Mã độc giả:</strong>
                <span class="text-dark"><?php echo $reader['ma_doc_gia']; ?></span>
            </p>
            <p><strong><i class="bi bi-person text-primary"></i> Tên độc giả:</strong>
                <span class="text-success"><?php echo $reader['ten_doc_gia']; ?></span>
            </p>
            <p><strong><i class="bi bi-calendar-event text-warning"></i> Ngày sinh:</strong>
                <span class="text-dark"><?php echo $reader['ngay_sinh']; ?></span>
            </p>
            <p><strong><i class="bi bi-telephone text-info"></i> Số điện thoại:</strong>
                <span class="text-dark"><?php echo $reader['so_dien_thoai']; ?></span>
            </p>
        </div>


        <!-- Lịch sử mượn sách -->
        <div class="card">
            <div class="card-header bg-light text-primary">
                <i class="bi bi-book-half"></i> Lịch sử mượn sách
            </div>
        </div>

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
                                    'tien_phat' => isset($history['tien_phat']) && is_numeric($history['tien_phat'])
                                        ? number_format($history['tien_phat'], 0, ',', '.') . ' VND'
                                        : '0 VND',


                                    // 'tien_phat' => isset($history['tien_phat']) && $history['tien_phat'] > 0 ? $history['tien_phat'] . ' VND' : '0 VND',
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