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
    public function editTemplate(?int $templateId = null)
    {
        $this->ensureDefaultTemplateExists();

        $template = OfferLetter::with('images')
            ->when($templateId, fn ($query) => $query->where('id', $templateId))
            ->firstOrFail();
        $templates = OfferLetter::orderBy('title')->orderBy('id')->get();

        return view('admin.offer_letters.edit_template', compact('template', 'templates'));
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
            'template_id' => 'nullable|exists:offer_letters,id',
            'save_as_title' => 'nullable|string|max:255',
            'action' => 'nullable|string|in:update,save_as',
        ]);

        $template = OfferLetter::find($request->template_id) ?: $this->ensureDefaultTemplateExists();
        $data = [
            'title' => $request->title ?: 'Offer Letter',
            'header' => $request->header,
            'content' => $request->content,
            'table_html' => $request->table_html,
            'footer' => $request->footer,
        ];

        if ($request->action === 'save_as') {
            $request->validate([
                'save_as_title' => 'required|string|max:255',
            ]);

            $newTemplate = OfferLetter::create(array_merge($data, [
                'title' => $request->save_as_title,
            ]));

            $template->loadMissing('images');
            foreach ($template->images as $image) {
                $newTemplate->images()->create([
                    'name' => $image->name,
                    'path' => $image->path,
                    'sort_order' => $image->sort_order,
                ]);
            }

            return redirect()
                ->route('admin.offerletter.template.edit.saved', $newTemplate->id)
                ->with('success', 'Offer letter template saved as "' . $newTemplate->title . '" successfully!');
        }

        $template->update($data);

        return back()->with('success', 'Offer letter template updated successfully!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:offer_letters,id',
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'name' => 'nullable|string|max:255',
        ]);

        $template = OfferLetter::findOrFail($request->template_id);

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
        $this->ensureDefaultTemplateExists();
        $templates = OfferLetter::orderBy('title')->orderBy('id')->get();
        $template = OfferLetter::find(request('template_id')) ?: $templates->first();

        $content = $this->renderOfferLetterForCandidate($template, $candidate, false);

        return view('admin.offer_letters.preview', [
            'content' => $content,
            'candidateId' => $candidateId,
            'candidateName' => $candidate->name,
            'aadharUrl' => $this->candidateAadharUrl($candidate),
            'templates' => $templates,
            'template' => $template,
        ]);
    }

    public function download($candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        $this->ensureDefaultTemplateExists();
        $template = OfferLetter::find(request('template_id')) ?: OfferLetter::firstOrFail();

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

    private function ensureDefaultTemplateExists(): OfferLetter
    {
        $template = OfferLetter::first();

        if ($template) {
            return $template;
        }

        return OfferLetter::create([
            'title' => 'Offer Letter',
            'header' => '<h2 style="text-align:center; margin:0 0 12px;">Offer Letter</h2>',
            'content' => '<p>Dear @{{name}},</p><p>We welcome you to our organization and look forward to your contribution to the growth of the organization.</p><p>We are pleased to offer you the position of <strong>@{{designation}}</strong> at <strong>@{{location}}</strong>.</p><p>Date of commencement: <strong>@{{date_of_commencement}}</strong></p><p>Monthly Salary: <strong>@{{monthly_salary}}</strong></p><p>Annual CTC: <strong>@{{ctc_annual}}</strong> (<em>@{{ctc_in_word}}</em>)</p><p>Please find the compensation structure below:</p><p>@{{ctc_table}}</p>',
            'table_html' => null,
            'footer' => '<p style="margin-top:18px;">Regards,<br><strong>Nukkad HRM</strong></p>',
        ]);
    }

    private function renderOfferLetterForCandidate(OfferLetter $template, Candidate $candidate, bool $forPdf = false): string
    {
        $date = now()->format('d-m-Y');

        $salary = $this->calculateSalaryStructure($candidate);
        $ctc = $this->buildCtcBreakdown($candidate, $salary);
        $ctcTableHtml = view('admin.offer_letters.partials.ctc_table', [
            'ctc' => $ctc,
        ])->render();

        $template->loadMissing('images');

        $header = $this->stripLegacyCompanyHeader((string) ($template->header ?? ''));
        $body = $this->stripLegacyCompanyHeader((string) ($template->content ?? ''));
        $footer = $this->stripLegacyCompanyHeader((string) ($template->footer ?? ''));

        // Custom table (editable in admin). If empty, fallback to auto CTC.
        $customTable = trim((string) ($template->table_html ?? ''));
        $tableHtml = $customTable !== '' ? $customTable : $ctcTableHtml;
        $tableHtml = $this->normalizeOfferTables($tableHtml);

        $imagesHtml = $this->renderImagesHtml($template);
        $aadharPreviewHtml = $this->renderAadharHtml($candidate, $forPdf);
        $aadharUrl = $this->candidateAadharUrl($candidate);
        $signatureHtml = $this->renderSignatureHtml($forPdf);

        // Build replacement map from all candidate fields + common aliases.
        $replacements = $this->buildCandidateReplacementMap($candidate, array_merge([
            'date' => $date,
            'ctc_table' => $ctcTableHtml,
            'table' => $tableHtml,
            'images' => $imagesHtml,
            'aadhar_url' => $aadharUrl,
            'aadhar_preview' => $aadharPreviewHtml,
            'signature' => $signatureHtml,
        ], $this->salaryPlaceholderMap($salary)));

        $header = str_replace(array_keys($replacements), array_values($replacements), $header);
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        $footer = str_replace(array_keys($replacements), array_values($replacements), $footer);
        $tableHtml = str_replace(array_keys($replacements), array_values($replacements), $tableHtml);
        $tableHtml = $this->normalizeOfferTables($tableHtml);

        // Clean up React/JSX accidental blocks so preview/PDF render properly.
        $body = $this->sanitizeReactishMarkup($body, $imagesHtml, $signatureHtml);
        $body = $this->placeSignatureAfterWelcomeLine($body, $signatureHtml);

        // If body includes @{{table}} placeholder, inject there; otherwise append table if needed.
        $hasTablePlaceholder = str_contains($body, '@{{table}}') || str_contains($body, '{{table}}');
        $hasAnyTable = str_contains($body, '<table') || str_contains($tableHtml, '<table');

        if ($hasTablePlaceholder) {
            $body = str_replace(['@{{table}}', '{{table}}'], $tableHtml, $body);
        } elseif (!$hasAnyTable) {
            $body .= '<br><br>' . $tableHtml;
        }
        $body = $this->normalizeOfferTables($body);

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
        $html = $this->removeDuplicateSignatureBlocks($html, $signatureHtml);

        // Support legacy placeholders like:
        // {employeeDetails.basicPay} and {employeeDetails.basicPay * 12}
        return $this->replaceEmployeeDetailsTokens($html, $candidate, $salary);
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

    private function renderSignatureHtml(bool $forPdf = false): string
    {
        // For PDF, prefer data-uri to avoid remote fetching issues.
        $path = public_path('assets/signature.svg');
        if (!is_file($path)) {
            return '';
        }

        $src = asset('assets/signature.svg');
        if ($forPdf) {
            $data = base64_encode((string) file_get_contents($path));
            $src = "data:image/svg+xml;base64,{$data}";
        }

        return '<div style="margin-top:10px;">'
            . '<img src="' . $src . '" alt="Signature" style="max-width:240px; height:auto; display:block;">'
            . '</div>';
    }

    private function placeSignatureAfterWelcomeLine(string $html, string $signatureHtml): string
    {
        if ($signatureHtml === '') {
            return $html;
        }

        $html = str_replace(['@{{signature}}', '{{signature}}'], '', $html);
        $html = preg_replace(
            '/<div[^>]*>\s*<img(?=[^>]*\balt=["\']Signature["\'])[^>]*>\s*<\/div>/i',
            '',
            $html
        ) ?? $html;
        $html = preg_replace(
            '/<img(?=[^>]*\balt=["\']Signature["\'])[^>]*>/i',
            '',
            $html
        ) ?? $html;

        $welcomePattern = '/(<p[^>]*>[^<]*We welcome you to our organization and look forward to your contribution to the growth of the organization\.[\s\S]*?<\/p>)/i';
        if (preg_match($welcomePattern, $html)) {
            return preg_replace($welcomePattern, '$1' . $signatureHtml, $html, 1) ?? $html;
        }

        $welcomeTextPattern = '/(We welcome you to our organization and look forward to your contribution to the growth of the organization\.)/i';
        if (preg_match($welcomeTextPattern, $html)) {
            return preg_replace($welcomeTextPattern, '$1' . $signatureHtml, $html, 1) ?? $html;
        }

        return $html . $signatureHtml;
    }

    private function removeDuplicateSignatureBlocks(string $html, string $signatureHtml): string
    {
        if ($signatureHtml === '') {
            return $html;
        }

        $found = false;

        return preg_replace_callback(
            '/<div[^>]*>\s*<img(?=[^>]*\balt=["\']Signature["\'])[^>]*>\s*<\/div>|<img(?=[^>]*\balt=["\']Signature["\'])[^>]*>/i',
            function (array $match) use (&$found) {
                if ($found) {
                    return '';
                }

                $found = true;
                return $match[0];
            },
            $html
        ) ?? $html;
    }

    private function normalizeOfferTables(string $html): string
    {
        if (!str_contains(strtolower($html), '<table')) {
            return $html;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $wrappedHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';

        if (!$dom->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();
            return $html;
        }

        foreach ($dom->getElementsByTagName('table') as $table) {
            $maxColumns = 0;
            $rows = [];

            foreach ($table->getElementsByTagName('tr') as $row) {
                $columns = 0;
                $cells = [];

                foreach ($row->childNodes as $cell) {
                    if (!$cell instanceof \DOMElement || !in_array(strtolower($cell->tagName), ['td', 'th'], true)) {
                        continue;
                    }

                    $columns += max(1, (int) $cell->getAttribute('colspan'));
                    $cells[] = $cell;
                }

                if ($columns > 0) {
                    $maxColumns = max($maxColumns, $columns);
                    $rows[] = ['columns' => $columns, 'cells' => $cells];
                }
            }

            if ($maxColumns < 2) {
                continue;
            }

            foreach ($rows as $row) {
                if ($row['columns'] >= $maxColumns || empty($row['cells'])) {
                    continue;
                }

                $lastCell = $row['cells'][count($row['cells']) - 1];
                $currentColspan = max(1, (int) $lastCell->getAttribute('colspan'));
                $lastCell->setAttribute('colspan', (string) ($currentColspan + ($maxColumns - $row['columns'])));
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            libxml_clear_errors();
            return $html;
        }

        $normalized = '';
        foreach ($body->childNodes as $child) {
            $normalized .= $dom->saveHTML($child);
        }

        libxml_clear_errors();
        return $normalized;
    }

    private function stripLegacyCompanyHeader(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $html = preg_replace('/^(\s*(&lt;&gt;|<>)\s*)+/i', '', $html) ?? $html;

        $patterns = [
            // Raw or HTML-encoded <> followed by HSBE address block.
            '/^(\s*(&lt;&gt;|<>)\s*)?HSBE\s+LIMITED\s*<br\s*\/?>\s*Corporate Office[^<]*<br\s*\/?>\s*Dynasty Business Park[^<]*<br\s*\/?>\s*Maharashtra[^<]*<br\s*\/?>\s*(?:<p[^>]*>EmailId:[^<]*<\/p>\s*<br\s*\/?>\s*)?(?:<div[^>]*><\/div>\s*)?/is',
            // Standalone leading diamond markers.
            '/^(\s*(&lt;&gt;|<>)\s*)+/i',
        ];

        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, '', $html, 1) ?? $html;
        }

        return ltrim($html);
    }

    private function sanitizeReactishMarkup(string $html, string $imagesHtml, string $signatureHtml): string
    {
        // 1) Replace a typical JSX images map block (or just the map expression).
        $html = preg_replace('/\{images\.map\([\s\S]*?\)\)\s*\}/', '@{{images}}', $html) ?? $html;

        // If wrapper div exists around the map, replace whole block with images.
        $html = preg_replace(
            '/<div[^>]*>\s*@\{\{images\}\}\s*<\/div>/i',
            '@{{images}}',
            $html
        ) ?? $html;

        // Now resolve @{{images}} into actual images HTML, or remove it when no images exist.
        $html = str_replace(['@{{images}}', '{{images}}'], $imagesHtml, $html);

        // 2) Remove React-style head signature binding if present.
        // Signature should be controlled only via @{{signature}} placeholder.
        $html = preg_replace('/<img[^>]*src=\{headImg\}[^>]*>/i', '', $html) ?? $html;

        return $html;
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

    private function replaceEmployeeDetailsTokens(string $html, Candidate $candidate, ?array $salary = null): string
    {
        $attrs = $candidate->getAttributes();
        $salary ??= $this->calculateSalaryStructure($candidate);
        $computed = [
            'basic_pay' => $salary['basic_monthly'],
            'hra' => $salary['hra_monthly'],
            'monthly_salary' => $salary['gross_monthly'],
            'gross_salary' => $salary['gross_monthly'],
            'special_allowance' => $salary['special_allowance_monthly'],
            'epf_employee' => $salary['epf_employee_monthly'],
            'epf_employer' => $salary['epf_employer_monthly'],
            'esic_employee' => $salary['esic_employee_monthly'],
            'esic_employer' => $salary['esic_employer_monthly'],
            'gratuity_employee' => $salary['gratuity_employee_monthly'],
            'gratuity_employer' => $salary['gratuity_employer_monthly'],
            'in_hand_salary' => $salary['in_hand_monthly'],
            'ctc_annual' => $salary['ctc_annual'],
        ];

        $keyMap = [
            // map common camelCase tokens to DB columns
            'basicPay' => 'basic_pay',
            'hra' => 'hra',
            'monthlySalary' => 'monthly_salary',
            'grossSalary' => 'gross_salary',
            'specialAllowance' => 'special_allowance',
            'epfEmployee' => 'epf_employee',
            'epfEmployer' => 'epf_employer',
            'esicEmployee' => 'esic_employee',
            'esicEmployer' => 'esic_employer',
            'gratuityEmployee' => 'gratuity_employee',
            'gratuityEmployer' => 'gratuity_employer',
            'inHandSalary' => 'in_hand_salary',
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

        $get = function (string $tokenKey) use ($attrs, $keyMap, $computed): float|string {
            $column = $keyMap[$tokenKey] ?? $tokenKey;
            if (array_key_exists($column, $computed)) {
                return (float) $computed[$column];
            }
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

    private function calculateSalaryStructure(Candidate $candidate): array
    {
        $ctcAnnual = (float) ($candidate->ctc_annual ?? 0);
        $ctcMonthly = $ctcAnnual > 0 ? $ctcAnnual / 12 : 0.0;

        if ($ctcAnnual > 0) {
            $basicAnnual = $ctcAnnual * 0.50;
            $basicMonthly = $basicAnnual / 12;
            $hraAnnual = $basicAnnual * 0.40;
            $hraMonthly = $hraAnnual / 12;
        } else {
            $basicMonthly = (float) ($candidate->basic_pay ?? 0);
            $hraMonthly = (float) ($candidate->hra ?? 0);
            $basicAnnual = $basicMonthly * 12;
            $hraAnnual = $hraMonthly * 12;
            $ctcMonthly = (float) ($candidate->monthly_salary ?? ($basicMonthly + $hraMonthly));
            $ctcAnnual = $ctcMonthly * 12;
        }

        $epfEmployeeMonthly = $basicMonthly * 0.12;
        $epfEmployerMonthly = $basicMonthly * 0.12;
        $gratuityEmployeeMonthly = 0.0;
        $gratuityEmployerMonthly = $basicMonthly * 0.0481;

        $grossMonthly = $ctcMonthly > 0
            ? ($ctcMonthly - ($basicMonthly * 0.1681)) / 1.0325
            : ($basicMonthly + $hraMonthly);

        $esicEmployeeMonthly = $grossMonthly * 0.0075;
        $esicEmployerMonthly = $grossMonthly * 0.0325;

        $specialAllowanceMonthly = max(0, $grossMonthly - $basicMonthly - $hraMonthly);
        $inHandMonthly = $grossMonthly - $epfEmployeeMonthly - $esicEmployeeMonthly;

        return [
            'ctc_monthly' => $ctcMonthly,
            'ctc_annual' => $ctcAnnual,
            'basic_monthly' => $basicMonthly,
            'basic_annual' => $basicAnnual,
            'hra_monthly' => $hraMonthly,
            'hra_annual' => $hraAnnual,
            'gross_monthly' => $grossMonthly,
            'gross_annual' => $grossMonthly * 12,
            'special_allowance_monthly' => $specialAllowanceMonthly,
            'special_allowance_annual' => $specialAllowanceMonthly * 12,
            'epf_employee_monthly' => $epfEmployeeMonthly,
            'epf_employee_annual' => $epfEmployeeMonthly * 12,
            'epf_employer_monthly' => $epfEmployerMonthly,
            'epf_employer_annual' => $epfEmployerMonthly * 12,
            'esic_employee_monthly' => $esicEmployeeMonthly,
            'esic_employee_annual' => $esicEmployeeMonthly * 12,
            'esic_employer_monthly' => $esicEmployerMonthly,
            'esic_employer_annual' => $esicEmployerMonthly * 12,
            'gratuity_employee_monthly' => $gratuityEmployeeMonthly,
            'gratuity_employee_annual' => $gratuityEmployeeMonthly * 12,
            'gratuity_employer_monthly' => $gratuityEmployerMonthly,
            'gratuity_employer_annual' => $gratuityEmployerMonthly * 12,
            'in_hand_monthly' => $inHandMonthly,
            'in_hand_annual' => $inHandMonthly * 12,
        ];
    }

    private function buildCtcBreakdown(Candidate $candidate, ?array $salary = null): array
    {
        $salary ??= $this->calculateSalaryStructure($candidate);

        return [
            'basic' => $this->money($salary['basic_monthly']),
            'basic_annual' => $this->money($salary['basic_annual']),
            'hra' => $this->money($salary['hra_monthly']),
            'hra_annual' => $this->money($salary['hra_annual']),
            'gross' => $this->money($salary['gross_monthly']),
            'gross_annual' => $this->money($salary['gross_annual']),
            'special_allowance' => $this->money($salary['special_allowance_monthly']),
            'special_allowance_annual' => $this->money($salary['special_allowance_annual']),
            'epf_employee' => $this->money($salary['epf_employee_monthly']),
            'epf_employee_annual' => $this->money($salary['epf_employee_annual']),
            'epf_employer' => $this->money($salary['epf_employer_monthly']),
            'epf_employer_annual' => $this->money($salary['epf_employer_annual']),
            'esic_employee' => $this->money($salary['esic_employee_monthly']),
            'esic_employee_annual' => $this->money($salary['esic_employee_annual']),
            'esic_employer' => $this->money($salary['esic_employer_monthly']),
            'esic_employer_annual' => $this->money($salary['esic_employer_annual']),
            'gratuity_employee' => $this->money($salary['gratuity_employee_monthly']),
            'gratuity_employee_annual' => $this->money($salary['gratuity_employee_annual']),
            'gratuity_employer' => $this->money($salary['gratuity_employer_monthly']),
            'gratuity_employer_annual' => $this->money($salary['gratuity_employer_annual']),
            'total_monthly' => $this->money($salary['gross_monthly']),
            'total_annual' => $this->money($salary['gross_annual']),
            'ctc_monthly' => $this->money($salary['ctc_monthly']),
            'ctc_annual' => $this->money($salary['ctc_annual']),
            'in_hand_monthly' => $this->money($salary['in_hand_monthly']),
            'in_hand_annual' => $this->money($salary['in_hand_annual']),
        ];
    }

    private function salaryPlaceholderMap(array $salary): array
    {
        $ctc = $this->buildCtcBreakdown(new Candidate(), $salary);

        return [
            'basic_pay' => $ctc['basic'],
            'hra' => $ctc['hra'],
            'monthly_salary' => $ctc['gross'],
            'gross_salary' => $ctc['gross'],
            'gross_salary_annual' => $ctc['gross_annual'],
            'special_allowance' => $ctc['special_allowance'],
            'special_allowance_annual' => $ctc['special_allowance_annual'],
            'epf_employee' => $ctc['epf_employee'],
            'epf_employee_annual' => $ctc['epf_employee_annual'],
            'epf_employer' => $ctc['epf_employer'],
            'epf_employer_annual' => $ctc['epf_employer_annual'],
            'esic_employee' => $ctc['esic_employee'],
            'esic_employee_annual' => $ctc['esic_employee_annual'],
            'esic_employer' => $ctc['esic_employer'],
            'esic_employer_annual' => $ctc['esic_employer_annual'],
            'gratuity_employee' => $ctc['gratuity_employee'],
            'gratuity_employee_annual' => $ctc['gratuity_employee_annual'],
            'gratuity_employer' => $ctc['gratuity_employer'],
            'gratuity_employer_annual' => $ctc['gratuity_employer_annual'],
            'in_hand_salary' => $ctc['in_hand_monthly'],
            'in_hand_salary_annual' => $ctc['in_hand_annual'],
        ];
    }

    private function money(float $value): string
    {
        // Keep it simple for letters/PDF: no decimals unless needed.
        $decimals = (abs($value - round($value)) < 0.00001) ? 0 : 2;
        return number_format($value, $decimals);
    }
    
}
