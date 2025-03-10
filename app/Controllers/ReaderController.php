<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reader;

class ReaderController extends Controller
{
    private $readerModel;

    public function __construct()
    {
        if(session_status()===PHP_SESSION_NONE){
            session_start();
        }
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
        if ($this->readerModel->isReaderBorrowing($id)) {
            $_SESSION['error'] = "Không thể xóa độc giả vì họ đang mượn sách!";
            header("Location: /readers");
            exit;
        }
        $this->readerModel->deleteReader($id);

        $_SESSION['success'] = "Độc giả đã được xóa thành công.";
        header("Location: /readers");
    }


    public function detail($id)
    {
        $readerDetail = $this->readerModel->detailReader($id);

        if (!$readerDetail) {
            echo "Không tìm thấy thông tin độc giả.";
            return;
        }

        $this->view('readers/detail', [
            'reader' => $readerDetail['reader'],
            'borrowHistory' => $readerDetail['borrowHistory'],
        ]);
    }

    public function search()
    {
        $keyword = $_GET['keyword'] ?? '';

        $readers = $this->readerModel->searchReaders($keyword);


        $this->view('readers/index', ['readers' => $readers]);
    }
}
