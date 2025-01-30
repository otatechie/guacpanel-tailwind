<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return Inertia::render('Admin/IndexUserPage', [
            'users' => $users,
        ]);
    }
}
