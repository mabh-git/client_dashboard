<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visit Export</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visits Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Type</th>
                <th>Status</th>
                <th>Planned Date</th>
                <th>Completed Date</th>
                <th>Fit for Work</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
            <tr>
                <td>{{ $visit->id }}</td>
                <td>{{ $visit->employee->name }}</td>
                <td>{{ $visit->type }}</td>
                <td>{{ $visit->etat }}</td>
                <td>{{ $visit->envisagee }}</td>
                <td>{{ $visit->effectuee }}</td>
                <td>{{ $visit->apte ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
