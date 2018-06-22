<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    use ApiResponser;
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function allowedAdminAction()
    {
        if (Gate::denies('admin-action')) {
            throw new ValidationException("Esta Accion no esta permitida");
        }
    }
}

