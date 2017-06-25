<h4>2.ตรวจสอบคุณสมบัติผู้ค้ำประกัน</h4>
{{ Form::hidden('step', '2') }}

<div id="sureties" class="form-group">
    @php($loan = App\Loan::find($loan_id))
    
    @foreach($loan->sureties as $surety)
          <div id="surety_{{ $surety->id }}" class="box box-primary" style="border-left: 1px solid #d2d6de; border-right: 1px solid #d2d6de;">
            <div class="box-header with-border">
                <h4 class="box-title" style="font-size: 14px; font-weight: 700;">ผู้ค้ำประกัน</h4>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        {{ $surety->profile->fullName }}

                        @if ($surety->pivot->yourself)
                            <span>(ค้ำประกันด้วยหุ้นตนเอง)</span>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        {{ number_format($surety->pivot->amount, 2, '.', ',') }} บาท
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