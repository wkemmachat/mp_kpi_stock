@extends('layouts.back.blank')


@section('title')
    Upload
@endsection

@section('content')

<div class="content-wrapper">

    {!! Toastr::render() !!}

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section class="content-header">
        <h1>
            Upload
            <small>Add/Edit/List Data</small>
        </h1>
        {{--  <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Examples</a></li>
          <li class="active">Blank page</li>
        </ol>  --}}
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Upload <font color="blue"><strong> Stock Real Time </strong></font> </h3>
                        </div>

                        <div class="box-body">
                            <div class="register-box-body">

                                <form action="{{route('product.stock_real_time_import')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file" class="form-control">
                                    <br>
                                    <button class="btn btn-success">Import Product Data</button>
                                </form>

                                <font color = 'red'>
                                    <br/> Please see the product structure in root folder (excel) before upload
                                </font>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="col-md-8 col-md-offset-2">
                <!-- Default box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upload <font color="blue"><strong> Products </strong></font> Data</h3>
                            </div>

                            <div class="box-body">
                                <div class="register-box-body">

                                    <form action="{{route('product.import')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="file" class="form-control">
                                        <br>
                                        <!--<button class="btn btn-success">Import Product Data</button>-->
                                    </form>

                                    <font color = 'red'>
                                        <br/> Please see the product structure in root folder (excel) before upload
                                    </font>
                                </div>
                            </div>
                        </div>
                </div>
        </div>


      <!-- /.box -->


    </section>
    <!-- /.content -->


</div>



@endsection




