<?php
$title = "Danh sách phiếu trả";
ob_start();

// Thiết lập phân trang
$limit = 10; // Số lượng phiếu trả trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Giả sử $totalRecords là tổng số phiếu trả từ database
$totalRecords = count($returns); // Hoặc truy vấn SQL COUNT(*) từ DB
$totalPages = ceil($totalRecords / $limit);

// Lọc danh sách phiếu trả theo trang hiện tại
$returnsPaginated = array_slice($returns, $offset, $limit);
?>

<div class="container">
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
                <th>Mã phiếu Mượn</th>
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
            <?php foreach ($returnsPaginated as $return): ?>
                <tr>
                    <td><?php echo $return['ma_phieu_muon']; ?></td>
                    <td><?php echo $return['ma_phieu_tra']; ?></td>
                    <td><?php echo $return['ma_ctpm']; ?></td>
                    <td><?php echo $return['ngay_muon']; ?></td>
                    <td><?php echo $return['ngay_tra_du_kien']; ?></td>
                    <td><?php echo $return['ngay_tra_thuc_te']; ?></td>
                    <td><?php echo number_format($return['tien_phat']); ?> VNĐ</td>
                    <td>
                        <a href="/returns/detail?ma_phieu_muon=<?php echo $return['ma_phieu_muon']; ?>" class="btn btn-info btn-sm">
                            Xem chi tiết
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
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
