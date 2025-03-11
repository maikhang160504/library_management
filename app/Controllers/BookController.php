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
   
    $searchQuery = ($_SERVER['REQUEST_METHOD'] === 'POST')
        ? ($_POST['query'] ?? '')
        : ($_GET['query'] ?? '');

    $selectedCategory = ($_SERVER['REQUEST_METHOD'] === 'POST')
        ? ($_POST['category'] ?? '')
        : ($_GET['category'] ?? '');

    // Xử lý kết hợp lọc + tìm kiếm
    if (!empty($searchQuery) && !empty($selectedCategory)) {
        $books = $this->bookModel->searchBooksInCategory($searchQuery, $selectedCategory);
    } elseif (!empty($searchQuery)) {
        $books = $this->bookModel->searchBooks($searchQuery);
    } elseif (!empty($selectedCategory)) {
        $books = $this->bookModel->getBooksByCategory($selectedCategory);
    } else {
        $books = $this->bookModel->getAllBooks();
    }

    $categories = $this->bookModel->getCategories();

    $this->view('books/index', [
        'books' => $books,
        'categories' => $categories,
        'selectedCategory' => $selectedCategory,   // ✅ Trả lại chính xác đã chọn
        'searchQuery' => $searchQuery               // ✅ Trả lại chính xác từ khóa
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

    public function store()
    {
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

        $result = $this->bookModel->addBook($data);

        if ($result) {
            $_SESSION['success'] = 'Thêm sách thành công!';
            header('Location: /books');
        } else {
            $this->redirectBackWithError(['general' => 'Có lỗi xảy ra khi thêm sách.'], $data);
        }
        exit;
    }

    private function redirectBackWithError($errors, $oldData)
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $oldData;
        header('Location: /books/add');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_sach'      => $_POST['ma_sach'],
                'ten_sach'     => $_POST['ten_sach'],
                'ten_tac_gia'  => $_POST['ten_tac_gia'],
                'ten_the_loai' => $_POST['ten_the_loai'],
                'nam_xuat_ban' => $_POST['nam_xuat_ban'],
                'nha_xuat_ban' => $_POST['nha_xuat_ban'],
                'so_luong'     => $_POST['so_luong']
            ];

            $result = $this->bookModel->updateBook($data);

            if ($result) {
                $_SESSION['success'] = "Cập nhật sách thành công!";
            } else {
                $_SESSION['error'] = "Cập nhật sách thất bại!";
            }

            header("Location: /books");
            exit;
        }
    }

    public function filter()
    {
        $category = $_GET['category'] ?? '';

        if (!empty($category)) {
            $books = $this->bookModel->getBooksByCategory($category);
        } else {
            $books = $this->bookModel->getAllBooks();
        }

        header('Content-Type: application/json');
        echo json_encode(['books' => $books]);
        exit;
    }

    public function search()
    {
        $query = $_GET['query'] ?? '';
        $books = $this->bookModel->searchBooks($query);

        header('Content-Type: application/json');
        echo json_encode(['books' => $books]);
        exit;
    }

    
}
