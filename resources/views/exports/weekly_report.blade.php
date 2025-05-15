<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Client Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c5282;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1, h2, h3 {
            color: #2c5282;
        }
        .period-info {
            background-color: #edf2f7;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .stat-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 15px;
            margin-right: 15px;
            margin-bottom: 15px;
            width: calc(50% - 30px);
        }
        .stat-box h3 {
            margin-top: 0;
            font-size: 16px;
            color: #4a5568;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
        }
        .completion-bar {
            height: 20px;
            background-color: #e2e8f0;
            border-radius: 10px;
            margin-top: 5px;
        }
        .completion-progress {
            height: 100%;
            background-color: #4299e1;
            border-radius: 10px;
        }
        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Weekly Client Report</h1>
        <h2>{{ $client->name }}</h2>
    </div>

    <div class="period-info">
        <p><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($period['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period['end'])->format('d/m/Y') }}</p>
        <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <h2>Overview</h2>

    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Employees</h3>
            <div class="stat-value">{{ $stats['employeeCount'] }}</div>
        </div>

        <div class="stat-box">
            <h3>Total Visits</h3>
            <div class="stat-value">{{ $stats['totalVisits'] }}</div>
        </div>

        <div class="stat-box">
            <h3>Completed Visits</h3>
            <div class="stat-value">{{ $stats['completedVisits'] }}</div>
        </div>

        <div class="stat-box">
            <h3>Completion Rate</h3>
            <div class="stat-value">{{ $stats['completionRate'] }}%</div>
            <div class="completion-bar">
                <div class="completion-progress" style="width: {{ $stats['completionRate'] }}%"></div>
            </div>
        </div>
    </div>

    <h2>Contact Information</h2>

    <table>
        <tr>
            <th>Contact Person</th>
            <td>{{ $client->contact_person ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $client->contact_email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>{{ $client->contact_phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>
                {{ $client->address ?? 'N/A' }}
                @if($client->city || $client->postal_code)
                    , {{ $client->postal_code ?? '' }} {{ $client->city ?? '' }}
                @endif
            </td>
        </tr>
    </table>

    <footer>
        <p>This is an automatically generated report. For questions or support, please contact our team.</p>
        <p>&copy; {{ date('Y') }} Client Dashboard System</p>
    </footer>
</body>
</html>
