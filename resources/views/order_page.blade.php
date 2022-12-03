@extends('layouts.app')

@section('content')
    <div class="vh-100">

        <nav class="navbar bg-light">
            <div class="container-fluid">
                <form class="d-flex" role="search" method="GET" action="/task1">
                    <input class="form-control me-2" type="text" placeholder="distributor" name="d_name" id="d_name">
                    <input type="date" name="from" class="form-control me-2">
                    <input type="date" name="to" class="form-control me-2">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </nav>

        @inject('computations', 'App\Services\Computations')

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Invoice</th>
                    <th scope="col">Purchaser</th>
                    <th scope="col">Distributor</th>
                    <th scope="col">Referred Distributors</th>
                    <th scope="col">Order Date</th>
                    <th scope="col">Order Total</th>
                    <th scope="col">Percentage</th>
                    <th scope="col">Commission</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($allOrders as $i)
                    <tr>
                        <?php
                        $referred_distributors = $computations->referredDistributors($i->referred_by);
                        $percentage = $computations->percentage($referred_distributors);
                        $order_total = $computations->orderTotal($i->id);
                        ?>
                        <th scope="row">{{ $i->invoice_number }}</th>
                        <td>{{ $i->first_name }} {{ $i->last_name }}</td>
                        <td>{{ $computations->distributorName($i->referred_by) }}</td>
                        <td>{{ $referred_distributors }}</td>
                        <td>{{ $i->order_date }}</td>
                        <td>{{ $order_total }}</td>
                        <td>{{ $percentage }}%</td>
                        <td>{{ $computations->commission($percentage, $order_total) }}</td>
                        <td>
                            <!-- Button trigger modal -->
                            <button type="button" class="orderDetails btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" data-id="{{ $i->id }}">
                                View Items {{ $i->id }}
                            </button>


                            <!-- Modal -->

                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">SKU</th>
                                                        <th scope="col">Product Name</th>
                                                        <th scope="col">Price</th>
                                                        <th scope="col">Quantity</th>
                                                        <th scope="col">Total</th>

                                                    </tr>
                                                </thead>
                                                <tbody class="products">

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>


                    </tr>
                @endforeach


            </tbody>
        </table>
        {{ $allOrders->links() }}
    </div>
    <script>
        var path = "{{ url('typeahead_autocomplete/action') }}";

        $('#d_name').typeahead({

            source: function(query, process) {

                return $.get(path, {
                    query: query
                }, function(data) {
                    console.log(data);
                    return process(data);

                });

            }

        });
    </script>

    <script>
        $(document).ready(function() {


            $('.orderDetails').on('click', function() {
                var order_id = $(this).data('id');
                $.get(`/task1/${order_id}`, function(data, textStatus, jqXHR) {

                    console.log(data);
                    $('.products').html('');

                    for (let index = 0; index < data.length; index++) {
                        const element = data[index];
                        const bigString = ` <tr>
                                            <th scope="row">${element.sku}</th>
                                               <td>${element.name}</td>
                                               <td>${element.price}</td>
                                             <td>${element.qantity}</td>
                                             <td>${element.qantity * element.price}</td>
                                            </tr>`
                        $('.products').append(bigString);

                    }

                });
            });


        });
    </script>
@endsection
