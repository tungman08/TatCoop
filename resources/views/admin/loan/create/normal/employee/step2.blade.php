<h4>2.ตรวจสอบคุณสมบัติผู้ค้ำประกัน</h4>
{{ Form::hidden('step', '2') }}

@if (!$loan->shareholding)
    <div class="form-group">
        {{ Form::label('check_surety_id', 'รหัสสมาชิกของผู้ค้ำ (ถ้าผู้กู้ต้องการใช้หุ้นตัวของตนเองค้ำ ให้ใส่รหัสสมาชิกของผู้กู้)', [
            'class'=>'control-label']) 
        }}
        <div class="input-group">
            {{ Form::text('check_surety_id', null, [
                'id' => 'check_surety_id',
                'placeholder' => 'รหัสสมาชิก 5 หลัก',
                'data-inputmask' => "'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                'data-mask',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
            <span class="input-group-btn">
                {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่ม', [
                    'id' => 'check_surety',
                    'class'=>'btn btn-default btn-flat'])
                }}
            </span>
        </div>
    </div>
@endif

<div id="sureties" class="form-group">
    @foreach($loan->sureties as $surety)
          <div id="surety_{{ $surety->id }}" class="box box-primary" style="border-left: 1px solid #d2d6de; border-right: 1px solid #d2d6de;">
            <div class="box-header with-border">
                <h4 class="box-title" style="font-size: 14px; font-weight: 700;">ผู้ค้ำประกัน</h4>

                @if (!$loan->shareholding)
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" onclick="javascript:var result = confirm('คุณต้องการลบผู้ค้ำประกันรายนี้ใช่ไหม?'); if (result) { removeSurety({{ $loan->id }}, {{ $surety->id }}); }"><i class="fa fa-times"></i></button>
                    </div>
                    <!-- /.box-tools -->
                @endif
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        {{ $surety->profile->fullname }}

                        @if ($surety->pivot->yourself)
                            @php($available = LoanCalculator::shareholding_available($surety))
                            <span>(ค้ำประกันด้วยหุ้นตนเอง เหลือหุ้นที่สามาถค้ำประกันได้ {{ ($available > 1200000) ? number_format(1200000, 2, '.', ',') : number_format($available, 2, '.', ',') }} บาท)</span>
                        @else
                            <span>(ค้ำประกันด้วยเงินเดือน)</span>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        <span>{{ number_format($surety->pivot->amount, 2, '.', ',') }} บาท</span>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
    @endforeach
</div>

<hr />

{{ Form::button('<i class="fa fa-arrow-circle-right"></i> ถัดไป', [
    'id' => 'step2',
    'type' => 'submit',
    'class'=>'btn btn-primary btn-flat nextBtn'])
}}

