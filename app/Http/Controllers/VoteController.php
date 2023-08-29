<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteRequest;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __invoke(VoteRequest $request)
    {









       return $request->all();
    }
}
