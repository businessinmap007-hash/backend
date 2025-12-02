<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Api\Transaction\TransactionResource;
use App\Libraries\Main;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{


    public $config;

    public function __construct(Main $config)
    {

        $this->config = $config;
    }


    public function index(Request $request)
    {
        $user = $request->user();

        $this->config->checkFawryOrders($user->id);
        /**
         * @@ User Operations Bu User Id.
         */
        $query = Transaction::whereUserId($user->id)->orderBy('id', 'desc');

        /**
         * @ Check if has operation and filter by them.
         */
        if (isset($request->operationType) && $request->operationType != "")
            $query->where('operation', $request->operationType);

        /**
         * @@ Get all filtered Operations.
         */
        $transactions = $query->get();

        return TransactionResource::collection($transactions);
    }


    public function getUserBalance(Request $request)
    {
        $user = $request->user();

        $this->config->checkFawryOrders($user->id);
        $transactionDeposit = Transaction::whereStatus('deposit')->whereUserId($user->id)->sum('price');
        $transactionWithdrawal = Transaction::whereStatus('withdrawal')->whereUserId($user->id)->sum('price');
        $total = $transactionDeposit > $transactionWithdrawal ? $transactionDeposit - $transactionWithdrawal : 0;
        return response()->json([
            'status' => 200,
            'total' => number_format($total, 2)
        ]);
    }
}
