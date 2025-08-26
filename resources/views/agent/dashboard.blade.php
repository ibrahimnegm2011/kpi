<x-app-layout>
    <div class="flex justify-between ml-4 mb-2">
        <h1 class="text-3xl text-black font-semibold"><i class="fas fa-chart-column mr-3"></i> Dashboard </h1>
    </div>
    <div class="flex flex-col lg:flex-row p-4 space-y-4 lg:space-y-0 lg:space-x-6">
        {{-- Left Sidebar Filters --}}
        <div class="w-full lg:w-1/4 space-y-4">

            {{-- Filter Card Style --}}
            <form class="p-4 rounded space-y-10" id="search-form">
                <div>
                    <div class="bg-primary-500 text-white text-center font-bold px-3 py-1 rounded-t-md">Company</div>
                    <div class="bg-primary-50 text-sm p-3 rounded-b">
                        <select class="w-full border border-primary-700" name="filter[company]">
                            <option value="" disabled {{!request('filter.company') ? 'selected': ''}}>Select Company</option>
                            @foreach($companies as $id => $name)
                                <option value="{{$id}}" {{request('filter.company') == $id ? 'selected': ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="bg-primary-500 text-white text-center font-bold px-3 py-1 rounded-t-md">Department</div>
                    <div class="bg-primary-50 text-sm p-3 rounded-b">
                        <select class="w-full border border-primary-700" name="filter[department]">
                            <option value="" disabled {{!request('filter.department') ? 'selected': ''}}>Select Department</option>
                            @foreach($departments as $id => $name)
                                <option value="{{$id}}" {{request('filter.department') == $id ? 'selected': ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="bg-primary-500 text-white text-center font-bold px-3 py-1 rounded-t-md">Year</div>
                    <div class="bg-primary-50 text-sm p-3 rounded-b">
                        <select class="w-full border border-primary-700" name="filter[year]">
                            @php
                                $minYear = \App\Models\Forecast::min('year') ?? date('Y');
                                $maxYear = \App\Models\Forecast::max('year') ?? date('Y');
                            @endphp
                            @if(isset($minYear) && isset($maxYear))
                                @for($year = $minYear; $year <= $maxYear; $year++)
                                    <option value="{{ $year }}" {{ request('filter.year', now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            @endif
                        </select>
                    </div>
                </div>

                <div>
                    <div class="bg-primary-500 text-white text-center font-bold px-3 py-1 rounded-t-md">Month</div>
                    <div class="bg-primary-50 text-sm p-3 rounded-b">
                        @php
                            $preselected = array_map('intval', json_decode(request('filter.months', '[]')));
                            $monthNames = [];
                            foreach (range(1, 12) as $m) {
                                $monthNames[$m] = \Carbon\Carbon::create()->month($m)->format('F');
                            }
                        @endphp

                        <div
                            x-data="monthMultiSelect({
                                initial: @js($preselected),
                                names: @js($monthNames)
                            })"
                            class="relative"
                        >
                            <!-- Trigger -->
                            <button
                                type="button"
                                class="w-full border border-primary-700 bg-white rounded px-3 py-2 text-left flex items-center justify-between"
                                @click="openMenu()"
                                @keydown.escape.prevent.stop="closeAndSubmitIfChanged()"
                            >
                                <span x-text="label()"></span>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div
                                x-show="open"
                                @click.away="closeAndSubmitIfChanged()"
                                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded shadow"
                                style="display: none;"
                            >
                                <div class="max-h-60 overflow-auto py-2">
                                    @foreach($monthNames as $num => $name)
                                        <label class="flex items-center gap-2 px-3 py-1 hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                value="{{ $num }}"
                                                :checked="draft.indexOf({{ $num }}) > -1"
                                                @change="toggle({{ $num }})"
                                            >
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-between border-t px-3 py-2 text-xs text-gray-600">
                                    <button type="button" class="hover:underline" @click="selectAll()">Select all</button>
                                    <button type="button" class="hover:underline" @click="clearAll()">Clear</button>
                                    <button type="button" class="text-primary-700 font-semibold" @click="closeAndSubmitIfChanged()">Done</button>
                                </div>
                            </div>

{{--                            <!-- Hidden inputs (only enabled for selected months) -->--}}
{{--                            @for($i = 1; $i <= 12; $i++)--}}
{{--                                <input type="hidden" name="filter[months][]" value="{{ $i }}" :disabled="selected.indexOf({{ $i }}) === -1">--}}
{{--                            @endfor--}}

                            <input type="hidden" name="filter[months]" x-ref="monthsField" :value="JSON.stringify(selected)">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right KPI Panels --}}
        <div class="w-full lg:w-3/4 space-y-6">
            @forelse($data as $category => $kpis)
                @php
                    $defaultKpiId = optional($kpis->first())->id;
                    $filtersJson = [
                        'company' => request('filter.company'),
                        'department' => request('filter.department'),
                        'year' => request('filter.year', now()->year),
                        'months' => $preselected,
                    ];
                @endphp
                <div class="bg-gradient-to-b from-transparent to-primary-50 rounded shadow p-4 relative kpi-card" data-initial-kpi-id="{{ $defaultKpiId }}">
                    <h2 class="text-lg font-semibold ml-2 pt-1">{{ $category }}</h2>

                    <div class="absolute top-4 right-4">
                        <select class="border border-primary-700 kpi-select">
                            @foreach($kpis as $kpi)
                                <option value="{{ $kpi->id }}" {{ $kpi->id == $defaultKpiId ? 'selected' : '' }}>
                                    {{ $kpi->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <img src="{{asset('images/underline.png')}}" class="w-48 h-10">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start mt-4">
                        <div class="col-span-1">
                            <div class="bg-gray-100 border border-gray-300 rounded-lg text-center space-y-2 pb-4">
                                <div class="w-full bg-secondary-500 text-white text-center text-xs font-bold px-3 py-1 rounded-t-md kpi-title">—</div>

                                <div class="flex items-center justify-center text-4xl font-bold mx-4 kpi-value-wrap">
                                    <span class="kpi-value">—</span>
                                    <span class="ml-5 mt-1 text-sm kpi-alert" style="display:none;">
                                        <span class="fa-stack fa-1x" aria-label="Important">
                                            <i class="fa-regular fa-circle fa-stack-2x"></i>   <!-- outline ring -->
                                            <i class="fa-solid fa-exclamation fa-stack-1x"></i> <!-- exclamation -->
                                        </span>
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 mx-4">
                                    Goal: <span class="kpi-goal">—</span>
                                    <span class="kpi-percent-wrap">
                                        (<span class="kpi-percent">—</span>)
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 kpi-loading" style="display:none;">Loading…</div>
                                <div class="text-xs text-red-600 kpi-error" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="col-span-3 self-center">
                            <div class="bg-gray-50 rounded flex items-center justify-center w-full" style="height: 10rem;">
                                <canvas class="kpi-chart w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <h2 class="text-lg font-semibold ml-2 pt-1 text-gray-500 text-center">
                    @if(request('filter.company') && request('filter.department'))
                        No KPIs for selected company/department.
                    @else
                        Please Select Company and Department.
                    @endif
                </h2>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
        <script>
            // Alpine v2 view-only component
            function monthMultiSelect(config) {
                const toNumberArray = (arr) => (Array.isArray(arr) ? arr.map(n => Number(n)) : []);

                return {
                    open: false,
                    selected: toNumberArray(config.initial || []),
                    draft: toNumberArray(config.initial || []),
                    names: config.names || {},

                    // Open with a fresh draft copy
                    openMenu() {
                        this.draft = this.selected.slice();
                        this.open = true;
                    },

                    // Close and submit only if changed
                    closeAndSubmitIfChanged() {
                        if (!this.open) return;
                        this.open = false;

                        const a = this.selected.slice().sort((x,y)=>x-y);
                        const b = this.draft.slice().sort((x,y)=>x-y);
                        const changed = JSON.stringify(a) !== JSON.stringify(b);

                        if (changed) {
                            this.selected = this.draft.slice();
                            this.$nextTick(() => {
                                $('#search-form').submit();
                            });
                        }
                    },

                    toggle(v) {
                        v = Number(v);
                        const i = this.draft.indexOf(v);
                        if (i > -1) this.draft.splice(i, 1);
                        else this.draft.push(v);
                    },

                    selectAll() { this.draft = [1,2,3,4,5,6,7,8,9,10,11,12]; },
                    clearAll()  { this.draft = []; },

                    label() {
                        if (this.selected.length === 0) return 'Select months';
                        if (this.selected.length === 12) return 'All months';
                        const picked = this.selected.slice().sort((a,b)=>a-b).map(n => this.names[n] || n);
                        return picked.length <= 2 ? picked.join(', ') : `${this.selected.length} selected`;
                    },
                };
            }

            $(function () {
                var $form = $('#search-form');
                if (!$form.length) return;

                function canSubmit() {
                    var company = ($form.find('[name="filter[company]"]').val() || '').toString().trim();
                    var department = ($form.find('[name="filter[department]"]').val() || '').toString().trim();
                    return !!company && !!department;
                }

                // Submit on any change if both required fields are set
                $form.on('change', 'select', function () {
                    if (canSubmit()) {
                        $form.trigger('submit');
                    }
                });

                // Guard manual submits (Enter key, etc.)
                $form.on('submit', function (e) {
                    if (!canSubmit()) {
                        e.preventDefault();
                    }
                });
            });


            (function ($) {
                function formatNumber(n) {
                    if (n == null || isNaN(n)) return '—';
                    const num = Number(n), abs = Math.abs(num);
                    if (abs >= 1_000_000) return (num / 1_000_000).toFixed(2) + 'M';
                    if (abs >= 1_000)     return (num / 1_000).toFixed(2) + 'k';
                    return num.toLocaleString(undefined, { maximumFractionDigits: 2 });
                }
                function formatPercent(p) {
                    if (p == null || isNaN(p)) return '—';
                    return Number(p).toFixed(2) + '%';
                }
                function monthName(n) {
                    const names = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    const i = Number(n) - 1;
                    return names[i] || String(n);
                }

                $(function () {
                    $('.kpi-card').each(function () {
                        var $card = $(this);
                        var endpoint = '{{route('agent.summary.chart')}}';
                        var selectedKpiId = $card.data('initial-kpi-id') || null;
                        var filters = {};
                        try { filters = JSON.parse('@json($filtersJson ?? [])' || '{}') || {}; } catch (e) { filters = {}; }

                        // Cache elements
                        var $select   = $card.find('.kpi-select');
                        var $title    = $card.find('.kpi-title');
                        var $value    = $card.find('.kpi-value');
                        var $goal     = $card.find('.kpi-goal');
                        var $percent  = $card.find('.kpi-percent');
                        var $valWrap  = $card.find('.kpi-value-wrap');
                        var $pctWrap  = $card.find('.kpi-percent-wrap');
                        var $alert    = $card.find('.kpi-alert');
                        var $loading  = $card.find('.kpi-loading');
                        var $error    = $card.find('.kpi-error');
                        var canvas    = $card.find('.kpi-chart')[0];
                        var chart     = null;

                        function setLoading(on) {
                            if ($loading.length) $loading.toggle(on);
                        }
                        function setError(msg) {
                            if (!$error.length) return;
                            if (msg) { $error.text(msg).show(); } else { $error.text('').hide(); }
                        }

                        function renderInfo(d) {
                            var name = d.name || '';
                            var val  = Number(d.value || 0);
                            var gol  = Number(d.goal  || 0);
                            var pct  = Number(d.percent || 0);

                            if ($title.length)   $title.text(name || '—');
                            if ($value.length)   $value.text(formatNumber(val));
                            if ($goal.length)    $goal.text(formatNumber(gol));
                            if ($percent.length) $percent.text(formatPercent(pct));

                            var isPos = isFinite(pct) && pct >= 100;
                            if ($valWrap.length) {
                                $valWrap.toggleClass('text-green-600', isPos)
                                    .toggleClass('text-red-600', !isPos);
                            }
                            if ($pctWrap.length) {
                                $pctWrap.toggleClass('text-green-600', isPos)
                                    .toggleClass('text-red-500', !isPos);
                            }
                            if ($alert.length) {

                                $alert.toggle(isFinite(pct) && pct < 100);
                            }
                        }

                        function renderChart(d) {
                            if (!canvas || !window.Chart) return;

                            var labels = Array.isArray(d.labels) && d.labels.length ? d.labels : [];
                            if (!labels.length && Array.isArray(d.months)) {
                                labels = d.months.map(monthName);
                            }

                            var targets = Array.isArray(d.targets) ? d.targets.map(Number) : [];
                            var values  = Array.isArray(d.values)  ? d.values.map(Number)  : [];

                            var len = Math.min(labels.length, targets.length, values.length);
                            var L = labels.slice(0, len);
                            var T = targets.slice(0, len);
                            var V = values.slice(0, len);

                            if (!chart) {
                                var ctx = canvas.getContext('2d');
                                chart = new window.Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: L,
                                        datasets: [
                                            {
                                                label: 'Actual',
                                                data: V,
                                                backgroundColor: '#5CA082',
                                                borderColor: '#5CA082',
                                                borderWidth: 1
                                            },
                                            {
                                                label: 'Target',
                                                data: T,
                                                backgroundColor: '#62C2E1',
                                                borderColor: '#62C2E1',
                                                borderWidth: 1
                                            },
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { position: 'bottom' },
                                            tooltip: { mode: 'index', intersect: false }
                                        },
                                        scales: {
                                            x: { stacked: false, ticks: { autoSkip: false, maxRotation: 0 } },
                                            y: { beginAtZero: true }
                                        }
                                    }
                                });
                            } else {
                                chart.data.labels = L;
                                chart.data.datasets[0].data = T;
                                chart.data.datasets[1].data = V;
                                chart.update('none');
                            }
                        }

                        function buildUrl() {
                            var params = {
                                kpi_id: String(selectedKpiId || ''),
                                company: filters.company || '',
                                department: filters.department || '',
                                year: String(filters.year || ''),
                                months: JSON.stringify(filters.months)
                            };
                            var qs = $.param(params, true);
                            return endpoint + '?' + qs;
                        }

                        function load() {
                            if (!endpoint || !selectedKpiId) return;
                            setLoading(true);
                            setError('');

                            $.ajax({url: buildUrl(), method: 'GET', dataType: 'json'})
                                .done(function (data) {
                                    renderInfo(data || {});
                                    renderChart(data || {});
                                })
                                .fail(function () {
                                    setError('Failed to load KPI');
                                })
                                .always(function () {
                                    setLoading(false);
                                });
                        }

                        // Select change
                        if ($select.length) {
                            $select.on('change', function () {
                                selectedKpiId = String($(this).val() || '');
                                load();
                            });
                        }

                        // Initial load
                        load();
                    });
                });
            })(jQuery);
        </script>

    @endpush
</x-app-layout>
