<?php

namespace App\Http\Controllers\Api\V1\Car;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * قائمة سيارات السائق (Driver)
     */
    public function myCars(Request $request)
    {
        $cars = Car::where('driver_id', $request->user()->id)->get();

        return response()->json([
            'status' => 200,
            'cars'   => $cars,
        ]);
    }

    /**
     * إضافة سيارة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'car_type'   => 'required|string|max:255',
            'car_model'  => 'required|string|max:255',
            'car_number' => 'required|string|max:50',
            'color'      => 'nullable|string|max:100',
            'year'       => 'nullable|integer',
            'image'      => 'nullable|string',
        ]);

        $car = Car::create([
            'driver_id'  => $request->user()->id,
            'car_type'   => $request->car_type,
            'car_model'  => $request->car_model,
            'car_number' => $request->car_number,
            'color'      => $request->color,
            'year'       => $request->year,
            'image'      => $request->image,
        ]);

        return response()->json([
            'status'  => 201,
            'message' => 'Car created successfully',
            'car'     => $car
        ]);
    }

    /**
     * عرض سيارة
     */
    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['status' => 404, 'message' => 'Car not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'car'    => $car,
        ]);
    }

    /**
     * تحديث السيارة
     */
    public function update(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['status' => 404, 'message' => 'Car not found'], 404);
        }

        if ($car->driver_id !== $request->user()->id) {
            return response()->json(['status' => 403, 'message' => 'Not allowed'], 403);
        }

        $car->update($request->only([
            'car_type', 'car_model', 'car_number', 'color', 'year', 'image'
        ]));

        return response()->json([
            'status' => 200,
            'message' => 'Car updated successfully',
            'car'     => $car,
        ]);
    }

    /**
     * حذف سيارة
     */
    public function destroy(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['status' => 404, 'message' => 'Car not found'], 404);
        }

        if ($car->driver_id !== $request->user()->id) {
            return response()->json(['status' => 403, 'message' => 'Not allowed'], 403);
        }

        $car->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Car deleted successfully',
        ]);
    }
}
