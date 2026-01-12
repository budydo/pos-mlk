<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionItemController extends Controller
{
    public function index()
    {
        return view('transaction_items.index');
    }

    public function show($id)
    {
        return view('transaction_items.show', compact('id'));
    }
}
