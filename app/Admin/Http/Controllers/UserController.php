<?php

namespace App\Admin\Http\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {

        $users = User::all();

        return view('users', ['users' => $users]);
    }
}
