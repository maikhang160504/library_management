<?php
$title = "Chi tiết Phiếu Trả";
ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">📄 Chi tiết Phiếu Trả</h2>

    <!-- Thông tin phiếu trả -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Thông tin Phiếu Trả</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong> Mã phiếu trả:</strong> <?php echo $returnDetail['ma_phieu_tra']; ?></li>
                <li class="list-group-item"><strong> Mã phiếu mượn:</strong> <?php echo $returnDetail['ma_phieu_muon']; ?></li>
                <li class="list-group-item"><strong> Độc giả:</strong> <?php echo $returnDetail['ten_doc_gia']; ?> </li>
                <li class="list-group-item"><strong> Số diện thoại:</strong> <?php echo $returnDetail['so_dien_thoai']; ?> </li>
                <li class="list-group-item"><strong> Ngày mượn:</strong> <?php echo $returnDetail['ngay_muon']; ?></li>
                <li class="list-group-item"><strong> Ngày trả dự kiến:</strong> <?php echo $returnDetail['ngay_tra']; ?></li>
                <li class="list-group-item"><strong> Ngày trả thực tế:</strong> <?php echo $returnDetail['ngay_tra_sach']; ?></li>
                <li class="list-group-item"><strong> Tiền phạt:</strong> <span class="badge bg-danger fs-5"><?php echo number_format($returnDetail['tien_phat'], 2); ?> VNĐ</span></li>
            </ul>
        </div>
    </div>

    <!-- Danh sách sách được trả -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Danh sách Sách Được Trả</h5>
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th> Mã sách</th>
                        <th class="text-start"> Tên sách</th>
                        <th class="text-start"> Tác giả</th>
                        <th> Số lượng</th>
                    </tr>
                </thead>
                <tbody class="align-middle text-center">
                    <?php foreach ($returnDetail['books'] as $book): ?>
                    <tr>
                        <td><?php echo $book['ma_sach']; ?></td>
                        <td class="text-start fw-bold"><?php echo $book['ten_sach']; ?></td>
                        <td class="text-start"><?php echo $book['ten_tac_gia']; ?></td>
                        <td class="fs-5 text-success"><strong><?php echo $book['so_luong']; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Nút quay lại -->
    <div class="text-center mt-4">
        <a href="/returns" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left-circle"></i> Quay lại</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
