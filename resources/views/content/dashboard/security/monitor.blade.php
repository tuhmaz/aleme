@extends('layouts/contentNavbarLayout')

@section('title', 'لوحة مراقبة الأمان')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
<style>
    /* تصميم عام للوحة المراقبة */
    .security-dashboard {
        background: linear-gradient(45deg, rgba(245, 246, 250, 0.6), rgba(255, 255, 255, 0.9));
        padding: 1.5rem 1rem;
        border-radius: 0.5rem;
    }
    
    /* أنماط درجة الأمان */
    .security-score {
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        background: linear-gradient(45deg, #28c76f, #00cfe8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .security-score-container {
        position: relative;
        padding: 1.5rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .security-score-container:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
    }
    
    .score-label {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    /* أنماط عدد التنبيهات */
    .alert-count {
        font-size: 2.2rem;
        font-weight: bold;
        color: #7367f0;
        margin-bottom: 0.5rem;
        line-height: 1.1;
    }
    
    .alert-label {
        font-size: 0.95rem;
        color: #6e6b7b;
        margin-top: 0.5rem;
    }
    
    /* أنماط البطاقات */
    .security-card {
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: none;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    
    .security-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
    /* أنماط رأس البطاقة */
    .security-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(34, 41, 47, 0.05);
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .security-card .card-title {
        color: #5e5873;
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* أنماط الجدول */
    .security-table {
        --bs-table-hover-bg: rgba(115, 103, 240, 0.05);
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .security-table th {
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #5e5873;
        padding: 1rem 1.5rem;
        white-space: nowrap;
    }
    
    .security-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
    }
    
    /* أنماط الشارات */
    .security-badge {
        padding: 0.5rem 0.85rem;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 0.358rem;
        text-transform: capitalize;
    }
    
    /* حاويات الرسوم البيانية */
    .chart-container {
        min-height: 300px;
        position: relative;
    }
    
    /* أنماط الأزرار */
    .btn-wave {
        position: relative;
        overflow: hidden;
    }
    
    .btn-wave .wave {
        position: absolute;
        display: block;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.65s linear;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(2.5);
            opacity: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y security-dashboard">
    <!-- ترويسة الصفحة -->
    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('الأمان') }} /</span> {{ __('لوحة المراقبة') }}
    </h4>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card security-card animate__animated animate__fadeIn shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-shield-lock me-2 text-primary"></i>
                        نظرة عامة على حالة الأمان
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard.security.alerts') }}" class="btn btn-primary btn-wave waves-effect waves-light">
                            <i class="ti ti-bell me-1"></i>
                            <span>التنبيهات الأمنية</span>
                            @if(isset($alertsSummary['unresolved']) && $alertsSummary['unresolved'] > 0)
                                <span class="badge bg-danger badge-notification rounded-pill ms-2">
                                    {{ $alertsSummary['unresolved'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('dashboard.security.logs') }}" class="btn btn-outline-primary btn-wave waves-effect">
                            <i class="ti ti-list me-1"></i>
                            <span>سجلات الأمان</span>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-wave waves-effect dropdown-toggle" type="button" id="securityActions" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-settings me-1"></i>
                                <span>إجراءات</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="securityActions">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#" 
                                       onclick="event.preventDefault(); document.getElementById('run-scan-form').submit();">
                                        <i class="ti ti-shield-check me-2"></i>
                                        تشغيل فحص أمني
                                    </a>
                                    <form id="run-scan-form" action="{{ route('dashboard.security.run-scan') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('dashboard.security.export-report') }}">
                                        <i class="ti ti-file-export me-2"></i>
                                        تصدير تقرير أمني
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قسم درجة الأمان والإحصائيات السريعة -->
<div class="row">
    <!-- بطاقة درجة الأمان -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card security-card h-100 shadow-sm border-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title">
                    <i class="ti ti-shield-check me-2 text-success"></i>
                    درجة الأمان الإجمالية
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-text-secondary p-0" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-refresh me-1"></i>تحديث</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-info-circle me-1"></i>تفاصيل</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="security-score-container">
                    <div id="security-score-chart"></div>
                    <div class="score-label">
                        <h2 class="mb-0 text-center">{{ isset($stats['security_score']) ? $stats['security_score'] : 85 }}/100</h2>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-center">
                        <p class="small text-muted mb-1">آخر تقييم</p>
                        <h6 class="mb-0 badge bg-label-primary p-2">{{ isset($stats['last_assessment']) ? $stats['last_assessment'] : now()->subHours(3)->format('Y-m-d H:i') }}</h6>
                    </div>
                    <div class="text-center">
                        <p class="small text-muted mb-1">التغير منذ آخر تقييم</p>
                        <h6 class="mb-0 badge {{ isset($stats['score_change']) && $stats['score_change'] >= 0 ? 'bg-label-success' : 'bg-label-danger' }} p-2">
                            @if(isset($stats['score_change']))
                                @if($stats['score_change'] > 0)
                                    <i class="ti ti-arrow-up me-1"></i>
                                @elseif($stats['score_change'] < 0)
                                    <i class="ti ti-arrow-down me-1"></i>
                                @else
                                    <i class="ti ti-minus me-1"></i>
                                @endif
                                {{ abs($stats['score_change']) }}%
                            @else
                                <i class="ti ti-arrow-up me-1"></i> 5%
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة التنبيهات -->
    <div class="col-lg-4 col-md-6 col-12">
        <div class="card security-card">
            <div class="card-header">
                <h4 class="card-title">التنبيهات الأمنية</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 border-end">
                        <div class="d-flex flex-column align-items-center">
                            <h3 class="alert-count text-danger mb-0">{{ $alertsSummary['critical_alerts'] ?? 0 }}</h3>
                            <span class="alert-label">تنبيهات حرجة</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column align-items-center">
                            <h3 class="alert-count text-warning mb-0">{{ $alertsSummary['threshold_alerts'] ?? 0 }}</h3>
                            <span class="alert-label">تنبيهات عتبة</span>
                        </div>
                    </div>
                </div>
                <div id="alerts-chart" class="mt-4"></div>
                
                <div class="mt-3">
                    <div class="progress rounded-pill" style="height: 8px;">
                        @php
                            $total = ($alertsSummary['unresolved'] ?? 0) + ($alertsSummary['resolved'] ?? 0);
                            $percent = $total > 0 ? round(($alertsSummary['resolved'] ?? 0) / $total * 100) : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" 
                            aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted">نسبة الحل</small>
                        <small class="text-success">{{ $percent }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الإحصائيات السريعة -->
    <div class="col-lg-4 col-md-12 col-12">
        <div class="card security-card">
            <div class="card-header">
                <h4 class="card-title">إحصائيات سريعة</h4>
            </div>
            <div class="card-body">
                <div class="row g-3 mt-3">
                    <div class="col-4 text-center">
                        <div class="p-2 bg-label-primary rounded-3 d-flex flex-column align-items-center">
                            <p class="small text-muted mb-1">اليوم</p>
                            <h5 class="mb-0 fw-semibold">{{ isset($stats['today_events']) ? $stats['today_events'] : 14 }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="p-2 bg-label-info rounded-3 d-flex flex-column align-items-center">
                            <p class="small text-muted mb-1">الأسبوع</p>
                            <h5 class="mb-0 fw-semibold">{{ isset($stats['week_events']) ? $stats['week_events'] : 87 }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="p-2 bg-label-secondary rounded-3 d-flex flex-column align-items-center">
                            <p class="small text-muted mb-1">الشهر</p>
                            <h5 class="mb-0 fw-semibold">{{ isset($stats['month_events']) ? $stats['month_events'] : 235 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <div class="text-center">
                        <h5 class="mb-0">{{ isset($stats['total_logs']) ? $stats['total_logs'] : 0 }}</h5>
                        <small>إجمالي السجلات</small>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-0">{{ isset($stats['critical_logs']) ? $stats['critical_logs'] : 0 }}</h5>
                        <small>السجلات الحرجة</small>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-0">{{ isset($stats['unresolved_issues']) ? $stats['unresolved_issues'] : 0 }}</h5>
                        <small>المشكلات غير المحلولة</small>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-0">{{ isset($stats['recent_suspicious']) ? $stats['recent_suspicious'] : 0 }}</h5>
                        <small>النشاط المشبوه الأخير</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <div>
                        <span>مشكلات غير محلولة</span>
                    </div>
                    <div>
                        <span class="fw-bold text-warning">{{ $stats['unresolved_issues'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <div>
                        <span>نشاط مشبوه (24 ساعة)</span>
                    </div>
                    <div>
                        <span class="fw-bold text-info">{{ $stats['recent_suspicious'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <span>عناوين IP محظورة</span>
                    </div>
                    <div>
                        <span class="fw-bold">{{ $stats['blocked_ips'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة الأحداث الأخيرة -->
    <div class="col-lg-4 col-md-12 col-12 mb-4">
        <div class="card security-card h-100 shadow-sm border-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title">
                    <i class="ti ti-activity me-2 text-info"></i>
                    الأحداث الأمنية الأخيرة
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-text-secondary" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-refresh me-1"></i>تحديث</a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.security.logs') }}"><i class="ti ti-list me-1"></i>عرض السجلات</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div id="recent-events-chart" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- قسم الرسوم البيانية -->
<div class="row">
    <!-- اتجاهات الأحداث الأمنية -->
    <div class="col-lg-8 col-md-12 col-12">
        <div class="card security-card">
            <div class="card-header">
                <h4 class="card-title">اتجاهات الأحداث الأمنية</h4>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="timelineRange" data-bs-toggle="dropdown" aria-expanded="false">
                        آخر 30 يوم
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="timelineRange">
                        <li><a class="dropdown-item" href="#">آخر 7 أيام</a></li>
                        <li><a class="dropdown-item active" href="#">آخر 30 يوم</a></li>
                        <li><a class="dropdown-item" href="#">آخر 90 يوم</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div id="security-events-timeline"></div>
            </div>
        </div>
    </div>

    <!-- توزيع أنواع الأحداث -->
    <div class="col-lg-4 col-md-12 col-12">
        <div class="card security-card">
            <div class="card-header">
                <h4 class="card-title">توزيع أنواع الأحداث</h4>
            </div>
            <div class="card-body">
                <div id="event-type-chart"></div>
            </div>
        </div>
    </div>
</div>

<!-- قسم الرسوم البيانية -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card security-card shadow-sm border-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title">
                    <i class="ti ti-chart-line me-2 text-primary"></i>
                    تحليلات الأمان على مدار الوقت
                </h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">أسبوع</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">شهر</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">سنة</button>
                </div>
            </div>
            <div class="card-body">
                <div id="security-timeline-chart" class="chart-container"></div>
            </div>
        </div>
    </div>
</div>

<!-- قسم الثغرات الأمنية -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card security-card shadow-sm border-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title">
                    <i class="ti ti-bug me-2 text-danger"></i>
                    الثغرات الأمنية المكتشفة
                </h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="بحث..." aria-label="بحث">
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="runSecurityScan()">
                        <i class="ti ti-refresh me-1"></i> فحص جديد
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table security-table align-middle">
                        <thead>
                            <tr>
                                <th>المشكلة</th>
                                <th>الخطورة</th>
                                <th>الوصف</th>
                                <th>الحل المقترح</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="badge bg-danger">مخاطر كلمات المرور</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-danger me-1">عالية</span>
                                        <i class="ti ti-alert-triangle text-danger"></i>
                                    </div>
                                </td>
                                <td>تم اكتشاف كلمات مرور افتراضية في ملفات التكوين</td>
                                <td>قم بتغيير كلمات المرور الافتراضية واستخدم متغيرات بيئية</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">
                                                    <i class="ti ti-check me-1"></i> معالجة
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#securityDetailModal" data-type="مخاطر كلمات المرور" data-description="تم اكتشاف كلمات مرور افتراضية في ملفات التكوين" data-recommendation="قم بتغيير كلمات المرور الافتراضية واستخدم متغيرات بيئية">
                                                    <i class="ti ti-info-circle me-1"></i> تفاصيل
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-warning">مشكلة CSRF</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-warning me-1">متوسطة</span>
                                        <i class="ti ti-alert-circle text-warning"></i>
                                    </div>
                                </td>
                                <td>بعض النماذج لا تستخدم حماية CSRF</td>
                                <td>أضف الرمز المميز @csrf إلى جميع النماذج</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">
                                                    <i class="ti ti-check me-1"></i> معالجة
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#securityDetailModal" data-type="مشكلة CSRF" data-description="بعض النماذج لا تستخدم حماية CSRF" data-recommendation="أضف الرمز المميز @csrf إلى جميع النماذج">
                                                    <i class="ti ti-info-circle me-1"></i> تفاصيل
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-info">تحديث الحماية</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-info me-1">منخفضة</span>
                                        <i class="ti ti-info-circle text-info"></i>
                                    </div>
                                </td>
                                <td>تحديثات أمنية متاحة لحزم Composer</td>
                                <td>قم بتنفيذ composer update لتحديث الحزم</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">
                                                    <i class="ti ti-check me-1"></i> معالجة
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#securityDetailModal" data-type="تحديث الحماية" data-description="تحديثات أمنية متاحة لحزم Composer" data-recommendation="قم بتنفيذ composer update لتحديث الحزم">
                                                    <i class="ti ti-info-circle me-1"></i> تفاصيل
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-danger">ثغرة SQL</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-danger me-1">عالية</span>
                                        <i class="ti ti-alert-triangle text-danger"></i>
                                    </div>
                                </td>
                                <td>احتمالية وجود ثغرات حقن SQL في بعض الاستعلامات</td>
                                <td>قم بتعيين كلمة مرور قوية لقاعدة البيانات في ملف .env</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">
                                                    <i class="ti ti-check me-1"></i> معالجة
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#securityDetailModal" data-type="ثغرة SQL" data-description="احتمالية وجود ثغرات حقن SQL في بعض الاستعلامات" data-recommendation="قم بتعيين كلمة مرور قوية لقاعدة البيانات في ملف .env">
                                                    <i class="ti ti-info-circle me-1"></i> تفاصيل
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('dashboard.security.logs') }}" 
                   class="btn btn-primary btn-wave waves-effect d-flex align-items-center animate__animated animate__fadeIn">
                    <i class="ti ti-shield-lock me-2"></i>
                    فحص أمني شامل
                </a>
                <a href="{{ route('dashboard.security.logs') }}" 
                   class="btn btn-outline-primary ms-2 btn-wave waves-effect d-flex align-items-center animate__animated animate__fadeIn">
                    <i class="ti ti-list me-2"></i>
                    عرض جميع السجلات
                    <i class="ti ti-chevron-right ms-2"></i>
                </a>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تفاصيل المشكلة الأمنية -->
<div class="modal fade" id="securityDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="securityDetailTitle">تفاصيل المشكلة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-info-circle text-primary me-2"></i>
                        <h6 class="mb-0">الوصف:</h6>
                    </div>
                    <p id="securityDetailDescription" class="text-body mb-0 ps-4"></p>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-bulb text-warning me-2"></i>
                        <h6 class="mb-0">التوصية:</h6>
                    </div>
                    <p id="securityDetailRecommendation" class="text-body mb-0 ps-4"></p>
                </div>
                <div class="alert alert-primary d-flex mt-4" role="alert">
                    <i class="ti ti-shield me-2"></i>
                    <div>
                        معالجة هذه الثغرة سيؤدي إلى تحسين درجة الأمان الإجمالية للنظام وحماية البيانات بشكل أفضل.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="fixIssueBtn">معالجة المشكلة</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/jquery/jquery.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/vendor/js/monitor.js'])
@endsection
