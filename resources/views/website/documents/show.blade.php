@extends('website.layouts.layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">
            <ol class="breadcrumb">
                <li><a href="{{ action('Website\HomeController@index') }}"><i class="fa fa-home fa-fw"></i></a></li>
                @if (empty($link))
                    <li class="active">{{ $header }}</li>
                @else
                    <li><a href="{{ url($link) }}">{{ $header }}</a></li>
                    <li class="active">{{ (is_object($files)) ? str_limit($files->display, 30) : 'ไม่พบเอกสาร' }}</li>
                @endif                
            </ol>
        </h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        @if (is_a($files, 'Illuminate\Database\Eloquent\Collection'))
            <div class="list-group">
                <ul class="nav">
                    @foreach($files as $file)
                        <li>
                            <a href="{{ url('/documents/' . str_plural(strtolower($file->document_type->name)) . '/' . $file->display) }}" class="list-group-item borderless">
                                <i class="fa fa-file-text-o fa-fw"></i> {{ $file->display }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>            
        @else
            @if (!is_null($files))
                <i class="fa fa-download fa-fw"></i> ดาวน์โหลด: 
                <a download="{{ $files->display }}" href="{{ url('/storage/download/documents/' . substr($files->file, 0, strlen($files->file) - 4) . '/' . $files->display . '.pdf') }}">
                    {{ $files->display }}.pdf
                </a>
                <object data="{{ url('/storage/file/documents/' . $files->file) }}" type="application/pdf" width="100%" height="1110" style="margin-top: 20px;"></object>
            @else
                <div class="well bg-danger">
                    <i class="fa fa-ban"></i> ไม่พบเอกสาร
                </div>
            @endif  
        @endif        
    </div>
</div>
@endsection
