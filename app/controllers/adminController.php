<?php
namespace App\Controllers;

class AdminController
{
    public function index()
    {
        require __DIR__ . '/../views/admin/index.php';
    }
}