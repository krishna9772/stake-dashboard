<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function storeWalletAddress(Request $request)
    {
        $walletAddress = $request->input('walletAddress');

        $walletAddress = Session::put('walletAddress', $walletAddress);

        return response()->json(['message' => 'Wallet address stored successfully']);
    }
}