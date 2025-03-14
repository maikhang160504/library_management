<?php

namespace App\Controllers;

use App\Models\Penalty;
use App\Core\Controller;
use App\Models\BorrowBook;
use App\Models\Book;

class ReportController extends Controller
{
    private $bookModel;
    private $borrowModel;
    private $penaltyModel;
    public function __construct()
    {
        $this->borrowModel = new BorrowBook();
        $this->penaltyModel = new Penalty();
        $this->bookModel = new Book();
    }

    // Hiển thị trang chính của báo cáo
    public function index()
    {
        $this->view('reports/index');
    }

    // Hiển thị thống kê sách mượn trong tháng
    public function BorrowStats()
    {

        $filter = $_GET['filter'] ?? 'this_month';
        // Lấy dữ liệu thống kê theo thời gian
        $details = $this->borrowModel->getBorrowStats($filter);
        $total_borrow = $this->borrowModel->getTotalBorrows($filter);
        $this->view('reports/monthly_borrow_stats', [
            'total_borrow' => $total_borrow,
            'details' => $details,
            'filter' => $filter
        ]);
    }

    // Hiển thị thống kê độc giả mượn sách trong năm
    public function yearlyReaderStats()
    {
        $stats = $this->borrowModel->getYearlyReaderStats();
        $details = $this->borrowModel->getYearlyReaderStatsDetail();
        $this->view('reports/yearly_reader_stats', ['stats' => $stats, 'details' => $details]);
    }

    public function topReaders_mostBorrowedBook()
    {
        $startDate = $_GET['startDate'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['endDate'] ?? date('Y-m-t');
       
        $readers = $this->borrowModel->getTopReaders($startDate, $endDate);
        $books = $this->borrowModel->getMostBorrowedBooks($startDate, $endDate);
        $this->view('reports/top_readers_most_borrows_books', ['readers' => $readers, 'books' => $books, 'startDate' => $startDate, 'endDate' => $endDate]);
    }

    // Hiển thị báo cáo mượn - trả sách theo tháng/năm
    public function borrowReturnReport()
    {

        $month = $_GET['month'] ?? date('m'); // Mặc định là tháng hiện tại
        $year = $_GET['year'] ?? date('Y'); // Mặc định là năm hiện tại

        $reports = $this->borrowModel->getBorrowReturnReport($month, $year);
        $this->view('reports/borrow_return_report', [
            'reports' => $reports,
            'selectedMonth' => $month,
            'selectedYear' => $year
        ]);
    }

    public function penaltiesStats()
{
    $filter = $_GET['filter'] ?? 'this_month';
    $currentPage = $_GET['page'] ?? 1; // Trang hiện tại
    $itemsPerPage = 10; // Số bản ghi mỗi trang

    $penalties = $this->penaltyModel->getPenaltiesByDate($filter, $currentPage, $itemsPerPage) ?? [];
    
    $total_penalty = !empty($penalties) ? array_sum(array_column($penalties, 'tien_phat')) : 0;

    $totalPenalties = $this->penaltyModel->getTotalPenalties($filter);
    $totalPages = ceil($totalPenalties / $itemsPerPage);


    $penaltySummary = [];
foreach ($penalties as $penalty) {
    $maDocGia = $penalty['ma_doc_gia'];
    if (!isset($penaltySummary[$maDocGia])) {
        $penaltySummary[$maDocGia] = [
            'ten_doc_gia' => $penalty['ten_doc_gia'],
            'tien_phat' => 0
        ];
    }
    $penaltySummary[$maDocGia]['tien_phat'] += $penalty['tien_phat'];
}

$chartLabels = array_column($penaltySummary, 'ten_doc_gia');
$chartData = array_column($penaltySummary, 'tien_phat');

$this->view('reports/penalties_stats', [
    'penalties' => $penalties,
    'filter' => $filter,
    'total_penalty' => $total_penalty,
    'totalPages' => $totalPages, 
    'currentPage' => $currentPage,
    'chartLabels' => $chartLabels,
    'chartData' => $chartData
]);
}

    public function penaltyReport()
    {
        
        $this->view('reports/penalties_stats');
    }

    

    public function upcomingReturns()
    {
        $days = $_GET['days'] ?? 3;
        // var_dump($days);
        $upcomingReturns = $this->borrowModel->getUpcomingReturns($days);
        $this->view('reports/upcoming_returns', ['upcomingReturns' => $upcomingReturns, 'days' => $days]);
    }
    public function leastBorrowedBooks() {
        $books = $this->borrowModel->getLeastBorrowedBooks();
        $this->view('reports/least_borrowed_books', ['books' => $books]);
    }
    public function exportExcel()   {
     
        $filter = $_GET['filter'] ?? 'this_month';
        $days = $_GET['days'] ?? 3;
        $type = isset($_POST['type']) ? $_POST['type'] : 'day';
        $startDate = $_GET['startDate'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['endDate'] ?? date('Y-m-t');
        $month = $_GET['month'] ?? date('m'); // Mặc định là tháng hiện tại
        $year = $_GET['year'] ?? date('Y'); // Mặc định là năm hiện tại
        $this->view('reports/export_excel', ['filter' => $filter, 'days' => $days, 'startDate' => $startDate, 'endDate' => $endDate, 'month' => $month, 'year' => $year, 'type' => $type]);
    }
    public function blackList() {
        $limit = isset($_GET['limit']) && $_GET['limit'] !== 'all' ? intval($_GET['limit']) : null;
        $blacklist = $this->borrowModel->getBlackList($limit);
        $this->view('reports/black_list', ['blacklist' => $blacklist, 'limit' => $limit]);
    }
    public function statisticsView()
    {
        $this->view('reports/statisticsView');
    }
    public function statistics()
    {
        // Nhận dữ liệu từ form
        $month = isset($_POST['month']) ? intval($_POST['month']) : null;
        $year = isset($_POST['year']) ? intval($_POST['year']) : null;
        $categoryId = isset($_POST['category']) ? intval($_POST['category']) : null;
    
        // Lấy danh sách chi tiết sách đã lọc
        $booksDetail = $this->bookModel->getStatisticsByMonthYearAndCategory($month, $year, $categoryId);
    
        // Tổng số lượng sách
        $total = 0;
        foreach ($booksDetail as $book) {
            $total += $book['so_luong'];
        }
    
        // Lấy danh sách thể loại sách từ DB để show dropdown
        $categoriesList = $this->bookModel->getAllCategories();
    
        // Gom nhóm lại theo thể loại để chuẩn bị dữ liệu vẽ biểu đồ
        $categoryCounts = []; // key = ten_the_loai, value = tổng số lượng
        foreach ($booksDetail as $book) {
            $categoryName = $book['ten_the_loai'];
            if (!isset($categoryCounts[$categoryName])) {
                $categoryCounts[$categoryName] = 0;
            }
            $categoryCounts[$categoryName] += $book['so_luong'];
        }
    
        // Đổ dữ liệu qua view
        $this->view('reports/statistics', [
            'booksDetail'     => $booksDetail,
            'total'           => $total,
            'month'           => $month,
            'year'            => $year,
            'categoryId'      => $categoryId,
            'categoriesList'  => $categoriesList, // dropdown
            'chartLabels'     => array_keys($categoryCounts), // chart labels
            'chartData'       => array_values($categoryCounts), // chart data
        ]);
    }
    

    public function exportExcelStatistic()
    {
        $month = isset($_GET['month']) ? intval($_GET['month']) : null;
        $year = isset($_GET['year']) ? intval($_GET['year']) : null;
        $categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
    
        // Lấy dữ liệu để export
        $booksDetail = $this->bookModel->getStatisticsByMonthYearAndCategory($month, $year, $categoryId);
    
        // Tạo Spreadsheet mới
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Tiêu đề bảng
        $sheet->setCellValue('A1', 'Mã sách');
        $sheet->setCellValue('B1', 'Tên sách');
        $sheet->setCellValue('C1', 'Tác giả');
        $sheet->setCellValue('D1', 'Thể loại');
        $sheet->setCellValue('E1', 'Số lượng');
        $sheet->setCellValue('F1', 'Thời gian');
    
        // Đổ dữ liệu vào file Excel
        $row = 2;
        foreach ($booksDetail as $book) {
            $sheet->setCellValue('A' . $row, $book['ma_sach']);
            $sheet->setCellValue('B' . $row, $book['ten_sach']);
            $sheet->setCellValue('C' . $row, $book['ten_tac_gia']);
            $sheet->setCellValue('D' . $row, $book['ten_the_loai']);
            $sheet->setCellValue('E' . $row, $book['so_luong']);
            $sheet->setCellValue('F' . $row, $book['period']);
            $row++;
        }
    
        // ====== TẠO TÊN FILE LINH HOẠT ======
        $fileName = "ThongKe_Sach";
    
        // Nếu lọc tháng riêng
        if (!empty($month) && empty($year)) {
            $fileName .= "_Thang{$month}";
        }
    
        // Nếu lọc năm riêng
        if (!empty($year) && empty($month)) {
            $fileName .= "_Nam{$year}";
        }
    
        // Nếu lọc cả tháng và năm
        if (!empty($month) && !empty($year)) {
            $fileName .= "_Thang{$month}_Nam{$year}";
        }
    
        // Nếu lọc thể loại riêng
        if (!empty($categoryId)) {
            // Lấy tên thể loại theo id
            $category = $this->bookModel->getCategoryById($categoryId);
            $categoryName = $category ? $this->sanitizeFileName($category['ten_the_loai']) : "TheLoai{$categoryId}";
            $fileName .= "_TheLoai_{$categoryName}";
        }
    
        // Nếu KHÔNG có lọc gì, thì chỉ thêm ngày giờ
        $fileName .= ".xlsx";
    
        // ====== DỌN DẸP OUTPUT BUFFER ======
        if (ob_get_length()) {
            ob_end_clean();
        }

        // ====== TẢI FILE ======
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
    
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    

    private function sanitizeFileName($string)
    {
        // Bước 1: Chuyển thành không dấu (tự viết)
        $string = $this->removeVietnameseTones($string);
    
        // Bước 2: Chuyển khoảng trắng thành dấu _
        $string = str_replace(' ', '_', $string);
    
        // Bước 3: Xoá ký tự không mong muốn
        $string = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
    
        return $string;
    }
    
    private function removeVietnameseTones($str)
    {
        $unicode = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach($unicode as $nonAccent=>$accent){
            $str = preg_replace("/($accent)/i", $nonAccent, $str);
        }
        return $str;
    }
    
    
}
