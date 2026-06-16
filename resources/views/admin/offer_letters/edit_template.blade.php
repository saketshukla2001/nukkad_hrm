@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2>Edit Offer Letter Template</h2>
            <p class="subtitle">Customize header, body, table and footer for offer letters</p>
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
                Tip: Body me table insert karne ke liye placeholder use karein: <span class="code-chip">@{{table}}</span>
            </div>
        </div>

        <div class="field" style="margin-bottom:14px;">
            <label>Footer</label>
            <div class="template-editor" contenteditable="true" data-template-editor="footer">{!! old('footer', $template->footer) !!}</div>
            <input type="hidden" name="footer" value="">
        </div>

        <div class="card-nested">
            <div class="page-header" style="margin-bottom:10px;">
                <div>
                    <h3>Offer Letter Images</h3>
                    <p class="subtitle">Upload images and show them using <span class="code-chip">@{{images}}</span></p>
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
                <div class="image-grid">
                    @foreach($template->images as $img)
                        <div class="image-card">
                            <div class="image-card-header">
                                <div class="image-card-title">{{ $img->name ?? 'Image' }}</div>
                                <a class="btn-danger btn-sm" href="{{ route('admin.offerletter.images.delete', $img->id) }}">Remove</a>
                            </div>
                            <img src="{{ asset($img->path) }}" alt="{{ $img->name }}" class="image-card-preview">
                        </div>
                    @endforeach
                </div>
            @else
                <p class="subtitle" style="margin-top:10px;">No images uploaded yet.</p>
            @endif
        </div>

        <div class="placeholder-section">
            <p class="subtitle" style="margin-bottom:8px;">Available placeholders — click to copy usage in template:</p>
            <div class="chip-grid">
                @foreach(['name','designation','location','date_of_commencement','monthly_salary','ctc_annual','ctc_in_word','reporting_boss','basic_pay','hra','epf_employee','esic_employee','gratuity_employer','in_hand_salary','annual_leave_days','sick_leave_days','target_percentage','ctc_table','table','images','aadhar_preview','aadhar_url','signature','date'] as $ph)
                    <span class="code-chip">{{ '{' . '{' . $ph . '}' . '}' }}</span>
                @endforeach
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
