<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Wallet;

class WalletController extends Controller
{

	public function store(Request $request)
	{

		$student = $request->student_id . ':example.com';

		$wallet = new Wallet;
		$wallet->name = $student;
		$wallet->wallet = 'wallet';
		$wallet->save();

		return response()->json([
			'wallet' => 'wallet',
			'public_key' => 'public key',
			'private_key' => 'private key',
		], 200);

	}

	public function update(Request $request)
	{

		$wallet = new Wallet;
		$wallet->name = $request->student_id;
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
			'balance' => number_format($balance, 4, '.', '') . ' EOS',
		], 200);

	}

}
