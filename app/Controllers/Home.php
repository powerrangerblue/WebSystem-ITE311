<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Load homepage template
        return view('template', ['content' => 'index']);
    }

    public function about(): string
    {
        // Load about page template
        return view('template', ['content' => 'about']);
    }

    public function contact(): string
    {
        // Load contact page template
        return view('template', ['content' => 'contact']);
    }
}
