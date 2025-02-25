<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    protected $data = [];
    public $model;
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function index(Request $request)
    {
        //$this->_params["item-per-page"]     = $this->getCookie('-item-per-page', 25);
        //$this->_params['model']             = $this->model->listItem($this->_params, ['task' => "admin-index"]);
        // echo '<pre>';
        // print_r($this->_viewAction);
        // echo '<pre>';
        // die();
         return view($this->_viewAction, ['params' => $this->_params]);
    }
}
