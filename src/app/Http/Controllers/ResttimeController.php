<?php

namespace App\Http\Controllers;

use App\Models\Resttime;
use App\Models\Worktime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ResttimeController extends Controller
{
    /**
     * 休憩開始ボタンの打刻 
     */
    public function punchIn()
    {
        /**
         * ⓵認証済みユーザの最新の休憩レコードを参照する変数$restTimestampを定義する
         * ⓶認証済みユーザの最新の勤怠レコードを参照する変数$workTimestampを定義する
         */
        $user = Auth::user();
        $restTimestamp = Resttime::where('worktime_id', $user->id)->latest()->orderBy('id', 'DESC')->first();
        $workTimestamp = Worktime::where('user_id', $user->id)->latest()->orderBy('id', 'DESC')->first();

        /**
         * 本日の日付を$newTimestampDayと定義する（後ほど日付の比較に使用） 
         */
        $newTimestampDay = Carbon::today();


        /**＜A.初回で休憩開始ボタンを押下した時の条件＞
         * ⓵$workTimestampに勤務開始日のデータが入っている場合は、勤務開始日を$workTimestampDayと定義してB.の処理へ進む
         * ⓶$workTimestampに勤務開始日のデータが無い場合(一番初期はこの状態)は、勤務開始の未打刻メッセージを表示させて
         * 処理終了(勤務開始していない場合は休憩開始できないように制御している)
         */
        if ($workTimestamp) {
            $workTimestampPunchIn = new Carbon($workTimestamp->work_day);
            $workTimestampDay = $workTimestampPunchIn->startOfDay();
        } else {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');
        }

        /**＜B.日を跨いで休憩開始ボタンを押下した時の条件＞
         * 最新レコードの勤務開始日と本日の日付が異なっている場合(つまり本日付の勤務開始レコードがまだ無い場合)は、
         * 勤務開始の未打刻のメッセージを表示させて処理終了(勤務開始していない場合は休憩開始できないように制御している)
         * ※勤務開始日と本日の日付が同日の場合はC.の処理へ進む
         */
        if (($workTimestampDay != $newTimestampDay)) {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');
        }

        /**＜C.勤務終了ボタンを押下した後に休憩開始ボタンを押下した時の条件＞
         * 勤務終了時間のデータが既に入っている場合は、勤務終了の打刻済みメッセージを表示させて処理終了
         * ※勤務終了時間のデータが入っていない場合はD.の処理へ進む
         */
        if (!empty($workTimestamp->work_end_at)) {
            return redirect()->back()->with('error', '既に勤務終了の打刻がされています')->with('work_ended', '*');
        }

        /**＜D.勤務開始後、初めて休憩開始ボタンを押下した後の条件＞
         * ⓵$restTimestampに休憩開始日のデータが入っている場合は、休憩開始日を$restTimestampDayと定義してE.の処理へ進む
         * ⓶$restTimestampに休憩開始日のデータが無い場合(一番初期はこの状態)は、勤務ID、勤務日、休憩開始時間のデータを
         * Resttimeテーブルに新規レコードで格納し、休憩開始の打刻メッセージを表示させて処理終了
         * ※勤務開始後、二回目以降に休憩開始ボタンを押下する場合はE.の処理へ進む
         */
        if ($restTimestamp) {
            $restTimestampPunchIn = new Carbon($restTimestamp->work_day);
            $restTimestampDay = $restTimestampPunchIn->startOfDay();
        } else {
            Resttime::create([
                'worktime_id' => $user->id,
                'work_day' => Carbon::today(),
                'rest_start_at' => Carbon::now()
            ]);
            return redirect()->back()->with('my_status', '休憩開始の打刻が完了しました')->with('rest_started', '*');
        }

        /**＜E.勤務開始後、二回目以降に休憩開始ボタンを押下した時の条件＞
         * ⓵$restTimestampDay(休憩開始日)と$newTimestampDay(本日)が同日で、かつ休憩終了時間のデータが入っていない場合
         * 　(つまり同日に休憩終了ボタンを押していないのに休憩開始ボタンを再度押した場合)は、
         * 　休憩開始の打刻済みメッセージを表示させて処理終了
         * ⓶上記の⓵に当てはまらない場合(例：$restTimestampDay(休憩開始日)と$newTimestampDay(本日)が同日で、
         * 　かつ休憩終了時間のデータが既に入っている場合)は、勤務ID、勤務日、休憩開始時間のデータをResttimeテーブルに
         * 　新規レコードで格納し、休憩開始の打刻メッセージを表示させて処理終了
         * 　※「機能一覧」に記載の "一日に何度も休憩が可能" にはこの処理で対応している
         */
        if (($restTimestampDay == $newTimestampDay) && (empty($restTimestamp->rest_end_at))) {
            return redirect()->back()->with('error', '既に休憩開始の打刻がされています')->with('rest_started', '*');
        } else {
            Resttime::create([
                'worktime_id' => $user->id,
                'work_day' => Carbon::today(),
                'rest_start_at' => Carbon::now()
            ]);
            return redirect()->back()->with('my_status', '休憩開始の打刻が完了しました')->with('rest_started', '*');
        }

        /**
         * 上記のいずれの条件にも当てはまらなかった場合、予期せぬエラーメッセージを表示させて処理終了 
         */
        return redirect()->back()->with('error', '予期せぬエラーが発生しました。システム管理者にお問い合わせ下さい');
    }



    /**
     * 休憩終了ボタンの打刻 
     */
    public function punchOut()
    {
        /**
         * ⓵認証済みユーザの最新の休憩レコードを参照する変数$restTimestampを定義する
         * ⓶認証済みユーザの最新の勤怠レコードを参照する変数$workTimestampを定義する
         */
        $user = Auth::user();
        $restTimestamp = Resttime::where('worktime_id', $user->id)->latest()->orderBy('id', 'DESC')->first();
        $workTimestamp = Worktime::where('user_id', $user->id)->latest()->orderBy('id', 'DESC')->first();

        /**
         * 本日の日付を$newTimestampDayと定義する（後ほど日付の比較に使用） 
         */
        $newTimestampDay = Carbon::today();


        /**＜A.初回で休憩終了ボタンを押下した時の条件＞
         * ⓵$workTimestampに勤務開始日のデータが入っている場合は、勤務開始日を$workTimestampDayと定義してB.の処理へ進む
         * ⓶$workTimestampに勤務開始日のデータが無い場合(一番初期はこの状態)は、勤務開始の未打刻メッセージを表示させて
         * 処理終了(勤務開始していない場合は休憩終了できないように制御している)
         */
        if ($workTimestamp) {
            $workTimestampPunchIn = new Carbon($workTimestamp->work_day);
            $workTimestampDay = $workTimestampPunchIn->startOfDay();
        } else {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');
        }

        /**＜B.日を跨いで休憩終了ボタンを押下した時の条件＞
         * 最新レコードの勤務開始日と本日の日付が異なっている場合(つまり本日付の勤務開始レコードがまだ無い場合)は、
         * 勤務開始の未打刻のメッセージを表示させて処理終了(勤務開始していない場合は休憩終了できないように制御している)
         * ※勤務開始日と本日の日付が同日の場合はC.の処理へ進む
         */
        if (($workTimestampDay != $newTimestampDay)) {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');         
        }

        /**＜C.勤務終了ボタンを押下した後に休憩終了ボタンを押下した時の条件＞
         * 勤務終了時間のデータが既に入っている場合は、勤務終了の打刻済みメッセージを表示させて処理終了
         * ※勤務終了時間のデータが入っていない場合はD.の処理へ進む
         */
        if (!empty($workTimestamp->work_end_at)) {
            return redirect()->back()->with('error', '既に勤務終了の打刻がされています')->with('work_ended', '*');
        }

        /**＜D.休憩終了ボタンを押下した時の条件＞
         * ⓵$restTimestampに休憩開始日のデータが入っている場合は、休憩開始日を$restTimestampDayと定義してE.の処理へ進む
         * ⓶$restTimestampに休憩開始日のデータが無い場合(一番初期はこの状態)は、休憩開始の未打刻メッセージを表示させて
         * 　処理終了(休憩開始していない場合は休憩終了できないように制御している)
         * ※勤務開始後、二回目以降に休憩開始ボタンを押下する場合はE.の処理へ進む
         */
        if ($restTimestamp) {
            $restTimestampPunchOut = new Carbon($restTimestamp->work_day);
            $restTimestampDay = $restTimestampPunchOut->startOfDay();
        } else {
            return redirect()->back()->with('error', 'まだ休憩開始の打刻がされていません')->with('before_rest_start', '*');
        }

        /**＜E.日を跨いで休憩終了ボタンを押下した時の条件＞
         * 最新レコードの休憩開始日と本日の日付が異なっている場合(つまり本日付の休憩開始レコードがまだ無い場合)は、
         * 休憩開始の未打刻のメッセージを表示させて処理終了(休憩開始していない場合は休憩開始できないように制御している)
         * ※例えば、前日に休憩開始ボタンを押した後、休憩終了ボタン(＋勤務終了ボタン)を押し忘れた状態で翌日に休憩終了ボタンを押しても、
         * 前日の休憩終了時間に本日付の休憩終了時間が入らないようにしている(休憩時間が日を跨いでしまうのを防ぐため)
         * ※勤務開始日と本日の日付が同日の場合はF.の処理へ進む
         */
        if (($restTimestampDay != $newTimestampDay)) {
            return redirect()->back()->with('error', 'まだ休憩開始の打刻がされていません')->with('before_rest_start', '*');
        }

        /**＜F.休憩終了ボタンを押下した時のその他の条件＞
         * ⓵休憩終了時間のレコードが既に入っている場合は、休憩終了の打刻済みメッセージを表示させて処理終了
         * ⓶上記の⓵に当てはまらない場合(例：休憩終了時間のデータがまだ入っていない場合)は、
         * 　休憩終了時間、休憩時間。合計休憩時間のデータをResttimeテーブルに格納してレコードを更新し、
         * 　休憩終了の打刻メッセージを表示させて処理終了
         * 　※この後に再度休憩開始ボタンを押すと、休憩開始ボタンの条件分岐E.⓶の処理により
         * 　　新規で休憩開始レコードが作成される
         * 　※「機能一覧」に記載の "一日に何度も休憩が可能" にはこの処理で対応している
         */
        if (!empty($restTimestamp->rest_end_at)) {
            return redirect()->back()->with('error', '既に休憩終了の打刻がされています')->with('rest_ended', '*');
        } else {
            $now = new Carbon();
            $punchIn = new Carbon($restTimestamp->rest_start_at);
            $restTime = $punchIn->diffInSeconds($now);            

            $restTimestamp->update([
                'rest_end_at' => Carbon::now(),
                'rest_time' => gmdate("H:i:s", $restTime),           
            ]);

            $totalRestTime =
            Resttime::where('worktime_id', $user->id)->select('worktime_id', 'work_day', DB::raw('SUM(rest_time) as total_rest_time'))
            ->groupBy('worktime_id', 'work_day')->orderBy('work_day', 'asc')->where('work_day', $workTimestamp->work_day)->first();
         
            $workTimestamp->update([                
                'total_rest_time' => $totalRestTime->total_rest_time
             ]);  
                    

            return redirect()->back()->with('my_status', '休憩終了の打刻が完了しました')->with('rest_ended', '*');
        }

        /**
         * 上記のいずれの条件にも当てはまらなかった場合、予期せぬエラーメッセージを表示させて処理終了 
         */
        return redirect()->back()->with('error', '予期せぬエラーが発生しました。システム管理者にお問い合わせ下さい');
        
    }
}