<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\EosController;
use App\Transaction;
use App\Wallet;
use App\Host;

class TransactionController extends Controller
{
	public function view(Request $request)
	{

		$student = $request->student_id . ':' . $request->lms_url;

		$wallets = Wallet::where('name', $student)->get();

		$arr = [];

		foreach ($wallets as $data) {

			$transactions = Transaction::select([
					'transaction_id',
					'status',
					'created_at as date',
					'host_id as lms_id',
					'event_type',
					'event_detail',
					'course_id',
					'amount'])
				->where('wallet_id', $data->id)
				->where('host_id', 1)
				->get();

			$arr[$data->wallet] = $transactions;
		}

		return response()->json($arr, 200);

	}

	public function store(Request $request)
	{
		
		$wallet = Wallet::where('name', $request->student_id)
			->orderBy('id', 'desc')
			->first();

		if (empty($wallet)) {

			$wallet = new Wallet;
			$wallet->name = $request->student_id;
			$wallet->wallet = '';
			$wallet->save();

		}

		$asset = '1.0000 EDE';

		$eos = EosController::transfer('edeos', $wallet->wallet, $asset, 'memo', 'edeos');

		$transaction = new Transaction;
		$transaction->transaction_id = ($eos->transaction_id) ? $eos->transaction_id : '0';
		$transaction->host_id = 1;
		$transaction->status = ($eos->transaction_id) ? '1' : '0';
		$transaction->course_id = $request->course_id;
		$transaction->event_type = $request->event_type;
		$transaction->event_detail = json_encode($request->event_details);
		$transaction->amount = '1.0000';

		$wallet->transactions()->save($transaction);

		return response()->json(['status' => 'ok'], 200);

	}
}
