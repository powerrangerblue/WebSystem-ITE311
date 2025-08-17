<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Load your custom template view instead of welcome_message
        return view('template'); // loads app/Views/template.php
    }
}
