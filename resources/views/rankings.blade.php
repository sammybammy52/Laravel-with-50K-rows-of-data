@extends('layouts.app')

@section('content')
    <div class="vh-100">

        <table class="table">
            <thead>
                <tr>

                    <th scope="col">Top</th>
                    <th scope="col">Distributor Name</th>
                    <th scope="col">Total Sales</th>

                </tr>
            </thead>
            <tbody>

                @foreach ($ranked as $i)
                    <tr>

                        <th scope="row">{{ $i->position }}</th>
                        <td>{{ $i->first_name }} {{ $i->last_name }}</td>
                        <td>{{ $i->sales }}</td>

                    </tr>
                @endforeach


            </tbody>
        </table>

    </div>

@endsection
