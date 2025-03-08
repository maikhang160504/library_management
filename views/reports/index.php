<?php
$title = "Báo cáo thống kê";
ob_start();
?>

<div class="container">
    <h2>Báo cáo thống kê</h2>
    <div class="list-group">
        <!-- Liên kết đến thống kê sách mượn trong tháng -->
        <a href="/reports/monthly-borrow-stats" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Thống kê sách mượn trong tháng</h5>
            </div>
            <p class="mb-1">Xem số lượng sách được mượn trong tháng hiện tại.</p>
        </a>

        <!-- Liên kết đến thống kê độc giả mượn sách trong năm -->
        <a href="/reports/yearly-reader-stats" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Thống kê độc giả mượn sách trong năm</h5>
            </div>
            <p class="mb-1">Xem số lượng độc giả mượn sách trong năm hiện tại.</p>
        </a>

        <!-- Liên kết đến thống kê sách được mượn nhiều nhất -->
        <a href="/reports/most-borrowed-books" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Sách được mượn nhiều nhất</h5>
            </div>
            <p class="mb-1">Xem danh sách sách được mượn nhiều nhất trong khoảng thời gian.</p>
        </a>

        <!-- Liên kết đến thống kê độc giả mượn nhiều sách nhất -->
        <a href="/reports/top-readers" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Độc giả mượn nhiều sách nhất</h5>
            </div>
            <p class="mb-1">Xem danh sách độc giả mượn nhiều sách nhất trong khoảng thời gian.</p>
        </a>

        <!-- Liên kết đến báo cáo mượn - trả sách theo tháng/năm -->
        <a href="/reports/borrow-return-report" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Báo cáo mượn - trả sách</h5>
            </div>
            <p class="mb-1">Xem báo cáo chi tiết mượn - trả sách theo tháng/năm.</p>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>