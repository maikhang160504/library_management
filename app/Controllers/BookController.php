<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

class BookController extends Controller
{
    private $bookModel;

    public function __construct()
    {
        $this->bookModel = new Book();
    }

    public function index()
    {
        $books = $this->bookModel->getAllBooks();
        $this->view('books/index', ['books' => $books]);
    }

    public function show($id)
    {
        $book = $this->bookModel->getBookById($id);
        $this->view('books/show', ['book' => $book]);
    }
}