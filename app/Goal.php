<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable =[
    'user_id',
    'goal_name',
    'goal_time',
    'status'
    ];

    const DONE = 0;
    const UNDONE = 1;

    public function setGoalTimeAttribute(int $value)
    {
        //goal_timeをセットするときは自動的に60をかける
        $this->attributes['goal_time'] = $value*60;
    }

    public function getGoalTimeAttribute($value)
    {
        // 分時間でデータベースに保存されているゴールタイムを変換して返す
        $result = intdiv($value, 60);
        return $result;
    }


    public function getAllGoal($user_id)
    {
        // ユーザーが持つゴールを未達成のものから抜き出す
        $result = Goal::where('user_id', $user_id) ->orderBy('status', 'desc')->get();
        return $result;
    }


    public function getTimeRecord()
    {
        // ゴールがもつタイムレコードを合計して返す
        $goal_id = $this->id;
        $records =  TimeRecord::where('goal_id', $goal_id)->get();
        $result = 0;
        foreach ($records as $record) {
            $result += $record->time_record;
        }
        $result = convertTime($result);
        return $result;
    }

    public function getAddTime()
    {
        //ゴールがもつ残り時間を返す
        $goal_time = $this->goal_time*60;
        $goal_id = $this->id;
        $records =  TimeRecord::where('goal_id', $goal_id)->get();
        $time_record = 0;
        foreach ($records as $record) {
            $time_record += $record->time_record;
        }
        $result = $goal_time - $time_record;
        $result = convertTime($result);
        return $result;
    }

    public function records()
    {
        return $this->hasMany('App\TimeRecord');
    }

    public function checkStatus()
    {
        //statusのチェック，変更
        $addTime = $this->getAddTime();
        if ($addTime <0 && $this->status == Goal::UNDONE) {
            $this->status = Goal::DONE;
            $this->save();
        } elseif ($addTime >0 && $this->status == Goal::DONE) {
            $this->status = Goal::UNDONE;
            $this->save();
        }
    }
}
