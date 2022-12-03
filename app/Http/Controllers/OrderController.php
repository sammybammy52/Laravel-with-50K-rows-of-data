<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends OrderFunctions
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('d_name');

        $allOrders = $this->allOrders($from, $to, $search);

        return view('order_page', [
            'allOrders' => $allOrders,
        ]);
    }

    public function show($order_id)
    {
        $details = $this->orderDetails($order_id);
        return $details;
    }

    public function autocomplete(Request $request)
    {
        $data = $request->all();
        $query = $data['query'];
        $filtered_result = $this->filter($query);
        return response()->json($filtered_result);

    }

    public function rank()
    {
        $ranked = $this->rankDistributors();

        return view('rankings', [
            'ranked' => $ranked,
        ]);

    }
}
