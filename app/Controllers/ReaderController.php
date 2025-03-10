<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reader;

class ReaderController extends Controller
{
    private $readerModel;

    public function __construct()
    {
        $this->readerModel = new Reader();
    }

    public function index()
    {
        $readers = $this->readerModel->getAllReaders();
        $this->view('readers/index', ['readers' => $readers]);
    }

    public function create()
    {
        $this->view('readers/create');
    }

    public function store()
    {
        $data = $_POST;
        $this->readerModel->addReader($data);
        header('Location: /readers');
    }

    public function edit($id)
    {
        $reader = $this->readerModel->getReaderById($id);
        $this->view('readers/edit', ['reader' => $reader]);
    }

    public function update($id)
    {
        $data = $_POST;
        $this->readerModel->updateReader($id, $data);
        header('Location: /readers');
    }

    public function delete($id)
    {
        $this->readerModel->deleteReader($id);
        header('Location: /readers');
    }
}
