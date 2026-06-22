@extends('mentor.layouts.dashboard')
@section('title') Harassment Analytics @endsection

@push('styles')
<style>
    :root {
        --primary: #4F46E5;
        --primary-light: #818CF8;
        --primary-dark: #3730A3;
        --gray-50: #F8FAFC;
        --gray-100: #F1F5F9;
        --gray-200: #E2E8F0;
        --gray-300: #CBD5E1;
        --gray-500: #64748B;
        --gray-700: #334155;
        --gray-900: #0F172A;
        --radius: 16px;
        --shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        --transition: all 0.2s ease;
    }

    .analytics-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    /* --- Header --- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .page-header-left h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
        letter-spacing: -0.025em;
        margin: 0;
        line-height: 1.2;
    }

    .page-header-left p {
        margin: 0.25rem 0 0;
        color: var(--gray-500);
        font-size: 0.95rem;
    }

    .page-header-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .btn:hover {
        background: var(--gray-200);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--gray-200);
    }

    .btn-outline:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
    }

    /* --- Filter Bar --- */
    .filter-bar {
        background: white;
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        border: 1px solid var(--gray-200);
    }

    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-bar label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .filter-bar select,
    .filter-bar input[type="date"] {
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
        font-size: 0.85rem;
        background: white;
        color: var(--gray-900);
        transition: var(--transition);
        min-width: 140px;
    }

    .filter-bar select:focus,
    .filter-bar input[type="date"]:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .filter-bar .btn-apply {
        background: var(--gray-900);
        color: white;
        padding: 0.4rem 1.25rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-bar .btn-apply:hover {
        background: var(--gray-700);
        transform: translateY(-1px);
    }

    /* --- Chart Card --- */
    .chart-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.75rem 2rem 2rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .chart-stats {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
    }

    .chart-stats .total-label {
        font-size: 0.9rem;
        color: var(--gray-500);
    }

    .chart-stats .total-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        letter-spacing: -0.02em;
    }

    .chart-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .chart-actions .btn-sm {
        padding: 0.35rem 0.85rem;
        font-size: 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--gray-700);
        cursor: pointer;
        transition: var(--transition);
    }

    .chart-actions .btn-sm:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-1px);
    }

    .chart-actions .btn-sm.primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .chart-actions .btn-sm.primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    /* --- Chart Wrapper --- */
    .chart-wrapper {
        position: relative;
        height: 360px;
        width: 100%;
    }

    /* --- Legend Section --- */
    .legend-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .legend-section h3 {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .legend-section p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-700);
    }

    .legend-swatch {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .analytics-container { padding: 1rem; }
        .page-header { flex-direction: column; align-items: stretch; }
        .page-header-right { flex-wrap: wrap; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { flex-wrap: wrap; }
        .chart-card { padding: 1.25rem; }
        .chart-card-header { flex-direction: column; align-items: stretch; }
        .chart-actions { justify-content: flex-start; }
        .legend-section { flex-direction: column; align-items: flex-start; }
    }

    @media (max-width: 480px) {
        .chart-wrapper { height: 280px; }
        .filter-bar select,
        .filter-bar input[type="date"] { min-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="analytics-container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-header-left">
            <h1>📊 Incident Reports by Type</h1>
            <p>Breakdown of harassment reports assigned to you, grouped by incident type.</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('mentor.harassment.index') }}" class="btn btn-outline">
                ← Back to reports
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <div class="filter-group">
            <label for="preset">Preset</label>
            <form method="GET" action="{{ route('mentor.harassment.analytics') }}" id="filterForm" class="flex items-center gap-2 flex-wrap">
                <select name="preset" id="preset" onchange="this.form.submit()">
                    <option value="">Custom range</option>
                    <option value="7" {{ (isset($range['preset']) && $range['preset']==7) ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ (isset($range['preset']) && $range['preset']==30) ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ (isset($range['preset']) && $range['preset']==90) ? 'selected' : '' }}>Last 90 days</option>
                </select>

                <label for="start_date">From</label>
                <input type="date" name="start_date" id="start_date" value="{{ $range['start_date'] ?? '' }}">

                <label for="end_date">To</label>
                <input type="date" name="end_date" id="end_date" value="{{ $range['end_date'] ?? '' }}">

                <button type="submit" class="btn-apply">Apply</button>
            </form>
        </div>
    </div>

    <!-- CHART CARD -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-stats">
                <span class="total-label">Total reports in range</span>
                <span class="total-number">{{ $total ?? 0 }}</span>
            </div>
            <div class="chart-actions">
                <button id="downloadChart" class="btn-sm primary">⬇ PNG</button>
                <button id="downloadCsv" class="btn-sm">⬇ CSV</button>
                <button id="downloadPdf" class="btn-sm">⬇ PDF</button>
            </div>
        </div>

        <div class="chart-wrapper">
            <canvas id="incidentTypeChart"></canvas>
        </div>

        <!-- LEGEND -->
        <div class="legend-section">
            <div>
                <h3>Legend</h3>
                <p>Count of reports per incident type assigned to you.</p>
            </div>
            <div class="legend-items" id="legendContainer">
                <!-- dynamically populated by JS -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    (function() {
        const labels = {!! json_encode($labels->toArray()) !!} || [];
        const data = {!! json_encode($data->toArray()) !!} || [];
        const percentages = {!! json_encode($percentages->toArray() ?? []) !!} || [];
        const total = {{ $total ?? 0 }};
        const reportRows = {!! json_encode($reportRows->toArray() ?? []) !!} || [];

        // Color palette
        const colorPalette = {
            physical: '#4F46E5',
            verbal:   '#EF4444',
            sexual:   '#F97316',
            cyber:    '#06B6D4',
            other:    '#6B7280'
        };

        const colors = labels.map(l => colorPalette[l] ?? '#9CA3AF');

        // Build legend
        const legendContainer = document.getElementById('legendContainer');
        if (legendContainer) {
            let html = '';
            labels.forEach((label, i) => {
                const displayLabel = label ? label.replace(/_/g, ' ') : label;
                const color = colors[i] || '#9CA3AF';
                html += `
                    <div class="legend-item">
                        <span class="legend-swatch" style="background:${color};"></span>
                        ${displayLabel} <span style="color:var(--gray-500);font-weight:400;">(${data[i] || 0})</span>
                    </div>
                `;
            });
            legendContainer.innerHTML = html;
        }

        // Init Chart
        const ctx = document.getElementById('incidentTypeChart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.map(l => l ? l.replace(/_/g, ' ') : l),
                datasets: [{
                    label: 'Reports',
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 8,
                    maxBarThickness: 48,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        title: {
                            display: true,
                            text: 'Count',
                            color: '#64748B',
                            font: { weight: '500', size: 12 }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { weight: '500', size: 13 },
                            color: '#334155'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#0F172A',
                        bodyColor: '#334155',
                        borderColor: '#E2E8F0',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        callbacks: {
                            label: function(ctx) {
                                const pct = percentages[ctx.dataIndex] ?? 0;
                                return ctx.dataset.label + ': ' + ctx.parsed.x + ' (' + pct + '%)';
                            }
                        }
                    },
                    datalabels: {
                        color: '#0F172A',
                        anchor: 'end',
                        align: 'right',
                        formatter: function(value, ctx) {
                            const pct = percentages[ctx.dataIndex] ?? 0;
                            return pct + '%';
                        },
                        font: { weight: '600', size: 12 },
                        offset: 2
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // --- DOWNLOAD PNG ---
        document.getElementById('downloadChart')?.addEventListener('click', function(e) {
            e.preventDefault();
            const url = chart.toBase64Image();
            const a = document.createElement('a');
            a.href = url;
            a.download = `harassment-analytics-{{ date('Y-m-d') }}.png`;
            document.body.appendChild(a);
            a.click();
            a.remove();
        });

        // --- DOWNLOAD CSV ---
        document.getElementById('downloadCsv')?.addEventListener('click', function(e) {
            e.preventDefault();
            let csv = 'Reference Number,Incident Type,Incident Title,Incident Date,Status,Created At\n';
            for (let i = 0; i < reportRows.length; i++) {
                const r = reportRows[i];
                csv += `"${r.reference_number || ''}","${r.incident_type || ''}","${(r.incident_title || '').replace(/"/g, '""')}","${r.incident_date || ''}","${r.status || ''}","${r.created_at || ''}"\n`;
            }
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `harassment-analytics-{{ date('Y-m-d') }}.csv`;
            document.body.appendChild(link);
            link.click();
            link.remove();
        });

        // --- DOWNLOAD PDF ---
        document.getElementById('downloadPdf')?.addEventListener('click', async function(e) {
            e.preventDefault();

            const loadScript = (src) =>
                new Promise((res, rej) => {
                    const s = document.createElement('script');
                    s.src = src;
                    s.onload = res;
                    s.onerror = rej;
                    document.head.appendChild(s);
                });

            try {
                if (typeof html2canvas === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }
                if (typeof jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                }

                const wrapper = document.querySelector('.chart-wrapper');
                const canvasEl = await html2canvas(wrapper, { scale: 2, backgroundColor: '#ffffff' });
                const imgData = canvasEl.toDataURL('image/png');

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                pdf.setFontSize(16);
                pdf.setTextColor('#0F172A');
                pdf.text('Harassment Reports Analytics', 40, 44);
                pdf.setFontSize(10);
                pdf.setTextColor('#64748B');
                pdf.text('Generated: {{ date('Y-m-d H:i') }}', 40, 64);

                const imgProps = pdf.getImageProperties(imgData);
                const imgWidth = pageWidth - 80;
                const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
                pdf.addImage(imgData, 'PNG', 40, 80, imgWidth, Math.min(imgHeight, pageHeight - 220));

                let y = 80 + Math.min(imgHeight, pageHeight - 220) + 24;
                pdf.setFontSize(11);
                pdf.setTextColor('#0F172A');
                pdf.text(`Total reports: ${total}`, 40, y);
                y += 18;

                const cols = ['Ref', 'Type', 'Title', 'Date', 'Status'];
                pdf.setFontSize(8);
                const rowHeight = 12;

                pdf.setFillColor('#F1F5F9');
                pdf.rect(40, y, pageWidth - 80, rowHeight, 'F');
                pdf.setTextColor('#0F172A');
                pdf.text(cols.join(' | '), 44, y + 9);
                y += rowHeight + 4;

                const maxRows = Math.min(reportRows.length, 40);
                for (let i = 0; i < maxRows; i++) {
                    const r = reportRows[i];
                    const line =
                        `${r.reference_number || ''} | ${r.incident_type || ''} | ${(r.incident_title || '').substring(0, 50)} | ${r.incident_date || ''} | ${r.status || ''}`;
                    if (y > pageHeight - 50) {
                        pdf.addPage();
                        y = 40;
                    }
                    pdf.text(line, 44, y + 9);
                    y += rowHeight + 4;
                }

                pdf.save(`harassment-analytics-{{ date('Y-m-d') }}.pdf`);
            } catch (err) {
                alert('Failed to generate PDF: ' + (err?.message || err));
            }
        });

    })();
</script>
@endpush

@endsection