<?php

namespace App\Controllers;

class TestController
{
    function index()
    {
        $data = ['teste', 'teos', 'funfo'];

        return view('home', ['empreendimento' => $data, 'name' => 'Gama']);
    }
    function create($data)
    {
        print_r($data['files']);
    }
}