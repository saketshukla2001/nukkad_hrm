<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        @page { margin: 16mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Times New Roman", serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
        }
        table {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse;
            margin: 16px 0;
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
            box-sizing: border-box;
        }
        table td.text-left {
            text-align: left;
        }

        /* DomPDF page-break fixes */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; }
        table { page-break-inside: auto; }

        img, svg { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    {!! $content !!}
</body>
</html>
