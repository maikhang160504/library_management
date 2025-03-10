<?php

namespace App\Controllers;

use App\Models\Penalty;
use App\Core\Controller;
use App\Models\BorrowBook;

class ReportController extends Controller
{
    private $borrowModel;
    private $penaltyModel;
    public function __construct()
    {
        $this->borrowModel = new BorrowBook();
        $this->penaltyModel = new Penalty();
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


        $penalties = $this->penaltyModel->getPenaltiesByDate($filter);

        $total_penalty = 0;
        foreach ($penalties as $penalty) {
            $total_penalty += $penalty['tien_phat'];
        }

        $this->view('reports/penalties_stats', [
            'penalties' => $penalties,
            'filter' => $filter,
            'total_penalty' => $total_penalty
        ]);
    }
    public function penaltyReport(){
        $this->view('reports/penalties');
    }
    public function upcomingReturns(){
        $days = $_GET['days'] ?? 3;
        // var_dump($days);
        $upcomingReturns = $this->borrowModel->getUpcomingReturns($days);
        $this->view('reports/upcoming_returns', ['upcomingReturns' => $upcomingReturns, 'days' => $days]);
    }
}
