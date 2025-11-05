<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="title">Payslip</h1>
                <p class="subtitle"> {{ $salary->payroll->month_string }}</p>
            </div>
            <div>
                <img src="{{ asset('logo.png') }}" alt="Company Logo" class="h-10 w-auto">
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Employee Details</h2>
                <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                    <div>
                        <span class="font-medium">Name:</span> {{ $salary->employee->name}}
                    </div>
                    <div>
                        <span class="font-medium">Position:</span> {{ $salary->employee->designation->name}}
                    </div>
                    <div>
                        <span class="font-medium">Employee ID:</span> {{ sprintf('%04d', $salary->employee_id)  }}
                    </div>
                    <div>
                        <span class="font-medium">Department:</span> {{ $employee->department ?? 'Finance' }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Salary Breakdown</h2>
                <table class="w-full mt-2 text-sm">
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Basic Salary</td>
                            <td class="py-2 text-right text-gray-800"><sup>KES</sup>{{ number_format($salary->gross_salary, 2) }}</td>
                        </tr>
                        
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600"> Total Deductions</td>
                            <td class="py-2 text-right text-red-600">-<sup>KES</sup>{{ number_format($salary->breakdown->getDeductions(), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-bold text-gray-700">Net Pay</td>
                            <td class="py-2 text-right font-bold text-green-700"><sup>KES</sup>{{ number_format($salary->breakdown->getNetpay(), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Deductions</h2>
                <table class="w-full mt-2 text-sm">
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">NSSF</td>
                            <td class="py-2 text-right text-red-600"><sup>KES</sup>{{ number_format($salary->breakdown->getNssfDeduction(), 2) }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">SHIF</td>
                            <td class="py-2 text-right text-red-600"><sup>KES</sup>{{ number_format($salary->breakdown->getShifDeduction(), 2) }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">AHL</td>
                            <td class="py-2 text-right text-red-600"><sup>KES</sup>{{ number_format($salary->breakdown->getAhlDeduction(), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium text-gray-600">PAYE</td>
                            <td class="py-2 text-right text-red-600"><sup>KES</sup>{{ number_format($salary->breakdown->getPaye(), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
        <div class="bg-gray-50 px-6 py-4 text-xs text-gray-500 flex justify-between items-center">
            <span>Generated on {{ date('Y-m-d') }}</span>
            <span>&copy; {{ date('Y') }} Your Company Name</span>
        </div>
    </div>
</body>
</html>