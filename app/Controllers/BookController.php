<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
    
        $totalBooks = $this->bookModel->countAllBooks();
        $totalPages = ceil($totalBooks / $limit);
    
        if (!empty($searchQuery) && !empty($selectedCategory)) {
            $books = $this->bookModel->searchBooksInCategoryPaging($searchQuery, $selectedCategory, $limit, $offset);
            $totalBooks = $this->bookModel->countSearchInCategory($searchQuery, $selectedCategory);
        } elseif (!empty($searchQuery)) {
            $books = $this->bookModel->searchBooksPaging($searchQuery, $limit, $offset);
            $totalBooks = $this->bookModel->countSearch($searchQuery);
        } elseif (!empty($selectedCategory)) {
            $books = $this->bookModel->getBooksByCategoryPaging($selectedCategory, $limit, $offset);
            $totalBooks = $this->bookModel->countBooksByCategory($selectedCategory);
        } else {
            $books = $this->bookModel->getBooksPaging($limit, $offset);
        }
    
        $totalPages = ceil($totalBooks / $limit);
    
        $categories = $this->bookModel->getCategories();
    
        $this->view('books/index', [
            'books' => $books,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
            'currentPage' => $page,
            'totalPages' => $totalPages
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
        'ten_sach'      => trim($_POST['ten_sach'] ?? ''),
        'ten_tac_gia'   => trim($_POST['ten_tac_gia'] ?? ''),
        'ten_the_loai'  => trim($_POST['ten_the_loai'] ?? ''),
        'nam_xuat_ban'  => trim($_POST['nam_xuat_ban'] ?? ''),
        'nha_xuat_ban'  => trim($_POST['nha_xuat_ban'] ?? ''),
        'so_luong'      => trim($_POST['so_luong'] ?? ''),
        'ngay_them'     => date('Y-m-d') 
    ];

    $errors = [];

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

public function edit()
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['error'] = "Không tìm thấy sách để chỉnh sửa!";
        header("Location: /books");
        exit;
    }

    $book = $this->bookModel->getBookById($id);

    if (!$book) {
        $_SESSION['error'] = "Sách không tồn tại!";
        header("Location: /books");
        exit;
    }

    include __DIR__ . '/../views/book/edit.php';
}

public function update()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = [
            'ma_sach'      => intval($_POST['ma_sach']),
            'ten_sach'     => trim($_POST['ten_sach']),
            'ten_tac_gia'  => trim($_POST['ten_tac_gia']),
            'ten_the_loai' => trim($_POST['ten_the_loai']),
            'nam_xuat_ban' => intval($_POST['nam_xuat_ban']),
            'nha_xuat_ban' => trim($_POST['nha_xuat_ban']),
            'so_luong'     => intval($_POST['so_luong'])
        ];

        if (empty($data['ten_sach']) || empty($data['ten_tac_gia']) || empty($data['ten_the_loai'])) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
            header("Location: /books/edit?id=" . $data['ma_sach']);
            exit;
        }


        $result = $this->bookModel->updateBook($data);

        if ($result) {
            $_SESSION['success'] = "Cập nhật sách thành công!";
        } else {
            $_SESSION['error'] = "Cập nhật sách thất bại!";
        }

        header("Location: /books");
        exit;
    }

    header("Location: /books");
    exit;
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

    public function export() {
        $bookModel = new Book();

        $query    = isset($_GET['query']) ? trim($_GET['query']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';

        if ($query !== '' && $category !== '') {
            $books = $bookModel->searchBooksInCategory($query, $category);
        } elseif ($query !== '') {
            $books = $bookModel->searchBooks($query);
        } elseif ($category !== '') {
            $books = $bookModel->getBooksByCategory($category);
        } else {
            $books = $bookModel->getAllBooks();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Mã sách');
        $sheet->setCellValue('B1', 'Tên sách');
        $sheet->setCellValue('C1', 'Tác giả');
        $sheet->setCellValue('D1', 'Thể loại');
        $sheet->setCellValue('E1', 'Số lượng');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($books as $book) {
            $sheet->setCellValue('A' . $row, $book['ma_sach']);
            $sheet->setCellValue('B' . $row, $book['ten_sach']);
            $sheet->setCellValue('C' . $row, $book['ten_tac_gia']);
            $sheet->setCellValue('D' . $row, $book['ten_the_loai']);
            $sheet->setCellValue('E' . $row, $book['so_luong']);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="books.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }



public function exportStatistics()
{
    $type = $_GET['type'] ?? 'day';

    if ($type === 'month') {
        $booksDetail = $this->bookModel->getStatisticsByMonth();
    } elseif ($type === 'year') {
        $booksDetail = $this->bookModel->getStatisticsByYear();
    } else {
        $booksDetail = $this->bookModel->getStatisticsByDay();
    }

    $total = 0;
    foreach ($booksDetail as $book) {
        $total += $book['so_luong'];
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();


    $sheet->setCellValue('A1', 'Thống kê sách theo ' . ucfirst($type));
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A3', 'Mã sách');
    $sheet->setCellValue('B3', 'Tên sách');
    $sheet->setCellValue('C3', 'Tác giả');
    $sheet->setCellValue('D3', 'Thể loại');
    $sheet->setCellValue('E3', 'Số lượng');
    $headerLabel = ($type === 'day') ? 'Ngày' : (($type === 'month') ? 'Tháng' : 'Năm');
    $sheet->setCellValue('F3', $headerLabel);
    $sheet->getStyle('A3:F3')->getFont()->setBold(true);

    $rowNum = 4;
    foreach ($booksDetail as $book) {
        $sheet->setCellValue('A' . $rowNum, $book['ma_sach']);
        $sheet->setCellValue('B' . $rowNum, $book['ten_sach']);
        $sheet->setCellValue('C' . $rowNum, $book['ten_tac_gia']);
        $sheet->setCellValue('D' . $rowNum, $book['ten_the_loai']);
        $sheet->setCellValue('E' . $rowNum, $book['so_luong']);
        $sheet->setCellValue('F' . $rowNum, $book['period']);
        $rowNum++;
    }

    $sheet->setCellValue('D' . $rowNum, 'Tổng cộng:');
    $sheet->setCellValue('E' . $rowNum, $total);
    $sheet->getStyle('D' . $rowNum . ':F' . $rowNum)->getFont()->setBold(true);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="thong_ke_sach_' . $type . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


}
