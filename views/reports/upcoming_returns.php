<?php
$title = "Thống kê độc giả sắp đến hạn trả sách";
ob_start();

// Xử lý bộ lọc nhập số ngày (mặc định là 3 ngày)
$days = isset($_GET['days']) ? (int) $_GET['days'] : 3;

// Gọi Stored Procedure lấy danh sách độc giả sắp đến hạn trả sách
?>

<div class="container mt-4">
    <!-- Tiêu đề và nút điều hướng -->
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary">
            <i class="bi bi-calendar-event"></i> Thống kê Độc Giả Sắp Đến Hạn Trả Sách
        </h2>
        <button class="btn btn-success position-absolute end-0" onclick="printReport()">
            <i class="bi bi-printer"></i> In Báo Cáo
        </button>
    </div>

    <!-- Bộ lọc nhập số ngày -->
    <form action="/reports/upcoming-returns" method="GET" class="no-print">
        <div class="row justify-content-center mb-3">
            <div class="col-auto">
                <label for="days" class="form-label fw-bold">Số ngày sắp đến hạn:</label>
            </div>
            <div class="col-auto">
                <input type="number" class="form-control" name="days" id="days" value="<?php echo $days; ?>" min="1">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </div>
    </form>

    <!-- Bảng danh sách độc giả sắp đến hạn trả sách -->
    <div class="card shadow-sm border-0 print-table">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-list-check"></i> Danh sách độc giả</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã Phiếu Mượn</th>
                            <th>Mã Độc Giả</th>
                            <th>Họ Tên</th>
                            <th>Mã Sách</th>
                            <th>Tên Sách</th>
                            <th>Ngày Mượn</th>
                            <th>Ngày Trả Dự Kiến</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($upcomingReturns)): ?>
                            <?php foreach ($upcomingReturns as $return): ?>
                                <tr>
                                    <td class="text-center"><?php echo $return['ma_phieu_muon']; ?></td>
                                    <td class="text-center"><?php echo $return['ma_doc_gia']; ?></td>
                                    <td><?php echo $return['ten_doc_gia']; ?></td>
                                    <td class="text-center"><?php echo $return['ma_sach']; ?></td>
                                    <td><?php echo $return['ten_sach']; ?></td>
                                    <td class="text-center"><?php echo $return['ngay_muon']; ?></td>
                                    <td class="text-center text-danger fw-bold"><?php echo $return['ngay_tra_du_kien']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có độc giả nào sắp đến hạn trả sách.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh cho chế độ in -->
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-table {
            width: 100%;
        }

        .table {
            page-break-inside: auto;
            font-size: 11px;
        }

        tr {
            page-break-inside: avoid;
        }

        th,
        td {
            padding: 4px;
        }

        @page {
            size: A4 landscape;
            /* Chế độ in ngang */
            margin: 20mm;
            /* Lề trang */
        }

        /* Căn chỉnh theo khổ A4 */

        /* Tiêu đề khi in */
        .card.print-table::before {
            content: "Thống kê Độc Giả Sắp Đến Hạn Trả Sách - In lúc: " attr(data-print-time);
            display: block;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
    }
</style>

<!-- Script in báo cáo -->
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