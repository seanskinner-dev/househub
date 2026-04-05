<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>

    <style>
        body {
            font-family: Georgia, serif;
            background:#f5f5f5;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .certificate {
            background:white;
            padding:60px;
            border:8px solid #333;
            width:800px;
            text-align:center;
        }

        h1 {
            font-size:36px;
            margin-bottom:20px;
        }

        .student {
            font-size:28px;
            margin:20px 0;
        }

        .award {
            font-size:22px;
            margin:20px 0;
        }

        .date {
            margin-top:40px;
            color:#555;
        }

        .print-btn {
            margin-top:30px;
            display:inline-block;
            padding:10px 20px;
            background:#333;
            color:white;
            text-decoration:none;
            cursor:pointer;
        }

        @media print {
            .print-btn {
                display:none;
            }
        }
    </style>
</head>

<body>

<div class="certificate">
    <h1>Certificate of Achievement</h1>

    <div>This certificate is proudly presented to</div>

    <div class="student">
        {{ $award->first_name }} {{ $award->last_name }}
    </div>

    <div class="award">
        For: {{ $award->name }}
    </div>

    <div>
        {{ $award->description }}
    </div>

    <div class="date">
        {{ \Carbon\Carbon::parse($award->awarded_at)->format('d F Y') }}
    </div>

    <div class="print-btn" onclick="window.print()">
        Print Certificate
    </div>
</div>

</body>
</html>
