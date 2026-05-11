@extends('admin.layout')

@section('content')
<style>
    .template-editor {
        width: 100%;
        min-height: 130px;
        padding: 12px 14px;
        border: 1px solid rgba(15,23,42,0.14);
        border-radius: 12px;
        background: #fff;
        color: rgba(15,23,42,0.9);
        line-height: 1.65;
        outline: none;
        overflow: auto;
    }

    .template-editor:focus {
        border-color: rgba(37,99,235,0.55);
        box-shadow: 0 0 0 4px rgba(37,99,235,0.10);
    }

    .template-editor-lg {
        min-height: 360px;
    }

    .template-editor table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }

    .template-editor th,
    .template-editor td {
        border: 1px solid rgba(15,23,42,0.28);
        padding: 8px;
    }
</style>

<div class="card">
    <div class="page-header">
        <div>
            <h2 style="margin:0;">Edit Offer Letter Template</h2>
            <p class="subtitle">Update template title and content.</p>
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

    <form id="offer-image-upload-form" method="POST" action="{{ route('admin.offerletter.images.upload') }}" enctype="multipart/form-data"></form>

    <form id="offer-template-form" method="POST" action="{{ route('admin.offerletter.template.update') }}">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">

        @if(!empty($templates) && $templates->count())
            <div class="field" style="margin-bottom:14px;">
                <label>Use Saved Template</label>
                <select onchange="if (this.value) window.location.href = this.value;">
                    @foreach($templates as $savedTemplate)
                        <option value="{{ route('admin.offerletter.template.edit.saved', $savedTemplate->id) }}" @selected($savedTemplate->id === $template->id)>
                            {{ $savedTemplate->title ?: 'Offer Letter' }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="field" style="margin-bottom:14px;">
            <label>Template Name</label>
            <input type="text" name="title" value="{{ old('title', $template->title) }}" placeholder="e.g. Offer Letter - Sales Executive">
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Header</label>
            <div class="template-editor" contenteditable="true" data-template-editor="header">{!! old('header', $template->header) !!}</div>
            <input type="hidden" name="header" value="">
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Body Content</label>
            <div class="template-editor template-editor-lg" contenteditable="true" data-template-editor="content">{!! old('content', $template->content) !!}</div>
            <input type="hidden" name="content" value="">
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Table (optional)</label>
            <div class="template-editor" contenteditable="true" data-template-editor="table_html">{!! old('table_html', $template->table_html) !!}</div>
            <input type="hidden" name="table_html" value="">
            <div style="margin-top:8px; font-size:12px; color:rgba(100,116,139,0.95);">
                Tip: Body me table yahi insert karne ke liye placeholder use karein: <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{table}}</code>
            </div>
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Footer</label>
            <div class="template-editor" contenteditable="true" data-template-editor="footer">{!! old('footer', $template->footer) !!}</div>
            <input type="hidden" name="footer" value="">
        </div>

        <div class="card" style="padding:14px; margin: 12px 0 14px; background: rgba(15,23,42,0.02);">
            <div class="page-header" style="margin-bottom:10px;">
                <div>
                    <h3 style="margin:0;">Offer Letter Images</h3>
                    <p class="subtitle">Upload images and show them in template using <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{images}}</code>.</p>
                </div>
            </div>

            <div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" form="offer-image-upload-form">
                <input type="hidden" name="template_id" value="{{ $template->id }}" form="offer-image-upload-form">
                <div class="form-grid" style="align-items:end;">
                    <div class="field">
                        <label>Image</label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required form="offer-image-upload-form">
                    </div>
                    <div class="field">
                        <label>Label (optional)</label>
                        <input type="text" name="name" placeholder="e.g. Annexure 1" form="offer-image-upload-form">
                    </div>
                    <div class="field">
                        <button type="submit" form="offer-image-upload-form" class="btn-primary">⬆️ Upload</button>
                    </div>
                </div>
            </div>

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
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{signature}}</code>
                <code style="padding:6px 10px; border-radius:999px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{date}}</code>
            </div>
            <div style="margin-top:8px; font-size:12px;">
                Note: Candidate ke almost saare DB fields directly placeholder ban sakte hain, jaise <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{monthly_salary}}</code>, <code style="padding:2px 6px; border-radius:8px; background:rgba(15,23,42,0.06); border:1px solid rgba(15,23,42,0.08);">@{{location_hq}}</code>, etc.
            </div>
        </div>

        <div style="display:flex; align-items:end; gap:10px; flex-wrap:wrap;">
            <button type="submit" name="action" value="update" form="offer-template-form" class="btn-primary">💾 Save Template</button>

            <div class="field" style="min-width:260px; margin:0;">
                <label>Save As New Template</label>
                <input type="text" name="save_as_title" form="offer-template-form" value="{{ old('save_as_title') }}" placeholder="e.g. Sales">
            </div>
            <button type="submit" name="action" value="save_as" form="offer-template-form" class="btn-secondary">Save As</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('offer-template-form');

        function syncTemplateEditors() {
            document.querySelectorAll('[data-template-editor]').forEach(function (editor) {
                const field = editor.dataset.templateEditor;
                const input = document.querySelector('input[type="hidden"][name="' + field + '"]');
                if (input) {
                    input.value = editor.innerHTML.trim();
                }
            });
        }

        syncTemplateEditors();

        if (form) {
            form.addEventListener('submit', syncTemplateEditors);
        }
    });
</script>
@endsection
