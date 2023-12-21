<?php

namespace App\Http\Controllers;

use App\Models\Resttime;
use App\Models\Worktime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorktimeController extends Controller
{
    /**
     * 勤務開始ボタンの打刻 
     */
    public function punchIn()
    {
        /**
         * 認証済みユーザの最新の勤怠レコードを参照する変数$workTimestampを定義する 
         */
        $user = Auth::user();
        $workTimestamp = Worktime::where('user_id', $user->id)->latest()->orderBy('id', 'DESC')->first();

        /**
         * 本日の日付を$newTimestampDayと定義する（後ほど日付の比較に使用） 
         */
        $newTimestampDay = Carbon::today();

        /**＜A.初回で勤務開始ボタンを押下した時の条件＞
         * ⓵$workTimestampに勤務開始日のデータが入っている場合は、勤務開始日を$workTimestampDayと定義してB.の処理へ進む
         * ⓶$workTimestampに勤務開始日のデータが無い場合(一番初期はこの状態)は、ユーザID、勤務日、勤務開始時間のデータを
         * 　Worktimeテーブルに新規レコードで格納し、勤務開始の打刻メッセージを表示させて処理終了
         */
        if ($workTimestamp) {
            $workTimestampPunchIn = new Carbon($workTimestamp->work_day);
            $workTimestampDay = $workTimestampPunchIn->startOfDay();
        } else {
            Worktime::create([
                'user_id' => $user->id,
                'work_day' => Carbon::today(),
                'work_start_at' => Carbon::now()
            ]);
            return redirect()->back()->with('my_status', '勤務開始の打刻が完了しました')->with('work_started', '*');
        }

        /**＜B.二回目以降に勤務開始ボタンを押下した時の条件＞
         * ⓵$workTimestampDay(勤務開始日)と$newTimestampDay(本日)が同日で、かつ勤務終了時間のデータが入っていない場合
         * 　(つまり同日に勤務終了ボタンを押していないのに勤務開始ボタンを再度押した場合)は、
         * 　勤務開始の打刻済みメッセージを表示させて処理終了
         * ⓶$workTimestampDay(勤務開始日)と$newTimestampDay(本日)が同日で、かつ勤務終了時間のデータが既に入っている場合
         * 　(つまり同日に勤務終了ボタンを押した後に勤務開始ボタンを再度押した場合)は、勤務終了の打刻済みメッセージを
         * 　表示させて処理終了
         * ⓷上記の⓵、⓶に当てはまらない場合(例：$workTimestampDay(勤務開始日)と$newTimestampDay(本日)が異なる場合など)は、
         * 　ユーザID、勤務日、勤務開始時間のデータをWorktimeテーブルに新規レコードで格納し、
         * 　勤務開始の打刻メッセージを表示させて処理終了
         * 　※「機能一覧」に記載の "日を跨いだ時点で翌日の出勤操作に切り替える" にはこの処理で対応している
         */
        if (($workTimestampDay == $newTimestampDay) && (empty($workTimestamp->work_end_at))) {
            return redirect()->back()->with('error', '既に勤務開始の打刻がされています')->with('work_started', '*');
        } elseif (($workTimestampDay == $newTimestampDay) && (!empty($workTimestamp->work_end_at))) {
            return redirect()->back()->with('error', '既に勤務終了の打刻がされています')->with('work_ended', '*');
        } else {
            Worktime::create([
                'user_id' => $user->id,
                'work_day' => Carbon::today(),
                'work_start_at' => Carbon::now()
            ]);
            return redirect()->back()->with('my_status', '勤務開始の打刻が完了しました')->with('work_started', '*');
        }

        /**
         * 上記のいずれの条件にも当てはまらなかった場合、予期せぬエラーメッセージを表示させて処理終了 
         */
        return redirect()->back()->with('error', '予期せぬエラーが発生しました。システム管理者にお問い合わせ下さい');
    }



    /**
     * 勤務終了ボタンの打刻 
     */
    public function punchOut()
    {
        /**
         * ⓵認証済みユーザの最新の勤怠レコードを参照する変数$workTimestampを定義する
         * ⓶認証済みユーザの最新の休憩レコードを参照する変数$restTimestampを定義する
         */
        $user = Auth::user();
        $workTimestamp = Worktime::where('user_id', $user->id)->latest()->orderBy('id', 'DESC')->first();
        $restTimestamp = Resttime::where('worktime_id', $user->id)->latest()->orderBy('id', 'DESC')->first();

        /**
         * 本日の日付を$newTimestampDayと定義する（後ほど日付の比較に使用） 
         */
        $newTimestampDay = Carbon::today();

        /**＜A.初回で勤務終了ボタンを押下した時の条件＞
         * ⓵$workTimestampに勤務開始日のデータが入っている場合は、勤務開始日を$workTimestampDayと定義してB.の処理へ進む
         * ⓶$workTimestampに勤務開始日のデータが無い場合(一番初期はこの状態)は、勤務開始の未打刻メッセージを表示させて
         * 　処理終了(勤務開始していない場合は勤務終了できないように制御している)
         */
        if ($workTimestamp) {
            $workTimestampPunchOut = new Carbon($workTimestamp->work_day);
            $workTimestampDay = $workTimestampPunchOut->startOfDay();
        } else {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');
        }

        /**＜B.日を跨いだ状態で勤務終了ボタンを押下した時の条件＞
         * 最新レコードの勤務開始日と本日の日付が異なっている場合(つまり本日付の勤務開始レコードがまだ無い場合)は、
         * 勤務開始の未打刻のメッセージを表示させて処理終了(勤務開始していない場合は勤務終了できないように制御している)
         * ※「機能一覧」に記載の "日を跨いだ時点で翌日の出勤操作に切り替える" にはこの処理で対応している
         * 　 例えば、前日に勤務終了ボタンを押し忘れた状態で翌日に勤務終了ボタンを押しても、
         * 　 前日の勤務終了時間に本日付の勤務終了時間が入らないようにしている(勤務時間が日を跨いでしまうのを防ぐため)
         * ※勤務開始日と本日の日付が同日の場合はC.の処理へ進む
         */
        if (($workTimestampDay != $newTimestampDay)) {
            return redirect()->back()->with('error', 'まだ勤務開始の打刻がされていません')->with('before_work_start', '*');
        }

        /**＜C.勤務終了ボタンを押下した時のその他の条件＞
         * ⓵休憩開始時間にデータが入っており、かつ休憩終了時間にデータが入っていない場合
         * 　(つまり休憩開始後、休憩終了ボタンを押していないのに勤務終了ボタンを押した場合など)は、
         * 　休憩終了の未打刻メッセージを表示させて処理終了
         * 　※休憩終了ボタンを押さないと勤務終了ボタンが押せないようにしている
         * ⓶勤務終了時間に既にデータが入っている場合は、勤務終了の打刻済みメッセージを表示させて処理終了
         * ⓷合計休憩時間のデータがある場合(休憩している場合)は、
         * 　勤務終了時間、勤務時間、合計勤務時間のデータをWorktimeテーブルに格納して
         * 　レコードを更新し、勤務終了の打刻メッセージを表示させて処理終了
         * 　なお、合計勤務時間は勤務終了時間－勤務開始時間－合計休憩時間で算出        　
         * ⓸上記の⓵、⓶、⓷に当てはまらない場合(例：休憩していない状態で勤務終了ボタンを押した場合など）は、
         * 　勤務終了時間、勤務時間、合計勤務時間、合計休憩時間のデータを
         * 　Worktimeテーブルに格納してレコードを更新し、勤務終了の打刻メッセージを表示させて処理終了
         * 　なお、休憩をしていないため、勤務時間と合計勤務時間には同じ時分秒を格納し、
         * 　合計休憩時間には00:00:00を格納する     
         */
        if ((!empty($restTimestamp->rest_start_at)) && (empty($restTimestamp->rest_end_at))) {
            return redirect()->back()->with('error', 'まだ休憩終了の打刻がされていません')->with('rest_started', '*');
        } elseif (!empty($workTimestamp->work_end_at)) {
            return redirect()->back()->with('error', '既に勤務終了の打刻がされています')->with('work_ended', '*');
        } elseif ($workTimestamp->total_rest_time) {

            $now = new Carbon();
            $punchIn = new Carbon($workTimestamp->work_start_at);
            $workTime = $punchIn->diffInSeconds($now);

            $workTimestamp->update([
                'work_end_at' => Carbon::now(),
                'work_time' => gmdate("H:i:s", $workTime),
            ]);

            $totalWorkTime =
                Worktime::where('user_id', $user->id)
                ->latest()->orderBy('work_day', 'desc')
                ->where('work_day', $workTimestamp->work_day)
                ->select(DB::raw('*, (work_time - total_rest_time) AS total_work_time'))
                ->first();

            $workTimestamp->update([
                'total_work_time' => $totalWorkTime->total_work_time
            ]);

            return redirect()->back()->with('my_status', '勤務終了の打刻が完了しました')->with('work_ended', '*');
        } else {
            $now = new Carbon();
            $punchIn = new Carbon($workTimestamp->work_start_at);
            $workTime = $punchIn->diffInSeconds($now);

            $workTimestamp->update([
                'work_end_at' => Carbon::now(),
                'work_time' => gmdate("H:i:s", $workTime),
                'total_work_time' => gmdate("H:i:s", $workTime),
                'total_rest_time' => '00:00:00'
            ]);

            return redirect()->back()->with('my_status', '勤務終了の打刻が完了しました')->with('work_ended', '*');
        }


        /**
         * 上記のいずれの条件にも当てはまらなかった場合、予期せぬエラーメッセージを表示させて処理終了 
         */
        return redirect()->back()->with('error', '予期せぬエラーが発生しました。システム管理者にお問い合わせ下さい');
    }
}
