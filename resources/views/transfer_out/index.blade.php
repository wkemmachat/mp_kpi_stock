@extends('layouts.back.blank')


@section('title')
    Transfer Out Without Approved
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
            Transfer Out
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
        {{--  <div class="row">  --}}
        <form action="{{ route('transfer_out.store_out')}}" method="post">

            @csrf
            <input type="hidden" name="in_or_out" value="out" maxlength="10" class="form-control pull-right" >

            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                <!-- Default box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Transfer <font color="blue"><strong> Out </strong></font> Data</h3>
                            </div>

                            <div class="box-body">
                                <div class="register-box-body">


                                        <div class="form-group">
                                            <label>Date: <font color="red">*</font></label>

                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="date_input" disabled value="{{ $now->format('d-m-Y H:i:s') }}" class=" form-control pull-right" required />
                                            </div>
                                            <!-- /.input group -->
                                        </div>


                                        <div class="form-group">
                                            <label>Document Id : <font color="red">* Unique</font></label>

                                            <div class="input-group ">
                                                <div class="input-group-addon">
                                                <i class="fa fa-sticky-note"></i>
                                                </div>
                                                <input type="text" name="document_reference_id_show" disabled value="OUT-{{ $now->format('d-m-Y-H-i-s') }}" maxlength="100" class="form-control pull-right" >
                                                <input type="hidden" name="document_reference_id" value="OUT-{{ $now->format('d-m-Y-H-i-s') }}" maxlength="100" class="form-control pull-right" >

                                            </div>

                                            @if ($errors->has('document_reference_id'))
                                                <span class="text-red" role="alert">
                                                    <strong>{{ $errors->first('document_reference_id') }}</strong>
                                                </span>
                                            @endif
                                            <!-- /.input group -->
                                        </div>

                                        <div class="form-group">
                                            <label>ขายในนามบริษัท: <font color="red">*</font></label>

                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                <i class="fa fa-file"></i>
                                                </div>
                                                <select name="out_type" class="form-control pull-right " required >
                                                    <option value="MP">MP</option>
                                                    <option value="FUR">FUR</option>
                                                    <option value="OFF">OFF</option>
                                                </select>
                                            </div>
                                            <!-- /.input group -->
                                        </div>

                                        <div class="form-group">
                                            <label>หมายเลขเอกสาร :</label>

                                            <div class="input-group ">
                                                <div class="input-group-addon">
                                                <i class="fa fa-sticky-note"></i>
                                                </div>
                                                <input type="text" name="remark" value="" maxlength="200" class="form-control pull-right" >
                                            </div>

                                            @if ($errors->has('remark'))
                                                <span class="text-red" role="alert">
                                                    <strong>{{ $errors->first('remark') }}</strong>
                                                </span>
                                            @endif
                                            <!-- /.input group -->
                                        </div>


                                        <div class="form-group">
                                            <label>Tranfser Out Without Approved/โอนออกแบบตัดสินค้าเลย :</label>

                                            <div class="input-group ">
                                                {{--  <div class="input-group-addon">
                                                <i class="fa fa-sticky-note"></i>
                                                </div>  --}}
                                                <div class="checkbox">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type="checkbox" name="checkForApprove" value="y" checked/>
                                                    <label>Check for Approved <font color="red">ถ้า Tick จะเป็นการตัด Stock เลย</font></label>
                                                </div>
                                            </div>

                                            @if ($errors->has('remark'))
                                                <span class="text-red" role="alert">
                                                    <strong>{{ $errors->first('remark') }}</strong>
                                                </span>
                                            @endif
                                            <!-- /.input group -->
                                        </div>

                                </div>
                            </div>
                        </div>
                </div>
            </div>

            {{--  Add Row  --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Transfer Out Data</h3>
                        </div>

                        <div class="box-body table-responsive">
                            <table class="table table-responsive" id="myTbl">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%">No.</th>
                                        <th class="text-center" style="width: 60%">Product</th>
                                        <th class="text-center" style="width: 35%">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="firstTr">
                                        <td class="text-center">1</td>
                                        <td>
                                            <select name="product_running_id[]" class="form-control pull-right select2 input-medium" required >
                                                <option value=""> -- Please Select Product -- </option>
                                                @foreach ($products as $product)
                                                    @if($product->stock_real_time != null ){
                                                            <option value="{{ $product->id }}">{{ $product->productId }} : {{ $product->productName }} , คงเหลือ {{ $product->stock_real_time->amount }} PCS</option>
                                                        }
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            {{--  <input type="text" name="amount" value="" class="form-control pull-right" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" >  --}}
                                            <input type="text" name="amount[]" value="" class="form-control pull-right plus_only" >

                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="btn btn-success" type="button" id="addRow" value="Add Row">
                                            <input class="btn btn-danger pull-right" type="button" id="removeRow" value="Remove Row">
                                            {{-- <a href="" class="btn btn-success" id="addRow">Add Row</a> --}}
                                            {{-- <a href="" class="btn btn-danger pull-right" id="removeRow">Remove Row</a> --}}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <br/><br/>
                            <div class="col-md-8 col-md-offset-2">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat ">Submit</button>
                            </div>
                        </div>

                    </div>

                </div>


            </div>


        </form>
        {{--  </div>  --}}

        @if(!empty($transfer_out_without_confirmed))
        <!-- List Data -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Data <strong><font color='red'>NOT CONFIRMED</font></strong></h3>
                    </div>

                    <div class="box-body table-responsive">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Product Id</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Document Id</th>
                                    <th class="text-center">Created Date / วันที่</th>
                                    {{--  <th class="text-center">Name / ผู้ทำงาน</th>  --}}

                                    <th class="text-center">หมายเลขเอกสาร</th>
                                    <th class="text-center">Confirmed</th>
                                    <th class="text-center">Delete</th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php
                                    $uniqueDocRefId=array();
                                    for($i=0 ; $i<count($transfer_out_without_confirmed) ;$i++) {
                                        $isDubplicate = false;
                                        if($i!=0){
                                            if(!in_array($transfer_out_without_confirmed[$i]->document_reference_id,$uniqueDocRefId,TRUE)){
                                                $isDubplicate = false;
                                                array_push($uniqueDocRefId,$transfer_out_without_confirmed[$i]->document_reference_id);
                                                $uniqueDocRefId = array_unique($uniqueDocRefId);
                                            }else{
                                                $uniqueDocRefId = array_unique($uniqueDocRefId);
                                                $before = count($uniqueDocRefId);
                                                array_push($uniqueDocRefId,$transfer_out_without_confirmed[$i]->document_reference_id);
                                                $after  = count($uniqueDocRefId);
                                                if($before != $after){
                                                    $isDubplicate = true;
                                                }
                                            }

                                        }else{
                                            array_push($uniqueDocRefId,$transfer_out_without_confirmed[$i]->document_reference_id);
                                        }
                                        $uniqueDocRefId = array_unique($uniqueDocRefId);
                                ?>
                                    <tr>
                                        <td class="text-center">{{ $i+1 }}</td>
                                        <td>{{ $transfer_out_without_confirmed[$i]->product_running->productId }}</td>
                                        <td>{{ $transfer_out_without_confirmed[$i]->product_running->productName }}</td>
                                        <td class="text-center">{{ $transfer_out_without_confirmed[$i]->amount}}</td>
                                        <td class="text-center">{{ $transfer_out_without_confirmed[$i]->out_type}}</td>
                                        <td class="text-center"><font color="blue">{{ $transfer_out_without_confirmed[$i]->document_reference_id }}</font></td>
                                        <td class="text-center">{{ date('d-M-Y',strtotime($transfer_out_without_confirmed[$i]->input_date)) }}</td>
                                        {{--  <td class="text-center">{{ $transfer_out_without_confirmed[$i]->user_key_in->name }}</td>  --}}
                                        <td class="text-center">{{ $transfer_out_without_confirmed[$i]->remark }}</td>
                                        <td class="text-center">
                                            {{--  <a href="{{ route('transfer_out_approve.approve_out',$transfer_out_without_confirmed[$i]->id) }}" class="btn btn-block btn-warning">Confirmed<a>  --}}
                                            @if(!$isDubplicate)
                                            <form class="" method="post" action="{{ route('transfer_out_approve.approve_out') }}">
                                                @method('POST')
                                                @csrf
                                                <input type="hidden" name="document_reference_id" value="{{ $transfer_out_without_confirmed[$i]->document_reference_id }}"/>
                                                <button type="submit" class="btn btn-block btn-warning" onclick="return confirm('Are you sure?')">Confirmed </button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$isDubplicate)
                                            <form class="form-delete" method="post" action="{{ route('transfer_out.destroy_out',$transfer_out_without_confirmed[$i]->id) }}">
                                                @method('DELETE')
                                                @csrf
                                                <input type="hidden" name="document_reference_id" value="{{ $transfer_out_without_confirmed[$i]->document_reference_id }}"/>
                                                <button type="submit" class="btn btn-block btn-danger" onclick="return confirm('Are you sure?')">x</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>


                        </table>
                        {{--  {{ $products->links() }}  --}}
                    </div>

                </div>
            </div>
        </div>
        @endif


        @if(!empty($transfer_out_confirmed))
        <!-- List Data -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Data <strong><font color='green'>CONFIRMED</font></strong></h3>
                    </div>

                    <div class="box-body table-responsive">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Product Id</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Document Id</th>
                                    <th class="text-center">Created Date / วันที่</th>
                                    <th class="text-center">Name / ผู้ทำงาน</th>
                                    <th class="text-center">หมายเลขเอกสาร</th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php
                                $uniqueDocRefId=array();
                                for($i=0 ; $i<count($transfer_out_confirmed) ;$i++) {
                                    $isDubplicate = false;
                                    if($i!=0){
                                        $uniqueDocRefId = array_unique($uniqueDocRefId);
                                        $before = count($uniqueDocRefId);
                                        array_push($uniqueDocRefId,$transfer_out_confirmed[$i]->document_reference_id);

                                        $after  = count($uniqueDocRefId);

                                        if($before != $after){
                                            $isDubplicate = true;

                                        }
                                    }else{
                                        array_push($uniqueDocRefId,$transfer_out_confirmed[$i]->document_reference_id);
                                    }
                                    $uniqueDocRefId = array_unique($uniqueDocRefId);
                                ?>
                                    <tr>
                                        <td class="text-center">{{ $i+1 }}</td>
                                        <td>{{ $transfer_out_confirmed[$i]->product_running->productId }}</td>
                                        <td>{{ $transfer_out_confirmed[$i]->product_running->productName }}</td>
                                        <td class="text-center">{{ $transfer_out_confirmed[$i]->amount}}</td>
                                        <td class="text-center">{{ $transfer_out_confirmed[$i]->out_type}}</td>
                                        <td class="text-center"><font color="blue">{{ $transfer_out_confirmed[$i]->document_reference_id }}</font></td>
                                        <td class="text-center">{{ date('d-M-Y',strtotime($transfer_out_confirmed[$i]->input_date)) }}</td>
                                        <td class="text-center">{{ $transfer_out_confirmed[$i]->user_key_in->name }}</td>
                                        <td class="text-center">{{ $transfer_out_confirmed[$i]->remark }}</td>

                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>


                        </table>
                    </div>

                </div>
            </div>
        </div>
        @endif

      <!-- /.box -->


    </section>
    <!-- /.content -->


</div>



@endsection

@section('js')
<script type="text/javascript">
    $(function(){
        var counter = 1;

        $("#addRow").click(function(){
            counter ++;

            var parentData   = '<tr>';
                parentData  += '<td class="text-center">'+counter+'</td>';

                parentData  += '<td>';
                parentData  += '<select name="product_running_id[]" class="form-control pull-right select2 input-medium" required >';
                parentData  += '<option value=""> -- Please Select Product -- </option>';
                parentData  += '@foreach ($products as $product)';

                parentData  += '@if($product->stock_real_time != null && $product->stock_real_time->amount > 0){';
                parentData  += '    <option value="{{ $product->id }}">{{ $product->productId }} : {{ $product->productName }} , คงเหลือ {{ $product->stock_real_time->amount }} PCS</option>';
                parentData  += '}';
                parentData  += '@endif';

                parentData  += '@endforeach';
                parentData  += '</select>';
                parentData  += '</td>';


                parentData 	+= '<td>';
                parentData 	+= '<input type="text" name="amount[]" value="" class="form-control pull-right plus_only" >' ;
                parentData 	+= '</td>';

                parentData += '</tr>';

                {{-- parentData  += '<td></td>';
                parentData  += '<td></td>';
                parentData  += '</tr>'; --}}

            $("#myTbl tbody").append(parentData);

            $("select.select2").select2();

        });

        $("#removeRow").click(function(){

            if($("#myTbl tbody tr").length>1){
                counter --;
                $("#myTbl tbody tr:last").remove();
            }else{
                alert("ต้องมีรายการข้อมูลอย่างน้อย 1 รายการ");
            }
        });

        $(document).ready(function() {
            $(".select2").select2();
            $(".plus_only").inputFilter(function(value) {
                return /^\d*$/.test(value);
            });

        });

    });
</script>
@endsection




