<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\Keeper;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class DrugsController extends Controller
{
    public function createNewDrug(Request $request){
        $token=$request->bearerToken();
        $token=hash('sha256',$token);
        $keeper=Keeper::where('api_token',$token)->first();
        $warehouse_id=$keeper->warehouse_id;
        $warehouse=Warehouse::where('id',$warehouse_id)->first();
        $drug=new Drug();
        $drug->scintific_name=$request->scintific_name; 
        $drug->commercial_name=$request->commercial_name; 
        $drug->category=$request->category; 
        $drug->company=$request->company;
        $drug->amount=$request->amount;
        $drug->expiration_date=$request->expiration_date; 
        $drug->price=$request->price; 
        $drug->save();
        
        $warehouse->drugs()->attach($drug->id,['amount'=>$request->amount]);
        return response()->json([
            'Added Successfuly'=>$drug
        ]);
    }
    public function listAllWarehouses(){
        $warehouses=Warehouse::all()->map(function($warehouses){
            return collect($warehouses)->only(['id','name']);
        });
        return response()->json([
            'All Warehouses'=>$warehouses
        ]);
    }

    public function listAllDrugs($id){
        // $drugs=Drug::get('commercial_name')->toArray();
        $warehouse=Warehouse::where('id',$id)->first();
          
        return response()->json([
            'ALL Drugs'=>$warehouse->drugs->pluck('commercial_name')
        ]);
    }

    public function listDetails($warehouse_id,$drug_id){
        $warehouse=Warehouse::find($warehouse_id);
        $drug=$warehouse->drugs()->find($drug_id)->only('scintific_name','commercial_name','category','company','amount','expiration_date','price');        
        return response()->json([
            'data'=>$drug
        ]);
    }


    public function search(Request $request){
        $input= $request->input('NameOrCategory');
        $drugs = Drug::where('commercial_name', $input)->orWhere('category', $input)->get();        
    
        if($drugs->isEmpty()){
            return response()->json([
                'message' => 'Not Found !!!'
            ]); 
        }
        else{
            $response = [];
            foreach ($drugs as $drug) {
                $warehouses = $drug->warehouses()->get();
                foreach ($warehouses as $warehouse) {
                    $response[] = [
                        'Name' => $drug->commercial_name,
                        'Category' => $drug->category,
                        'Price'=>$drug->price,
                        'Warehouse' => $warehouse->name,
                        'Amount' => $warehouse->pivot->amount
                    ];
                }
            }
            return response()->json($response);
        }
    }
    public function sort(Request $request){
        $drugs=Drug::where('category',$request->input('category'))->get();

        $response = [];
            foreach ($drugs as $drug) {
                $warehouses = $drug->warehouses()->get();
                foreach ($warehouses as $warehouse) {
                    $response[] = [
                        'Name' => $drug->commercial_name,
                        'Category' => $drug->category,
                        'Price'=>$drug->price,
                        'Warehouse' => $warehouse->name,
                        'Amount' => $warehouse->pivot->amount
                    ];
                }
            }
            return response()->json($response);
    }
}
