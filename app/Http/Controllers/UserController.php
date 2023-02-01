<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    private $user;
    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
    } 
    public function index()
    {
        return User::where('id', $this->user->id)->with('userLinks')->get();
    }
 
    public function show($id)
    {
        return User::where('id', $this->user->id)->with(["userLink" => function($q) use ($id){
            $q->where('links.id', '=', $id);
        }])->get();

    }
}
