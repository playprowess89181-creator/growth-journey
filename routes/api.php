<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CommunityGroupController;
use App\Http\Controllers\Api\CommunityPostController;
use App\Http\Controllers\Api\CommunityQnaQuestionController;
use App\Http\Controllers\Api\DialogueCommentController;
use App\Http\Controllers\Api\DialogueTopicController;
use App\Http\Controllers\Api\DialogueTopicRequestController;
use App\Http\Controllers\Api\PrayerRequestController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TrackHabitController;
use App\Http\Controllers\Api\VocabularyController;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->patch('/user/profile', [AuthController::class, 'updateProfile']);

Route::middleware('auth:sanctum')->get('/user/community-groups', [CommunityGroupController::class, 'joinedGroups']);

Route::middleware('auth:sanctum')->post('/user/onboarding', [AuthController::class, 'saveOnboarding']);

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register/request-otp', [AuthController::class, 'requestRegistrationOtp']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/password/request-otp', [AuthController::class, 'requestPasswordResetOtp']);
    Route::post('/password/verify-otp', [AuthController::class, 'verifyPasswordResetOtp']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'google']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('progress')->group(function () {
    Route::get('/', [ProgressController::class, 'snapshot']);
    Route::post('/lessons/{lesson}/complete', [ProgressController::class, 'completeLesson']);
    Route::post('/levels/{level}/complete', [ProgressController::class, 'completeLevel']);
});

// Community Groups API Routes
Route::prefix('community-groups')->group(function () {
    Route::get('/', [CommunityGroupController::class, 'index']);
    Route::get('/categories', [CommunityGroupController::class, 'categories']);
    Route::get('/{communityGroup}', [CommunityGroupController::class, 'show']);
    Route::get('/{communityGroup}/habits', [TrackHabitController::class, 'groupHabits']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CommunityGroupController::class, 'store']);
        Route::put('/{communityGroup}', [CommunityGroupController::class, 'update']);
        Route::delete('/{communityGroup}', [CommunityGroupController::class, 'destroy']);
        Route::get('/{communityGroup}/membership', [CommunityGroupController::class, 'membership']);
        Route::post('/{communityGroup}/join', [CommunityGroupController::class, 'join']);
        Route::post('/{communityGroup}/leave', [CommunityGroupController::class, 'leave']);
        Route::get('/{communityGroup}/habits/stats', [TrackHabitController::class, 'memberStats']);
        Route::get('/{communityGroup}/habits/{habit}/entries', [TrackHabitController::class, 'habitEntries']);
        Route::post('/{communityGroup}/habits/{habit}/stats', [TrackHabitController::class, 'updateMemberStats']);
    });
});

// Community Posts API Routes
Route::prefix('community-posts')->group(function () {
    Route::get('/', [CommunityPostController::class, 'index']);
    Route::get('/{communityPost}', [CommunityPostController::class, 'show']);
    Route::get('/group/{communityGroup}', [CommunityPostController::class, 'byGroup']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CommunityPostController::class, 'store']);
        Route::put('/{communityPost}', [CommunityPostController::class, 'update']);
        Route::delete('/{communityPost}', [CommunityPostController::class, 'destroy']);
        Route::patch('/{communityPost}/toggle-publish', [CommunityPostController::class, 'togglePublish']);
        Route::patch('/{communityPost}/toggle-pin', [CommunityPostController::class, 'togglePin']);
    });
});

// Community Q&A API Routes
Route::prefix('community-qna')->group(function () {
    Route::get('/questions', [CommunityQnaQuestionController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/questions', [CommunityQnaQuestionController::class, 'store']);
    });
});

// Inter-religious Dialogue API Routes
Route::prefix('dialogue')->group(function () {
    Route::get('/topics', [DialogueTopicController::class, 'index']);
    Route::get('/topics/{topic}', [DialogueTopicController::class, 'show']);
    Route::get('/topics/{topic}/comments', [DialogueCommentController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/topics/{topic}/upvote', [DialogueTopicController::class, 'toggleUpvote']);
        Route::post('/topics/{topic}/comments', [DialogueCommentController::class, 'store']);
        Route::get('/topic-requests', [DialogueTopicRequestController::class, 'index']);
        Route::post('/topic-requests', [DialogueTopicRequestController::class, 'store']);
        Route::put('/topic-requests/{topicRequest}', [DialogueTopicRequestController::class, 'update']);
        Route::delete('/topic-requests/{topicRequest}', [DialogueTopicRequestController::class, 'destroy']);
    });
});

// Comments API Routes
Route::prefix('comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']);
    Route::get('/{comment}', [CommentController::class, 'show']);
    Route::get('/post/{post}', [CommentController::class, 'byPost']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
        Route::put('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
        Route::patch('/{comment}/approve', [CommentController::class, 'approve']);
        Route::patch('/{comment}/reject', [CommentController::class, 'reject']);
        Route::post('/bulk-approve', [CommentController::class, 'bulkApprove']);
        Route::post('/bulk-reject', [CommentController::class, 'bulkReject']);
        Route::post('/bulk-delete', [CommentController::class, 'bulkDelete']);
    });
});

// Reports API Routes
Route::prefix('reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/', [ReportController::class, 'store']);
    Route::get('/reasons', [ReportController::class, 'reasons']);
    Route::get('/can-report', [ReportController::class, 'canReport']);
    Route::get('/{report}', [ReportController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vocabulary/categories', [VocabularyController::class, 'categories']);
    Route::get('/categories/{category}/words', [VocabularyController::class, 'categoryWords']);
    Route::post('/vocabulary/words/{word}/complete', [VocabularyController::class, 'completeWord']);
});

Route::prefix('prayer-requests')->group(function () {
    Route::get('/', [PrayerRequestController::class, 'index']);
    Route::get('/{prayerRequest}/comments', [PrayerRequestController::class, 'comments']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/mine', [PrayerRequestController::class, 'mine']);
        Route::post('/', [PrayerRequestController::class, 'store']);
        Route::post('/{prayerRequest}/pray', [PrayerRequestController::class, 'pray']);
        Route::post('/{prayerRequest}/comments', [PrayerRequestController::class, 'addComment']);
    });
});

Route::get('/modules', function (Request $request) {
    $status = $request->query('status');
    $lang = strtolower((string) $request->query('lang', 'en'));
    if ($lang === '') {
        $lang = 'en';
    }

    $query = Module::query()
        ->with([
            'translations' => function ($q) use ($lang) {
                $q->whereIn('language_code', [$lang, 'en']);
            },
            'levels' => function ($q) {
                $q->orderBy('id')
                    ->withCount(['lessons' => function ($lessons) {
                        $lessons->where('status', 'active');
                    }]);
            },
        ])
        ->orderBy('order')
        ->orderBy('created_at');
    if (is_string($status) && $status !== '') {
        $query->where('status', $status);
    } else {
        $query->where('status', 'active');
    }

    $modules = $query->get()->map(function (Module $module) use ($lang) {
        $translation = $module->translations->firstWhere('language_code', $lang)
            ?? $module->translations->firstWhere('language_code', 'en')
            ?? $module->translations->first();

        return [
            'id' => $module->id,
            'status' => $module->status,
            'order' => $module->order,
            'title' => $translation?->title,
            'description' => $translation?->description,
            'levels' => $module->levels->values()->map(function ($level, $index) {
                return [
                    'id' => $level->id,
                    'order' => $index + 1,
                    'status' => $level->status,
                    'lessons_count' => (int) ($level->lessons_count ?? 0),
                ];
            })->values(),
            'created_at' => optional($module->created_at)->toIso8601String(),
            'updated_at' => optional($module->updated_at)->toIso8601String(),
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $modules,
    ]);
});

Route::get('/modules/{module}/levels', function (Request $request, Module $module) {
    $status = $request->query('status');

    $query = $module->levels()->orderBy('id');
    if (is_string($status) && $status !== '') {
        $query->where('status', $status);
    } else {
        $query->where('status', 'active');
    }

    $levels = $query->withCount(['lessons' => function ($lessons) {
        $lessons->where('status', 'active');
    }])->get()->values()->map(function (Level $level, $index) {
        return [
            'id' => $level->id,
            'order' => $index + 1,
            'status' => $level->status,
            'lessons_count' => (int) ($level->lessons_count ?? 0),
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $levels,
    ]);
});

Route::get('/levels/{level}/lessons', function (Request $request, Level $level) {
    $status = $request->query('status');
    $lang = strtolower((string) $request->query('lang', 'en'));
    if ($lang === '') {
        $lang = 'en';
    }

    $query = $level->lessons()
        ->with([
            'translations' => function ($q) use ($lang) {
                $q->whereIn('language_code', [$lang, 'en']);
            },
        ])
        ->orderBy('order');

    if (is_string($status) && $status !== '') {
        $query->where('status', $status);
    } else {
        $query->where('status', 'active');
    }

    $lessons = $query->get()->map(function (Lesson $lesson) use ($lang) {
        $translation = $lesson->translations->firstWhere('language_code', $lang)
            ?? $lesson->translations->firstWhere('language_code', 'en')
            ?? $lesson->translations->first();

        return [
            'id' => $lesson->id,
            'order' => $lesson->order,
            'status' => $lesson->status,
            'title' => $translation?->title,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $lessons,
    ]);
});

Route::get('/lessons/{lesson}', function (Request $request, Lesson $lesson) {
    $lang = strtolower((string) $request->query('lang', 'en'));
    if ($lang === '') {
        $lang = 'en';
    }

    $lesson->load([
        'translations' => function ($q) use ($lang) {
            $q->whereIn('language_code', [$lang, 'en']);
        },
    ]);

    $translation = $lesson->translations->firstWhere('language_code', $lang)
        ?? $lesson->translations->firstWhere('language_code', 'en')
        ?? $lesson->translations->first();

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $lesson->id,
            'order' => $lesson->order,
            'status' => $lesson->status,
            'title' => $translation?->title,
            'content' => $translation?->content,
            'difficulty' => $lesson->difficulty ?? 'Beginner',
        ],
    ]);
});
