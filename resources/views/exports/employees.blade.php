<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Export</title>
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
        <h1>Employee Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Matricule</th>
                <th>Department</th>
                <th>Position</th>
                <th>Start Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->id }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->matricule }}</td>
                <td>{{ $employee->departement }}</td>
                <td>{{ $employee->poste }}</td>
                <td>{{ $employee->startDate }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
