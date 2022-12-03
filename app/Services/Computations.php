<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class Computations
{
    public function distributorName($id)
    {
        $name = DB::table('users')
        ->select('first_name', 'last_name')
        ->where('id','=',$id)
        ->get();
        if (count($name) > 0) {
            $fullName = $name[0]->first_name." ".$name[0]->last_name;
            return $fullName;
        }
        else {
            return '';
        }
    }

    public function referredDistributors($id)
    {
        $query = DB::select('SELECT COUNT(id) as referred_distributors FROM `users` WHERE referred_by = ?', [$id]);

        $number = $query[0]->referred_distributors;
        return $number;
    }

    public function orderTotal($order_id)
    {
        $query = DB::select('SELECT SUM(order_items.qantity*products.price) as order_total FROM `order_items` INNER JOIN `products` ON order_items.product_id = products.id WHERE order_items.order_id = ?', [$order_id]);
        $number = $query[0]->order_total;
        return $number;
    }
    public function percentage($number)
    {

        if ($number < 5) {
            return 5;
        }
        elseif ($number > 4 && $number < 11) {
            return 10;
        }
        elseif ($number > 10 && $number < 21) {
            return 15;
        }
        elseif ($number > 20 && $number < 31) {
            return 20;
        }
        elseif ($number > 30) {
            return 30;
        }
    }

    public function commission($percentage, $order_total)
    {
        return ($percentage/100) * $order_total;
    }

    public function viewItems($order_id)
    {
        $query = DB::select('SELECT p.sku, p.name, p.price, o.qantity FROM `order_items` as o INNER JOIN `products` as p ON o.product_id = p.id WHERE o.order_id = ?', [$order_id]);
        return $query;
    }
}
