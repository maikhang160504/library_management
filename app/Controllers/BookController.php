<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

class BookController extends Controller
{
    private $bookModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->bookModel = new Book();
    }

    public function index()
    {
        
        $limit = 5;
    $page = $this->getPage();
    $offset = $this->calculateOffset($page, $limit);

    $books = $this->bookModel->getBooksPaginated($limit, $offset);
    $totalBooks = $this->bookModel->countBooks();
    $totalPages = $this->calculateTotalPages($totalBooks, $limit);

    $this->view('books/index', [
        'books' => $books,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
    }

    public function show($id)
    {
        $book = $this->bookModel->getBookById($id);
        $this->view('books/show', ['book' => $book]);
    }

    public function add()
    {
        $this->view('books/add');
    }

    public function store() {
        $data = [
            'ten_sach' => trim($_POST['ten_sach'] ?? ''),
            'ten_tac_gia' => trim($_POST['ten_tac_gia'] ?? ''),
            'ten_the_loai' => trim($_POST['ten_the_loai'] ?? ''),
            'nam_xuat_ban' => trim($_POST['nam_xuat_ban'] ?? ''),
            'nha_xuat_ban' => trim($_POST['nha_xuat_ban'] ?? ''),
            'so_luong' => trim($_POST['so_luong'] ?? '')
        ];

        $errors = [];

        // Validate
        if (empty($data['ten_sach'])) $errors['ten_sach'] = 'Tên sách không được để trống.';
        if (empty($data['ten_tac_gia'])) $errors['ten_tac_gia'] = 'Tên tác giả không được để trống.';
        if (empty($data['ten_the_loai'])) $errors['ten_the_loai'] = 'Thể loại không được để trống.';
        if (empty($data['nam_xuat_ban'])) {
            $errors['nam_xuat_ban'] = 'Năm xuất bản không được để trống.';
        } elseif (!is_numeric($data['nam_xuat_ban']) || $data['nam_xuat_ban'] < 1800 || $data['nam_xuat_ban'] > 2100) {
            $errors['nam_xuat_ban'] = 'Năm xuất bản không hợp lệ.';
        }

        if (empty($data['nha_xuat_ban'])) $errors['nha_xuat_ban'] = 'Nhà xuất bản không được để trống.';
        if (empty($data['so_luong'])) {
            $errors['so_luong'] = 'Số lượng không được để trống.';
        } elseif (!is_numeric($data['so_luong']) || $data['so_luong'] <= 0) {
            $errors['so_luong'] = 'Số lượng phải là số lớn hơn 0.';
        }

        if (!empty($errors)) {
            $this->redirectBackWithError($errors, $data);
        }

        // Thêm sách
        $result = $this->bookModel->addBook($data);

        if ($result) {
            $_SESSION['success'] = 'Thêm sách thành công!';
            header('Location: /books');
        } else {
            $this->redirectBackWithError(['general' => 'Có lỗi xảy ra khi thêm sách.'], $data);
        }
        exit;
    }

    private function redirectBackWithError($errors, $oldData) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $oldData;
        header('Location: /books/add');
        exit;
    }  

    private function getPage()
{
    return isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
}

private function calculateOffset($page, $limit)
{
    return ($page - 1) * $limit;
}

private function calculateTotalPages($total, $limit)
{
    return ceil($total / $limit);
}

public function updateQuantity() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ma_sach = $_POST['ma_sach'] ?? null;
        // Lấy số lượng mới từ ô nhập, có thể là chuỗi rỗng nếu không nhập gì
        $new_quantity = $_POST['so_luong'] ?? '';
        
        // Lấy thông tin sách hiện tại
        $book = $this->bookModel->getBookById($ma_sach);
        if (!$book) {
            $_SESSION['error'] = "Không tìm thấy sách!";
            header("Location: /books");
            exit;
        }
        
        // Nếu ô số lượng mới trống hoặc bằng số lượng cũ thì không cập nhật
        if ($new_quantity === '' || $book['so_luong'] == $new_quantity) {
            $_SESSION['error'] = "Không có thay đổi số lượng cho sách: \"{$book['ten_sach']}\"";
            header("Location: /books");
            exit;
        }
        
        // Validate số lượng mới: kiểm tra có phải số và >= 0
        if (!is_numeric($new_quantity) || $new_quantity < 0) {
            $_SESSION['error'] = "Số lượng mới không hợp lệ!";
            header("Location: /books");
            exit;
        }
        
        // Thực hiện cập nhật số lượng
        $result = $this->bookModel->updateQuantity($ma_sach, $new_quantity);
        
        if ($result) {
            $_SESSION['success'] = "Cập nhật số lượng cho sách: \"{$book['ten_sach']}\" thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật số lượng!";
        }
        header("Location: /books");
        exit;
    }
}

}