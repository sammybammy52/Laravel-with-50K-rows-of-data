<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderFunctions extends Controller
{
    public function allOrders($from, $to, $search)
    {
        if ($from != null && $to != null && $search != null) {

            $seye = DB::table('users')
          ->select('users.id')
          ->join('user_category','users.id','=','user_category.user_id')
          ->where('user_category.category_id','=',1)
          ->where('users.first_name','LIKE',"%{$search}%")
          ->get();
            $array = [];

            foreach ($seye as $i) {
                array_push($array, $i->id);
            }
            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereIn('referred_by', $array)
            ->whereBetween('orders.order_date', [$from, $to])
            ->simplePaginate(10);

            return $allOrders;
        }
        elseif ($from != null && $to != null) {
            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereBetween('orders.order_date', [$from, $to])
            ->simplePaginate(10);
            return $allOrders;
        }
        elseif ($search != null) {

            $seye = DB::table('users')
          ->select('users.id')
          ->join('user_category','users.id','=','user_category.user_id')
          ->where('user_category.category_id','=',1)
          ->where('users.first_name','LIKE',"%{$search}%")
          ->get();
            $array = [];

            foreach ($seye as $i) {
                array_push($array, $i->id);
            }

            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereIn('referred_by', $array)
            ->simplePaginate(10);


            return $allOrders;
        } else {
            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->simplePaginate(10);
            return $allOrders;
        }
    }
    public function orderDetails($order_id)
    {
        $query = DB::select('SELECT p.sku, p.name, p.price, o.qantity FROM `order_items` as o INNER JOIN `products` as p ON o.product_id = p.id WHERE o.order_id = ?', [$order_id]);
        return $query;
    }

    public function filter($query)
    {
        $filtered_result = DB::table('users')
        ->select('users.first_name')
        ->join('user_category','users.id','=','user_category.user_id')
        ->where('user_category.category_id','=',1)
        ->where('users.first_name','LIKE',"%{$query}%")
        ->get();

        $data = array();
        foreach ($filtered_result as $i) {
            $data[] = $i->first_name;
        }

        return $data;


    }

    public function rankDistributors()
    {
        DB::statement('CREATE TEMPORARY TABLE table1
        SELECT DISTINCT o.order_id, SUM(p.price*o.qantity) as total FROM `order_items` as o INNER JOIN products as p on o.product_id = p.id GROUP BY o.order_id');
        DB::statement('CREATE TEMPORARY TABLE table2
        SELECT o.id, u.referred_by FROM `orders` as o INNER JOIN users AS u ON o.purchaser_id = u.id');

        DB::statement('CREATE TEMPORARY TABLE table3
        SELECT DISTINCT t2.referred_by as dId, SUM(t1.total) as sales FROM `table1` as t1 INNER JOIN table2 as t2 ON t1.order_id = t2.id GROUP BY dId ORDER BY sales DESC LIMIT 100');

        $ranked = DB::select('SELECT u.first_name, u.last_name, t3.sales, DENSE_RANK() OVER (ORDER BY sales DESC) position from `table3` as t3 INNER JOIN users as u on t3.dId = u.id');

        return $ranked;
    }
}
