<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BorrowBook;
class ReportController extends Controller
{
    private $borrowModel;

    public function __construct()
    {
        $this->borrowModel = new BorrowBook();
    }

    // Hiển thị trang chính của báo cáo
    public function index()
    {
        $this->view('reports/index');
    }

    // Hiển thị thống kê sách mượn trong tháng
    public function monthlyBorrowStats()
    {
        $stats = $this->borrowModel->getMonthlyBorrowStats();
        $this->view('reports/monthly_borrow_stats', ['stats' => $stats]);
    }

    // Hiển thị thống kê độc giả mượn sách trong năm
    public function yearlyReaderStats()
    {
        $stats = $this->borrowModel->getYearlyReaderStats();
        $this->view('reports/yearly_reader_stats', ['stats' => $stats]);
    }

    // Hiển thị thống kê sách được mượn nhiều nhất
    public function mostBorrowedBooks()
    {
        $startDate = $_GET['startDate'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['endDate'] ?? date('Y-m-t'); // Mặc định là cuối tháng

        $books = $this->borrowModel->getMostBorrowedBooks($startDate, $endDate);
        $this->view('reports/most_borrowed_books', ['books' => $books, 'startDate' => $startDate, 'endDate' => $endDate]);
    }

    // Hiển thị thống kê độc giả mượn nhiều sách nhất
    public function topReaders()
    {
        $startDate = $_GET['startDate'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['endDate'] ?? date('Y-m-t'); // Mặc định là cuối tháng

        $readers = $this->borrowModel->getTopReaders($startDate, $endDate);
        $this->view('reports/top_readers', ['readers' => $readers, 'startDate' => $startDate, 'endDate' => $endDate]);
    }

    // Hiển thị báo cáo mượn - trả sách theo tháng/năm
    public function borrowReturnReport()
    {
        $thang = $_GET['thang'] ?? date('m'); // Mặc định là tháng hiện tại
        $nam = $_GET['nam'] ?? date('Y'); // Mặc định là năm hiện tại

        $report = $this->borrowModel->getBorrowReturnReport($thang, $nam);
        $this->view('reports/borrow_return_report', ['report' => $report, 'thang' => $thang, 'nam' => $nam]);
    }
}
