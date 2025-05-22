<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }

        .meta {
            margin-bottom: 20px;
        }

        .meta div {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 8px;
        }

        .summary {
            margin-top: 30px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div>Generated on {{ $date }}</div>
    </div>

    <div class="meta">
        <div><strong>Period:</strong> {{ $from_date }} to {{ $to_date }}</div>
        <div><strong>Total Hours:</strong> {{ number_format($total_hours, 2) }}</div>
        <div><strong>Billable Hours:</strong> {{ number_format($billable_hours, 2) }}</div>
        <div><strong>Non-Billable Hours:</strong> {{ number_format($non_billable_hours, 2) }}</div>
    </div>

    <h3>Project Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Total Hours</th>
                <th>Billable Hours</th>
                <th>Billable Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project['project_title'] }}</td>
                    <td>{{ $project['client_name'] }}</td>
                    <td>{{ number_format($project['total_hours'], 2) }}</td>
                    <td>{{ number_format($project['billable_hours'], 2) }}</td>
                    <td>${{ number_format($project['billable_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Detailed Time Logs</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Hours</th>
                <th>Description</th>
                <th>Billable</th>
                <th>Tags</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($time_logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->start_time)->format('Y-m-d') }}</td>
                    <td>{{ $log->project->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }}</td>
                    <td>{{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->format('H:i') : '-' }}</td>
                    <td>{{ number_format($log->hours, 2) }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->is_billable ? 'Yes' : 'No' }}</td>
                    <td>{{ $log->tags }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Freelance Time Tracker - Generated Report</p>
    </div>
</body>

</html>
