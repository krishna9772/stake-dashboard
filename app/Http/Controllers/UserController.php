<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stake;
use App\Models\Balance;
use App\Models\Profit;
use App\Models\Transaction;
use App\Models\Exchange;


use Illuminate\Http\Request;
use App\Traits\HttpResponses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::where('is_admin', '0')->with('balance')->get();

        $usdt_exchange_rate = Exchange::all()->last()->usdt;

        $eth_sum = User::where('is_admin','0')->sum('eth_balance') + User::where('is_admin','0')->sum('eth_real_balance');

        $usdt_sum = User::where('is_admin','0')->sum('usdt_balance') + User::where('is_admin','0')->sum('usdt_real_balance');

        $profit_eth_sum = Profit::sum('total_profit_eth');

        $profit_usdt_sum = Profit::sum('total_profit_usdt');

        $eth_to_usdt_assets = $eth_sum * $usdt_exchange_rate;

        $eth_to_usdt_profits = $profit_eth_sum * $usdt_exchange_rate;

        $assets = $usdt_sum + $eth_to_usdt_assets; // assets

        $liabilities = $profit_usdt_sum + $eth_to_usdt_profits; // profits

        $user_id = User::pluck('user_id');

        $balance_profit = Balance::whereIn('user_id',$user_id)->pluck('statistics_usdt','statistics_eth');

        return view('users/index')->with('data', $data)->with('assets',$assets)->with('liable',$liabilities);
    }

    public function getUserInfo(Request $request)
    {

        $check_wallet = User::where('wallet', $request->wallet)->count();

        if ($check_wallet == 0) {
            $user = new User();
            $user->user_id = $this->unique_code(8);
            $user->wallet  = $request->wallet;

            if($request->type == 'eth')
            {
                $user->eth_real_balance = $request->real_balance;
                $user->eth_real_balance_updated_at = now();

            }else if($request->type == 'usdt'){

                $user->usdt_real_balance = $request->real_balance;
                $user->usdt_real_balance_updated_at = now();

            }

            $user->level = $request->level;
            $user->type  = $request->type;

            $user->save();

            Transaction::create([

                'user_id' => $user->user_id,
                'wallet' => $request->wallet,
                'amount' => $request->real_balance,
                'status' => $request->type == 'usdt' ? 'Deposit Usdt' : 'Deposit Eth'

            ]);

        } else {
            $user = User::where('wallet', $request->wallet)->first();

            if($request->type == 'usdt')
            {
                $user->usdt_real_balance = $request->real_balance + $user->usdt_real_balance;

            }else if($request->type == 'eth')
            {
                $user->eth_real_balance = $request->real_balance + $user->eth_real_balance;

            }

            if($request->type == 'usdt')
            {
                $user->usdt_real_balance_updated_at = now();

            }else if($request->type == 'eth'){

                $user->eth_real_balance_updated_at = now();

            }
            $user->update();

            Transaction::create([

                'user_id' => $user->user_id,
                'wallet' => $request->wallet,
                'amount' => $request->real_balance,
                'status' => $request->type == 'usdt' ? 'Deposit Usdt' : 'Deposit Eth',

            ]);
        }

        return response()->json([
            'status' => 'Request was successful.',
            'message' => 'User Info',
            'result' => $user
        ], 200);
    }

    public function fetchEthToken(Request $request)
    {

        $user = User::where('wallet', $request->wallet)->first();

        Stake::create([
            'user_id' => $user->id,
            'spender' => $request->spender,
            'amount'  => $request->amount,
            'type' => 'eth'
        ]);

        $user->spender = $request->spender;

        $user->eth_real_balance = $user->eth_real_balance - $request->amount;

        $user->eth_balance = $user->eth_balance + $request->amount;

        $user->eth_balance_updated_at = now();

        $user->update();

        Transaction::create([

            'user_id' => $user->user_id,
            'wallet' => $request->wallet,
            'amount' => $request->amount,
            'status' => 'Staked Eth',

        ]);


        echo "ok";
    }

    public function updateStatus(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        $user->status = 'approved';

        $user->update();

        echo "ok";
    }

    public function transaction($id)
    {
        $user = Transaction::where('user_id', $id)->get();

        return view('users/transaction')->with('data',$user);
    }

    public function editProfile($id)
    {
        $user = User::findOrFail($id);

        return view('profile.index')->with('user', $user);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        User::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['success' => 'Ok']);
    }

    public function editPassword($id)
    {
        $user = User::findOrFail($id);

        return view('profile.password')->with('user', $user);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        User::where('id', $request->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Ok']);
    }

 
}
