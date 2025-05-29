<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VisitorService;
use App\Models\User;
use App\Models\VisitorTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    protected $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    /**
     * عرض لوحة تحكم التحليلات
     */
    public function index()
    {
        $data = [
            'title' => 'لوحة التحليلات',
            'visitorStats' => $this->getVisitorStats(),
            'userStats' => $this->getUserStats(),
            'countryStats' => $this->getCountryStats(),
        ];

        return view('content.analytics.dashboard', $data);
    }

    /**
     * الحصول على إحصائيات الزوار
     */
    public function getVisitorStats()
    {
        // استخدام خدمة الزوار لجلب الإحصائيات
        $stats = $this->visitorService->getVisitorStats();
        $activeVisitors = $this->visitorService->getActiveVisitors(5);

        return [
            'current' => $stats['current'],
            'total_today' => $stats['total_today'],
            'change' => $stats['change'],
            'history' => $stats['history'],
            'active_visitors' => $activeVisitors,
        ];
    }

    /**
     * الحصول على إحصائيات المستخدمين
     */
    public function getUserStats()
    {
        // عدد المستخدمين المسجلين
        $totalUsers = Cache::remember('total_users', 3600, function () {
            return User::count();
        });

        // عدد المستخدمين النشطين (آخر 5 دقائق)
        $activeUsers = Cache::remember('active_users', 300, function () {
            return User::where('last_activity', '>=', now()->subMinutes(5))->count();
        });

        // عدد المستخدمين الجدد اليوم
        $newUsersToday = Cache::remember('new_users_today', 3600, function () {
            return User::whereDate('created_at', today())->count();
        });

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'new_today' => $newUsersToday,
        ];
    }

    /**
     * الحصول على إحصائيات الدول
     */
    public function getCountryStats()
    {
        // إحصائيات الدول (آخر 7 أيام)
        $countryStats = Cache::remember('country_stats', 3600, function () {
            return DB::table('visitors_tracking')
                ->select('country', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->whereNotNull('country')
                ->where('country', '!=', 'Unknown')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'country' => $item->country,
                        'count' => $item->count,
                    ];
                })
                ->toArray();
        });

        return $countryStats;
    }

    /**
     * واجهة API لإحصائيات الزوار
     */
    public function visitors()
    {
        return response()->json([
            'visitor_stats' => $this->getVisitorStats(),
            'user_stats' => $this->getUserStats(),
            'country_stats' => $this->getCountryStats(),
        ]);
    }
}
