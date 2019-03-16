<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Administrator;
use App\Reward;
use App\User;
use App\Winner;
use Auth;
use DB;
use Datatables;
use Diamond;
use History;
use Response;
use Session;
use stdClass;

class RewardController extends Controller
{
    /**
     * Only administartor authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'admins';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins', ['except' => 'getUnauthorize']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index() {
        $rewards = Reward::orderBy('created_at', 'desc')
            ->get();

        return view('admin.reward.index', [
            'rewards' => $rewards
        ]);
    }

    public function show($id) {
        $reward = Reward::find($id);

        return view('admin.reward.show', [
            'reward' => $reward
        ]);
    }

    public function destroy($id) {
        $reward = Reward::find($id);
        
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบการจับรางวัลวันที่ ' . Diamond::parse($reward->created_at)->thai_format('j F Y'));

        $reward->delete();

        return redirect()->action('Admin\RewardController@index')
            ->with('flash_message', 'ลบข้อมูลการจับรางวัลเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getSlotmachine() {
        $session_id = Session::getId();

        if (Reward::where('session', $session_id)->count() == 0) {
            $reward = new Reward();
            $reward->session = $session_id;

            $admin = Administrator::find(Auth::guard($this->guard)->id());
            $admin->rewards()->save($reward);

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'สร้างการจับรางวัลวันที่ ' . Diamond::today()->thai_format('j F Y'));
        }

        return view('admin.reward.slotmachine');
    }

    public function postWinners() {
        $session_id = Session::getId();

        $winners = DB::table('rewards')
            ->join('winners', 'rewards.id', '=', 'winners.reward_id')
            ->join('members', 'winners.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('rewards.session', $session_id)
            ->orderBy('winners.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-smile-o\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("IF(winners.status = 0, '<span class=\"label label-danger\">สละสิทธิ์</span>', '<span class=\"label label-primary\">รับรางวัล</span>') as status")
            ]);

        return Datatables::queryBuilder($winners)->make(true);
    }

    public function postShuffle() {
        // เลือกห้อง
        $session_id = Session::getId();

        // เลือก user ที่ลงทะเบียนแล้ว และยังไม่ได้รางวัล
        $users = User::where('users.confirmed', true)
            ->whereNull('users.deleted_at')
            ->whereNotIn('users.member_id', Winner::join('rewards', 'winners.reward_id', '=', 'rewards.id')
                ->where('rewards.session', $session_id)->pluck('winners.member_id'))
            ->get();

        // สุ่ม user ผู้โชคดี
        $user = $users->random();
        $code = str_split($user->member->memberCode);

        // ส่งรหัสและชื่อไปแสดงผล
        $member = new stdClass();
        $member->member_id = $user->member->id;
        $member->member_name = $user->member->profile->fullname;

        return Response::json($member);
    }

    public function postSavewinner(Request $request) {
        $session_id = Session::getId();
        
        $winner = new Winner();
        $winner->member_id = $request->input('member_id');
        $winner->status = $request->input('status') === 'true';

        $reward = Reward::where('session', $session_id)->first();
        $reward->winners()->save($winner);

        $winners = DB::table('rewards')
            ->join('winners', 'rewards.id', '=', 'winners.reward_id')
            ->join('members', 'winners.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('rewards.session', $session_id)
            ->orderBy('winners.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-smile-o\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("IF(winners.status = 0, '<span class=\"label label-danger\">สละสิทธิ์</span>', '<span class=\"label label-primary\">รับรางวัล</span>') as status")
            ]);

        return Datatables::queryBuilder($winners)->make(true);
    }
}
