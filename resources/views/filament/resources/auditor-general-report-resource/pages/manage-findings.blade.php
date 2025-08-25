<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Current Report Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm text-blue-600">Current Findings</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $this->record->total_findings_count }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-green-600">Total Amount (GH¢)</div>
                    <div class="text-2xl font-bold text-green-900">{{ number_format($this->record->total_amount_involved, 2) }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-sm text-purple-600">Total Recoveries (GH¢)</div>
                    <div class="text-2xl font-bold text-purple-900">{{ number_format($this->record->total_recoveries, 2) }}</div>
                </div>
                <div class="bg-amber-50 p-4 rounded-lg">
                    <div class="text-sm text-amber-600">Recovery Rate</div>
                    <div class="text-2xl font-bold text-amber-900">
                        {{ $this->record->total_amount_involved > 0 ? number_format(($this->record->total_recoveries / $this->record->total_amount_involved) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Findings Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Available Findings</h3>
                <p class="text-sm text-gray-600 mt-1">Select findings to include in this Auditor General's Report</p>
            </div>
            
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>