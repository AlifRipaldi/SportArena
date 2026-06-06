<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lapangan;

class HomeController extends Controller
{
    public function index()
    {
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
