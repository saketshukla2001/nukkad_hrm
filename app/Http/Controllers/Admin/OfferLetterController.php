<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferLetter;
use App\Models\Candidate;
use App\Models\OfferLetterImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class OfferLetterController extends Controller
{
    // Show edit template page
    public function editTemplate()
    {
        $template = OfferLetter::with('images')->first();

        if (!$template) {
            $template = OfferLetter::create([
                'title' => 'Offer Letter',
                'header' => '<h2 style="text-align:center; margin:0 0 12px;">Offer Letter</h2>',
                'content' => '<p>Dear @{{name}},</p><p>We welcome you to our organization and look forward to your contribution to the growth of the organization.</p><p>We are pleased to offer you the position of <strong>@{{designation}}</strong> at <strong>@{{location}}</strong>.</p><p>Date of commencement: <strong>@{{date_of_commencement}}</strong></p><p>Monthly Salary: <strong>@{{monthly_salary}}</strong></p><p>Annual CTC: <strong>@{{ctc_annual}}</strong> (<em>@{{ctc_in_word}}</em>)</p><p>@{{images}}</p><p>Please find the compensation structure below:</p><p>@{{ctc_table}}</p>',
                'table_html' => null,
                'footer' => '<p style="margin-top:18px;">Regards,<br><strong>Nukkad HRM</strong></p>',
            ]);
            $template->load('images');
        }

        return view('admin.offer_letters.edit_template', compact('template'));
    }

    // Update template
    public function updateTemplate(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string',
            'header' => 'nullable|string',
            'content' => 'required|string',
            'table_html' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        $template = OfferLetter::first();
        $template->update([
            'title' => $request->title,
            'header' => $request->header,
            'content' => $request->content,
            'table_html' => $request->table_html,
            'footer' => $request->footer,
        ]);

        return back()->with('success', 'Offer letter template updated successfully!');
    }

    public function uploadImage(Request $request)
    {
        $template = OfferLetter::firstOrFail();

        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . Str::random(6) . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $file->getClientOriginalName());
        $dir = public_path('uploads/offer_letters');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $file->move($dir, $filename);

        OfferLetterImage::create([
            'offer_letter_id' => $template->id,
            'name' => $request->name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => 'uploads/offer_letters/' . $filename,
            'sort_order' => (int) (OfferLetterImage::where('offer_letter_id', $template->id)->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Image uploaded successfully!');
    }

    public function deleteImage($imageId)
    {
        $image = OfferLetterImage::findOrFail($imageId);

        $full = public_path($image->path);
        if (is_file($full)) {
            @unlink($full);
        }

        $image->delete();

        return back()->with('success', 'Image removed successfully!');
    }

    // Generate offer letter for candidate
    public function generate($candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        $template  = OfferLetter::firstOrFail();

        $content = $this->renderOfferLetterForCandidate($template, $candidate, false);

        return view('admin.offer_letters.preview', [
            'content' => $content,
            'candidateId' => $candidateId,
            'candidateName' => $candidate->name,
            'aadharUrl' => $this->candidateAadharUrl($candidate),
        ]);
    }

    public function download($candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        $template  = OfferLetter::firstOrFail();

        $content = $this->renderOfferLetterForCandidate($template, $candidate, true);

        $html = view('admin.offer_letters.pdf', [
            'content' => $content,
        ])->render();

        $safeName = Str::slug($candidate->name ?: 'candidate');
        $filename = "offer-letter-{$safeName}.pdf";

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->download($filename);
    }

    private function renderOfferLetterForCandidate(OfferLetter $template, Candidate $candidate, bool $forPdf = false): string
    {
        $date = now()->format('d-m-Y');

        $ctc = $this->buildCtcBreakdown($candidate);
        $ctcTableHtml = view('admin.offer_letters.partials.ctc_table', [
            'ctc' => $ctc,
        ])->render();

        $template->loadMissing('images');

        $header = (string) ($template->header ?? '');
        $body = (string) ($template->content ?? '');
        $footer = (string) ($template->footer ?? '');

        // Custom table (editable in admin). If empty, fallback to auto CTC.
        $customTable = trim((string) ($template->table_html ?? ''));
        $tableHtml = $customTable !== '' ? $customTable : $ctcTableHtml;

        $imagesHtml = $this->renderImagesHtml($template);
        $aadharPreviewHtml = $this->renderAadharHtml($candidate, $forPdf);
        $aadharUrl = $this->candidateAadharUrl($candidate);

        // Build replacement map from all candidate fields + common aliases.
        $replacements = $this->buildCandidateReplacementMap($candidate, [
            'date' => $date,
            'ctc_table' => $ctcTableHtml,
            'table' => $tableHtml,
            'images' => $imagesHtml,
            'aadhar_url' => $aadharUrl,
            'aadhar_preview' => $aadharPreviewHtml,
        ]);

        $header = str_replace(array_keys($replacements), array_values($replacements), $header);
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        $footer = str_replace(array_keys($replacements), array_values($replacements), $footer);
        $tableHtml = str_replace(array_keys($replacements), array_values($replacements), $tableHtml);

        // Replace any React/JSX accidental block for images with our placeholder
        $body = preg_replace('/\{images\.map\([\s\S]*?\)\)\s*\}/', '@{{images}}', $body) ?? $body;

        // If body includes @{{table}} placeholder, inject there; otherwise append table if needed.
        $hasTablePlaceholder = str_contains($body, '@{{table}}') || str_contains($body, '{{table}}');
        $hasAnyTable = str_contains($body, '<table') || str_contains($tableHtml, '<table');

        if ($hasTablePlaceholder) {
            $body = str_replace(['@{{table}}', '{{table}}'], $tableHtml, $body);
        } elseif (!$hasAnyTable) {
            $body .= '<br><br>' . $tableHtml;
        }

        // If template didn't include aadhar placeholder, auto append it (preview + PDF)
        $hasAadharPlaceholder =
            str_contains($body, '@{{aadhar_preview}}') ||
            str_contains($body, '{{aadhar_preview}}') ||
            str_contains($body, '@{{aadhar_url}}') ||
            str_contains($body, '{{aadhar_url}}');

        if (!$hasAadharPlaceholder && $aadharPreviewHtml !== '') {
            $body .= '<br><br>' . $aadharPreviewHtml;
        }

        $html = trim($header . "\n" . $body . "\n" . $footer);

        // Support legacy placeholders like:
        // {employeeDetails.basicPay} and {employeeDetails.basicPay * 12}
        return $this->replaceEmployeeDetailsTokens($html, $candidate);
    }

    private function candidateAadharUrl(Candidate $candidate): ?string
    {
        if (empty($candidate->aadhar_file)) {
            return null;
        }

        return asset('uploads/aadhar/' . ltrim($candidate->aadhar_file, '/'));
    }

    private function renderAadharHtml(Candidate $candidate, bool $forPdf = false): string
    {
        if (empty($candidate->aadhar_file)) {
            return '';
        }

        $url = $this->candidateAadharUrl($candidate);

        $ext = strtolower(pathinfo($candidate->aadhar_file, PATHINFO_EXTENSION));
        $label = 'Aadhar';

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
        if ($isImage) {
            $src = $url ?: '';

            // DomPDF is more reliable with data-uri images than http URLs.
            if ($forPdf) {
                $fullPath = public_path('uploads/aadhar/' . ltrim($candidate->aadhar_file, '/'));
                if (is_file($fullPath)) {
                    $mime = match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'webp' => 'image/webp',
                        default => 'image/jpeg',
                    };
                    $data = base64_encode((string) file_get_contents($fullPath));
                    $src = "data:{$mime};base64,{$data}";
                }
            }

            return '<div style="margin:10px 0;">'
                . '<div style="font-weight:700; margin-bottom:6px;">' . $label . '</div>'
                . '<img src="' . $src . '" alt="' . $label . '" style="max-width:260px; height:auto; border:1px solid #000; padding:3px;">'
                . '</div>';
        }

        // PDF or other file: show link
        if (!$url) {
            return '';
        }
        return '<div style="margin:10px 0;">'
            . '<div style="font-weight:700; margin-bottom:6px;">' . $label . '</div>'
            . '<a href="' . $url . '">' . $url . '</a>'
            . '</div>';
    }

    private function buildCandidateReplacementMap(Candidate $candidate, array $extra = []): array
    {
        $map = [];

        foreach ($candidate->getAttributes() as $key => $value) {
            $stringValue = is_null($value) ? '' : (string) $value;
            $map["@{{{$key}}}"] = $stringValue;
            $map["{{{$key}}}"] = $stringValue;
        }

        // friendly aliases
        $aliases = [
            'name' => $candidate->name,
            'candidate_name' => $candidate->name,
            'location' => $candidate->location_hq,
            'joining_date' => $candidate->date_of_commencement,
            'annual_ctc' => $candidate->ctc_annual,
        ];

        foreach ($aliases as $key => $value) {
            $stringValue = is_null($value) ? '' : (string) $value;
            $map["@{{{$key}}}"] = $stringValue;
            $map["{{{$key}}}"] = $stringValue;
        }

        foreach ($extra as $key => $value) {
            $stringValue = is_null($value) ? '' : (string) $value;
            $map["@{{{$key}}}"] = $stringValue;
            $map["{{{$key}}}"] = $stringValue;
        }

        return $map;
    }

    private function replaceEmployeeDetailsTokens(string $html, Candidate $candidate): string
    {
        $attrs = $candidate->getAttributes();

        $keyMap = [
            // map common camelCase tokens to DB columns
            'basicPay' => 'basic_pay',
            'hra' => 'hra',
            'monthlySalary' => 'monthly_salary',
            'annualCtc' => 'ctc_annual',
            'ctcAnnual' => 'ctc_annual',
            'ctcInWord' => 'ctc_in_word',
            'name' => 'name',
            'designation' => 'designation',
            'location' => 'location_hq',
            'locationHq' => 'location_hq',
            'dateOfCommencement' => 'date_of_commencement',
            'reportingBoss' => 'reporting_boss',
            'annualLeaveDays' => 'annual_leave_days',
            'sickLeaveDays' => 'sick_leave_days',
            'targetPercentage' => 'target_percentage',
        ];

        $get = function (string $tokenKey) use ($attrs, $keyMap): float|string {
            $column = $keyMap[$tokenKey] ?? $tokenKey;
            $value = $attrs[$column] ?? '';
            return is_numeric($value) ? (float) $value : (string) $value;
        };

        // Replace `{employeeDetails.someKey}` and `{employeeDetails.someKey * 12}`
        return preg_replace_callback(
            '/\{employeeDetails\.([a-zA-Z0-9_]+)(?:\s*\*\s*([0-9]+(?:\.[0-9]+)?))?\}/',
            function (array $m) use ($get) {
                $key = $m[1];
                $mult = isset($m[2]) ? (float) $m[2] : null;

                $value = $get($key);

                if (is_float($value) || is_int($value)) {
                    $num = (float) $value;
                    if ($mult !== null) {
                        $num *= $mult;
                    }
                    // keep whole numbers clean
                    $decimals = (abs($num - round($num)) < 0.00001) ? 0 : 2;
                    return number_format($num, $decimals);
                }

                // non-numeric value: multiplier doesn't apply
                return (string) $value;
            },
            $html
        );
    }

    private function renderImagesHtml(OfferLetter $template): string
    {
        if (!$template->relationLoaded('images')) {
            $template->load('images');
        }

        if ($template->images->isEmpty()) {
            return '';
        }

        $items = '';
        foreach ($template->images as $img) {
            $src = asset($img->path);
            $alt = e($img->name ?? '');
            $items .= '<div style="display:inline-block; margin:6px 10px 6px 0; text-align:center;">'
                . '<img src="' . $src . '" alt="' . $alt . '" style="max-width:220px; height:auto; display:block; border:1px solid #000; padding:3px;">'
                . ($alt !== '' ? '<div style="font-size:12px; margin-top:4px;">' . $alt . '</div>' : '')
                . '</div>';
        }

        return '<div style="margin:10px 0;">' . $items . '</div>';
    }

    private function buildCtcBreakdown(Candidate $candidate): array
    {
        $basicMonthly = (float) ($candidate->basic_pay ?? 0);
        $hraMonthly = (float) ($candidate->hra ?? 0);
        $totalMonthly = (float) ($candidate->monthly_salary ?? 0);

        $basicAnnual = $basicMonthly * 12;
        $hraAnnual = $hraMonthly * 12;
        $totalAnnual = (float) ($candidate->ctc_annual ?? ($totalMonthly * 12));

        return [
            'basic' => $this->money($basicMonthly),
            'basic_annual' => $this->money($basicAnnual),
            'hra' => $this->money($hraMonthly),
            'hra_annual' => $this->money($hraAnnual),
            'total_monthly' => $this->money($totalMonthly),
            'total_annual' => $this->money($totalAnnual),
        ];
    }

    private function money(float $value): string
    {
        // Keep it simple for letters/PDF: no decimals unless needed.
        $decimals = (abs($value - round($value)) < 0.00001) ? 0 : 2;
        return number_format($value, $decimals);
    }
    
}
