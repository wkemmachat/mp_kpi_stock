<?php

namespace App\Http\Controllers;

use App\TransferInOut;
use Illuminate\Http\Request;
use App\Product;
use Carbon\Carbon;
use Kamaln7\Toastr\Facades\Toastr;
use Auth;
use App\StockRealTime;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTransferInOut;

class TransferInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_in()
    {
        $now = now();
        // echo $now->toDateTimeString();
        $products = Product::orderBy('productId', 'asc')->get();
        // dd($products);

        $transfer_in_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','in')->orderby('id','asc')->get();
        // $transfer_in_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','in')->orderby('id','desc')->paginate(10);

        return view('transfer_in.index',compact('products','now','transfer_in_without_confirmed'));
        // return view('dashboard',compact('roles','date_collection','total_amount_this_month',
        // 'total_amount_last_month','total_defect_this_month','total_defect_last_month','startThisMonth','startLastMonth','date_collection_not_input_data'));
    }

    public function index_in_approve()
    {
        // $now = now();
        // $products = Product::orderBy('productId', 'asc')->get();

        $transfer_in_without_confirmed  = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','in')->orderby('id','asc')->get();
        $transfer_in_confirmed          = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','in')->orderby('id','desc')->paginate(10);

        return view('transfer_in_approve.index',compact('transfer_in_without_confirmed','transfer_in_confirmed'));
    }

    public function index_out()
    {
        $now = now();
        $products = Product::orderBy('productId', 'asc')->get();

        $transfer_out_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','out')->orderby('id','asc')->get();
        $transfer_out_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','out')->orderby('id','desc')->limit(20)->get();


        return view('transfer_out.index',compact('products','now','transfer_out_without_confirmed','transfer_out_confirmed'));
    }

    public function index_out_approve()
    {
        // $now = now();
        // $products = Product::orderBy('productId', 'asc')->get();

        $transfer_out_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','out')->orderby('id','asc')->get();
        $transfer_out_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','out')->orderby('id','desc')->paginate(10);

        return view('transfer_out_approve.index',compact('transfer_out_without_confirmed','transfer_out_confirmed'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_in(Request $request)
    {

        $request->validate([
            'document_reference_id' => 'required',
            'product_running_id.*' => 'required|integer',
            'amount.*' => 'required|integer',
            'in_or_out' => 'required',
        ]);

        if($request->remark == null){
            $request->remark = '';
        }


        // dd($request->all());

        // check Unique
        if (count($request->product_running_id) !== count(array_unique($request->product_running_id))){

            // echo 'not equal';
            $message = "Error, Same Product Id!!";
            Toastr::warning($message, $title = "Error Action", $options = []);


        }else{
            // echo 'equal';

            $products   = $request->product_running_id;
            $amounts    = $request->amount;
            for ($i = 0; $i < count($products); $i++) {
                // dd($products[$i]);
                if($amounts[$i]<=0){
                    continue;
                }
                $transferToBeSave = new TransferInOut();
                $transferToBeSave->user_key_in_id   = Auth::user()->id;
                $transferToBeSave->product_running_id  = $products[$i];
                $transferToBeSave->amount           = $amounts[$i];
                $transferToBeSave->remark           = $request->remark;
                $transferToBeSave->document_reference_id           = $request->document_reference_id;
                $transferToBeSave->remark           = $request->remark;
                $transferToBeSave->input_date       = now();
                $transferToBeSave->isConfirmed      = 0;
                $transferToBeSave->in_or_out        = $request->in_or_out;

                $transferToBeSave->save();
            } // end for

            $message = "Successfully add data";
            Toastr::success($message, $title = "Successfully Action", $options = []);

        } // end else

        $now = now();
        $products = Product::orderBy('productId', 'asc')->get();
        $transfer_in_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','in')->orderby('id','asc')->get();
        $transfer_in_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','in')->orderby('id','desc')->paginate(10);

        return view('transfer_in.index',compact('products','now','transfer_in_without_confirmed','transfer_in_confirmed'));
        // dd($request->product_running_id);


    }

    public function store_out(Request $request)
    {
        $isError = false;

        $request->validate([
            'document_reference_id' => 'required',
            'product_running_id.*' => 'required|integer',
            'amount.*' => 'required|integer',
            'in_or_out' => 'required',
            'out_type' => 'required',
            // 'checkForApprove' => 'require',
        ]);

        if($request->remark == null){
            $request->remark = '';
        }

        if($request->checkForApprove == null){
            $request->checkForApprove = '';
        }

        // dd($request->checkForApprove);


        // dd($request->all());

        // check enough amount
        $productCheckArray   = $request->product_running_id;
        $amountCheckArray    = $request->amount;

        // check stock enough first
        //Edit 21Aug19

        // edit 1 OCT 2020 check negative stock out
        /*
        for ($i = 0; $i < count($productCheckArray); $i++) {

           $stockRealTimeByProduct =  StockRealTime::where('product_running_id','=',$productCheckArray[$i])->first();
           if($amountCheckArray[$i]>$stockRealTimeByProduct->amount){
                $message = "Error, Not Enough Amount !!";
                Toastr::warning($message, $title = "Error Action", $options = []);
                $isError = true;
                break;
           }
        }
        */
        // end edit 1 OCT 2020 check negative stock out


        if(!$isError){
            // check Unique
            if (count($request->product_running_id) !== count(array_unique($request->product_running_id))){

                // echo 'not equal';
                $message = "Error, Same Product Id!!";
                Toastr::warning($message, $title = "Error Action", $options = []);
                $isError = true;

            }else{
                // echo 'equal';

                $products   = $request->product_running_id;
                $amounts    = $request->amount;
                for ($i = 0; $i < count($products); $i++) {
                    // dd($products[$i]);
                    if($amounts[$i]<=0){
                        continue;
                    }
                    $transferToBeSave = new TransferInOut();
                    $transferToBeSave->user_key_in_id   = Auth::user()->id;
                    $transferToBeSave->product_running_id  = $products[$i];
                    $transferToBeSave->amount           = $amounts[$i];
                    $transferToBeSave->remark           = $request->remark;
                    $transferToBeSave->document_reference_id           = $request->document_reference_id;
                    $transferToBeSave->remark           = $request->remark;
                    $transferToBeSave->input_date       = now();
                    $transferToBeSave->isConfirmed      = 0;
                    $transferToBeSave->in_or_out        = $request->in_or_out;
                    $transferToBeSave->out_type        = $request->out_type;

                    // $transferToBeSave->save();

                    // checkForApprove = y
                    if($request->checkForApprove=='y'){

                        $transferToBeSave->isConfirmed      = 1;
                        if($transferToBeSave->save()){
                            // save and cut stock
                            // dd("true");
                            $stockRealtimeObj = StockRealTime::where('product_running_id','=',$transferToBeSave->product_running->id)->first();
                            if($stockRealtimeObj==null){
                                // Error
                                // impossible
                                dd("error No Stock to deduct");
                            }else{
                                // update
                                $stockRealtimeObj->amount              -= $transferToBeSave->amount;
                                $stockRealtimeObj->transfer_in_out_id  = $transferToBeSave->id;
                                $stockRealtimeObj->save();
                            }
                        }

                    }else{
                        // normal save
                        // dd("false");
                        $transferToBeSave->save();
                    }



                } // end for

                $message = "Successfully add data";
                Toastr::success($message, $title = "Successfully Action", $options = []);

            } // end else

        } // end if(!$isError)

        $now = now();
        $products = Product::orderBy('productId', 'asc')->get();
        $transfer_out_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','out')->orderby('id','asc')->get();
        $transfer_out_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','out')->orderby('id','desc')->paginate(10);

        return view('transfer_out.index',compact('products','now','transfer_out_without_confirmed','transfer_out_confirmed'));
        // dd($request->product_running_id);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TransferInOut  $transferInOut
     * @return \Illuminate\Http\Response
     */
    public function show(TransferInOut $transferInOut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TransferInOut  $transferInOut
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferInOut $transferInOut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransferInOut  $transferInOut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransferInOut $transferInOut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransferInOut  $transferInOut
     * @return \Illuminate\Http\Response
     */
    public function destroy_in(Request $request,$id)
    {

        // dd($transferInOut->id);

        $transferInOutArray = TransferInOut::where('document_reference_id','=',$request->document_reference_id)->get();
        for ($i = 0; $i < count($transferInOutArray); $i++) {
            $transferInOutArray[$i]->delete();
        }
        $message = "Successfully delete data";
        Toastr::success($message, $title = "Successfully Action", $options = []);

        $now = now();
        $products = Product::orderBy('productId', 'asc')->get();
        $transfer_in_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','in')->orderby('id','asc')->get();

        return view('transfer_in.index',compact('products','now','transfer_in_without_confirmed'));
    }

    public function destroy_out(Request $request,$id)
    {

        // dd($transferInOut->id);

        $transferInOutArray = TransferInOut::where('document_reference_id','=',$request->document_reference_id)->get();
        for ($i = 0; $i < count($transferInOutArray); $i++) {
            $transferInOutArray[$i]->delete();
        }
        $message = "Successfully delete data";
        Toastr::success($message, $title = "Successfully Action", $options = []);

        $now = now();
        $products = Product::orderBy('productId', 'asc')->get();
        $transfer_out_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','out')->orderby('id','asc')->get();

        return view('transfer_out.index',compact('products','now','transfer_out_without_confirmed'));
    }

    public function approve_in(Request $request)
    {
        // dd($request->document_reference_id);
        $transferInOutArray = TransferInOut::where('document_reference_id','=',$request->document_reference_id)->get();
        for ($i = 0; $i < count($transferInOutArray); $i++) {

            //update
            $transferInOutArray[$i]->isConfirmed = 1;

            // dd($transferInOutArray[$i]->product_running->id);

            if($transferInOutArray[$i]->save()){
                // dd($transferToBeSave->id);
                // echo 'Save id -> '.$transferToBeSave->id;
                // $kpi_outputs = KpiOutput::where('role_id','=',$roleSelected->id)->orderBy('updated_at', 'desc')->paginate(10);

                $stockRealtimeObj = StockRealTime::where('product_running_id','=',$transferInOutArray[$i]->product_running->id)->first();
                if($stockRealtimeObj==null){
                    // create
                    $stockRealtimeToBeSave                      = new StockRealTime();
                    $stockRealtimeToBeSave->product_running_id  = $transferInOutArray[$i]->product_running->id;
                    $stockRealtimeToBeSave->amount              = $transferInOutArray[$i]->amount;
                    $stockRealtimeToBeSave->transfer_in_out_id  = $transferInOutArray[$i]->id;
                    $stockRealtimeToBeSave->save();

                }else{
                    // update
                    $stockRealtimeObj->amount              += $transferInOutArray[$i]->amount;
                    $stockRealtimeObj->transfer_in_out_id  = $transferInOutArray[$i]->id;
                    $stockRealtimeObj->save();
                }


            } // end if($transferToBeSave->save())

        }

        $message = "Successfully approved data";
        Toastr::success($message, $title = "Successfully Action", $options = []);

        // $now = now();
        // $products = Product::orderBy('productId', 'asc')->get();
        $transfer_in_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','in')->orderby('id','asc')->get();
        $transfer_in_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','in')->orderby('id','desc')->paginate(10);

        return view('transfer_in_approve.index',compact('transfer_in_without_confirmed','transfer_in_confirmed'));
    }

    public function approve_out(Request $request)
    {
        $isError = false;
        // dd($request->document_reference_id);
        $transferInOutArray = TransferInOut::where('document_reference_id','=',$request->document_reference_id)->get();

        // check stock enough first
        // edit 21Aug19
        for ($i = 0; $i < count($transferInOutArray); $i++) {
            // dd($transferInOutArray[$i]->product_running->id);

            $stockRealTimeByProduct =  StockRealTime::where('product_running_id','=',$transferInOutArray[$i]->product_running->id)->first();
            if($transferInOutArray[$i]->amount > $stockRealTimeByProduct->amount){
                $message = "Error, Not Enough Amount !!";
                Toastr::warning($message, $title = "Error Action", $options = []);
                $isError = true;
                break;
            }
        }


        if(!$isError){
            //update
            for ($i = 0; $i < count($transferInOutArray); $i++) {

                $transferInOutArray[$i]->isConfirmed = 1;

                // dd($transferInOutArray[$i]->product_running->id);

                if($transferInOutArray[$i]->save()){
                    // dd($transferToBeSave->id);
                    // echo 'Save id -> '.$transferToBeSave->id;
                    // $kpi_outputs = KpiOutput::where('role_id','=',$roleSelected->id)->orderBy('updated_at', 'desc')->paginate(10);

                    $stockRealtimeObj = StockRealTime::where('product_running_id','=',$transferInOutArray[$i]->product_running->id)->first();
                    if($stockRealtimeObj==null){
                        // Error
                        // impossible
                    }else{
                        // update
                        $stockRealtimeObj->amount              -= $transferInOutArray[$i]->amount;
                        $stockRealtimeObj->transfer_in_out_id  = $transferInOutArray[$i]->id;
                        $stockRealtimeObj->save();
                    }


                } // end if($transferToBeSave->save())

            }

            $message = "Successfully approved data";
            Toastr::success($message, $title = "Successfully Action", $options = []);
        } // end if (!$isError)


        // $now = now();
        // $products = Product::orderBy('productId', 'asc')->get();
        $transfer_out_without_confirmed = TransferInOut::where('isConfirmed','=',0)->where('in_or_out','=','out')->orderby('id','asc')->get();
        $transfer_out_confirmed = TransferInOut::where('isConfirmed','=',1)->where('in_or_out','=','out')->orderby('id','desc')->paginate(10);

        return view('transfer_out_approve.index',compact('transfer_out_without_confirmed','transfer_out_confirmed'));
    }

    public function exportTransferInOut(Request $request)
    {
        return Excel::download(new ExportTransferInOut($request), 'exportTransferInOut.xlsx');
    }
}
