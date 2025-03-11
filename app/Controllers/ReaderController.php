<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reader;

class ReaderController extends Controller
{
    private $readerModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->readerModel = new Reader();
    }

    public function index()
    {

        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($currentPage - 1) * $perPage;
        $readers = $this->readerModel->getReadersWithPagination($start, $perPage);

        $totalReaders = $this->readerModel->getTotalReaders();

        $totalPages = ceil($totalReaders / $perPage);

        $this->view('readers/index', [
            'readers' => $readers,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }



    public function create()
    {
        $this->view('readers/create');
    }


public function store()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = [
            'ten_doc_gia' => trim($_POST['ten_doc_gia']),
            'ngay_sinh' => trim($_POST['ngay_sinh']),
            'so_dien_thoai' => trim($_POST['so_dien_thoai'])
        ];

        $errors = [];

        if (empty($data['ten_doc_gia'])) {
            $errors['ten_doc_gia'] = "Tên độc giả không được để trống.";
        }

        if (empty($data['ngay_sinh']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['ngay_sinh'])) {
            $errors['ngay_sinh'] = "Ngày sinh không hợp lệ.";
        }

        if (!preg_match('/^(0\d{9}|\+84\d{9})$/', $data['so_dien_thoai'])) {
            $errors['so_dien_thoai'] = "Số điện thoại không hợp lệ.";
        }

        if ($this->readerModel->checkPhoneExists($data['so_dien_thoai'])) {
            $errors['so_dien_thoai'] = "Số điện thoại đã tồn tại.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['oldData'] = $data;
            header("Location: /readers/create");
            exit;
        }

        $result = $this->readerModel->addReader($data);

        if ($result === true) {
            $_SESSION['success'] = "Độc giả đã được thêm thành công.";
            header("Location: /readers");
            exit;
        } else {
            $_SESSION['errors'] = ["Lỗi khi thêm độc giả. Vui lòng thử lại!"];
            header("Location: /readers/create");
            exit;
        }
    }
}




    public function edit($id)
    {
        $reader = $this->readerModel->getReaderById($id);
        $this->view('readers/edit', ['reader' => $reader]);
    }


public function update($id)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oldReader = $this->readerModel->getReaderById($id);

        if (!$oldReader) {
            $_SESSION['error'] = "Không tìm thấy độc giả.";
            header("Location: /readers");
            exit;
        }

        $data = [
            'ten_doc_gia' => trim($_POST['ten_doc_gia']),
            'ngay_sinh' => trim($_POST['ngay_sinh']),
            'so_dien_thoai' => trim($_POST['so_dien_thoai'])
        ];

        $errors = [];

        // if ($data['ten_doc_gia'] === $oldReader['ten_doc_gia'] &&
        //     $data['ngay_sinh'] === $oldReader['ngay_sinh'] &&
        //     $data['so_dien_thoai'] === $oldReader['so_dien_thoai']) {
        //     $errors['no_changes'] = "Bạn chưa thay đổi thông tin nào.";
        // }

        if (empty($data['ten_doc_gia'])) {
            $errors['ten_doc_gia'] = "Tên độc giả không được để trống.";
        }

        if (empty($data['ngay_sinh']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['ngay_sinh'])) {
            $errors['ngay_sinh'] = "Ngày sinh không hợp lệ.";
        }

        if (!preg_match('/^(0\d{9}|\+84\d{9})$/', $data['so_dien_thoai'])) {
            $errors['so_dien_thoai'] = "Số điện thoại không hợp lệ.";
        }

        if ($data['so_dien_thoai'] !== $oldReader['so_dien_thoai'] &&
            $this->readerModel->checkPhoneExists($data['so_dien_thoai'])) {
            $errors['so_dien_thoai'] = "Số điện thoại đã tồn tại.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['oldData'] = $data;
            header("Location: /readers/edit/$id");
            exit;
        }

        // Cập nhật dữ liệu
        $result = $this->readerModel->updateReader($id, $data);

        if ($result === true) {
            $_SESSION['success'] = "Độc giả đã được cập nhật thành công.";
            header("Location: /readers");
            exit;
        } else {
            $_SESSION['errors'] = ["Lỗi khi cập nhật độc giả. Vui lòng thử lại!"];
            header("Location: /readers/edit/$id");
            exit;
        }
    }
}


    
    public function delete($id)
    {
        $result = $this->readerModel->deleteReader($id);

        if ($result === true) {
            $_SESSION['success'] = "Độc giả đã được xóa thành công.";
        } else {
            if (strpos($result, 'SQLSTATE[45000]') !== false) {
                $result = 'Không thể xóa độc giả khi họ vẫn còn sách chưa trả';
            }
            $_SESSION['error'] = $result;
        }

        header("Location: /readers");
    }



    public function detail($id)
    {
        $readerDetail = $this->readerModel->detailReader($id);

        if (!$readerDetail) {
            echo "Không tìm thấy thông tin độc giả.";
            return;
        }

        $this->view('readers/detail', [
            'reader' => $readerDetail['reader'],
            'borrowHistory' => $readerDetail['borrowHistory'],
        ]);
    }


    public function search()
    {
        $keyword = $_GET['keyword'] ?? '';
        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($currentPage - 1) * $perPage;

        $readers = $this->readerModel->getReadersWithPagination($start, $perPage, $keyword);
        $totalReaders = $this->readerModel->getTotalReaders($keyword);
        $totalPages = ceil($totalReaders / $perPage);

        $this->view('readers/index', [
            'readers' => $readers,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }
}
