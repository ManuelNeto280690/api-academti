<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $totalEnrolled = Enrollment::where('user_id', $user->id)->count();
        $completedCourses = Enrollment::where('user_id', $user->id)->where('progress', '>=', 100)->count();
        $activeCoursesCount = Enrollment::where('user_id', $user->id)->where('progress', '>', 0)->where('progress', '<', 100)->count();
            
        // Estimate hours based on completed lessons (30 min per lesson)
        $completedLessonsCount = DB::table('lesson_user')->where('user_id', $user->id)->whereNotNull('completed_at')->count();
        $hoursStudied = round($completedLessonsCount * 0.5, 1);
            
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.trainer', 'certification.category'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($enrollment) {
                if ($enrollment->course) {
                    return [
                        'id' => $enrollment->course->id,
                        'title' => $enrollment->course->title,
                        'progress' => $enrollment->progress,
                        'status' => $enrollment->status,
                        'modalidade' => $enrollment->course->modalidade,
                        'trainer' => $enrollment->course->trainer ? $enrollment->course->trainer->name : 'N/A',
                        'image' => $enrollment->course->imagem,
                        'is_certification' => false,
                    ];
                } elseif ($enrollment->certification) {
                    return [
                        'id' => $enrollment->certification->id,
                        'title' => $enrollment->certification->title,
                        'progress' => $enrollment->progress,
                        'status' => $enrollment->status,
                        'modalidade' => $enrollment->certification->type,
                        'trainer' => 'CEFTIC Official',
                        'image' => null,
                        'is_certification' => true,
                    ];
                }
                return null;
            })->filter();

        // Get notifications
        $notifications = $user->platformNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'text' => $notif->message,
                    'type' => $notif->type,
                    'time' => $notif->created_at->diffForHumans(),
                    'is_read' => $notif->is_read
                ];
            });

        // Get upcoming events
        $upcomingEvents = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(3)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'type' => $event->type,
                    'instructor' => $event->instructor,
                    'date' => clone $event->date, // Carbon instance
                    'live' => $event->is_live
                ];
            })->map(function($event) {
                $dateObj = $event['date'];
                if ($dateObj->isToday()) $dateStr = 'Hoje, ' . $dateObj->format('H:i');
                elseif ($dateObj->isTomorrow()) $dateStr = 'Amanhã, ' . $dateObj->format('H:i');
                else $dateStr = $dateObj->format('d M, H:i');
                $event['date'] = $dateStr;
                return $event;
            });
            
        // Get achievements
        $achievements = $user->achievements()
            ->orderByPivot('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($ach) {
                return [
                    'id' => $ach->id,
                    'title' => $ach->title,
                    'icon' => $ach->icon ?? 'Trophy',
                    'points' => $ach->points,
                    'new' => (bool)$ach->pivot->is_new,
                    'date' => $ach->pivot->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'stats' => [
                'total_courses' => $totalEnrolled,
                'completed_courses' => $completedCourses,
                'active_courses' => $activeCoursesCount,
                'certificates' => $completedCourses,
                'hours' => $hoursStudied,
                'points' => $user->xp_points ?? 0,
                'streak_days' => $user->streak_days ?? 0,
            ],
            'active_enrollments' => array_values($enrollments->toArray()),
            'notifications' => $notifications,
            'upcoming_events' => $upcomingEvents,
            'achievements' => $achievements,
            'study_days' => [true, true, true, false, true, true, false], // Dummy array for now
        ]);
    }
}
