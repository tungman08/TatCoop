<?php

namespace App\Classes;

use App\AdministratorHistory;
use App\UserHistory;
use App\HistoryType;
use DB;
use stdClass;

class History
{
    public function user($id, $index=0) {
        $history = [];

        $groups = UserHistory::where('user_id', $id)
            ->groupBy(DB::raw('date(created_at)'))
            ->select(DB::raw('date(created_at) as created_at'))
            ->orderBy('created_at', 'desc')
            ->skip($index)
            ->take(1)
            ->get();

        foreach ($groups as $g) {
            $history_date = $g->created_at;
            $history_items = UserHistory::with('history_type')->whereDate('created_at', '=', $g->created_at)->orderBy('created_at')->get();
            
            $item = new stdClass();
            $item->date = $history_date;
            $item->items = $history_items;
            
            $history[] = $item;
        }

        return collect($history);
    }

    public function administrator($id, $index=0) {
        $history = [];

        $groups = AdministratorHistory::where('admin_id', $id)
            ->groupBy(DB::raw('date(created_at)'))
            ->select(DB::raw('date(created_at) as created_at'))
            ->orderBy('created_at', 'desc')
            ->skip($index)
            ->take(1)
            ->get();

        foreach ($groups as $g) {
            $history_date = $g->created_at;
            $history_items = AdministratorHistory::with('history_type')->whereDate('created_at', '=', $g->created_at)->orderBy('created_at')->get();

            $item = new stdClass();
            $item->date = $history_date;
            $item->items = $history_items;

            $history[] = $item;
        }

        return collect($history);
    }

    public function addAdminHistory($id, $type, $description = null) {
        $history = new AdministratorHistory();
        $history->admin_id = $id; 
        $history->history_type_id = $this->history_type($type);
        $history->description = $description;
        $history->save();

        return $history->id;
    }

    public function addUserHistory($id, $type, $description = null) {
        $history = new UserHistory();
        $history->user_id = $id; 
        $history->history_type_id = $this->history_type($type);
        $history->description = $description;
        $history->save();

        return $history->id;
    }

    public function countAdminHistory($id) {
        return AdministratorHistory::where('admin_id', $id)
            ->groupBy(DB::raw('date(created_at)'))
            ->get()
            ->count();
    }

    public function countUserHistory($id) {
        return UserHistory::where('user_id', $id)
            ->groupBy(DB::raw('date(created_at)'))
            ->get()
            ->count();
    }

    protected function history_type($type) {
        $history_type = HistoryType::where('name', $type)->first();

        return ($history_type != null) ? $history_type->id : 0;
    }
}