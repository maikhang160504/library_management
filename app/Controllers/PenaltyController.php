<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Penalty;

class PenaltyController extends Controller
{
    private $penaltyModel;

    public function __construct()
    {
        $this->penaltyModel = new Penalty();
    }

    public function index()
    {
        $penalties = $this->penaltyModel->getAllPenalties();
        $this->view('penalties/index', ['penalties' => $penalties]);
    }

    public function create()
    {
        $this->view('penalties/create');
    }

    public function store()
    {
        $data = $_POST;
        $this->penaltyModel->addPenalty($data); 
        header('Location: /penalties');
    }

    public function edit($id)
    {
        $penalty = $this->penaltyModel->getPenaltyById($id); 
        $this->view('penalties/edit', ['penalty' => $penalty]);
    }

    public function update($id)
    {
        $data = $_POST;
        $this->penaltyModel->updatePenalty($id, $data);
        header('Location: /penalties');
    }

    public function delete($id)
    {
        $this->penaltyModel->deletePenalty($id); 
        header('Location: /penalties');
    }
}
