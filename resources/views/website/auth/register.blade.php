@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>ลงทะเบียนเข้าใช้งาน</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['action' => 'Website\AuthController@postRegister', 'method'=>'post', 'role' => 'form']) }}
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password', ['id'=>'password', 'required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password"></span>
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password_confirmation', ['id'=>'password_confirmation', 'required', 'class'=>'form-control', 'placeholder'=>'ยืนยันรหัสผ่าน', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password_confirmation"></span>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-qrcode"></i></span>
                {{ Form::text('citizen_code', null, ['required', 'class'=>'form-control', 'placeholder'=>'เลขประจำตัวประชาชน',
                    'data-inputmask'=>'\'mask\': \'9-9999-99999-99-9\',\'placeholder\':\'0\',\'removeMaskOnSubmit\':true',
                    'data-mask',
                    'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-qrcode"></i></span>
                {{ Form::text('member_id', null, ['required', 'id'=>'member_id', 'class'=>'form-control', 'placeholder'=>'เลขทะเบียนสมาชิกสหกรณ์',
                    'data-inputmask'=>'\'mask\': \'99999\',\'placeholder\':\'0\',\'removeMaskOnSubmit\':true',
                    'data-mask',
                    'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('terms', (1 or true), null, [
                            'onclick'=>'javascript:$("button:submit").attr("disabled", !this.checked);']) }}
                        ยินยอมตาม <a href="#termsModal" data-toggle="modal" data-placement="top">ข้อกำหนด</a>
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-5">
                {{ Form::button('<i class="glyphicon glyphicon-plus margin-r-sm"></i>ลงทะเบียน', ['type' => 'submit',
                    'disabled', 'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- registed flash session data -->
    @if (session('registed'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>ลงทะเบียนเสร็จสิ้น</h4>
            {{ session('registed') }}
        </div>
    @endif

    <!-- error messages -->
    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>การลงทะเบียนผิดพลาด!</h4>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

    <a href="{{ action('Website\AuthController@getLogin') }}">ลงทะเบียนไว้แล้ว</a><br>
</div>
<!-- /.login-box-body -->

<!-- Terms Modal -->
<div id="termsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">เงื่อนไขการใช้งานระบบ</h4>
            </div>
            <div class="modal-body">
                <p>คุณยอมรับว่าผู้ควบคุมระบบ และผู้ตรวจทานของกระดานข่าวนี้ มีสิทธิ์อ่าน ลบ หรือแก้ไขทุกข้อความ และผู้ควบคุมระบบ ผู้ตรวจทาน ไม่สามารถรับผิดชอบต่อข้อความที่คุณได้แสดงความคิดเห็น (ยกเว้นว่าพวกเขาจะเป็นผู้โพสต์เอง)</p>

                <p>คุณตกลงว่าจะไม่โพสต์ข้อความที่ หยาบคาย, ลามก, ไม่แสดงความเคารพ, หมิ่นประมาท, เป็นที่รังเกียจ, ขู่เข็ญ, ส่อไปในทางเพศ หรืออื่น ๆ ที่ขัดต่อกฎหมาย การกระทำเช่นนั้นอาจทำให้คุณถูกหวงห้ามทันที และอย่างถาวร (และผู้ให้บริการของคุณก็จะได้รับการแจ้งเตือนด้วย) หมายเลข IP ของทุกโพสต์จะถูกบันทึกเพื่อใช้เป็นหลักฐาน. คุณยินยอมให้ เว็บมาเฟีย, ผู้ควบคุมระบบ และ ผู้ตรวจทานของกระดานข่าวนี้มีสิทธิ์ลบ, แก้ไข, ย้าย หรือปิดหัวข้อใด ๆ ได้ตลอดเวลาที่สมควร คุณยินยอมให้ข้อมูลทุกอย่างของคุณถูกเก็บไว้ในฐานข้อมูล ซึ่งข้อมูลเหล่านี้จะไม่ถูกเปิดเผยต่อผู้อื่นโดยไม่ได้รับการยินยอมจากคุณ เว็บมาเฟีย, ผู้ควบคุมระบบ และ ผู้ตรวจทานไม่สามารถรับผิดชอบต่อการถูกเจาะข้อมูล แล้วนำไปสร้างความเดือดร้อนต่าง ๆ</p>

                <p>กระดานข่าวนี้ใช้ระบบคุ๊กกี้ เพื่อเก็บข้อมูลไว้ในคอมพิวเตอร์ของคุณ คุ๊กกี้ เหล่านี้จะไม่มีข้อมูลที่คุณได้กรอกไว้เหมือนด้านบน แต่เพื่อช่วยให้คุณใช้งานได้ง่ายขึ้น อีเมลจะถูกใช้เพื่อยืนยันข้อมูลการสมัครสมาชิกและรหัสผ่านของคุณเท่านั้น (และใช้สำหรับส่ง รหัสผ่านอันใหม่เมื่อคุณลืมรหัสผ่านเก่า)</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    @parent

    <style>
        .toggle-password {
            cursor: pointer;
            pointer-events: auto;
            color: #777;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
    $(document).ready(function () {
        $(".toggle-password").click(function() {
            $(this).toggleClass("glyphicon-eye-open glyphicon-eye-close");

            var input = $($(this).attr("toggle"));

            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } 
            else {
                input.attr("type", "password");
            }
        });
    });   
    </script>
@endsection
