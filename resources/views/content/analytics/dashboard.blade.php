@extends('layouts/contentNavbarLayout')

@section('title', $title)

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

@section('page-style')
  @vite([
    'resources/assets/vendor/scss/pages/card-analytics.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/charts-apex.js'
  ])
@endsection

@section('content')
<div class="row">
  <!-- إحصائيات الزوار -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div>
          <h5 class="card-title mb-0">إحصائيات الزوار</h5>
          <small class="text-muted">تحليل حركة الزوار على مدار اليوم</small>
        </div>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="visitorStatsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            اليوم
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="visitorStatsDropdown">
            <a class="dropdown-item" href="javascript:void(0);">اليوم</a>
            <a class="dropdown-item" href="javascript:void(0);">الأسبوع</a>
            <a class="dropdown-item" href="javascript:void(0);">الشهر</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="visitorsChart"></div>
      </div>
    </div>
  </div>

  <!-- بطاقات الإحصائيات -->
  <div class="col-lg-4 col-md-12 mb-4">
    <div class="row">
      <!-- الزوار الحاليين -->
      <div class="col-sm-6 col-lg-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="card-info">
                <h5 class="mb-0">{{ $visitorStats['current'] }}</h5>
                <small>الزوار الحاليين</small>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="ti ti-user-scan ti-sm"></i>
                </span>
              </div>
            </div>
            <div class="d-flex align-items-center mt-3">
              <div class="progress w-100" style="height: 8px;">
                <div class="progress-bar bg-primary" style="width: {{ min(100, ($visitorStats['current'] / max(1, $visitorStats['total_today'])) * 100) }}%" role="progressbar" aria-valuenow="{{ $visitorStats['current'] }}" aria-valuemin="0" aria-valuemax="{{ $visitorStats['total_today'] }}"></div>
              </div>
            </div>
            <small class="mt-1">{{ $visitorStats['change'] > 0 ? '+' : '' }}{{ $visitorStats['change'] }}% مقارنة بالساعة السابقة</small>
          </div>
        </div>
      </div>

      <!-- إجمالي الزوار اليوم -->
      <div class="col-sm-6 col-lg-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="card-info">
                <h5 class="mb-0">{{ $visitorStats['total_today'] }}</h5>
                <small>إجمالي الزوار اليوم</small>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-success rounded p-2">
                  <i class="ti ti-users ti-sm"></i>
                </span>
              </div>
            </div>
            <div class="d-flex align-items-center mt-3">
              <div class="progress w-100" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: 100%" role="progressbar" aria-valuenow="{{ $visitorStats['total_today'] }}" aria-valuemin="0" aria-valuemax="{{ $visitorStats['total_today'] }}"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- إحصائيات المستخدمين -->
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">إحصائيات المستخدمين</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center">
            <div class="badge rounded bg-label-primary me-2">
              <i class="ti ti-users ti-sm"></i>
            </div>
            <div>
              <h6 class="mb-0">إجمالي المستخدمين</h6>
            </div>
          </div>
          <h6 class="mb-0">{{ $userStats['total'] }}</h6>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center">
            <div class="badge rounded bg-label-success me-2">
              <i class="ti ti-user-check ti-sm"></i>
            </div>
            <div>
              <h6 class="mb-0">المستخدمين النشطين</h6>
            </div>
          </div>
          <h6 class="mb-0">{{ $userStats['active'] }}</h6>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div class="badge rounded bg-label-info me-2">
              <i class="ti ti-user-plus ti-sm"></i>
            </div>
            <div>
              <h6 class="mb-0">مستخدمين جدد اليوم</h6>
            </div>
          </div>
          <h6 class="mb-0">{{ $userStats['new_today'] }}</h6>
        </div>
      </div>
    </div>
  </div>

  <!-- إحصائيات الدول -->
  <div class="col-lg-8 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">الزوار حسب الدولة</h5>
        <small class="text-muted">آخر 7 أيام</small>
      </div>
      <div class="card-body">
        <div id="countriesChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- الزوار النشطين حالياً -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">الزوار النشطين حالياً</h5>
        <small class="text-muted">آخر 5 دقائق</small>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>عنوان IP</th>
              <th>الدولة</th>
              <th>المدينة</th>
              <th>المتصفح</th>
              <th>نظام التشغيل</th>
              <th>آخر نشاط</th>
            </tr>
          </thead>
          <tbody>
            @forelse($visitorStats['active_visitors'] as $index => $visitor)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $visitor['ip'] }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <span>{{ $visitor['country'] }}</span>
                </div>
              </td>
              <td>{{ $visitor['city'] }}</td>
              <td>{{ $visitor['browser'] }}</td>
              <td>{{ $visitor['os'] }}</td>
              <td>{{ $visitor['last_active']->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center">لا يوجد زوار نشطين حالياً</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // مخطط الزوار
  const visitorsChartEl = document.querySelector('#visitorsChart');
  if (visitorsChartEl) {
    const visitorsChart = new ApexCharts(visitorsChartEl, {
      chart: {
        height: 300,
        type: 'line',
        toolbar: {
          show: false
        }
      },
      series: [{
        name: 'الزوار',
        data: @json(array_column($visitorStats['history'], 'count'))
      }],
      xaxis: {
        categories: @json(array_map(function($item) {
          return \Carbon\Carbon::parse($item['timestamp'])->format('H:i');
        }, $visitorStats['history'])),
        labels: {
          style: {
            cssClass: 'text-muted'
          }
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          style: {
            cssClass: 'text-muted'
          }
        }
      },
      colors: ['#696cff'],
      stroke: {
        curve: 'smooth',
        width: 3
      },
      grid: {
        borderColor: '#f1f1f1',
        padding: {
          top: 10,
          bottom: -10
        }
      },
      tooltip: {
        shared: true
      },
      legend: {
        show: false
      },
      responsive: [{
        breakpoint: 600,
        options: {
          chart: {
            height: 240
          }
        }
      }]
    });
    visitorsChart.render();
  }

  // مخطط الدول
  const countriesChartEl = document.querySelector('#countriesChart');
  if (countriesChartEl) {
    const countriesChart = new ApexCharts(countriesChartEl, {
      chart: {
        height: 300,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: '40%',
          distributed: true
        }
      },
      series: [{
        name: 'الزوار',
        data: @json(array_column($countryStats, 'count'))
      }],
      xaxis: {
        categories: @json(array_column($countryStats, 'country')),
        labels: {
          style: {
            cssClass: 'text-muted'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            cssClass: 'text-muted'
          }
        }
      },
      colors: [
        '#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0',
        '#3F51B5', '#546E7A', '#D4526E', '#8D5B4C', '#F86624'
      ],
      legend: {
        show: false
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return val + ' زائر';
          }
        }
      }
    });
    countriesChart.render();
  }
});
</script>
@endpush
