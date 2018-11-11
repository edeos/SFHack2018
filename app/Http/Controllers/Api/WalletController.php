<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\EosController;
use App\Transaction;
use App\Wallet;

class WalletController extends Controller
{

	public function store(Request $request)
	{
		$name = 'user'.rand(1, 5).rand(1, 5).rand(1, 5).rand(1, 5).rand(1, 5);
		$eos = EosController::create($name);

		if ($eos) {

			$wallet = new Wallet;
			$wallet->name = $request->student_id.':example.com';
			$wallet->wallet = $eos['wallet'];
			$wallet->save();

			return response()->json([
				'wallet' => $eos['wallet'],
				'public_key' => $eos['public'],
				'private_key' => $eos['private'],
			], 200);

		} else {
			
			return response()->json([
				'success' => 'false',
			], 503);

		}
	}

	public function update(Request $request)
	{

		$wallet = new Wallet;
		$wallet->name = $request->student_id.':example.com';
		$wallet->wallet = $request->wallet;
		$wallet->save();

		return response()->json([
			'status' => 'success',
		], 200);

	}

	public function balance(Request $request)
	{

		$student = $request->student_id . ':' . $request->lms_url;

		$wallets = Wallet::where('name', $student)->get();

		$balance = '0.0000';

		foreach ($wallets as $data) {

			$transactions = Transaction::select('amount')
				->where('wallet_id', $data->id)
				->where('host_id', 1)
				->sum('amount');

			$balance += $transactions;
		}

		return response()->json([
			'balance' => number_format($balance, 4, '.', '') . ' EDE',
		], 200);

	}

}
