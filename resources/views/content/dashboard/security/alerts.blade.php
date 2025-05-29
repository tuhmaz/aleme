@extends('layouts/contentNavbarLayout')

@section('title', 'التنبيهات الأمنية')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])

@endsection

@section('page-style')
<style>
    .alert-badge {
        min-width: 80px;
    }
    .filter-card {
        transition: all 0.3s ease;
    }
    .filter-card:hover {
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">التنبيهات الأمنية</h4>
                <div class="d-flex">
                    <a href="{{ route('dashboard.security.monitor') }}" class="btn btn-outline-primary me-1">
                        <i data-feather="activity"></i>
                        <span>لوحة المراقبة</span>
                    </a>
                    <a href="{{ route('dashboard.security.logs') }}" class="btn btn-outline-primary me-1">
                        <i data-feather="list"></i>
                        <span>سجلات الأمان</span>
                    </a>
                    <a href="{{ route('dashboard.security.export-report') }}" class="btn btn-outline-secondary">
                        <i data-feather="download"></i>
                        <span>تصدير</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- بطاقة الفلترة -->
<div class="row">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-header">
                <h4 class="card-title">فلترة التنبيهات</h4>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i data-feather="filter"></i>
                    <span>عرض/إخفاء</span>
                </button>
            </div>
            <div class="collapse" id="filterCollapse">
                <div class="card-body">
                    <form action="{{ route('dashboard.security.alerts') }}" method="GET" id="filter-form">
                        <div class="row">
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="event_type">نوع الحدث</label>
                                <select class="form-select" id="event_type" name="event_type">
                                    <option value="">الكل</option>
                                    @foreach($eventTypes as $key => $value)
                                        <option value="{{ $value }}" {{ request('event_type') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="severity">مستوى الخطورة</label>
                                <select class="form-select" id="severity" name="severity">
                                    <option value="">الكل</option>
                                    @foreach($severityLevels as $key => $value)
                                        <option value="{{ $value }}" {{ request('severity') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="is_resolved">الحالة</label>
                                <select class="form-select" id="is_resolved" name="is_resolved">
                                    <option value="">الكل</option>
                                    <option value="false" {{ request('is_resolved') === 'false' ? 'selected' : '' }}>غير محلول</option>
                                    <option value="true" {{ request('is_resolved') === 'true' ? 'selected' : '' }}>محلول</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="ip">عنوان IP</label>
                                <input type="text" class="form-control" id="ip" name="ip" placeholder="أدخل عنوان IP" value="{{ request('ip') }}">
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="date_from">من تاريخ</label>
                                <input type="text" class="form-control flatpickr-basic" id="date_from" name="date_from" placeholder="YYYY-MM-DD" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <label class="form-label" for="date_to">إلى تاريخ</label>
                                <input type="text" class="form-control flatpickr-basic" id="date_to" name="date_to" placeholder="YYYY-MM-DD" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-6 col-12 mb-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-1">تطبيق الفلتر</button>
                                <a href="{{ route('dashboard.security.alerts') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول التنبيهات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover security-alerts-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>نوع الحدث</th>
                                <th>الوصف</th>
                                <th>عنوان IP</th>
                                <th>المستخدم</th>
                                <th>الخطورة</th>
                                <th>درجة الخطر</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                                <tr>
                                    <td>{{ $alert->id }}</td>
                                    <td>
                                        <span class="badge bg-{{ $alert->event_type_color }} alert-badge">{{ $alert->event_type }}</span>
                                    </td>
                                    <td>
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $alert->description }}">
                                            {{ \Illuminate\Support\Str::limit($alert->description, 30) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.security.ip-details', $alert->ip_address) }}" class="text-primary">
                                            {{ $alert->ip_address }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($alert->user)
                                            <a href="{{ route('dashboard.users.show', $alert->user->id) }}" class="text-primary">
                                                {{ $alert->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alert->severity == 'critical')
                                            <span class="badge bg-danger">حرج</span>
                                        @elseif($alert->severity == 'danger')
                                            <span class="badge bg-danger">خطر</span>
                                        @elseif($alert->severity == 'warning')
                                            <span class="badge bg-warning">تحذير</span>
                                        @else
                                            <span class="badge bg-info">معلومات</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-1">{{ $alert->risk_score }}</span>
                                            <div class="progress w-100 me-1" style="height: 6px">
                                                <div
                                                    class="progress-bar"
                                                    style="width: {{ $alert->risk_score }}%; background-color: 
                                                        {{ $alert->risk_score >= 70 ? '#ea5455' : ($alert->risk_score >= 40 ? '#ff9f43' : '#28c76f') }}"
                                                    role="progressbar"
                                                    aria-valuenow="{{ $alert->risk_score }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($alert->is_resolved)
                                            <span class="badge bg-success">محلول</span>
                                        @else
                                            <span class="badge bg-danger">غير محلول</span>
                                        @endif
                                    </td>
                                    <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('dashboard.security.alerts.show', $alert->id) }}">
                                                    <i data-feather="eye" class="me-50"></i>
                                                    <span>عرض التفاصيل</span>
                                                </a>
                                                @if(!$alert->is_resolved)
                                                    <a class="dropdown-item resolve-alert" href="javascript:void(0);" data-id="{{ $alert->id }}">
                                                        <i data-feather="check" class="me-50"></i>
                                                        <span>تحديد كمحلول</span>
                                                    </a>
                                                @else
                                                    <a class="dropdown-item unresolve-alert" href="javascript:void(0);" data-id="{{ $alert->id }}">
                                                        <i data-feather="x" class="me-50"></i>
                                                        <span>تحديد كغير محلول</span>
                                                    </a>
                                                @endif
                                                @if($alert->ip_address)
                                                    <a class="dropdown-item block-ip" href="javascript:void(0);" data-ip="{{ $alert->ip_address }}">
                                                        <i data-feather="shield" class="me-50"></i>
                                                        <span>حظر عنوان IP</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">لا توجد تنبيهات أمنية</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    {{ $alerts->links() }}
                </div>
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
                    <input type="hidden" id="alert_id" name="alert_id">
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
                    <input type="hidden" id="ip_address" name="ip_address">
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

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/jquery/jquery.js',
    'resources/assets/vendor/libs/datatables/datatables-bootstrap5.js',
  
    
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])



@endsection

@section('page-script')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/js/alerts.js'
])

@endsection
