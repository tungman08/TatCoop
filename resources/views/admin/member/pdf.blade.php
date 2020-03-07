<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <!-- Theme style -->
    {{ Html::style(elixir('css/admin-lte.css')) }}

    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/THSarabunNew.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ asset('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: normal;
            src: url("{{ asset('fonts/THSarabunNew-Italic.ttf') }}") format('truetype');
        }    

        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/THSarabunNew-BoldItalic.ttf') }}") format('truetype');
        }

        * {
            font-family: "THSarabunNew";
            font-size: 16px;
        }

        h3 {
            line-height: 0.6;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 2px solid #ddd;
        }

        .table tr th {
            background-color: #fcfcfc;
            font-style: bold;    
        }

        .table tr th, .table tr td {
            padding: 3px 8px;
        }

        .table tr th {
            vertical-align: middle;
        }

        .table tr td {
            vertical-align: top;
        }

        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
            padding: 0px 0px 8px 0px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    อยู่ในระหว่างดำเนินการ กรุณาเลือกเมนูพิมพ์เอกสาร
</body>
</html>