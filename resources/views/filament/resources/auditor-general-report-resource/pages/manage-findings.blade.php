<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Current Report Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Report Summary</h3>
            <div class="flex justify-between gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Total Findings</div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ $this->record->total_findings_count }}</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Monitory Findings</div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ $this->record->total_monitory_findings_count }}</div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Other Findings</div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ $this->record->total_non_monitory_findings_count }}</div>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="text-sm text-green-600 dark:text-green-400">Total Amount Due (GH¢)</div>
                    <div class="text-2xl font-bold text-green-900 dark:text-green-300">
                        {{ number_format($this->record->total_amount_involved, 2) }}
                    </div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <div class="text-sm text-purple-600 dark:text-purple-400">Total Recoveries (GH¢)</div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-300">{{ number_format($this->record->total_recoveries, 2) }}</div>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg">
                    <div class="text-sm text-amber-600 dark:text-amber-400">Recovery Rate</div>
                    <div class="text-2xl font-bold text-amber-900 dark:text-amber-300">
                        {{ $this->record->total_amount_involved > 0 ? number_format(($this->record->total_recoveries / $this->record->total_amount_involved) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Findings Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Available Findings</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select findings to include in this Auditor General's Report</p>
            </div>

            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>