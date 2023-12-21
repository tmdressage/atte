<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class EmailVerificationController extends Controller
{
    /**
     * 確認メール送信画面
     */
    public function verify(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email');
    }

    /**
     * 確認メール送信
     */
    public function notification(Request $request)
    {
        $user = $request->user();

        // メール確認済みの場合は打刻ページへ
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // 確認メール送信
        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('my_status', 'verification-link-sent');
    }

    /**
     * 確認メールリンクの検証
     */
    public function verification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        // email_verified_atカラムに認証日時を入力
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1')->with('my_status', 'ログインしました');
    }
}
