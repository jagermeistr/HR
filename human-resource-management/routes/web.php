<?php

use App\Livewire\Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Livewire\Volt\Volt;
use App\Http\Controllers\MpesaB2CController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Admin\Dashboard::class)->name('dashboard');

    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', Admin\Companies\Index::class)->name('index');
        Route::get('/create', Admin\Companies\Create::class)->name('create');
        Route::get('/{id}/edit', Admin\Companies\Edit::class)->name('edit');
    });




    Route::middleware(('company.context'))->group(function () {
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', Admin\Departments\Index::class)->name('index');
            Route::get('/create', Admin\Departments\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Departments\Edit::class)->name('edit');
        });

        Route::prefix('designations')->name('designations.')->group(function () {
            Route::get('/', Admin\Designations\Index::class)->name('index');
            Route::get('/create', Admin\Designations\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Designations\Edit::class)->name('edit');
        });

        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', Admin\Employees\Index::class)->name('index');
            Route::get('/create', Admin\Employees\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Employees\Edit::class)->name('edit');
        });

        Route::prefix('farmers')->name('farmers.')->group(function () {
            Route::get('/', Admin\Farmers\Index::class)->name('index');
            Route::get('/create', Admin\Farmers\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Farmers\Edit::class)->name('edit');
        });



        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', Admin\Feedback\Index::class)->name('index');
            Route::get('/create', Admin\Feedback\Create::class)->name('create');
            Route::get('/{feedback}', Admin\Feedback\Show::class)->name('show');
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', Admin\Attendance\Index::class)->name('index');
            Route::get('/history', Admin\Attendance\History::class)->name('history'); 
        });

        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Leave\Index::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Leave\Create::class)->name('create');
            Route::get('/{leaveRequest}/edit', \App\Livewire\Admin\Leave\Edit::class)->name('edit');
        });


        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/', Admin\Contracts\Index::class)->name('index');
            Route::get('/create', Admin\Contracts\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Contracts\Edit::class)->name('edit');
        });

        Route::prefix('productions')->name('productions.')->group(function () {
            Route::get('/', Admin\Productions\Index::class)->name('index');
            Route::get('/create', Admin\Productions\Create::class)->name('create');
        });

        Route::prefix('collectioncenters')->name('collectioncenters.')->group(function () {
            Route::get('/', Admin\CollectionCenters\Index::class)->name('index');
            Route::get('/create', Admin\CollectionCenters\Create::class)->name('create');
        });

        Route::prefix('payrolls')->name('payrolls.')->group(function () {
            Route::get('/', Admin\Payrolls\Index::class)->name('index');
            Route::get('/{id}/show', Admin\Payrolls\Show::class)->name('show');
        });
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', Admin\Payments\Index::class)->name('index');
            Route::get('/{id}/show', Admin\Payments\Show::class)->name('show');
        });
    });
});



// ... your existing routes should be here ...

// =====================
// DEBUG ROUTES - ADD THESE AT THE BOTTOM
// =====================

Route::get('/debug-url', function () {
    $feedback = \App\Models\Feedback::latest()->first();
    
    echo "<h1>URL Generation Debug</h1>";
    echo "APP_URL: " . config('app.url') . "<br>";
    echo "url() helper: " . url('/') . "<br>";
    echo "current(): " . url()->current() . "<br>";
    
    if ($feedback) {
        echo "Feedback URL: " . url('/feedback/' . $feedback->id) . "<br>";
    }
    
    return "URL debug complete";
});

Route::get('/test-notification-fresh', function () {
    try {
        $employee = \App\Models\Employee::find(193); // Jeremy Mwangi
        $feedback = \App\Models\Feedback::latest()->first(); // Use latest feedback
        
        if ($employee && $feedback) {
            $employee->notify(new \App\Notifications\NewFeedbackNotification($feedback));
            return "✅ Fresh notification sent! Check Laravel logs for the URL used.";
        }
        return "Employee or feedback not found";
        
    } catch (\Exception $e) {
        return "❌ Notification failed: " . $e->getMessage();
    }
});

Route::get('/force-clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    
    // Manually delete cached files
    $files = glob('bootstrap/cache/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    return "✅ All caches cleared and files deleted!";
});

Route::get('/check-config', function () {
    echo "<h1>Current Configuration</h1>";
    echo "APP_URL: " . config('app.url') . "<br>";
    echo "MAIL_FROM: " . config('mail.from.address') . "<br>";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "<br>";
    
    // Test if config is using current ngrok
    if (str_contains(config('app.url'), '27e3422d8fc9')) {
        echo "✅ Using CURRENT ngrok URL<br>";
    } else {
        echo "❌ Using OLD ngrok URL: " . config('app.url') . "<br>";
    }
    
    return "Check complete";
});

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    
    return "✅ All caches cleared!";
});

// M-Pesa B2C Callback Routes (exclude from web middleware)
Route::withoutMiddleware(['web'])->group(function () {
    Route::post('/mpesa/b2c/result', [App\Http\Controllers\MpesaB2CController::class, 'handleB2CResult']);
    Route::post('/mpesa/b2c/timeout', [App\Http\Controllers\MpesaB2CController::class, 'handleB2CTimeout']);
    Route::post('/mpesa/b2c/queue-timeout', [App\Http\Controllers\MpesaB2CController::class, 'handleQueueTimeout']);
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__ . '/auth.php';
