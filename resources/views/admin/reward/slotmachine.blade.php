@extends('admin.reward.layout')

@section('content')
<!-- Slot machine -->
<input type="hidden" id="reward_id" value="{{ $reward->id }}" />
<input type="hidden" id="config_id" value="{{ $reward->rewardConfigs->first()->id }}" />
<input type="hidden" id="member_id" />
<input type="hidden" id="member_name" />

<!-- Rounded switch -->
<div class="toggle-switch">
    <label class="switch">
        <input type="checkbox" id="speed" value="0">
        <span class="slider round"></span>
    </label>
    <span class="text-default"><strong>Speed Mode</strong></span>
</div>

<div id="casino">
    <div class="content">
        <div class="toggle-sidebar pull-right">
            <button id="show-winners" class="btn btn-primary btn-flat btn-sm">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <h1>
            <i class="fa fa-heart"></i>
            <i class="fa fa-heart"></i>
            จับรางวัล
            <select id="rewardconfigs">
                @foreach ($reward->rewardConfigs as $config)
                    <option value="{{ $config->id }}">{{ ($config->special) ? 'พิเศษ' : ' ' .number_format($config->price, 0, '.', ',') . ' บาท' }}</option>
                @endforeach
            </select>
            <i class="fa fa-heart"></i>
            <i class="fa fa-heart"></i>
        </h1>

        <div class="shuffle">
            <div id="casino1" class="slotMachine" style="margin-left: -65px;">
                <div class="slot slot0"></div>
                <div class="slot slot1"></div>
                <div class="slot slot2"></div>
                <div class="slot slot3"></div>
                <div class="slot slot4"></div>
                <div class="slot slot5"></div>
                <div class="slot slot6"></div>
                <div class="slot slot7"></div>
                <div class="slot slot8"></div>
                <div class="slot slot9"></div>
            </div>

            <div id="casino2" class="slotMachine">
                <div class="slot slot0"></div>
                <div class="slot slot1"></div>
                <div class="slot slot2"></div>
                <div class="slot slot3"></div>
                <div class="slot slot4"></div>
                <div class="slot slot5"></div>
                <div class="slot slot6"></div>
                <div class="slot slot7"></div>
                <div class="slot slot8"></div>
                <div class="slot slot9"></div>
            </div>

            <div id="casino3" class="slotMachine">
                <div class="slot slot0"></div>
                <div class="slot slot1"></div>
                <div class="slot slot2"></div>
                <div class="slot slot3"></div>
                <div class="slot slot4"></div>
                <div class="slot slot5"></div>
                <div class="slot slot6"></div>
                <div class="slot slot7"></div>
                <div class="slot slot8"></div>
                <div class="slot slot9"></div>
            </div>

            <div id="casino4" class="slotMachine">
                <div class="slot slot0"></div>
                <div class="slot slot1"></div>
                <div class="slot slot2"></div>
                <div class="slot slot3"></div>
                <div class="slot slot4"></div>
                <div class="slot slot5"></div>
                <div class="slot slot6"></div>
                <div class="slot slot7"></div>
                <div class="slot slot8"></div>
                <div class="slot slot9"></div>
            </div>

            <div id="shuffle" class="btn-group" role="group">
                <button id="casinoShuffle" type="button" class="btn btn-primary btn-lg">จับรางวัล!</button>
            </div>            
        </div>
    </div>
</div>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#config').html('รางวัล' + $('#rewardconfigs').children("option:selected").html());
            $('#config_modal').html('รางวัล' + $('#rewardconfigs').children("option:selected").html());

            winners();

            btnShuffle.addEventListener('click', () => {
                shuffle();
            });

            $('#rewardconfigs').change(function () {
                $('#config_id').val($(this).children("option:selected").val());
                $('#config').html('รางวัล' + $(this).children("option:selected").html());
                $('#config_modal').html('รางวัล' + $(this).children("option:selected").html());
                winners();
            });

            $('#show-winners').click(function () {
                winners_modal();
            });
        });

        const btnShuffle = document.querySelector('#casinoShuffle');
        const mCasino1 = new SlotMachine(document.querySelector('#casino1'), { active: 0, delay: 500 });
        const mCasino2 = new SlotMachine(document.querySelector('#casino2'), { active: 0, delay: 500 });
        const mCasino3 = new SlotMachine(document.querySelector('#casino3'), { active: 0, delay: 500 });
        const mCasino4 = new SlotMachine(document.querySelector('#casino4'), { active: 0, delay: 500 });

        const normalSpeed = [3000, 6000, 10000, 15000, 15500, 18000];
        const fastSpeed = [1000, 1500, 2000, 2500, 2800, 4500];

        function shuffle() {
            let formData = new FormData();
                formData.append('reward_id', $('#reward_id').val());
                formData.append('config_id', $('#config_id').val());

            let member_id = 0;

            $.ajax({
                url: '/admin/reward/shuffle',
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#casinoShuffle').prop('disabled', true);
                    $('#casinoShuffle').html('โปรดรอสักครู่...');

                    mCasino1.shuffle(99999);
                    mCasino2.shuffle(99999);
                    mCasino3.shuffle(99999);
                    mCasino4.shuffle(99999);
                },
                complete: function(){
                    if (member_id != 0) {
                        let delay = shuffle_array(speedMode() ? fastSpeed.slice(0, 4) : normalSpeed.slice(0, 4));

                        setTimeout(() => mCasino1.stop(), delay[0]);
                        setTimeout(() => mCasino2.stop(), delay[1]);
                        setTimeout(() => mCasino3.stop(), delay[2]);
                        setTimeout(() => mCasino4.stop(), delay[3]);

                        setTimeout(() => $(".slotMachine").addClass("winner"), speedMode() ? fastSpeed[4] : normalSpeed[4]);
                        setTimeout(() => { 
                            $(".slotMachine").removeClass("winner");
                            display_winner(); 
                        }, speedMode() ? fastSpeed[5] : normalSpeed[5]);
                    }
                    else {
                        mCasino1.stop();
                        mCasino2.stop();
                        mCasino3.stop();
                        mCasino4.stop();

                        $('#casinoShuffle').prop('disabled', true);
                        $('#casinoShuffle').html('ไม่มีผู้ลงทะเบียนเหลือแล้ว');
                    }
                },
                success: function (member) {
                    $('#member_id').val(member.member_id);
                    $('#member_name').val(member.member_name);
                    
                    member_id = member.member_id;
                    let code = pad(member.member_id, 4).split('');

                    mCasino1.changeSettings({ randomize: () => { return parseInt(code[0]); }});
                    mCasino2.changeSettings({ randomize: () => { return parseInt(code[1]); }});
                    mCasino3.changeSettings({ randomize: () => { return parseInt(code[2]); }});
                    mCasino4.changeSettings({ randomize: () => { return parseInt(code[3]); }});
                }
            });
        }

        function winners() {
            $('#dataTables').dataTable().fnDestroy();
            $('#dataTables').dataTable({
                "ajax": {
                    "url": "/admin/reward/winners",
                    "type": "post",
                    "data": {
                        "reward_id": $('#reward_id').val(),
                        "config_id": $('#config_id').val()
                    },   
                },
                "iDisplayLength": 10,
                "ordering": false,
                "lengthChange": false,
                "searching": false,
                "columns": [
                    { "data": "code" },
                    { "data": "fullname" },
                    { "data": "status" }
                ]
            }); 
            
            finish();
        }

        function display_winner() {
            $.confirm({
                icon: 'fa fa-heart',
                theme: 'material',
                closeIcon: false,
                animation: 'scale',
                type: 'blue',
                boxWidth: '500px',
                useBootstrap: false,
                title: 'ขอแสดงความยินดี!',
                content: create_content(),
                buttons: {
                    reward: {
                        text: 'รับรางวัล',
                        btnClass: 'btn-primary',
                        action: () => {
                            save_winner(true);
                        }
                    },
                    waiver: {
                        text: 'สละสิทธิ์',
                        btnClass: 'btn-danger',
                        action: () => {
                            save_winner(false);
                        }
                    },
                }
            });
        }

        function save_winner(status) {
            $('#dataTables').dataTable().fnDestroy();
            $('#dataTables').dataTable({
                "ajax": {
                    "url": "/admin/reward/savewinner",
                    "type": "post",
                    "data": {
                        "reward_id": $('#reward_id').val(),
                        "config_id": $('#config_id').val(),
                        "member_id": $('#member_id').val(),
                        "status": status
                    },
                    complete: function(){
                        mCasino1.shuffle(99999);
                        mCasino2.shuffle(99999);
                        mCasino3.shuffle(99999);
                        mCasino4.shuffle(99999);

                        mCasino1.changeSettings({ randomize: () => { return 0; }});
                        mCasino2.changeSettings({ randomize: () => { return 0; }});
                        mCasino3.changeSettings({ randomize: () => { return 0; }});
                        mCasino4.changeSettings({ randomize: () => { return 0; }});

                        mCasino1.stop();
                        mCasino2.stop();
                        mCasino3.stop();
                        mCasino4.stop();

                        setTimeout(() => {
                            $('#member_id').removeAttr('value');
                            $('#member_name').removeAttr('value');

                            finish();
                        }, 1000);
                    }       
                },
                "iDisplayLength": 10,
                "ordering": false,
                "lengthChange": false,
                "searching": false,
                "columns": [
                    { "data": "code" },
                    { "data": "fullname" },
                    { "data": "status" }
                ]
            });   
        }

        function finish() {
            var formData = new FormData();
                formData.append('reward_id', $('#reward_id').val());
                formData.append('config_id', $('#config_id').val());

            $.ajax({
                url: '/admin/reward/finish',
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result) {
                        $('#casinoShuffle').prop('disabled', true);
                        $('#casinoShuffle').html('รางวัลหมดแล้ว!');
                    }
                    else {
                        $('#casinoShuffle').prop('disabled', false);
                        $('#casinoShuffle').html('จับรางวัล!');
                    }
                }
            });
        }

        function winners_modal() {
            $('#winners-dataTables').dataTable().fnDestroy();
            $('#winners-dataTables').dataTable({
                "ajax": {
                    "url": "/admin/reward/winners",
                    "type": "post",
                    "data": {
                        "reward_id": $('#reward_id').val(),
                        "config_id": $('#config_id').val()
                    },   
                    complete: function () {
                        $('#winners-modal').modal('show');
                    }
                },
                "iDisplayLength": 10,
                "ordering": false,
                "lengthChange": false,
                "searching": false,
                "columns": [
                    { "data": "code" },
                    { "data": "fullname" },
                    { "data": "status" }
                ]
            }); 
        }

        function create_content() {
            let member_id = $('#member_id').val();
            let code = pad(member_id, 4).split('');

            let content = '<div style="margin-top: 20px; text-align: center;">';
            content += '<img src="/slotmachine/slot' + code[0] + '.png" alt="slot' + code[0] + '" style="border: 5px solid #3498db; margin-right: 2px;" />';
            content += '<img src="/slotmachine/slot' + code[1] + '.png" alt="slot' + code[1] + '" style="border: 5px solid #3498db; margin-right: 2px;" />';
            content += '<img src="/slotmachine/slot' + code[2] + '.png" alt="slot' + code[0] + '" style="border: 5px solid #3498db; margin-right: 2px;" />';
            content += '<img src="/slotmachine/slot' + code[3] + '.png" alt="slot' + code[0] + '" style="border: 5px solid #3498db;" />';
            content += '<br /><br />';
            content += '<span style="font-size: 30px;">' + $('#member_name').val() + '</span>';
            content += '</div>';

            return content;
        }

        function speedMode() {
            return $('#speed').prop("checked") == true;
        }

        function pad(n, width, z) {
            z = z || '0';
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        }

        function shuffle_array(o) {
            for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);

            return o;
        };
    </script>
@endsection