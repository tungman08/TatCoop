<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Administrator;
use App\Role;
use App\Shareholding;
use Auth;
use DB;
use Diamond;
use History;
use Mail;
use Validator;

class BoardController extends Controller
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
     * Responds to requests to GET /unauthorize
     */
    public function getUnauthorize() {
        return 'unauthorize';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.board.index', [
            'boards' => Administrator::viewer()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.board.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $rules = [
            'name' => 'required', 
            'lastname' => 'required', 
            'email' => 'required|unique:administrators,email',
            'new_password' => 'required|min:6|confirmed', 
            'new_password_confirmation' => 'required|min:6',
        ];

        $attributeNames = [
            'name' => 'ชื่อ',
            'lastname' => 'นามสกุล',
            'email' => 'อีเมลคณะกรรมการ',
            'new_password' => 'รหัสผ่าน',
            'new_password_confirmation' => 'ยืนยันรหัสผ่าน',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['new_password', 'new_password_confirmation']));
        }
        else {
            DB::transaction(function() use ($request) {
                $role = Role::find(3);

                $board = new Administrator();
                $board->name = $request->input('name');
                $board->lastname = $request->input('lastname');
                $board->email = strtolower($request->input('email'));
                $board->password = $request->input('new_password');
                $role->admins()->save();

                History::addAdminHistory($board->id, 'สร้างบัญชีคณะกรรมการ');
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มบัญชี ' . $board->fullname . ' (' . $board->email . ') เป็นคณะกรรมการ');

                Mail::send('admin.emails.newboard', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($board) {
                    $message->to($board->email, $board->fullname)
                        ->subject('คุณได้รับการแต่งตั้งเป็นคณะกรรมการเว็บไซต์ www.tatcoop.com');
                });
            });

            return redirect()->action('Admin\BoardController@index')
                ->with('flash_message', 'เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'')
                ->with('callout_class', 'callout-success');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $board = Administrator::find($id);

        if ($board->role_id != 3) {
            return redirect()->to('admin/board');
        }

        return view('admin.board.edit', [
            'board' => $board
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $rules = [
            'name' => 'required', 
            'lastname' => 'required', 
        ];

        $attributeNames = [
            'name' => 'ชื่อ',
            'lastname' => 'นามสกุล',
            'email' => 'อีเมลคณะกรรมการ',
            'new_password' => 'รหัสผ่าน',
            'new_password_confirmation' => 'ยืนยันรหัสผ่าน',
        ];

        $validator = Validator::make($request->except('email'), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            if (!empty($request->input('new_password'))) {
                if ($request->input('new_password') != $request->input('new_password_confirmation')) {
                    $validator->errors()->add('password_notmatch', 'ข้อมูล รหัสผ่าน ไม่ตรงกัน');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['new_password', 'new_password_confirmation']));
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $board = Administrator::find($id);
                $board->name = $request->input('name');
                $board->lastname = $request->input('lastname');

                if (!empty($request->input('new_password'))) {
                    $board->password = $request->input('new_password');
                    $board->password_changed = false;
                }

                $board->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขบัญชีคณะกรรมการชื่อ ' . $board->fullname . ' (' . $board->email . ')');

                if (!empty($request->input('new_password'))) {
                    Mail::send('admin.emails.updateboard', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($board) {
                        $message->to($board->email, $board->fullname)
                            ->subject('บัญชีคณะกรรมการ www.tatcoop.com ของคุณได้มีการแก้ไขเรียบร้อย');
                    });
                }
            });

            return redirect()->action('Admin\BoardController@index')
                ->with('flash_message', (!empty($request->input('new_password'))) ? 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'' : 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    /**
     * Show the form for delete the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete($id) {
        $board = Administrator::find($id);

        if ($board->role_id != 3) {
            return redirect()->to('admin/board');
        }

        return view('admin.board.delete', [
            'board' => $board
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id) {
        $rules = [
            'email' => 'required', 
        ];

        $attributeNames = [
            'email' => 'บัญชีผู้ใช้',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $id) {
            $board = Administrator::findOrFail($id);   

            if ($board->email != $request->input('email')) {
                $validator->errors()->add('password_notmatch', 'ชื่อบัญชีผู้ใช้ที่กรอกไม่ตรงกับบัญชีผู้ที่ต้องการลบ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            $board = Administrator::findOrFail($id);

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบบัญชีคณะกรรมการชื่อ ' . $board->fullname . ' (' . $board->email . ')');

            $board->delete();

            return redirect()->action('Admin\BoardController@index')
                ->with('flash_message', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว ยกเลิกคำสั่งให้คลิกที่นี่ ')
                ->with('callout_class', 'callout-warning')
                ->with('flash_link', '/admin/board/' . $id . '/undelete');
        }
    }

    /**
     * Show all deleted items in storage.
     *
     * @return Response
     */
    public function getInactive() {
        $boards = Administrator::onlyTrashed()
            ->where('role_id', 3)
            ->get();

        return view('admin.board.inactive', [
            'boards' => $boards
        ]);
    }

    /**
     * Restore a deleted this item.
     *
     * @return Response
     */
    public function postRestore($id) {
        $board = Administrator::withTrashed()->findOrFail($id);
        $board->restore();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'คืนสภาพข้อมูล', 'คืนสภาพบัญชีคณะกรรมการชื่อ ' . $board->fullname . ' (' . $board->email . ')');

        return redirect()->action('Admin\BoardController@index')
            ->with('flash_message', 'คืนค่าบัญชีผู้ใช้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    /**
     * Force delete this item.
     *
     * @return Response
     */
    public function postForceDelete($id) {
        $board = Administrator::withTrashed()->findOrFail($id);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูลอย่างถาวร', 'ลบบัญชีคณะกรรมการชื่อ ' . $board->fullname . ' (' . $board->email . ') ออกจากระบบอย่างถาวร');

        $board->forceDelete();

        return redirect()->action('Admin\BoardController@index')
            ->with('flash_message', 'ลบบัญชีผู้ใช้อย่างถาวรเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-danger');
    }
}
