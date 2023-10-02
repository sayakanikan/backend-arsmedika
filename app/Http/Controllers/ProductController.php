<?php

namespace App\Http\Controllers;

use App\Models\CustomerBalance;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use App\Models\ProductValue;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function product() {
        $product = Product::get();
        $product->makeHidden(['created_at', 'updated_at']);

        if ($product) {
            return response_json(200, 'success', $product);
        }
        return response_json(404, 'failed', 'Product not found');
    }

    public function index(){
        $product = ProductDetail::with(['product_value' => function($queryValue){
            $queryValue->with(['product' => function($queryProduct){
                $queryProduct->select('id', 'product_name')->get();
            }])->select('id', 'product_id', 'product_detail_id', 'value')->get();
        }])->latest()->get();
        $product->makeHidden(['created_at', 'updated_at']);

        if ($product->first() != null) {
            return response_json(200, 'success', $product);
        }
        return response_json(404, 'failed', 'Product Detail not found');
    }

    public function create(Request $request){
        // Validate input
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'item' => 'required',
            'value' => 'required',
            'product_per' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        if ($request->maximum_used == 'null') {
            $request->maximum_used = null;
        }

        $product_detail = ProductDetail::where('code', $request->code)->first();
        // Check if the product is already exists
        if ($product_detail == null) {
            $product_detail = ProductDetail::create([
                'code' => $request->code,
                'item' => $request->item,
                'product_per' => $request->product_per,
                'maximum_used' => $request->maximum_used,
            ]);
        }

        $product = $request->value;
        // return json_decode($request->value);
        foreach ($product as $item) {
            $product_value = ProductValue::where('product_id', $item['product_id'])->where('product_detail_id', $product_detail->id)->first();
            if ($product_value != null) {
                return response_json(409, 'failed', 'Product already exists');
            }
            // Add product
            $product_value = ProductValue::create([
                'product_id' => $item['product_id'],
                'product_detail_id' => $product_detail->id,
                'value' => $item['value'],
            ]);
    
            // Update customer balance when new product is added
            $balance = CustomerBalance::where('product_id', $item['product_id'])->pluck('customer_id')->unique()->values();
    
            if ($balance != null) {
                foreach ($balance as $id_customer) {
                    CustomerBalance::create([
                        'customer_id' => $id_customer,
                        'product_id' => $item['product_id'],
                        'product_detail_id' => $product_detail->id,
                        'balance' => $product_value->value,
                        'maximum_used_balance' => $request->maximum_used,
                    ]);
                }
            }
        }

        if ($product_detail) {
            return response_json(200, 'success', 'Product added successfully');
        }
        return response_json(500, 'failed', 'Failed to add product');
    }

    public function delete(Request $request) {
        $id = $request->id;

        // Delete customer account
        $product_detail = ProductDetail::where('id', $id)->first();

        if ($product_detail != null) {
            $product_detail->delete();

            // Delete product value
            ProductValue::where('product_detail_id', $id)->delete();

            // Delete customer balance
            CustomerBalance::where('product_detail_id', $id)->delete();
            
            return response_json(200, 'success', 'Customer deleted successfully');
        }
        
        return response_json(404, 'failed', 'Customer not found');
    }

    public function product_code(){
        $code = ProductDetail::pluck('code')->unique()->values();

        return response_json(200, 'success', $code);
    }
}
