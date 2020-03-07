<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Administrator;
use App\Member;
use App\User;
use App\Reward;
use App\RewardConfig;
use App\RewardWinner;
use Auth;
use DB;
use Datatables;
use Diamond;
use History;
use Response;
use Session;
use Validator;
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

    public function create() {
        return view('admin.reward.create');
    }

    public function store(Request $request) {
        $rules = [
            'rewardConfigs.*.price' => 'required|numeric|min:1',
            'rewardConfigs.*.amount' => 'required|numeric|min:1'
        ];

        $attributeNames = [
            'rewardConfigs.*.price' => 'เงินรางวัล',
            'rewardConfigs.*.amount' => 'จำนวนรางวัล',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $configs = collect($request->input('rewardConfigs'));
        $validator->after(function($validator) use ($configs) {
            $configs->each(function ($item, $key) use ($validator) {
                if (!empty($item['register']) && !empty($item['special'])) {
                    if ($item['register'] && $item['special']) {
                        $validator->errors()->add('impossible', 'การลงทะเบียนและรางวัลพิเศษไม่สามารถใช้พร้อมกันได้');
                    }
                }
            });
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $session_id = Session::getId();

                if (Reward::where('session', $session_id)->count() == 0) {
                    $reward = new Reward();
                    $reward->session = $session_id;
                    $reward->reward_status_id = 2;

                    $admin = Administrator::find(Auth::guard($this->guard)->id());
                    $admin->rewards()->save($reward);

                    foreach ($request->input('rewardConfigs') as $item) {
                        $config = new RewardConfig();
                        $config->price = $item['price'];
                        $config->amount = $item['amount'];
                        $config->register = !empty($item['register']);
                        $config->special = !empty($item['special']);
                        $reward->rewardConfigs()->save($config);
                    }
        
                    History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'สร้างการจับรางวัลวันที่ ' . Diamond::today()->thai_format('j F Y'));
                }
            });

            return redirect()->action('Admin\RewardController@index')
                ->with('flash_message', 'สร้างการจับรางวัลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        $reward = Reward::find($id);

        return view('admin.reward.show', [
            'reward' => $reward
        ]);
    }

    public function edit($id) {
        $reward = Reward::find($id);

        return view('admin.reward.edit', [
            'reward' => $reward
        ]);
    }

    public function update($id, Request $request) {
        $rules = [
            'rewardConfigs.*.price' => 'required|numeric|min:1',
            'rewardConfigs.*.amount' => 'required|numeric|min:1'
        ];

        $attributeNames = [
            'rewardConfigs.*.price' => 'เงินรางวัล',
            'rewardConfigs.*.amount' => 'จำนวนรางวัล',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);
        
        $configs = collect($request->input('rewardConfigs'));
        $validator->after(function($validator) use ($configs) {
            $configs->each(function ($item, $key) use ($validator) {
                if (!empty($item['register']) && !empty($item['special'])) {
                    if ($item['register'] && $item['special']) {
                        $validator->errors()->add('impossible', 'การลงทะเบียนและรางวัลพิเศษไม่สามารถใช้พร้อมกันได้');
                    }
                }
            });
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $reward = Reward::find($id);
                $reward->rewardConfigs()->delete();
                $reward->save();

                foreach ($request->input('rewardConfigs') as $item) {
                    $config = new RewardConfig();
                    $config->price = $item['price'];
                    $config->amount = $item['amount'];
                    $config->register = !empty($item['register']);
                    $config->special = !empty($item['special']);
                    $reward->rewardConfigs()->save($config);
                }
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขการจับรางวัลวันที่ ' . Diamond::today()->thai_format('j F Y'));
            });

            return redirect()->action('Admin\RewardController@index')
                ->with('flash_message', 'แก้ไขการจับรางวัลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        $reward = Reward::find($id);

        if ($reward->reward_status_id == 4) {
            return redirect()->back()
                ->withErrors([['complete' => 'ไม่อนุญาตให้ลบได้ เนื่องจากการจับรางวัลเสร็จสมบูรณ์แล้ว']]);
        }
        else {
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบการจับรางวัลวันที่ ' . Diamond::parse($reward->created_at)->thai_format('j F Y'));
    
            $reward->delete();
    
            return redirect()->action('Admin\RewardController@index')
                ->with('flash_message', 'ลบข้อมูลการจับรางวัลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getRegister($id) {
        $reward = Reward::find($id);

        return view('admin.reward.register', [
            'reward' => $reward
        ]);
    }

    public function postRegister(Request $request) {
        $members = DB::table('rewards')
            ->join('member_reward', 'rewards.id', '=', 'member_reward.reward_id')
            ->join('members', 'member_reward.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('member_reward.reward_id', $request->input('reward_id'))
            ->orderBy('member_reward.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("CONCAT(DATE_FORMAT(member_reward.created_at, '%e %b '), DATE_FORMAT(member_reward.created_at, '%Y') + 543, DATE_FORMAT(member_reward.created_at, ' เวลา %H:%i')) as register_at"),
                DB::raw("CASE WHEN rewards.reward_status_id < 3 THEN CONCAT('<a href=\"javascript:void(0);\" onclick=\"deleteMember(', members.id, ', \'', profiles.name, ' ', profiles.lastname,'\');\"><i class=\"fa fa-trash\"></a>') ELSE '-' END as action")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }
    
    public function postLate(Request $request) {
        $members = DB::table('rewards')
            ->join('member_reward', 'rewards.id', '=', 'member_reward.reward_id')
            ->join('members', 'member_reward.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('member_reward.reward_id', $request->input('reward_id'))
            ->orderBy('member_reward.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("CONCAT(DATE_FORMAT(member_reward.created_at, '%e %b '), DATE_FORMAT(member_reward.created_at, '%Y') + 543, DATE_FORMAT(member_reward.created_at, ' เวลา %H:%i')) as register_at"),
                DB::raw("CONCAT('<a href=\"javascript:void(0);\" onclick=\"deleteMember(', members.id, ', \'', profiles.name, ' ', profiles.lastname,'\');\"><i class=\"fa fa-trash\"></a>') as action")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }

    public function postCheckmember(Request $request) {
        $member = Member::where('id', $request->input('member_id'))
            ->whereNull('leave_date')
            ->first();

        if (!empty($member)) {
            $result = new stdClass();
            $result->id = $member->id;
            $result->name = $member->profile->fullname;

            return Response::json($result);
        }  
            
        return Response::json(false);
    }

    public function postAddmember(Request $request) {
        $reward = Reward::find($request->input('reward_id'));
        $member = Member::find($request->input('member_id'));

        if ($reward->members->where('id', $member->id)->count() == 0) {
            DB::transaction(function() use ($member, $reward) {
                $reward->members()->attach($member);
            });

            return Response::json(true);
        }

        return Response::json(false);
    }

    public function postDeletemember(Request $request) {
        $reward = Reward::find($request->input('reward_id'));
        $member = Member::find($request->input('member_id'));

        DB::transaction(function() use ($member, $reward) {
            $reward->members()->detach($member);
        });

        return Response::json(true);
    }

    public function postCloseRegister($id) {
        DB::transaction(function() use ($id) {
            $reward = Reward::find($id);
            $reward->reward_status_id = 3;
            $reward->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'ปิดการลงทะเบียน การจับรางวัลวันที่ ' . Diamond::today()->thai_format('j F Y'));
        });

        return redirect()->action('Admin\RewardController@index')
            ->with('flash_message', 'ปิดการลงทะเบียนแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getSlotmachine($id) {
        $reward = Reward::find($id);

        return view('admin.reward.slotmachine', [
            'reward' => $reward
        ]);
    }

    public function postWinners(Request $request) {
        $reward_id = $request->input('reward_id');
        $config_id = $request->input('config_id');

        $winners = DB::table('rewards')
            ->join('reward_configs', 'rewards.id', '=', 'reward_configs.reward_id')
            ->join('reward_winners', 'reward_configs.id', '=', 'reward_winners.reward_config_id')
            ->join('members', 'reward_winners.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('reward_configs.id', $config_id)
            ->orderBy('reward_winners.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-smile-o\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("IF(reward_winners.status = 0, '<span class=\"label label-danger\">สละสิทธิ์</span>', '<span class=\"label label-primary\">รับรางวัล</span>') as status")
            ]);

        return Datatables::queryBuilder($winners)->make(true);
    }

    public function postShuffle(Request $request) {
        // เลือกห้อง
        $reward = Reward::find($request->input('reward_id'));
        $config = RewardConfig::find($request->input('config_id'));

        if ($config->register && !$config->special) {
            // เลือก member ที่ลงทะเบียนหน้างาน ไม่รวมที่ได้รางวัลพิเศษ และยังไม่ได้รางวัล
            $winners = Member::whereNull('members.leave_date')
                ->whereIn('members.id', $reward->members->pluck('id'))
                ->whereNotIn('members.id', RewardWinner::join('reward_configs', 'reward_winners.reward_config_id', '=', 'reward_configs.id')
                    ->join('rewards', 'reward_configs.reward_id', '=', 'rewards.id')
                    ->where('rewards.id', $reward->id)
                    ->where('reward_configs.register', true)
                    ->where('reward_configs.special', false)
                    ->pluck('reward_winners.member_id'))
                ->get();
        }
        else if (!$config->register && !$config->special) {
            // เลือก member ที่ยังไม่ได้รางวัล และไม่รวมที่ได้รางวัลพิเศษ รวมถึงผู้ที่สละสิทธิ์รางวัลอื่นด้วย
            $winners = Member::whereNull('members.leave_date')
                ->whereNotIn('members.id', RewardWinner::join('reward_configs', 'reward_winners.reward_config_id', '=', 'reward_configs.id')
                    ->join('rewards', 'reward_configs.reward_id', '=', 'rewards.id')
                    ->where('rewards.id', $reward->id)
                    ->where('reward_winners.status', true)
                    ->where('reward_configs.special', false)
                    ->pluck('reward_winners.member_id'))
                ->get();
        }
        else if (!$config->register && $config->special) {
            // เลือก user ที่ลงทะเบียนใช้แอปแล้ว และยังไม่ได้รางวัลพิเศษ ซึ่งอาจจะได้รับรางวัลปกติไปแล้วก็ได้
            $winners = User::where('users.confirmed', true)
                ->whereNull('users.deleted_at')
                ->whereNotIn('users.member_id', RewardWinner::join('reward_configs', 'reward_winners.reward_config_id', '=', 'reward_configs.id')
                    ->where('reward_configs.id', $config->id)
                    ->pluck('reward_winners.member_id'))
                ->get();
        }
        else {
            // เป็นไปไม่ได้
            $winners = collect([]);
        }

        if ($winners->count() > 0) {
            // สุ่ม user ผู้โชคดี
            $winner = $winners->random();
            $member = get_class($winner) == "App\User" ? $winner->member : $winner;

            // ส่งรหัสและชื่อไปแสดงผล
            $user = new stdClass();
            $user->member_id = $member->id;
            $user->member_name = $member->profile->fullname;
        }
        else {
            // เกิดข้อผิดพลาด
            $user = new stdClass();
            $user->member_id = 0;
            $user->member_name = '';
        }

        return Response::json($user);
    }

    public function postSavewinner(Request $request) {
        // เลือกห้อง
        $reward = Reward::find($request->input('reward_id'));
        $config = RewardConfig::find($request->input('config_id'));
        
        $winner = new RewardWinner();
        $winner->member_id = $request->input('member_id');
        $winner->status = $request->input('status') === 'true';
        $config->rewardWinners()->save($winner);

        $winners = DB::table('rewards')
            ->join('reward_configs', 'rewards.id', '=', 'reward_configs.reward_id')
            ->join('reward_winners', 'reward_configs.id', '=', 'reward_winners.reward_config_id')
            ->join('members', 'reward_winners.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->where('reward_configs.id', $config->id)
            ->orderBy('reward_winners.created_at', 'desc')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-smile-o\"></i> ', profiles.name, ' ', profiles.lastname, '</span>') as fullname"),
                DB::raw("IF(reward_winners.status = 0, '<span class=\"label label-danger\">สละสิทธิ์</span>', '<span class=\"label label-primary\">รับรางวัล</span>') as status")
            ]);

        return Datatables::queryBuilder($winners)->make(true);
    }

    public function postFinish(Request $request) {
        $reward = Reward::find($request->input('reward_id'));
        $config = RewardConfig::find($request->input('config_id'));
        $result = RewardWinner::where('reward_config_id', $config->id)->where('status', true)->count() >= $config->amount;

        $finish = true;
        foreach ($reward->rewardConfigs as $rewardConfig) {
            $finish = $finish && (RewardWinner::where('reward_config_id', $rewardConfig->id)->where('status', true)->count() >= $rewardConfig->amount);
        }

        if ($finish) {
            DB::transaction(function() use ($reward) {
                $reward->reward_status_id = 4;
                $reward->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'ปิดการจับรางวัลวันที่ ' . Diamond::today()->thai_format('j F Y'));
            });
        }

        return Response::json($result);
    }

    public function getLate($id) {
        $reward = Reward::find($id);

        return view('admin.reward.late', [
            'reward' => $reward
        ]);
    }
}
