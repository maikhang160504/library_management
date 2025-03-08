<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Borrow;

class BorrowController extends Controller {
    public function index() {
        $borrow = new Borrow();
        $borrows = $borrow->getAllBorrows();
        $this->view('borrows/index', ['borrows' => $borrows]);
    }
}
