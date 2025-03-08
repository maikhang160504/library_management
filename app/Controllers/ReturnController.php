<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ReturnBook;

class ReturnController extends Controller
{
    private $returnModel;

    public function __construct()
    {
        $this->returnModel = new ReturnBook();
    }

    // Hiển thị danh sách phiếu trả
    public function index()
    {
        $returns = $this->returnModel->getAllReturns();
        $this->view('returns/index', ['returns' => $returns]);
    }

    // Xác nhận trả sách
    public function return()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ma_ctpm'])) {
            $ma_ctpm = $_GET['ma_ctpm'];
            $ngay_tra_sach = date('Y-m-d'); // Ngày trả là ngày hiện tại

            if ($this->returnModel->returnBook($ma_ctpm, $ngay_tra_sach)) {
                header('Location: /borrows');
                exit();
            } else {
                echo "Có lỗi xảy ra khi xác nhận trả sách!";
            }
        } else {
            echo "Yêu cầu không hợp lệ!";
        }
    }
}
