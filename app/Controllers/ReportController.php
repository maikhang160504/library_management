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
        $details = $this->borrowModel->getMonthlyBorrowStatsdetail();
        $this->view('reports/monthly_borrow_stats', ['stats' => $stats,'details'=> $details]);

    }

    // Hiển thị thống kê độc giả mượn sách trong năm
    public function yearlyReaderStats()
    {
        $stats = $this->borrowModel->getYearlyReaderStats();
        $details = $this->borrowModel->getYearlyReaderStatsDetail();
        $this->view('reports/yearly_reader_stats', ['stats' => $stats, 'details' => $details]);
    }

    public function topReaders_mostBorrowedBook(){
        $startDate = $_GET['startDate'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['endDate'] ?? date('Y-m-t'); 
        var_dump($startDate, $endDate);
        $readers = $this->borrowModel->getTopReaders($startDate, $endDate);
        $books = $this->borrowModel->getMostBorrowedBooks($startDate, $endDate);
        $this->view('reports/top_readers_most_borrows_books', ['readers' => $readers, 'books' => $books, 'startDate' => $startDate, 'endDate' => $endDate]);
        
    }

    // Hiển thị báo cáo mượn - trả sách theo tháng/năm
    public function borrowReturnReport()
    {
        $month = $_GET['month'] ?? date('m'); // Mặc định là tháng hiện tại
        $year = $_GET['year'] ?? date('Y'); // Mặc định là năm hiện tại
        $stats = $this->borrowModel->getMonthlyBorrowStats();
        $reports = $this->borrowModel->getBorrowReturnReport($month, $year);
        $this->view('reports/borrow_return_report', [
            'stats'=> $stats,
            'reports' => $reports,
            'selectedMonth' => $month,
            'selectedYear' => $year
        ]);
    }
    
}
