<div class="box-body">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <button type="button" class="btn btn-primary btn-circle">1</button>
                <p>ขั้นตอนที่ 1</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" {!! ($step < 2) ? 'class="btn btn-default btn-circle" disabled="disabled"' : 'class="btn btn-primary btn-circle"' !!}>2</button>
                <p>ขั้นตอนที่ 2</p>
            </div>
        </div>
    </div>
        
    @if ($step == 1)
        <div class="row setup-content" id="step-1">
            @include('admin.loan.create.special.employee.step1')
        </div>
    @endif

    @if ($step == 2)
        <div class="row setup-content" id="step-2">
            @include('admin.loan.create.special.employee.step2')
        </div>
    @endif
</div>