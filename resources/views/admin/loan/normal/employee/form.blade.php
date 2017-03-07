<div class="box-body">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                <p>ขั้นตอนที่ 1</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                <p>ขั้นตอนที่ 2</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                <p>ขั้นตอนที่ 3</p>
            </div>
        </div>
    </div>
        
    <div class="row setup-content" id="step-1">
        @include('admin.loan.normal.employee.step1', ['edit' => false])
    </div>

    <div class="row setup-content" id="step-2">
        @include('admin.loan.normal.employee.step2', ['edit' => false])
    </div>

    <div class="row setup-content" id="step-3">
        @include('admin.loan.normal.employee.step3', ['edit' => false])
    </div>
    
</div>