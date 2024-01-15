<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index() {
        $clients = DB::table('clients')->get();

        foreach ($clients as &$client) {
            $cars = DB::table('cars')->where('client_id', $client->id)->get();

            $client->cars = $cars;
        }

        return $clients;
    }

    public function store(Request $request) {
        $name    = $request->input('name');
        $sex     = $request->input('sex');
        $phone   = $request->input('phone');
        $address = $request->input('address');
        $cars    = $request->input('cars');

        if(!is_array($cars)) return response()->json(['status' => 'ERROR', 'message' => 'Cars must be an array']);
        if(count($cars) === 0) return response()->json(['status' => 'ERROR', 'message' => 'Client doesnot have a car']);

        $now = now();

        $client_id = DB::table('clients')->insertGetId(
            [
                'name'       => $name,
                'sex'        => $sex,
                'phone'      => $phone,
                'address'    => $address,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        $carsParamToInsert = [];

        foreach ($cars as $car) {
            $carsParamToInsert[] = [
                'brand'     => $car['brand'],
                'model'     => $car['model'],
                'color'     => $car['color'],
                'number'    => $car['number'],
                'is_parked' => $car['is_parked'],
                'client_id' => $client_id,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        DB::table('cars')->insert($carsParamToInsert);

        return response()->json(['status' => 'SUCCESS', 'message' => 'SAVED']);
    }

    public function update (Request $request, string $client_id) {
        $client_id = filter_var($client_id, FILTER_VALIDATE_INT);

        $name    = $request->input('name');
        $sex     = $request->input('sex');
        $phone   = $request->input('phone');
        $address = $request->input('address');

        $affectedRows = DB::table('clients')
              ->where('id', $client_id)
              ->update([
                'name'       => $name,
                'sex'        => $sex,
                'phone'      => $phone,
                'address'    => $address,
                'updated_at' => now()
              ]);

        if($affectedRows === 0) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'NOTHING_CHANGED']);
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'UPDATED']);
    }

    public function destroy ($client_id) {
        $client_id = filter_var($client_id, FILTER_VALIDATE_INT);

        DB::table('clients')->where('id', $client_id)->delete();

        return response()->json(['status' => 'SUCCESS', 'message' => 'DELETED']);
    }
}
