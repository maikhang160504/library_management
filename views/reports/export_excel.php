<?php
$title = "Xuất Báo Cáo Excel";
ob_start();

$reports = [
    [
        "id"    => "borrow_stats",
        "title" => "📊 Thống kê sách mượn trong tháng",
        "url"   => "/reports/borrow-stats?export=excel&month=$selectedMonth&year=$selectedYear",
        "desc"  => "Số lượng sách được mượn trong tháng $selectedMonth/$selectedYear."
    ],
    [
        "id"    => "yearly_reader_stats",
        "title" => "📚 Thống kê độc giả mượn sách trong năm",
        "url"   => "/reports/yearly-reader-stats?export=excel&year=$selectedYear",
        "desc"  => "Danh sách độc giả mượn sách nhiều nhất trong năm $selectedYear."
    ],
    [
        "id"    => "top_readers_books",
        "title" => "🏆 Độc giả mượn nhiều sách nhất & Sách được mượn nhiều nhất",
        "url"   => "/reports/top-readers-most-borrowed-book?export=excel",
        "desc"  => "Ai mượn nhiều nhất? Cuốn sách nào được mượn nhiều nhất?"
    ],
    [
        "id"    => "upcoming_returns",
        "title" => "⏳ Thống kê độc giả sắp đến hạn trả sách",
        "url"   => "/reports/upcoming-returns?export=excel&days=$days",
        "desc"  => "Danh sách độc giả có sách sắp đến hạn trả trong $days ngày tới."
    ],
    [
        "id"    => "borrow_return_report",
        "title" => "📜 Báo cáo mượn - trả sách",
        "url"   => "/reports/borrow-return-report?export=excel&month=$selectedMonth&year=$selectedYear",
        "desc"  => "Chi tiết lịch sử mượn và trả sách trong tháng $selectedMonth/$selectedYear."
    ],
    [
        "id"    => "least_borrowed_books",
        "title" => "📖 Thống kê sách ít được mượn",
        "url"   => "/reports/least-borrowed-books?export=excel",
        "desc"  => "Danh sách các sách ít được mượn nhất trong hệ thống."
    ],
    [
        "id"    => "penalties_stats",
        "title" => "💰 Thống kê phí phạt",
        "url"   => "/reports/penalties_stats?export=excel",
        "desc"  => "Thống kê tổng phí phạt của độc giả."
    ],
    [
        "id"    => "black_list",
        "title" => "🚫 Danh sách Đen",
        "url"   => "/reports/black-list?export=blacklist",
        "desc"  => "Danh sách độc giả có nhiều vi phạm khi mượn sách."
    ],
    [
        "id"    => "book_import_stats",
        "title" => "📦 Danh sách nhập sách theo ngày",
        "url"   => "/reports/exportExcelStatistic?month=$month&year=$year&category=$categoryId",
        "desc"  => "Danh sách sách được nhập vào hệ thống theo ngày."
    ]
];
?>

<div class="container mt-4">
    <h2 class="text-center">📊 Xuất Báo Cáo Excel</h2>

    <div class="row row-cols-1 row-cols-md-3 g-4 mt-4">
        <?php foreach ($reports as $report) : ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= $report['title'] ?></h5>
                        <p class="card-text flex-grow-1"><?= $report['desc'] ?></p>
                        <button class="btn btn-success mt-auto export-btn" data-url="<?= $report['url'] ?>">
                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".export-btn").forEach(button => {
        button.addEventListener("click", function() {
            let reportUrl = this.getAttribute("data-url");
            window.open(reportUrl, "_blank");
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
