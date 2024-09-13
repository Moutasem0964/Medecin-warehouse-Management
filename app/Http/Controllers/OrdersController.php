<?php

namespace App\Http\Controllers;

use App\Models\Order;

use App\Models\Drug;
use App\Models\Keeper;
use App\Models\Pharmacist;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function createNewOrder(Request $request)
    {
        $token = $request->bearerToken();
        $token = hash('sha256', $token);
        $pharmacist = Pharmacist::where('api_token', $token)->first();
        $pharmacist_id = $pharmacist->id;
        $order = new Order();
        $order->pharmacist_id = $pharmacist_id;
        $warehouse_name = $request->header('warehouse_name');
        if (!$warehouse_name) {
            return response()->json([
                'message' => 'warehouse name not provided'
            ]);
        }
        $warehouse = Warehouse::where('name', $warehouse_name)->first();
        if (!$warehouse) {
            return response()->json([
                'message' => 'warehouse name not found'
            ]);
        }
        $order->warehouse_id = $warehouse->id;
        $order->save();
        $drugs = $request->basket;

        foreach ($drugs as $drug) {

            $drug_ordered = Drug::where('commercial_name', $drug['name'])
                ->whereHas('warehouses', function ($query) use ($warehouse) {
                    $query->where('warehouses.id', $warehouse->id);
                })->first();



            // Attach the drug to the order with the specified amount in the order_drug table
            $order->drugs()->attach($drug_ordered->id, ['amount' => $drug['amount']]);
        }
        return response()->json([
            'message' => 'Added successfully',

        ]);
    }

    public function listAllOreders(Request $request)
    {
        $token = $request->bearerToken();
        $token = hash('sha256', $token);
        $keeper = Keeper::where('api_token', $token)->first();
        $warehouse_id=$keeper->warehouse_id;
        $orders = Order::where('warehouse_id', $warehouse_id)
        ->with(['pharmacist', 'pharmacist.pharmacy'])
        ->get();
        return response()->json([
            'All Orders' => $orders->map(function ($order) {
                return [
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'pharmacy_name' => $order->pharmacist->pharmacy->name,
                    'pharmacist_name' => $order->pharmacist->name,
                ];
            })
        ]);
    }

    public function listMyOreders(Request $request)
    {
        $token = $request->bearerToken();
        $token = hash('sha256', $token);
        $pharmacist = Pharmacist::where('api_token', $token)->first();
        $pharmacist_id = $pharmacist->id;
        $orders = Order::where('pharmacist_id', $pharmacist_id)->with('warehouse')->get();
        return response()->json([
            'All Orders' => $orders->map(function ($order) {
                return [
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'warehouse_name' => $order->warehouse->name,
                ];
            })
        ]);
    }

    public function editOreder(Request $request)
{
    $order=Order::where('id',$request->header('order_id'))->first();
    // Retrieve the bearer token and hash it
    $token = $request->bearerToken();
    $token = hash('sha256', $token);

    // Find the warehouse keeper
    $keeper = Keeper::where('api_token', $token)->first();

    // Check if the keeper is associated with the warehouse of the order
    if ($keeper->warehouse_id != $order->warehouse_id) {
        return response()->json([
            'message' => 'You do not have permission to edit this order'
        ], 403);
    }

    // Update the status and payment_status of the order
    $oldstatus=$order->status;
    $order->status = $request->input('status');
    $order->payment_status = $request->input('payment_status');

    // If the status is 'sent' and the old status wasn't 'sent', update the drug amounts
    if ($oldstatus!='sen'&&$order->status == 'sent') {
        foreach ($order->drugs as $drug) {
            $order_amount=$drug->pivot->amount;
            $drug->amount =$drug->amount - $order_amount;

            // If the drug amount is 0, delete the drug
            
                $drug->save();
            
        }  
    return response()->json([
        'message' => 'Order updated successfully',
        'order' => $order
    ]);
}

}
}
