@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2 style="margin:0;">Edit Offer Letter Template</h2>
            <p class="subtitle">Update template title and content. HTML is allowed.</p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.offerletter.template.update') }}">
        @csrf

        <div class="field" style="margin-bottom:14px;">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $template->title) }}" placeholder="e.g. Offer Letter - Sales Executive">
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Header (HTML allowed)</label>
            <textarea name="header" rows="6" placeholder="Header HTML...">{{ old('header', $template->header) }}</textarea>
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Body Content (HTML allowed)</label>
            <textarea name="content" rows="18" placeholder="Write the offer letter HTML here...">{{ old('content', $template->content) }}</textarea>
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Table (optional, HTML allowed)</label>
            <textarea name="table_html" rows="12" placeholder="Paste your full table HTML here (optional). If empty, auto CTC table will be used.">{{ old('table_html', $template->table_html) }}</textarea>
            <div style="margin-top:8px; font-size:12px; color:rgba(100,116,139,0.95);">
                Tip: Body me table yahi insert karne ke liye placeholder use karein: <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{table}}</code>
            </div>
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Footer (HTML allowed)</label>
            <textarea name="footer" rows="6" placeholder="Footer HTML...">{{ old('footer', $template->footer) }}</textarea>
        </div>

        <div class="card" style="padding:14px; margin: 12px 0 14px; background: rgba(15,23,42,0.02);">
            <div class="page-header" style="margin-bottom:10px;">
                <div>
                    <h3 style="margin:0;">Offer Letter Images</h3>
                    <p class="subtitle">Upload images and show them in template using <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{images}}</code>.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.offerletter.images.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid" style="align-items:end;">
                    <div class="field">
                        <label>Image</label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                    </div>
                    <div class="field">
                        <label>Label (optional)</label>
                        <input type="text" name="name" placeholder="e.g. Annexure 1">
                    </div>
                    <div class="field">
                        <button type="submit" class="btn-primary">⬆️ Upload</button>
                    </div>
                </div>
            </form>

            @if(!empty($template->images) && $template->images->count())
                <div style="margin-top:12px; display:flex; flex-wrap:wrap; gap:10px;">
                    @foreach($template->images as $img)
                        <div style="border:1px solid rgba(15,23,42,0.12); border-radius:12px; padding:10px; width:220px; background:#fff;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
                                <div style="font-weight:800; font-size:13px; color:rgba(15,23,42,0.85); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $img->name ?? 'Image' }}
                                </div>
                                <a class="btn-danger" style="padding:6px 10px;" href="{{ route('admin.offerletter.images.delete', $img->id) }}">Remove</a>
                            </div>
                            <div style="margin-top:10px;">
                                <img src="{{ asset($img->path) }}" alt="{{ $img->name }}" style="max-width:100%; height:auto; border-radius:10px; border:1px solid rgba(15,23,42,0.12);">
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="margin-top:10px; font-size:13px; color:rgba(100,116,139,0.95);">
                    No images uploaded yet.
                </div>
            @endif
        </div>

        <div style="font-size:13px; color:rgba(100,116,139,0.95); margin-bottom:14px;">
            You can use placeholders:
            <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:8px;">
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{name}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{designation}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{location}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{date_of_commencement}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{monthly_salary}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{ctc_annual}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{ctc_in_word}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{reporting_boss}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{basic_pay}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{hra}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{annual_leave_days}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{sick_leave_days}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{target_percentage}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{ctc_table}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{table}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{images}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{aadhar_preview}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{aadhar_url}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{date}}</code>
            </div>
            <div style="margin-top:8px; font-size:12px;">
                Note: Candidate ke almost saare DB fields directly placeholder ban sakte hain, jaise <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{monthly_salary}}</code>, <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{location_hq}}</code>, etc.
            </div>
        </div>

        <button type="submit" class="btn-primary">💾 Save Template</button>
    </form>
</div>
@endsection
