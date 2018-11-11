<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EosController extends Controller
{

	private static function unlock()
	{

		return 'wallet unlock -n mainWallet --password ' . env('EOS_PRIVATE_KEY');

	}

	private static function create_key()
	{

		return 'create key --to-console';

	}

	private static function wallet_import($key)
	{

		return 'wallet import --private-key ' . $key . ' -n mainWallet';

	}

	private static function create_account($name, $key)
	{

		return 'create account edeos ' . $name . '  ' . $key . ' ' . env('EOS_PUBLIC_KEY') . ' -j';

	}

	private static function push_action($from, $to, $asset, $memo,  $owner)
	{

		return 'push action edeos transfer \'["' . $from . '", "' . $to . '", "' . $asset . '", "' . $memo . '"]\' -p ' . $owner . ' -j';

	}

	private static function run($data)
	{

		$cleos = "sudo docker exec eosio /opt/eosio/bin/cleos --url http://127.0.0.1:7777 --wallet-url http://127.0.0.1:5555 ";

		return shell_exec($cleos . $data);

	}

	public static function create($name = null)
	{

		// unlock wallet
		$data = self::unlock($name);
		$output = self::run($data);

		// Create keys
		$data = self::create_key();
		$output = self::run($data);

		$pattern = '/Private key: (?P<private>\w+)\sPublic key: (?P<public>\w+)/';
		preg_match($pattern, $output, $keys);

		// Import keys
		$data = self::wallet_import($keys['private']);
		$import = self::run($data);

		// Create account
		$data = self::create_account($name, $keys['public']);
		$account = json_decode(self::run($data));

		
		if ($account->transaction_id && $account->processed->receipt->status == "executed") {

			$keys['wallet'] = $name;

			// private, public
			return $keys;

		} else return false;
		
		return $keys;
	}

	public static function transfer($from, $to, $asset, $memo, $owner)
	{

		// unlock wallet
		$data = self::unlock();
		$output = self::run($data);

		// transfer
		$data = self::push_action($from, $to, $asset, $memo, $owner);
		$output = json_decode(self::run($data));

		return $output;

	}
}