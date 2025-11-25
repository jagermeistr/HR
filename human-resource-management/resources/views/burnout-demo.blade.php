<!DOCTYPE html>
<html>
<head>
    <title>Burnout Detection Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .low { color: green; font-weight: bold; }
        .medium { color: orange; font-weight: bold; }
        .high { color: red; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .risk-high { background-color: #ffebee; }
        .risk-medium { background-color: #fff3e0; }
        .risk-low { background-color: #e8f5e8; }
    </style>
</head>
<body>
    <h1>🔥 Employee Burnout Detection System</h1>
    
    <table>
        <tr>
            <th>Employee</th>
            <th>Burnout Risk</th>
            <th>Avg Hours/Day</th>
            <th>Late Days</th>
            <th>Absent Days</th>
            <th>Overtime Days</th>
            <th>Weekend Work</th>
            <th>Status</th>
        </tr>
        @foreach($results as $result)
        <tr class="risk-{{ $result['burnout_risk'] }}">
            <td><strong>{{ $result['employee'] }}</strong></td>
            <td class="{{ $result['burnout_risk'] }}">
                {{ strtoupper($result['burnout_risk']) }} RISK
            </td>
            <td>{{ $result['average_hours'] }}h</td>
            <td>{{ $result['late_days'] }}</td>
            <td>{{ $result['absent_days'] }}</td>
            <td>{{ $result['overtime_days'] }}</td>
            <td>{{ $result['weekend_work'] }}</td>
            <td>
                @if($result['burnout_risk'] == 'high')
                🔴 HIGH RISK - Immediate Intervention Needed
                @elseif($result['burnout_risk'] == 'medium')
                🟡 MEDIUM RISK - Monitor Closely
                @else
                🟢 LOW RISK - Healthy Work Pattern
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    
    <div style="margin-top: 30px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
        <h3>🎯 Burnout Detection Criteria:</h3>
        <ul>
            <li>📊 <strong>Average Hours > 9h:</strong> +3 points</li>
            <li>⏰ <strong>Average Hours > 11h:</strong> +2 points</li>
            <li>🚨 <strong>Late Days > 5:</strong> +2 points</li>
            <li>❌ <strong>Absent Days > 3:</strong> +2 points</li>
            <li>💼 <strong>Overtime Days (>10h) > 8:</strong> +3 points</li>
            <li>📅 <strong>Weekend Work > 4:</strong> +2 points</li>
            <li><strong>High Risk:</strong> ≥8 points | <strong>Medium Risk:</strong> 5-7 points</li>
        </ul>
    </div>
</body>
</html>