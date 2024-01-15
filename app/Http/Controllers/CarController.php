<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index()
    {
        $cars = DB::table('cars')->get();

        foreach ($cars as &$car) {
            $client = DB::table('clients')->where('id', $car->client_id)->take(1)->get();
            $car->client = !empty($client) ? $client[0] : null;
        }

        return response()->json($cars);
    }

    public function store(Request $request)
    {
        $brand = $request->input('brand');
        $model = $request->input('model');
        $color = $request->input('color');
        $number = $request->input('number');
        $is_parked = $request->input('is_parked');
        $client_id = $request->input('client_id');

        $client = DB::table('clients')->where('id', $client_id)->take(1)->get();

        if (empty($client)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Such client doesnot exist']);
        }

        if (empty($brand)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'brand\' is required']);
        if (empty($model)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'model\' is required']);
        if (empty($color)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'color\' is required']);
        if (empty($number)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'number\' is required']);

        $now = now();

        DB::table('cars')->insert([
            'brand'         => $brand,
            'model'         => $model,
            'color'         => $color,
            'number'        => $number,
            'is_parked'     => $is_parked,
            'client_id'     => $client_id,
            'updated_at'    => $now,
            'created_at'    => $now
        ]);

        return response()->json(['status' => 'SUCCESS', 'message' => 'SAVED']);
    }

    public function update(Request $request, string $car_id)
    {
        $car_id = filter_var($car_id, FILTER_VALIDATE_INT);

        $brand = $request->input('brand');
        $model = $request->input('model');
        $color = $request->input('color');
        $number = $request->input('number');
        $is_parked = $request->input('is_parked');
        $client_id = $request->input('client_id');

        $client = DB::table('clients')->where('id', $client_id)->take(1)->get();

        if (empty($client)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Such client doesnot exist']);
        }

        if (empty($brand)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'brand\' is required']);
        if (empty($model)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'model\' is required']);
        if (empty($color)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'color\' is required']);
        if (empty($number)) return response()->json(['status' => 'ERROR', 'message' => 'the field \'number\' is required']);

        $affectedRows = DB::table('cars')
            ->where('id', $car_id)
            ->update([
                'brand'         => $brand,
                'model'         => $model,
                'color'         => $color,
                'number'        => $number,
                'is_parked'     => $is_parked,
                'client_id'     => $client_id,
                'updated_at' => now()
            ]);

        if ($affectedRows === 0) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'NOTHING_CHANGED']);
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'UPDATED']);
    }

    public function destroy($car_id)
    {
        $car_id = filter_var($car_id, FILTER_VALIDATE_INT);

        DB::table('cars')->where('id', $car_id)->delete();

        return response()->json(['status' => 'SUCCESS', 'message' => 'DELETED']);
    }

    public function getParkingStatus(Request $request) {
        $isParked = boolval($request->query('is_parked', true));

        $parkedCars = DB::table('cars')->where('is_parked', $isParked)->get();
        $totalParkedCars = $parkedCars->count();

        $response = [
            'totalc_cars' => $totalParkedCars,
            'cars' => $parkedCars
        ];

        return response()->json($response);
    }
}
