<?php
$title = "Quản lí phiếu mượn Sách";
ob_start();

// Thiết lập phân trang
$limit = 5; // Số phiếu mượn hiển thị trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Giả sử $totalRecords là tổng số phiếu mượn từ database
$totalRecords = count($borrows); // Hoặc truy vấn SQL COUNT(*)
$totalPages = ceil($totalRecords / $limit);

// Lọc danh sách phiếu mượn theo trang hiện tại
$borrowsPaginated = array_slice($borrows, $offset, $limit);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách phiếu mượn</h2>
        <div>
            <a href="/borrows" class="btn btn-primary me-2">Quản lý Mượn Sách</a>
            <a href="/returns" class="btn btn-warning">Quản lý Trả Sách</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã phiếu mượn</th>
                <th>Mã độc giả</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrowsPaginated as $borrow): ?>
            <tr>
                <td><?php echo $borrow['ma_phieu_muon']; ?></td>
                <td><?php echo $borrow['ma_doc_gia']; ?></td>
                <td><?php echo $borrow['ngay_muon']; ?></td>
                <td><?php echo $borrow['ngay_tra']; ?></td>
                <td><?php echo $borrow['trang_thai']; ?></td>
                <td>
                    <?php if ($borrow['trang_thai'] === 'Đang mượn'): ?>
                        <a href="/returns/return?ma_phieu_muon=<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-success btn-sm">Trả sách</a>
                    <?php else: ?>
                        <span class="text-muted">Đã trả</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/borrows/detail/<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination justify-content-end">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Trước</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sau</a>
            </li>
        </ul>
    </nav>

    <a href="/borrows/create" class="btn btn-primary">Tạo phiếu mượn</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
