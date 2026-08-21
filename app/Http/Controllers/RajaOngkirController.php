<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Get list of provinces.
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->rajaOngkir->getProvinces();
        return response()->json([
            'status' => 'success',
            'data' => $provinces,
        ]);
    }

    /**
     * Get list of cities for given province ID.
     */
    public function getCities($provinceId): JsonResponse
    {
        $cities = $this->rajaOngkir->getCities((int) $provinceId);
        return response()->json([
            'status' => 'success',
            'data' => $cities,
        ]);
    }

    /**
     * Calculate live shipping costs and packing fees for items currently in cart.
     */
    public function checkOngkir(Request $request): JsonResponse
    {
        $request->validate([
            'destination_city_id' => 'required|integer',
            'courier' => 'required|string',
        ]);

        $cart = session()->get('keranjang', []);
        if (empty($cart)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Keranjang belanja Anda masih kosong.',
            ], 400);
        }

        $totalWeight = $this->rajaOngkir->calculateTotalWeight($cart);
        $packing = $this->rajaOngkir->calculatePackingCost($cart);
        $shipping = $this->rajaOngkir->calculateCost(
            (int) $request->input('destination_city_id'),
            $totalWeight,
            (string) $request->input('courier')
        );

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['harga'] * $item['jumlah']);
        }

        return response()->json([
            'status' => 'success',
            'subtotal' => $subtotal,
            'total_weight_gram' => $totalWeight,
            'total_weight_kg' => round($totalWeight / 1000, 2),
            'packing' => $packing,
            'shipping' => $shipping,
        ]);
    }
}
