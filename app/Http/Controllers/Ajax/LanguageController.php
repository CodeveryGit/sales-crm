<?php

namespace Codevery\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use Codevery\Http\Controllers\Controller;
use Session;

class LanguageController extends Controller
{
    public function setLocale(Request $request)
    {
        Session::put('locale', $request['locale']);
    }
}
