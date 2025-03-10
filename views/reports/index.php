<?php
$title = "Báo cáo thống kê";
ob_start();
?>

<div class="container mt-4">

<h2 class="mb-4">📊 Báo cáo thống kê</h2>

    <div class="row">
        <!-- Thống kê sách mượn trong tháng -->
        <div class="col-md-6 mb-4">
            <a href="/reports/borrow-stats" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-3x text-primary me-3"></i>
                        <div>
                            <h5 class="card-title">Thống kê sách mượn trong tháng</h5>
                            <p class="card-text text-muted">Xem số lượng sách được mượn trong tháng hiện tại.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Thống kê độc giả mượn sách trong năm -->
        <div class="col-md-6 mb-4">
            <a href="/reports/yearly-reader-stats" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-users fa-3x text-success me-3"></i>
                        <div>
                            <h5 class="card-title">Thống kê độc giả mượn sách trong năm</h5>
                            <p class="card-text text-muted">Xem số lượng độc giả mượn sách trong năm hiện tại.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sách được mượn nhiều nhất  và đọc giả mượn nhiều nhất -->
        <div class="col-md-6 mb-4">
            <a href="/reports/top-readers-most-borrowed-book" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-book fa-3x text-warning me-3"></i>
                        <div>
                            <h5 class="card-title">Đọc giả mượn nhiều sách nhất và Sách được mượn nhiều nhất</h5>
                            <p class="card-text text-muted">Xem dọc giả mượn nhiều sách nhất và sách được mượn nhiều nhất.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Báo cáo mượn - trả sách -->
        <div class="col-md-6 mb-4">
            <a href="/reports/borrow-return-report" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-chart-line fa-3x text-info me-3"></i>
                        <div>
                            <h5 class="card-title">Báo cáo mượn - trả sách</h5>
                            <p class="card-text text-muted">Xem báo cáo chi tiết mượn - trả sách theo tháng/năm.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Báo cáo phí phạt -->
        <div class="col-md-6 mb-4">
            <a href="/reports/penalties" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-coins fa-3x text-info me-3"></i>
                        <div>
                            <h5 class="card-title">Báo cáo phí phạt</h5>
                            <p class="card-text text-muted">Xem báo cáo phí phạt theo tháng/năm.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>