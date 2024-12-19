<?php

namespace App\Http\Controllers\Api;


use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->smsService->sendSms(
            $request->phone,
            $request->message
        );

        return response()->json($response);
    }
}
