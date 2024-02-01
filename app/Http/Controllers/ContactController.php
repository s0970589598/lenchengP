<?php
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail; // 假設你有一個Mail類別在App\Mail命名空間下

class ContactController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'message' => $request->input('message'),
        ];

        try {
            Mail::to('recipient@example.com')->send(new ContactFormMail($data));
            return redirect()->route('success'); // 導向到成功頁面
        } catch (\Exception $e) {
            // 處理發送郵件失敗的情況
            return back()->withErrors(['error' => '郵件發送失敗']);
        }
    }
}
