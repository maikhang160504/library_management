<?php
$title = "Thống kê sách mượn theo tháng/năm";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary  position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary">
            <i class="bi bi-bar-chart-line"></i> Thống kê Sách Mượn - Tháng <?php echo $selectedMonth; ?> / <?php echo $selectedYear; ?>
        </h2>
        <button class="btn btn-success position-absolute  end-0" onclick="printReport()">
            <i class="bi bi-printer"></i> In Báo Cáo
        </button>
    </div>
<!-- Bộ lọc tháng & năm -->
<form action="/reports/borrow-return-report" method="GET" class="row g-2 align-items-center justify-content-center no-print">
    <div class="col-auto">
        <label for="month" class="form-label fw-bold">Tháng</label>
        <select id="month" name="month" class="form-select">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php if ($m == $selectedMonth) echo "selected"; ?>>Tháng <?php echo $m; ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="col-auto">
        <label for="year" class="form-label fw-bold">Năm</label>
        <input type="number" id="year" name="year" class="form-control" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo $selectedYear; ?>" required>
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-funnel"></i> Lọc</button>
    </div>
</form>


    <!-- Danh sách sách được mượn -->
    <div class="card shadow-sm border-0 print-table">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-book-half"></i> Chi tiết Sách Mượn</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" >
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã phiếu mượn</th>
                            <th>Mã độc giả</th>
                            <th>Mã sách</th>
                            <th>Mã tác giả</th>
                            <th>Ngày mượn</th>
                            <th>Ngày trả dự kiến</th>
                            <th>Ngày trả thực tế</th>
                            <th>Trạng thái</th>
                            <th>Tiền phạt</th>
                            <th>Số lần mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $book): ?>
                        <tr>
                            <td class="text-center"><?php echo $book['ma_phieu_muon']; ?></td>
                            <td class="text-center"><?php echo $book['ma_doc_gia']; ?></td>
                            <td class="text-center"><?php echo $book['ma_sach']; ?></td>
                            <td class="text-center"><?php echo $book['ma_tac_gia']; ?></td>
                            <td class="text-center"><?php echo $book['ngay_muon']; ?></td>
                            <td class="text-center"><?php echo $book['ngay_tra_du_kien']; ?></td>
                            <td class="text-center"><?php echo $book['ngay_tra_thuc_te']; ?></td>
                            <td class="text-center"><?php echo $book['trang_thai']; ?></td>
                            <td class="text-center text-danger fw-bold"><?php echo number_format($book['tien_phat'], 0, ',', '.'); ?> đ</td>
                            <td class="text-center text-primary">
                                <strong><?php echo $book['so_lan_muon']; ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .print-table { width: 100%; }
        .table { page-break-inside: auto; font-size: 12px; }
        tr { page-break-inside: avoid; }
        th, td { padding: 4px; }
        
        /* Tiêu đề khi in */
        body::before {
            content: "Thống kê Sách Mượn - Tháng <?php echo $selectedMonth; ?> / <?php echo $selectedYear; ?> - In lúc: " attr(data-print-time);
            display: block;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
    }
</style>

<script>
    function printReport() {
        document.body.setAttribute('data-print-time', new Date().toLocaleString());
        window.print();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
