<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
 public function index()
    {
        $customers = Customer::all();
        return view('Customers.index', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:255',
                'email' => 'required|email|',
                'address' => 'required'
            ]
        );

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:255',
                'email' => 'required|email|',
                'address' => 'required'
            ]
        );

        $customers = Customer::findOrFail($id);
        $customers->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'customer berhasil dihapus');
    }
}
