@extends('layout.dashboard')

@section('content')
@php
        App::setLocale(auth()->user()->lang);
    @endphp
<div class="row">
    <div class="col-12">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h4>{{ __('lang.AvailableStock') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card bg-white m-b-30">
            <div class="card-body new-user">
                    <table class="table table-bordered table-striped table-hover text-center mb-0" id="datatable">
                        <thead class="th-color">
                            <tr>
                                <th class="border-top-0">{{ __('lang.Ser') }}</th>
                                <th class="border-top-0">{{ __('lang.Product') }}</th>
                                <th class="border-top-0">{{ __('lang.Size') }}</th>
                                <th class="border-top-0">{{ __('lang.AvailableStock') }}</th>
                                <th class="border-top-0">{{ __('lang.SalePrice') }}</th>
                                <th class="border-top-0">{{ __('lang.StockValue') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ser = 0;
                            $total = 0;
                            @endphp

                            @foreach ($data as $item)
                            @php
                                $ser += 1;
                                $total += $item['value'];
                            @endphp
                            <tr>
                                <td> {{ $ser }} </td>
                                <td>{{$item['product']}}</td>
                                <td>{{$item['size']}}</td>
                                <td>{{$item['balance']}}</td>
                                <td>{{$item['price']}}</td>
                                <td>{{$item['value']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;"> <strong>Total</strong> </td>
                                <td style="text-align: center;"> <strong>{{ $total }}</strong> </td>
                            </tr>
                        </tfoot>
                    </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
    .dataTables_paginate {
        display: block
    }

</style>
<script>

    $(document).ready(function (){

    });
    var elems = document.getElementsByClassName('confirmation');
    var confirmIt = function (e) {
        if (!confirm('Are you sure to delete account?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>

@endsection
