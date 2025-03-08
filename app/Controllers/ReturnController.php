<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ReturnBook;

class ReturnController extends Controller {
    public function index() {
        $returnBook = new ReturnBook();
        $returns = $returnBook->getAllReturns();
        $this->view('returns/index', ['returns' => $returns]);
    }
}
