<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

}
