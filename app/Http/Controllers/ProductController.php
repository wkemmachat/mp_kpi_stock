<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Product;
use App\StockRealTime;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Kamaln7\Toastr\Facades\Toastr;
use App\Exports\ExportProduct;
use App\Exports\exportProductCollection;
use App\Exports\exportProductCollectionQuery;
use App\Imports\ProductsImport;
use App\Imports\StockRealTimeImport;
use Carbon\Carbon;
use App\Exports\ExportProductView;
use App\ProductCategory;
use Illuminate\Support\Facades\File;
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        // $kpi_outputs = KpiOutput::where('role_id','=',$roleSelected->id)->orderBy('updated_at', 'desc')->paginate(10);
        $products = Product::orderBy('updated_at', 'desc')->paginate(10);
        $categories = ProductCategory::all();
        // return view('kpi.index',compact('roleSelected','usersHaveRoleArray','kpi_outputs'));
        return view('product.index',compact('products','categories'));
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
    public function store(Request $request)
    {
        $request->validate([
            'productId' => 'required',
            'productName' => 'required',
            'productCategory_running_Id' =>'required',
            'min' => 'required',
            'active' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($request->remark == null){
            $request->remark = '';
        }
        $image = $request->file('image');
        if($image!=null){
            $new_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $new_name);
        }else{
            $new_name = '';
        }
        // dd($new_name);
        // save
        $productToBeSave = new Product();
        // $productToBeSave->input_date        = Carbon::createFromFormat('d-m-Y', $request->date_input)->format('Y-m-d');
        $productToBeSave->user_key_in_id    = Auth::user()->id;
        $productToBeSave->productId         = $request->productId;
        $productToBeSave->productName       = $request->productName;
        $productToBeSave->remark            = $request->remark;
        $productToBeSave->min               = $request->min;
        $productToBeSave->active            = $request->active;
        $productToBeSave->productCategoryRunning_id     = $request->productCategory_running_Id;
        $productToBeSave->imageName         = $new_name;
        $productToBeSave->save();
        $products = Product::orderBy('updated_at', 'desc')->paginate(10);
        $categories = ProductCategory::all();
        $message = "Successfully add data";
        Toastr::success($message, $title = "Successfully Action", $options = []);
        return view('product.index',compact('products','categories'));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\KpiOutput  $kpiOutput
     * @return \Illuminate\Http\Response
     */
    public function show(KpiOutput $kpiOutput)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KpiOutput  $kpiOutput
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // dd($id);
        $productSelected = Product::findOrFail($id);
        $categories = ProductCategory::all();
        // dd($categories);
        // dd($userSelected);
        // $users = User::all();
        return view('product.edit',compact('productSelected','categories'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KpiOutput  $kpiOutput
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $productSelected = Product::findOrFail($id);
        $validatedData = $request->validate([
            'productId' => 'required|max:255',
            'productName' => 'required|max:255',
            'productCategory_running_Id' =>'required',
            'min' => 'required',
            'active' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        // update image
        $image = $request->file('image');
        if($image!=null){
            $image_path = "images/".$productSelected->imageName;  // Value is not URL but directory file path
            // dd($image_path);
            if(file_exists($image_path)) {
                // dd("true");
                File::delete($image_path);
            }
            // $image->delete(public_path('images'), $productSelected->imageName);
            $new_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $new_name);
        }else{
            $new_name = '';
        }
        if($request->remark == null){
            $request->remark = '';
        }
        // update
        $productSelected->productId     = $request['productId'];
        $productSelected->productName   = $request['productName'];
        $productSelected->remark        = $request->remark = '';
        $productSelected->min           = $request['min'];
        $productSelected->active        = $request['active'];
        $productSelected->productCategoryRunning_id     = $request->productCategory_running_Id;
        $productSelected->imageName     = $new_name;
        $productSelected->save();
        $products = Product::orderBy('updated_at', 'desc')->paginate(10);
        $categories = ProductCategory::all();
        $message = "Successfully add data";
        Toastr::success($message, $title = "Successfully Action", $options = []);
        return view('product.index',compact('products','categories'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KpiOutput  $kpiOutput
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
    }
    public function exportProduct(Request $request)
    {
        return Excel::download(new ExportProduct($request), 'product.xlsx');
    }
    public function exportProductView(Request $request)
    {
        return Excel::download(new ExportProductView($request), 'productview.xlsx');
    }
    public function exportProductCollection(Request $request)
    {
        $data = [
            [
                'name' => 'Povilas',
                'surname' => 'Korop',
                'email' => 'povilas@laraveldaily.com',
                'twitter' => '@povilaskorop'
            ],
            [
                'name' => 'Taylor',
                'surname' => 'Otwell',
                'email' => 'taylor@laravel.com',
                'twitter' => '@taylorotwell'
            ]
        ];
        return Excel::download(new ExportProductCollection($data), 'productcollection.xlsx');
    }
    public function exportProductCollectionQuery(Request $request)
    {
        $startDate = Carbon::createFromFormat('d-m-Y', $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $request->endDate)->format('Y-m-d');
        // dd($startDate);
        $productArray =  Product::where('updated_at','>=',$startDate)->where('updated_at','<=',$endDate)
        ->orderby('id', 'asc');
        // $collectionArray = $productArray;
        // foreach ($collectionArray as $key => $collect) {
            // $collect->num = $key;
            // dd($collect->num);
        // }
        // dd(sizeof($productArray));
        // query data
        foreach ($productArray as $key => $product) {
        }
        $age_obj_1 = array(['Name'=>'Peter','Age'=>'10'],
                    ['Name'=>'Ben','Age'=>'20']);
        $age_obj_2 = array(['Name'=>'Kem','Age'=>'30']);
        $age_obj_3 = $age_obj_1+$age_obj_2;
        dd($age_obj_3);
        foreach ($productArray as $key => $productInLoop) {
            # code...
        }
        $data = [
            [
                'name' => 'Povilas',
                'surname' => 'Korop',
                'email' => 'povilas@laraveldaily.com',
                'twitter' => '@povilaskorop'
            ],
            [
                'name' => 'Taylor',
                'surname' => 'Otwell',
                'email' => 'taylor@laravel.com',
                'twitter' => '@taylorotwell'
            ]
        ];
        return Excel::download(new ExportProductCollectionQuery($productArray), 'productcollection.xlsx');
    }
    public function import()
    {
        Excel::import(new ProductsImport,request()->file('file'));
        return back();
    }

    public function stock_real_time_import()
    {

        // delete all in database
        $stockRealTimeArray = StockRealTime::all();


        for ($i = 0; $i < count($stockRealTimeArray); $i++) {
            // dd($transferInOutArray[$i]->product_running->id);
            $stockRealTimeArray[$i]->delete();

        }

        Excel::import(new StockRealTimeImport,request()->file('file'));
        return back();
    }


    public function upload_index()
    {
        return view('upload.index');
    }
}
