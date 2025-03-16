<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BorrowBook;
use App\Models\Book;
use App\Models\Reader;

class BorrowController extends Controller
{
    private $borrowModel;
    private $readerModel;
    private $bookModel;

    public function __construct()
    {
        $this->borrowModel = new BorrowBook();
        $this->readerModel = new Reader();
        $this->bookModel = new Book();
    }

    public function index()
    {
        $filter_status = isset($_GET['status']) ? $_GET['status'] : '';

        if ($filter_status === 'Đang mượn') {
            $borrows = $this->borrowModel->getUnreturnedBorrows();
        } elseif ($filter_status === 'Đã trả') {
            $borrows = $this->borrowModel->getReturnedBorrows();
        } else {
            $borrows = $this->borrowModel->getAllBorrows();
        }

        $this->view('borrows/index', ['borrows' => $borrows]);
    }

    // Hiển thị form tạo phiếu mượn
    public function create()
    {
        $readers = $this->readerModel->getAllReaders();
        $books = $this->bookModel->getAllBooks();
        $this->view('borrows/create', ['readers' => $readers, 'books' => $books]);
    }


    // Xử lý tạo phiếu mượn
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ma_doc_gia = $_POST['ma_doc_gia'];
            $ngay_muon = $_POST['ngay_muon'];
            $ngay_tra = $_POST['ngay_tra'];
            $danh_sach_sach = $_POST['danh_sach_sach'];

            // Kiểm tra số lượng sách còn lại
            foreach ($danh_sach_sach as $sach) {
                $so_luong_con = $this->borrowModel->checkBookQuantity($sach['ma_sach']);
                if ($so_luong_con < $sach['so_luong']) {
                    echo "Sách có mã {$sach['ma_sach']} không đủ số lượng!";
                    return;
                }
            }
            $result = $this->borrowModel->createBorrow($ma_doc_gia, $ngay_muon, $ngay_tra, $danh_sach_sach);
            // Tạo phiếu mượn
            if ($result['success']) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Tạo phiếu mượn thanh công!'
                ];
                header('Location: /borrows/detail/' . $result['ma_phieu_muon']);
                exit();
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger', 
                    'message' => $result['message']
                ];
                header('Location: /borrows/create');
                exit();
            }
        }
    }

    public function show($ma_phieu_muon)
    {
        $borrowDetail = $this->borrowModel->getBorrowDetail($ma_phieu_muon);

        if (!$borrowDetail) {
            echo "Không tìm thấy phiếu mượn!";
            return;
        }

        $this->view('borrows/show', ['borrowDetail' => $borrowDetail]);
    }
    public function renew()
    {
        // Lấy dữ liệu từ URL
        $maPhieuMuon = $_GET['ma_phieu_muon'] ?? null;
        $soNgayGiaHan = 5; // Mặc định gia hạn 7 ngày

        if ($maPhieuMuon && $soNgayGiaHan > 0) {
            $success = $this->borrowModel->renewBorrow($maPhieuMuon, $soNgayGiaHan);
            if ($success) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Gia hạn thành công!'
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => 'Gia hạn thất bại!'
                ];
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Dữ liệu không hợp lệ!'
            ];
        }


        header("Location: /borrows");
        exit;
    }
}
