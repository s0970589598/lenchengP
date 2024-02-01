<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Mail\ContactFormMail; // 假設你有一個Mail類別在App\Mail命名空間下

class ContactController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'message' => $request->input('message'),
        ];
        Log::info($data);
        try {
            Mail::to('st92308@gmail.com')->send(new ContactFormMail($data));
            // return redirect()->route('success'); // 導向到成功頁面
            return response()->json(['message' => 'success']);

        } catch (\Exception $e) {
            // 處理發送郵件失敗的情況
            Log::error($e->getMessage());

            // return back()->withErrors(['error' => '郵件發送失敗']);
            return response()->json(['message' => $e->getMessage()]);

        }
    }
}
