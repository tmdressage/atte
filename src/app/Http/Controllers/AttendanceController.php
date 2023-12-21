<?php

namespace App\Http\Controllers;

use App\Models\Worktime;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Request;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        //日付ページネーション作成
        $dates =
            Worktime::select(DB::raw('DATE(work_day) as date'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->paginate(1, ["*"], 'date-page')
            ->appends(["user-page" => $request->input('user-page')]);
            
        //ユーザの勤怠情報ページネーション作成
        $users =
            User::join('worktimes', 'users.id', 'user_id')
            ->orderBy('user_id', 'asc')
            ->whereIn('worktimes.work_day', $dates)
            ->paginate(5, ["*"], 'user-page')
            ->appends(["date-page" => $request->input('date-page')]);

        return view('attendance', ['dates' => $dates], ['users' => $users]);
    }
}
