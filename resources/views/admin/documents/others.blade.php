@foreach ($others as $other)
    <div class="form-group" style="position: relative;">
        <label>
            <i class="fa fa-file-text-o fa-fw"></i>
            {{ $other->display }}
        </label>
        <input type="file" 
            name="file-{{ $other->id }}" id="file-{{ $other->id }}"
            onchange="javascript:updateOther({{ $other->id }});"
            class="file-upload"
            accept="application/pdf" />
        <div class="input-group">
            <div class="form-control file-caption kv-fileinput-caption" tabindex="500">
                <div id="caption-{{ $other->id }}" class="file-caption-name">
                    {{ $other->file }}
                </div>
            </div>
            <div class="input-group-btn">
                <button type="button" class="btn btn-default btn-background" onclick="$('#file-{{ $other->id }}').click();">
                    <i class="fa fa-folder-open-o"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach
