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
        $penalties = $this->penaltyModel->getAllPenalties();
        $this->view('penalties/index', ['penalties' => $penalties]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $penalties = $this->penaltyModel->getAllPenalties($keyword);
        $this->view('penalties/index', ['penalties' => $penalties]);
    }

    
}
