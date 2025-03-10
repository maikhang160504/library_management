<?php
$title = "Chi tiết Phiếu Mượn";
ob_start();
?>

<div class="container mt-4">
<div class="d-flex align-items-center justify-content-center position-relative my-4">
    <a href="/borrows" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
        <i class="bi bi-arrow-left-circle"></i> Quay lại
    </a>
    <h2 class="mb-4">📊 Báo cáo thống kê</h2>
    <h2 class="mb-4 text-center">📖 Chi tiết Phiếu Mượn</h2>
</div>

   

    <!-- Thông tin phiếu mượn -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Thông tin Phiếu Mượn</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Mã phiếu mượn:</strong> <?php echo $borrowDetail['ma_phieu_muon']; ?></li>
                <li class="list-group-item"><strong>Độc giả:</strong> <?php echo $borrowDetail['ten_doc_gia']; ?>  </li>
                <li class="list-group-item"><strong> Số diện thoại:</strong> <?php echo $borrowDetail['so_dien_thoai']; ?> </li>
                <li class="list-group-item"><strong> Ngày mượn:</strong> <?php echo $borrowDetail['ngay_muon']; ?></li>
                <li class="list-group-item"><strong> Ngày trả dự kiến:</strong> <?php echo $borrowDetail['ngay_tra']; ?></li>
                <li class="list-group-item"><strong>Trạng thái:</strong> <span class="badge bg-<?php echo ($borrowDetail['trang_thai'] == 'Đã trả') ? 'success' : 'warning'; ?>">
                    <?php echo $borrowDetail['trang_thai']; ?>
                </span></li>
            </ul>
        </div>
    </div>

    <!-- Danh sách sách được mượn -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Danh sách Sách Được Mượn</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã sách</th>
                        <th class="text-start">Tên sách</th>
                        <th class="text-start">Tác giả</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowDetail['books'] as $book): ?>
                    <tr>
                        <td><?php echo $book['ma_sach']; ?></td>
                        <td class="text-start"><?php echo $book['ten_sach']; ?></td>
                        <td class="text-start"><?php echo $book['ten_tac_gia']; ?></td>
                        <td><strong><?php echo $book['so_luong']; ?></strong></td>
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
