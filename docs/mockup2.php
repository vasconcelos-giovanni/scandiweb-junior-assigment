<?php
// Create
Product::create(['name' => 'New Product']);

Product::select(['id', 'name'])->where('price', '=', '10')->take(2)->get();

// UPDATE
// Eloquent
Product::update(['id' => '1']);
// My way
$product = Product::where('id', 1)->limit(1)->get()[0];
$product->sku = 'MY-SKU-001';
$product->price = 99.99;
$product->save();

// DELETE
// Eloquent
Product::delete(['id' => '1']);
// My way
$product = Product::where('id', 1)->limit(1)->get()[0];
$product->delete();