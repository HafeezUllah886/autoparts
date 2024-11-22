@extends('layout.dashboard')
@php
        App::setLocale(auth()->user()->lang);
    @endphp
@section('content')
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<div class="row">
    <div class="col-12">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h4>{{ __('lang.Products') }}</h4>
                <div class="d-flex justify-content-end">
                    @php
                        $currentMonth = date('Y-m');
                        $firstDateOfMonth = date('Y-m-01', strtotime($currentMonth));
                        $lastDateOfMonth = date('Y-m-t', strtotime($currentMonth));
                    @endphp
                <a href="{{ route('priceAdjustment') }}" class="btn btn-dark mr-2" >Price Adjustment</a>
                <a href="{{ url('/profit/') }}/{{ $firstDateOfMonth }}/{{ $lastDateOfMonth }}" class="btn btn-info mr-2" >{{ __('lang.Profit/Loss') }}</a>
              {{--   <a href="{{ url('/products/trashed') }}" class="btn btn-dark mr-2" >{{ __('lang.Trashed') }}</a> --}}
                <button class="btn btn-success mr-2" data-toggle="modal" data-target="#modal">{{ __('lang.CreateNew') }}</button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#importModal">Import</button>
                <a href="{{ route('priceAdjustment.export') }}" class="btn btn-info mr-2" >Export</a>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card bg-white m-b-30">
            <div class="card-body table-responsive new-user">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-center mb-0" id="datatable1">
                        <thead class="th-color">
                            <tr>
                                <th class="border-top-0">{{ __('lang.Ser') }}</th>
                                <th class="border-top-0">{{ __('lang.Product') }}</th>
                                <th class="border-top-0">Part No.</th>
                                <th class="border-top-0">Model</th>
                                <th class="border-top-0">Brand</th>
                                <th class="border-top-0">Made In</th>
                                <th class="border-top-0">{{ __('lang.Size') }}</th>
                                <th class="border-top-0">UOM</th>
                                <th class="border-top-0">Purchase Price</th>
                                <th class="border-top-0">{{ __('lang.SalePrice') }}</th>
{{--                                 <th class="border-top-0">{{ __('lang.Category') }}</th>
                                <th class="border-top-0">{{ __('lang.Company') }}</th> --}}
                                <th>{{ __('lang.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $ser = 0;
                            @endphp
                            @foreach ($products as $pro)
                            @php
                            $ser += 1;
                            @endphp
                            <tr>
                                <td> {{ $ser }} </td>
                                <td>{{ $pro->name }}</td>
                                <td>{{ $pro->partno }}</td>
                                <td>{{ $pro->model }}</td>
                                <td>{{ $pro->brand }}</td>
                                <td>{{ $pro->madein }}</td>
                                <td>{{ $pro->size }}</td>
                                <td>{{ $pro->uom }}</td>
                                <td>{{ $pro->pprice }}</td>
                                <td>{{ $pro->price }}</td>
                               {{--  <td>{{ $pro->category->cat }}</td>
                                <td>{{ $pro->company->name }}</td> --}}

                                <td>

                                    <button onclick='edit_pro({{ $pro->id }})' class="btn btn-primary">Edit</button>
                                  {{--   <a href="{{ url('/product/delete/') }}/{{ $pro->id }}" class="btn btn-danger">Delete</a> --}}
                                    </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Model Starts Here --}}
<div class="modal" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ url('/product/import') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Select Excel File | <a href="{{url('assets/product_sample.xlsx')}}">Download sample file </a></label>
                        <input type="file" required name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lang.Close') }}</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Model Starts Here --}}
<div class="modal" id="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('lang.AddNewProduct') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">{{ __('lang.Product') }}</label>
                                <input type="text" required name="name" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="partno">Part No.</label>
                            <input type="text" name="partno" id="partno" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" name="model" id="model" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="madein">Made In</label>
                            <input type="text" name="madein" id="madein" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="size">{{ __('lang.Size') }}</label>
                            <input type="text" name="size" id="size" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="uom">UOM</label>
                            <input type="text" name="uom" id="uom" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="pprice">Purchase Price</label>
                            <input type="number" required name="pprice" id="pprice" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="price">{{ __('lang.SalePrice') }}</label>
                            <input type="number" required name="price" id="price" class="form-control">
                        </div>
                        </div>

                    </div>

                    {{-- <div class="form-group">
                        <label for="cat">{{ __('lang.Category') }}</label>
                        <select name="cat" required class="select2" id="cat">
                            @foreach ($cats as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="coy">{{ __('lang.Company') }}</label>
                        <select name="coy" required class="select2" id="coy">
                            @foreach ($coys as $coy)
                                <option value="{{ $coy->id }}">{{ $coy->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('lang.Add') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lang.Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Model Starts Here --}}
<div class="modal" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('lang.EditProduct') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <form action="{{ url('/product/edit') }}" method="post">
                        @csrf
                        <div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="name">{{ __('lang.Product') }}</label>
            <input type="text" required id="edit_name" name="name" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="partno">Part No.</label>
            <input type="text" name="partno" id="edit_partno" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="model">Model</label>
            <input type="text" name="model" id="edit_model" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="brand">Brand</label>
            <input type="text" name="brand" id="edit_brand" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="madein">Made In</label>
            <input type="text" name="madein" id="edit_madein" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="size">{{ __('lang.Size') }}</label>
            <input type="text" name="size" id="edit_size" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
        <label for="uom">UOM</label>
        <input type="text" name="uom" id="edit_uom" class="form-control">
    </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="pprice">Purchase Price</label>
            <input type="number" required name="pprice" id="edit_pprice" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="price">{{ __('lang.SalePrice') }}</label>
            <input type="number" required id="edit_price" name="price" class="form-control">
        </div>
    </div>
</div>
                    {{-- <div class="form-group">
                        <label for="cat">{{ __('lang.Category') }}</label>
                        <select name="cat" class="form-control" id="edit_cat">
                            @foreach ($cats as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="coy">{{ __('lang.Company') }}</label>
                        <select name="coy" class="form-control" id="edit_coy">
                            @foreach ($coys as $coy)
                                <option value="{{ $coy->id }}">{{ $coy->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                </div>
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">{{ __('lang.Save') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lang.Close') }}</button>
                </div>
            </form>
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
    $('#datatable1').DataTable({
        "bSort": true
        , "bLengthChange": true
        , "bPaginate": true
        , "bFilter": true
        , "bInfo": true,

    });

    function edit_pro(id) {
        $.ajax({
        method: 'get',
        url: "{{url('/products/get_pro')}}",
        data: {id:id},
        success: function(abc){
            console.log(abc.pro['size']);
            $('#edit_name').val(abc.pro['name']);
            $('#edit_partno').val(abc.pro['partno']);
            $('#edit_model').val(abc.pro['model']);
            $('#edit_brand').val(abc.pro['brand']);
            $('#edit_madein').val(abc.pro['madein']);

            $('#edit_price').val(abc.pro['price']);
            $('#edit_pprice').val(abc.pro['pprice']);
            $('#edit_uom').val(abc.pro['uom']);
            $('#edit_size').attr('value', abc.pro['size']);

            $('#edit_id').val(abc.pro['id']);
            $('#edit').modal('show');
        }
    });

    }


</script>
@endsection
