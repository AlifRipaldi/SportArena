<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lapangan;

class HomeController extends Controller
{
    public function index()
    {
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }

        $lapangan = array();
        $dataError = null;

        try {
            $lapangan = (new Lapangan())->popular(4);
        } catch (\Throwable $exception) {
            $dataError = 'Data lapangan belum bisa dimuat. Periksa koneksi database.';
        }

        return $this->view('home/index', array(
            'title' => 'Arena Sport | Booking System',
            'lapangan' => $lapangan,
            'dataError' => $dataError,
        ));
    }
}
