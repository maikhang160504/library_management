<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Penalty;
use App\Services\EmailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PenaltyController extends Controller
{
    private $penaltyModel;
    private $emailService;

    public function __construct()
    {
        $this->penaltyModel = new Penalty();
        $this->emailService = new EmailService();
    }

    public function index()
{
    $perPage = 10;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($currentPage - 1) * $perPage;

    $penalties = $this->penaltyModel->getPenaltiesWithPagination($start, $perPage);
    $totalPenalties = $this->penaltyModel->getTotalPenalties();
    $totalPages = ceil($totalPenalties / $perPage);

   
    $this->view('penalties/index', [
        'penalties' => $penalties,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage
    ]);
}

   

    public function search()
    {
        $keyword = $_GET['search'] ?? '';
        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($currentPage - 1) * $perPage;
    
        $penalties = $this->penaltyModel->getPenaltiesWithPagination($start, $perPage, $keyword);
        $totalPenalties = $this->penaltyModel->getTotalPenalties($keyword);
        $totalPages = ceil($totalPenalties / $perPage);
    
        $this->view('penalties/index', [ 
            'penalties' => $penalties,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }
    
}
