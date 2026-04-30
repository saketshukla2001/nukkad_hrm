@extends('admin.layout')

@section('content')
<style>
    .a4-page {
        width: 210mm;
        min-height: 297mm;
        margin: 20px auto;
        padding: 16mm;
        background: #fff;
        border: 1px solid #e5e7eb;
        font-family: "Times New Roman", serif;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Tables inside content */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        table-layout: fixed;
    }
    table th, table td {
        border: 1px solid #000;
        padding: 6px 7px;
        text-align: center;
        vertical-align: top;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: normal;
    }
    table td.text-left {
        text-align: left;
    }

    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    tr { page-break-inside: avoid; }

    img, svg { max-width: 100%; height: auto; }

    @media print {
        .no-print { display: none; }
        body * { visibility: hidden; }
        .a4-page, .a4-page * { visibility: visible; }
        .a4-page {
            position: absolute;
            left: 0; top: 0;
            width: 210mm; min-height: 297mm;
            margin: 0; padding: 20mm;
            border: none;
        }
    }
</style>

<div class="card">
    <div class="page-header no-print">
        <div>
            <h2 style="margin:0;">Offer Letter Preview</h2>
            @if(!empty($candidateName))
                <p class="subtitle">Candidate: {{ $candidateName }}</p>
            @endif
        </div>
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            @if(!empty($candidateId))
                <a class="btn-primary" href="{{ route('admin.offerletter.download', $candidateId) }}">⬇️ Download PDF</a>
            @endif
            @if(!empty($aadharUrl))
                <a class="btn-secondary" href="{{ $aadharUrl }}" target="_blank" rel="noopener">🪪 Download Aadhar</a>
            @endif
            <button onclick="window.print()" class="btn-secondary">🖨️ Print</button>
        </div>
    </div>

    <div class="a4-page">
        {{-- Bas admin ka HTML content, as-it-is --}}
        {!! $content !!}
    </div>

    <div class="no-print" style="margin-top:12px; color:rgba(100,116,139,0.95); font-size:13px;">
        Tip: Best quality ke liye “Download PDF” use karein.
    </div>
</div>
@endsection
