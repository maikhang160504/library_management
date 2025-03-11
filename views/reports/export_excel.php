<?php
$title = "Xuất Báo Cáo Excel";
ob_start();

// Các giá trị động (có thể được lấy từ form hoặc biến trước đó)
$selectedMonth = date('m'); // Lấy tháng hiện tại (mặc định)
$selectedYear = date('Y'); // Lấy năm hiện tại
$days = 7; // Mặc định 7 ngày tới

?>

<div class="container mt-4">
    <h2 class="text-center">📊 Xuất Báo Cáo Excel</h2>
    <form id="reportForm">
        <div class="mb-3">
            <label for="report_type" class="form-label">Chọn loại báo cáo</label>
            <select class="form-select" id="report_type" name="report_type" required>
                <option value="">-- Chọn loại báo cáo --</option>
                <option value="/reports/borrow-stats?export=excel&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>">
                    Thống kê sách mượn trong tháng
                </option>
                <option value="/reports/yearly-reader-stats?export=excel&year=<?php echo $selectedYear; ?>">
                    Thống kê độc giả mượn sách trong năm
                </option>
                <option value="/reports/top-readers-most-borrowed-book?export=excel">
                    Độc giả mượn nhiều sách nhất và Sách được mượn nhiều nhất
                </option>
                <option value="/reports/upcoming-returns?export=excel&days=<?php echo $days; ?>">
                    Thống kê độc giả sắp đến hạn trả sách
                </option>
                <option value="/reports/borrow-return-report?export=excel&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>">
                    Báo cáo mượn - trả sách
                </option>
                <option value="/reports/least-borrowed-books?export=excel">
                    Thống kê sách ít được mượn
                </option>
                <option value="/reports/penalties_stats?export=excel&filter=<?= $filter ?>">
                    Thống kê phí phạt
                </option>
                <option value="/reports/black-list?export=blacklist">
                    Danh sách Đen
                </option>
            </select>

        </div>
        <button type="button" class="btn btn-success" id="exportBtn" disabled>
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </button>
    </form>
</div>

<script>
document.getElementById("report_type").addEventListener("change", function() {
    let selectedUrl = this.value;
    let exportBtn = document.getElementById("exportBtn");

    if (selectedUrl) {
        exportBtn.disabled = false; // Bật nút khi đã chọn báo cáo
        exportBtn.setAttribute("onclick", `window.location.href='${selectedUrl}'`);
    } else {
        exportBtn.disabled = true; // Tắt nút nếu không chọn báo cáo
        exportBtn.removeAttribute("onclick");
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
