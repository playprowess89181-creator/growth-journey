<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CommunityGroupController;
use App\Http\Controllers\Admin\CommunityPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\Dialogue\DialogueCommentController as AdminDialogueCommentController;
use App\Http\Controllers\Admin\Dialogue\DialogueReportsController as AdminDialogueReportsController;
use App\Http\Controllers\Admin\Dialogue\DialogueTopicController as AdminDialogueTopicController;
use App\Http\Controllers\Admin\Dialogue\DialogueTopicRequestController as AdminDialogueTopicRequestController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PrayerWall\PrayerCommentController as AdminPrayerCommentController;
use App\Http\Controllers\Admin\PrayerWall\PrayerInteractionController as AdminPrayerInteractionController;
use App\Http\Controllers\Admin\PrayerWall\PrayerRequestController as AdminPrayerRequestController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\TrackHabitController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\VocabularyCategoryController;
use App\Http\Controllers\Admin\VocabularyController;
use App\Http\Controllers\Admin\VocabularyUploadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('auth.login');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/bulk-verify', [UserManagementController::class, 'bulkVerify'])->name('users.bulk-verify');

    // Modules
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

    // Levels (managed inside a Module)
    Route::post('/modules/{module}/levels', [LevelController::class, 'store'])->name('levels.store');
    Route::put('/modules/{module}/levels/{level}', [LevelController::class, 'update'])->name('levels.update');
    Route::delete('/modules/{module}/levels/{level}', [LevelController::class, 'destroy'])->name('levels.destroy');
    Route::get('/modules/{module}/levels/{level}', [LevelController::class, 'show'])->name('levels.show');

    // Lessons (managed inside a Level)
    Route::get('/modules/{module}/levels/{level}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/modules/{module}/levels/{level}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/modules/{module}/levels/{level}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/modules/{module}/levels/{level}/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/modules/{module}/levels/{level}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Vocabulary
    Route::prefix('vocabulary')->name('vocabulary.')->group(function () {
        Route::get('/', [VocabularyController::class, 'index'])->name('index');
        Route::get('/words', [VocabularyController::class, 'wordsIndex'])->name('words.index');
        Route::get('/words/{word}', [VocabularyController::class, 'showWord'])->name('words.show');
        Route::put('/words/{word}', [VocabularyController::class, 'updateWord'])->name('words.update');
        Route::delete('/words/{word}', [VocabularyController::class, 'destroyWord'])->name('words.destroy');

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [VocabularyCategoryController::class, 'index'])->name('index');
            Route::get('/create', [VocabularyCategoryController::class, 'create'])->name('create');
            Route::post('/', [VocabularyCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [VocabularyCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [VocabularyCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [VocabularyCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('upload-words')->name('upload-words.')->group(function () {
            Route::get('/', [VocabularyUploadController::class, 'index'])->name('index');
            Route::get('/template', [VocabularyUploadController::class, 'template'])->name('template');
            Route::post('/preview', [VocabularyUploadController::class, 'preview'])->name('preview');
            Route::post('/', [VocabularyUploadController::class, 'store'])->name('store');
        });
    });

    // Community Groups
    Route::prefix('community')->name('community.')->group(function () {
        Route::resource('groups', CommunityGroupController::class);
        Route::post('/groups/{group}/toggle-status', [CommunityGroupController::class, 'toggleStatus'])->name('groups.toggle-status');
            Route::delete('/groups/{group}/members/{user}', [CommunityGroupController::class, 'removeMember'])->name('groups.members.remove');

        // Bulk Actions for Groups
        Route::post('/groups/bulk-activate', [CommunityGroupController::class, 'bulkActivate'])->name('groups.bulk-activate');
        Route::post('/groups/bulk-deactivate', [CommunityGroupController::class, 'bulkDeactivate'])->name('groups.bulk-deactivate');
        Route::post('/groups/bulk-delete', [CommunityGroupController::class, 'bulkDelete'])->name('groups.bulk-delete');

        // Community Posts
        Route::resource('posts', CommunityPostController::class);
        Route::patch('/posts/{post}/publish', [CommunityPostController::class, 'publish'])->name('posts.publish');
        Route::patch('/posts/{post}/unpublish', [CommunityPostController::class, 'unpublish'])->name('posts.unpublish');
        Route::patch('/posts/{post}/pin', [CommunityPostController::class, 'pin'])->name('posts.pin');
        Route::patch('/posts/{post}/unpin', [CommunityPostController::class, 'unpin'])->name('posts.unpin');

        // Bulk Actions for Posts
        Route::post('/posts/bulk-publish', [CommunityPostController::class, 'bulkPublish'])->name('posts.bulk-publish');
        Route::post('/posts/bulk-unpublish', [CommunityPostController::class, 'bulkUnpublish'])->name('posts.bulk-unpublish');
        Route::post('/posts/bulk-pin', [CommunityPostController::class, 'bulkPin'])->name('posts.bulk-pin');
        Route::post('/posts/bulk-unpin', [CommunityPostController::class, 'bulkUnpin'])->name('posts.bulk-unpin');
        Route::post('/posts/bulk-delete', [CommunityPostController::class, 'bulkDelete'])->name('posts.bulk-delete');

        // Comments
        Route::resource('comments', CommentController::class)->except(['create', 'store']);
        Route::post('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::post('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
        Route::get('/posts/{post}/comments', [CommentController::class, 'getByPost'])->name('posts.comments');

        // Bulk Actions for Comments
        Route::post('/comments/bulk-approve', [CommentController::class, 'bulkApprove'])->name('comments.bulk-approve');
        Route::post('/comments/bulk-reject', [CommentController::class, 'bulkReject'])->name('comments.bulk-reject');
        Route::post('/comments/bulk-delete', [CommentController::class, 'bulkDelete'])->name('comments.bulk-delete');

        // Reports
        Route::resource('reports', ReportsController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('/reports/bulk-update', [ReportsController::class, 'bulkUpdate'])->name('reports.bulk-update');
    });

    Route::prefix('dialogue')->name('dialogue.')->group(function () {
        Route::resource('topics', AdminDialogueTopicController::class)->except(['show']);
        Route::resource('topic-requests', AdminDialogueTopicRequestController::class)->except(['show', 'create', 'store']);
        Route::get('/comments', [AdminDialogueCommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [AdminDialogueCommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject', [AdminDialogueCommentController::class, 'reject'])->name('comments.reject');
        Route::delete('/comments/{comment}', [AdminDialogueCommentController::class, 'destroy'])->name('comments.destroy');
        Route::resource('reports', AdminDialogueReportsController::class)->only(['index', 'show', 'update', 'destroy']);
    });

    Route::prefix('prayer-wall')->name('prayer-wall.')->group(function () {
        Route::resource('requests', AdminPrayerRequestController::class)->except(['show']);
        Route::get('/comments', [AdminPrayerCommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [AdminPrayerCommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject', [AdminPrayerCommentController::class, 'reject'])->name('comments.reject');
        Route::delete('/comments/{comment}', [AdminPrayerCommentController::class, 'destroy'])->name('comments.destroy');
        Route::get('/prayers', [AdminPrayerInteractionController::class, 'index'])->name('prayers.index');
        Route::delete('/prayers/{prayer}', [AdminPrayerInteractionController::class, 'destroy'])->name('prayers.destroy');
    });

    Route::get('/track-habits', [TrackHabitController::class, 'index'])->name('track-habits.index');
    Route::get('/track-habits/{group}', [TrackHabitController::class, 'show'])->name('track-habits.show');
    Route::get('/track-habits/{group}/habits/create', [TrackHabitController::class, 'create'])->name('track-habits.habits.create');
    Route::post('/track-habits/{group}/habits', [TrackHabitController::class, 'store'])->name('track-habits.habits.store');
    Route::get('/track-habits/{group}/habits/{habit}/edit', [TrackHabitController::class, 'edit'])->name('track-habits.habits.edit');
    Route::put('/track-habits/{group}/habits/{habit}', [TrackHabitController::class, 'update'])->name('track-habits.habits.update');
    Route::delete('/track-habits/{group}/habits/{habit}', [TrackHabitController::class, 'destroy'])->name('track-habits.habits.destroy');
    Route::get('/track-habits/{group}/members/{member}/stats', [TrackHabitController::class, 'memberStats'])->name('track-habits.members.stats');
    Route::post('/track-habits/{group}/members/{member}/freeze', [TrackHabitController::class, 'freezeStreak'])->name('track-habits.members.freeze');

});

require __DIR__.'/auth.php';
