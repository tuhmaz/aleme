@extends('layouts/contentNavbarLayout')

@section('title', 'تفاصيل التنبيه الأمني')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
<link rel="stylesheet" href="{{ asset('vendors/css/timeline/timeline.css') }}">
@endsection

@section('page-style')
<style>
    .security-detail-card {
        transition: all 0.3s ease;
    }
    .security-detail-card:hover {
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
    .timeline-item {
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.2rem;
        border: 1px solid #ebe9f1;
        position: relative;
    }
    .timeline-item:hover {
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.05);
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        right: -40px;
        top: 1.5rem;
        border: 3px solid #fff;
        background-color: #7367f0;
    }
    .timeline-item.critical:before {
        background-color: #ea5455;
    }
    .timeline-item.warning:before {
        background-color: #ff9f43;
    }
    .timeline-item.info:before {
        background-color: #00cfe8;
    }
    .timeline-item.success:before {
        background-color: #28c76f;
    }
    .risk-score-high {
        color: #ea5455;
    }
    .risk-score-medium {
        color: #ff9f43;
    }
    .risk-score-low {
        color: #28c76f;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">تفاصيل التنبيه الأمني</h4>
                    <div class="d-flex">
                        <a href="{{ route('dashboard.security.alerts') }}" class="btn btn-outline-primary me-1">
                            <i data-feather="arrow-left"></i>
                            <span>العودة إلى التنبيهات</span>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="alertActions" data-bs-toggle="dropdown" aria-expanded="false">
                                <i data-feather="more-vertical"></i>
                                <span>إجراءات</span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="alertActions">
                                @if(!$log->is_resolved)
                                    <li>
                                        <a class="dropdown-item resolve-alert" href="javascript:void(0);" data-id="{{ $log->id }}">
                                            <i data-feather="check" class="me-50"></i>
                                            <span>تحديد كمحلول</span>
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item unresolve-alert" href="javascript:void(0);" data-id="{{ $log->id }}">
                                            <i data-feather="x" class="me-50"></i>
                                            <span>تحديد كغير محلول</span>
                                        </a>
                                    </li>
                                @endif
                                @if($log->ip_address)
                                    <li>
                                        <a class="dropdown-item block-ip" href="javascript:void(0);" data-ip="{{ $log->ip_address }}">
                                            <i data-feather="shield" class="me-50"></i>
                                            <span>حظر عنوان IP</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('dashboard.security.logs.destroy', $log) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                            <i data-feather="trash" class="me-50"></i>
                                            <span>حذف السجل</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات التنبيه -->
    <div class="col-lg-8 col-md-7 col-12">
        <div class="card security-detail-card">
            <div class="card-header">
                <h4 class="card-title">معلومات التنبيه</h4>
                <div class="d-flex align-items-center">
                    <span class="badge bg-{{ $log->event_type_color }} me-1">{{ $log->event_type }}</span>
                    @if($log->is_resolved)
                        <span class="badge bg-success">محلول</span>
                    @else
                        <span class="badge bg-danger">غير محلول</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <h5>الوصف</h5>
                        <p>{{ $log->description }}</p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>معرف السجل</h6>
                        <p>{{ $log->id }}</p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>تاريخ الحدث</h6>
                        <p>{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>عنوان IP</h6>
                        <p>
                            <a href="{{ route('dashboard.security.ip-details', $log->ip_address) }}" class="text-primary">
                                {{ $log->ip_address }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>وكيل المستخدم</h6>
                        <p class="text-truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>المستخدم</h6>
                        <p>
                            @if($log->user)
                                <a href="{{ route('dashboard.users.show', $log->user->id) }}" class="text-primary">
                                    {{ $log->user->name }}
                                </a>
                            @else
                                <span class="text-muted">غير مسجل</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>المسار</h6>
                        <p>{{ $log->route }}</p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>مستوى الخطورة</h6>
                        <p>
                            @if($log->severity == 'critical')
                                <span class="badge bg-danger">حرج</span>
                            @elseif($log->severity == 'danger')
                                <span class="badge bg-danger">خطر</span>
                            @elseif($log->severity == 'warning')
                                <span class="badge bg-warning">تحذير</span>
                            @else
                                <span class="badge bg-info">معلومات</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <h6>درجة الخطر</h6>
                        <p class="{{ $log->risk_score >= 70 ? 'risk-score-high' : ($log->risk_score >= 40 ? 'risk-score-medium' : 'risk-score-low') }}">
                            <strong>{{ $log->risk_score }}/100</strong>
                        </p>
                    </div>
                    @if($log->country_code || $log->city)
                        <div class="col-md-6 col-12 mb-2">
                            <h6>الموقع</h6>
                            <p>
                                @if($log->country_code)
                                    <span class="me-1">{{ $log->country_code }}</span>
                                @endif
                                @if($log->city)
                                    - {{ $log->city }}
                                @endif
                            </p>
                        </div>
                    @endif
                    @if($log->attack_type)
                        <div class="col-md-6 col-12 mb-2">
                            <h6>نوع الهجوم</h6>
                            <p>{{ $log->attack_type }}</p>
                        </div>
                    @endif
                    @if($log->is_resolved)
                        <div class="col-12">
                            <hr>
                            <h5>معلومات الحل</h5>
                            <div class="row">
                                <div class="col-md-6 col-12 mb-2">
                                    <h6>تاريخ الحل</h6>
                                    <p>{{ $log->resolved_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <h6>تم الحل بواسطة</h6>
                                    <p>
                                        @if($log->resolvedByUser)
                                            <a href="{{ route('dashboard.users.show', $log->resolvedByUser->id) }}" class="text-primary">
                                                {{ $log->resolvedByUser->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">النظام</span>
                                        @endif
                                    </p>
                                </div>
                                @if($log->resolution_notes)
                                    <div class="col-12 mb-2">
                                        <h6>ملاحظات الحل</h6>
                                        <p>{{ $log->resolution_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- بيانات الطلب -->
        @if($log->request_data)
            <div class="card security-detail-card">
                <div class="card-header">
                    <h4 class="card-title">بيانات الطلب</h4>
                </div>
                <div class="card-body">
                    <pre class="language-json"><code>{{ json_encode($log->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        @endif
    </div>

    <!-- معلومات إضافية -->
    <div class="col-lg-4 col-md-5 col-12">
        <!-- الأحداث المشابهة -->
        <div class="card security-detail-card mb-4">
            <div class="card-header">
                <h4 class="card-title">أحداث مشابهة</h4>
            </div>
            <div class="card-body">
                @if(count($similarLogs) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($similarLogs as $similarLog)
                            <a href="{{ route('dashboard.security.alerts.show', $similarLog->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-{{ $similarLog->event_type_color }} mb-1">{{ $similarLog->event_type }}</span>
                                        <p class="mb-1 text-truncate" style="max-width: 200px;">{{ $similarLog->description }}</p>
                                        <small class="text-muted">{{ $similarLog->ip_address }}</small>
                                    </div>
                                    <small class="text-muted">{{ $similarLog->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted my-3">لا توجد أحداث مشابهة</p>
                @endif
            </div>
        </div>

        <!-- أحداث من نفس عنوان IP -->
        <div class="card security-detail-card">
            <div class="card-header">
                <h4 class="card-title">أحداث من نفس عنوان IP</h4>
            </div>
            <div class="card-body">
                @if(count($ipLogs) > 0)
                    <div class="position-relative">
                        <div class="timeline-line"></div>
                        @foreach($ipLogs as $ipLog)
                            <div class="timeline-item {{ $ipLog->severity }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-{{ $ipLog->event_type_color }}">{{ $ipLog->event_type }}</span>
                                    <small class="text-muted">{{ $ipLog->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                                <p class="mb-1">{{ \Illuminate\Support\Str::limit($ipLog->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $ipLog->route }}</small>
                                    <a href="{{ route('dashboard.security.alerts.show', $ipLog->id) }}" class="btn btn-sm btn-outline-primary">
                                        عرض
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted my-3">لا توجد أحداث أخرى من نفس عنوان IP</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- نموذج تحديد التنبيه كمحلول -->
<div class="modal fade" id="resolveAlertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديد التنبيه كمحلول</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="resolveAlertForm">
                    <input type="hidden" id="alert_id" name="alert_id" value="{{ $log->id }}">
                    <input type="hidden" id="is_resolved" name="is_resolved" value="1">
                    <div class="mb-3">
                        <label for="resolution_notes" class="form-label">ملاحظات الحل</label>
                        <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3" placeholder="أدخل ملاحظات حول كيفية حل هذا التنبيه"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="submitResolveAlert">تأكيد</button>
            </div>
        </div>
    </div>
</div>

<!-- نموذج حظر عنوان IP -->
<div class="modal fade" id="blockIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حظر عنوان IP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="blockIpForm">
                    <input type="hidden" id="ip_address" name="ip_address" value="{{ $log->ip_address }}">
                    <div class="mb-3">
                        <label for="block_reason" class="form-label">سبب الحظر</label>
                        <textarea class="form-control" id="block_reason" name="block_reason" rows="3" placeholder="أدخل سبب حظر عنوان IP هذا"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="block_duration" class="form-label">مدة الحظر</label>
                        <select class="form-select" id="block_duration" name="block_duration">
                            <option value="1">ساعة واحدة</option>
                            <option value="24" selected>24 ساعة</option>
                            <option value="168">أسبوع واحد</option>
                            <option value="720">شهر واحد</option>
                            <option value="0">دائم</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="submitBlockIp">تأكيد الحظر</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
@vite([
    'resources/assets/vendor/js/alerts-detals.js'
])

@endsection
