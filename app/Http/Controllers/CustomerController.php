<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request){
        if ($request->id) {
            $customer = Customer::where('id', $request->id)->first();
        } else {
            $customer = Customer::all();
        }

        if ($customer->first() != null) {
            return response_json(200, 'success', $customer);
        }
        return response_json(404, 'failed', 'Customer not found');
    }

    public function create(Request $request){
        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'name' => 'required',
            'customer_no' => 'required',
            'gender' => 'required',
            'age' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        // Get the premi
        if ($request->product_id == 1) {
            if ($request->gender == 'L') {
                $premi = 750000;
            } else if ($request->gender == 'P') {
                $premi = 850000;
            }

            if ($request->age < 12) {
                $premi = 825000;
            }
        } else if ($request->product_id == 2) {
            if ($request->gender == 'L') {
                $premi = 1250000;
            } else if ($request->gender == 'P') {
                $premi = 1350000;
            }

            if ($request->age < 12) {
                $premi = 1300000;
            }
        } else if ($request->product_id == 3) {
            if ($request->gender == 'L') {
                $premi = 1750000;
            } else if ($request->gender == 'P') {
                $premi = 2000000;
            }

            if ($request->age < 12) {
                $premi = 1800000;
            }
        } else if ($request->product_id == 4) {
            if ($request->gender == 'L') {
                $premi = 2450000;
            } else if ($request->gender == 'P') {
                $premi = 2700000;
            }

            if ($request->age < 12) {
                $premi = 2500000;
            }
        }

        // Create customer account
        $customer = Customer::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'customer_no' => $request->customer_no,
            'gender' => $request->gender,
            'age' => $request->age,
            'premi' => $premi,
        ]);

        // Create customer balance
        $product_detail = ProductDetail::where('product_id', $request->product_id)->get();
        
        foreach ($product_detail as $item) {
            CustomerBalance::create([
                'customer_id' => $customer->id,
                'product_detail_id' => $item->id,
                'balance' => $item->value,
                'maximum_used_balance' => $item->maximum_used
            ]);
        }

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $customer);
    }

    public function update(Request $request, string $id){
        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'name' => 'required',
            'customer_no' => 'required',
            'gender' => 'required',
            'age' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        // Get the premi
        if ($request->product_id == 1) {
            if ($request->gender == 'L') {
                $premi = 750000;
            } else if ($request->gender == 'P') {
                $premi = 850000;
            }

            if ($request->age < 12) {
                $premi = 825000;
            }
        } else if ($request->product_id == 2) {
            if ($request->gender == 'L') {
                $premi = 1250000;
            } else if ($request->gender == 'P') {
                $premi = 1350000;
            }

            if ($request->age < 12) {
                $premi = 1300000;
            }
        } else if ($request->product_id == 3) {
            if ($request->gender == 'L') {
                $premi = 1750000;
            } else if ($request->gender == 'P') {
                $premi = 2000000;
            }

            if ($request->age < 12) {
                $premi = 1800000;
            }
        } else if ($request->product_id == 4) {
            if ($request->gender == 'L') {
                $premi = 2450000;
            } else if ($request->gender == 'P') {
                $premi = 2700000;
            }

            if ($request->age < 12) {
                $premi = 2500000;
            }
        }

        // Update customer account
        $customer = Customer::where('id', $id)->first();
        $customer->update([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'customer_no' => $request->customer_no,
            'gender' => $request->gender,
            'age' => $request->age,
            'premi' => $premi,
        ]);

        // Update customer balance if customer changed their product
        if ($request->product_id != $customer->product_id) {
            $product_detail = ProductDetail::where('product_id', $request->product_id)->get();
        
            foreach ($product_detail as $item) {
                CustomerBalance::where('customer_id', $id)->where('product_detail_id', $item->id)->update([
                    'customer_id' => $customer->id,
                    'product_detail_id' => $item->id,
                    'balance' => $item->value,
                    'maximum_used_balance' => $item->maximum_used
                ]);
            }
        }

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $customer);
    }

    public function delete(string $id){
        // Delete customer account
        $customer = Customer::where('id', $id)->first();

        if ($customer != null) {
            $customer->delete();
            // Delete customer balance
            CustomerBalance::where('customer_id', $id)->delete();
            
            return response_json(200, 'success', 'Customer deleted successfully');
        }
        
        return response_json(404, 'failed', 'Customer not found');
    }
}
