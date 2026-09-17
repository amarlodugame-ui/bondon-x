<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index()
    {
        return view('theme.support.index', ['pageTitle' => 'সহায়তা']);
    }

    public function faq()
    {
        return view('theme.support.faq', ['pageTitle' => 'সাধারণ প্রশ্ন']);
    }

    public function membership()
    {
        return view('theme.support.membership', ['pageTitle' => 'সদস্যতা']);
    }

    public function returnRefund()
    {
        return view('theme.support.return-refund', ['pageTitle' => 'রিটার্ন ও রিফান্ড']);
    }

    public function shipping()
    {
        return view('theme.support.shipping', ['pageTitle' => 'শিপিং ও ডেলিভারি']);
    }

    public function contact()
    {
        return view('theme.support.contact', ['pageTitle' => 'যোগাযোগ']);
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:191'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $receiver = gs()->email ?? null;
        if (!$receiver) {
            return back()->withInput()->with('error', 'সাপোর্ট ইমেইল এখনো সেট করা হয়নি।');
        }

        try {
            $body = "নাম: {$data['name']}\nইমেইল: {$data['email']}\nমোবাইল: ".($data['mobile'] ?: 'N/A')."\nবিষয়: {$data['subject']}\n\nবার্তা:\n{$data['message']}";
            Mail::raw($body, function ($mail) use ($data, $receiver) {
                $mail->to($receiver)->replyTo($data['email'], $data['name'])->subject('Website Support: '.$data['subject']);
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'বার্তা পাঠানো যায়নি। পরে আবার চেষ্টা করুন।');
        }

        return back()->with('success', 'আপনার বার্তা পাঠানো হয়েছে। আমাদের টিম প্রয়োজন অনুযায়ী যোগাযোগ করবে।');
    }

    public function terms()
    {
        return view('theme.support.terms', ['pageTitle' => 'শর্তাবলী']);
    }

    public function privacy()
    {
        return view('theme.support.privacy', ['pageTitle' => 'গোপনীয়তা নীতি']);
    }
}
