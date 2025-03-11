<?php

use App\Models\Penalty;

$title = "Danh sách phí phạt";
ob_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phí phạt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Quản lý phí phạt </h2>

        <div class="mb-3">
            <form method="GET" action="/penalty/search">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm theo mã độc giả, tên, số tiền phạt" />
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </form>
        </div>

            <div>
                <!-- Nút Quay lại -->
                <a href="/penalties" class="btn btn-secondary mb-3">Quay lại danh sách đầy đủ</a>
            </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Mã độc giả</th>
                        <th>Tên độc giả</th>
                        <th>Ngày hết hạn</th>
                        <th>Số tiền phạt</th>
                        <th>Trạng thái thanh toán</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($penalties)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có kết quả tìm kiếm.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($penalties as $penalty): ?>
                        <tr>
                            <td><?php echo $penalty['ma_doc_gia'] ?> </td>
                            <td><?php echo $penalty['ten_doc_gia'] ?></td>
                            <td><?php echo $penalty['ngay_het_han'] ?></td>
                            <td><?= number_format($penalty['tien_phat'], 0, ',', '.') ?> VND</td>
                            <td>
                                <!-- Kiểm tra trạng thái thanh toán từ phiếu mượn -->
                                <?php if ($penalty['trang_thai'] == 'Đã trả'): ?>
                                    <span class="badge bg-success">Đã thanh toán</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Chưa thanh toán</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/readers/<?php echo $penalty['ma_doc_gia']; ?>/detail" class="btn btn-info btn-sm">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif;?>
                </tbody>
            </table>
        </div>

        <!-- <div class="mb-3">
            <button class="btn btn-primary">Gửi email nhắc nhở hàng loạt</button>
        </div> -->

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>