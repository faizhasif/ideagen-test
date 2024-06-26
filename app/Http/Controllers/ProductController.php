<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Http\Requests\ProductListingRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['string'],
            'category' => ['string'],
            'min_price' => ['numeric', 'min:0'],
            'max_price' => ['numeric', 'min:0'],
        ])
        ->sometimes('min_price', 'lt:max_price', function (Fluent $input) {
            return isset($input->max_price);
        })
        ->sometimes('max_price', 'gt:min_price', function (Fluent $input) {
            return isset($input->min_price);
        });

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Validation Errors',
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $products = Product::where('id', '>', 0);

        if (isset($request->name)) {
            $products = $products->where('name', 'like', "%{$request->name}%");
        }

        if (isset($request->category)) {
            $products = $products->where('category', $request->category);
        }

        if (isset($request->min_price) && isset($request->max_price)) {
            $products = $products->whereBetween('price', [$request->min_price, $request->max_price]);
        } else {
            if (isset($request->min_price)) {
                $products = $products->where('price', '>=', $request->min_price);
            }

            if (isset($request->max_price)) {
                $products = $products->where('price', '<=', $request->max_price);
            }
        }

        $products = $products->get();
        return response()->json([
            'message' => 'Listing Successful',
            'data' => $products,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
