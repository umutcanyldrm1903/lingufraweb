<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\QnaController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CheckOutController;
use App\Http\Controllers\Frontend\FavoriteController;
use App\Http\Controllers\Frontend\HomePageController;
use App\Http\Controllers\Frontend\LearningController;
use App\Http\Controllers\Frontend\AboutPageController;
use App\Http\Controllers\Frontend\CoursePageController;
use App\Http\Controllers\Global\CloudStorageController;
use App\Http\Controllers\Frontend\StudentOrderController;
use App\Http\Controllers\Frontend\CourseContentController;
use App\Http\Controllers\Frontend\StudentReviewController;
use App\Http\Controllers\Frontend\BecomeInstructorController;
use App\Http\Controllers\Frontend\CorporateController;
use App\Http\Controllers\Frontend\InstructorCourseController;
use App\Http\Controllers\Frontend\InstructorPayoutController;
use App\Http\Controllers\Frontend\StudentDashboardController;
use App\Http\Controllers\Frontend\StudentMessageController;
use App\Http\Controllers\Frontend\TinymceImageUploadController;
use App\Http\Controllers\Frontend\InstructorDashboardController;
use App\Http\Controllers\Frontend\InstructorHomeworkController;
use App\Http\Controllers\Frontend\InstructorLessonQnaController;
use App\Http\Controllers\Frontend\InstructorLessonController;
use App\Http\Controllers\Frontend\InstructorMessageController;
use App\Http\Controllers\Frontend\InstructorLibraryController;
use App\Http\Controllers\Frontend\StudentProfileSettingController;
use App\Http\Controllers\Frontend\InstructorAnnouncementController;
use App\Http\Controllers\Frontend\InstructorLiveCredentialController;
use App\Http\Controllers\Frontend\InstructorProfileSettingController;
use App\Http\Controllers\Frontend\InstructorReportController;
use App\Http\Controllers\Frontend\InstructorScheduleController;
use App\Http\Controllers\Frontend\InstructorStudentController;
use App\Http\Controllers\Frontend\StudentHomeworkController;
use App\Http\Controllers\Frontend\StudentLibraryController;
use App\Http\Controllers\Frontend\PlacementTestController;
use App\Http\Controllers\Outreach\OutreachUnsubscribeController;

Route::group(['middleware' => 'maintenance.mode'], function () {

    /**
     * ============================================================================
     * Global Routes
     * ============================================================================
     */

    Route::get('set-language', [DashboardController::class, 'setLanguage'])->name('set-language');
    Route::get('set-currency', [HomePageController::class, 'setCurrency'])->name('set-currency');

    Route::get('/', [HomePageController::class, 'index'])->name('home');
    Route::get('robots.txt', [HomePageController::class, 'robots'])->name('robots');
    Route::get('sitemap.xml', [HomePageController::class, 'sitemap'])->name('sitemap');
    Route::get('outreach/unsubscribe/{token}', OutreachUnsubscribeController::class)->name('outreach.unsubscribe');
    Route::get('lingufranca-performans-sistemi', [HomePageController::class, 'linguFrancaPerformance'])
        ->name('lingufranca-performance');
    Route::get('lingufranca-performans-sistemi/asset/{asset}', [HomePageController::class, 'linguFrancaPerformanceAsset'])
        ->where('asset', '[A-Za-z0-9\\-_]+')
        ->name('lingufranca-performance.asset');

    Route::get('corporate', [CorporateController::class, 'index'])->name('corporate.index');
    Route::get('corporate-form', [CorporateController::class, 'form'])->name('corporate.form');
    Route::post('corporate-form', [CorporateController::class, 'submit'])->name('corporate.submit');
    Route::get('ingilizce-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'hub')
        ->name('english-private-lessons');
    Route::get('online-ingilizce-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'online')
        ->name('english-private-lessons.online');
    Route::get('ingilizce-konusma-dersi', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'speaking')
        ->name('english-private-lessons.speaking');
    Route::get('is-ingilizcesi-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'business')
        ->name('english-private-lessons.business');
    Route::get('istanbul-ingilizce-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'istanbul')
        ->name('english-private-lessons.istanbul');
    Route::get('ankara-ingilizce-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'ankara')
        ->name('english-private-lessons.ankara');
    Route::get('izmir-ingilizce-ozel-ders', [HomePageController::class, 'englishPrivateLesson'])
        ->defaults('page', 'izmir')
        ->name('english-private-lessons.izmir');

    Route::get('countries', [HomePageController::class, 'countries'])->name('countries');
    Route::get('states/{country_id}', [HomePageController::class, 'states'])->name('states');
    Route::get('cities/{state_id}', [HomePageController::class, 'cities'])->name('cities');

    /** become a instructor */
    Route::get('become-instructor', [BecomeInstructorController::class, 'index'])->name('become-instructor')->middleware('auth');
    Route::post('become-instructor', [BecomeInstructorController::class, 'store'])->name('become-instructor.create')->middleware('auth');

    Route::get('courses', [CoursePageController::class, 'index'])->name('courses');
    Route::get('fetch-courses', [CoursePageController::class, 'fetchCourses'])->name('fetch-courses');
    Route::get('course/{slug}', [CoursePageController::class, 'show'])->name('course.show');

    /** cart routes */
    Route::get('cart', [CartController::class, 'index'])->name('cart');
    Route::post('add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add-to-cart');
    Route::get('remove-cart-item/{rowId}', [CartController::class, 'removeCartItem'])->name('remove-cart-item');
    Route::post('apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::get('remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');

    /** Blog Routes */
    Route::get('blog', [BlogController::class, 'index'])->name('blogs');
    Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::post('blog/submit-comment', [BlogController::class, 'submitComment'])->name('blog.submit-comment');
    Route::get('all-instructors', [HomePageController::class, 'allInstructors'])->name('all-instructors');
    Route::get('instructor-details/{id}/{slug?}', [HomePageController::class, 'instructorDetails'])->name('instructor-details');
    Route::post('quick-connect/{id}', [HomePageController::class, 'quickConnect'])->name('quick-connect');

    /** About page routes */
    Route::get('about-us', [AboutPageController::class, 'index'])->name('about-us');
    /** Contact page routes */
    Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('contact/send-mail', [ContactController::class, 'sendMail'])->name('contact.send-mail');
    /** Legal pages */
    Route::view('mobile-app-privacy-policy', 'frontend.pages.mobile-app-privacy-policy')->name('mobile-app-privacy-policy');
    Route::view('teslimat-ve-iade-sartlari', 'frontend.pages.delivery-return-terms')->name('delivery-return-terms');
    Route::view('mesafeli-satis-sozlesmesi', 'frontend.pages.distance-sales-contract')->name('distance-sales-contract');

    /** Custom pages */
    Route::get('page/{slug}', [HomePageController::class, 'customPage'])->name('custom-page');

    /** other routes */
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['auth:admin'], 'as' => 'admin.'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    Route::group(['prefix' => 'frontend-filemanager', 'middleware' => ['web']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });

    Route::get('change-theme/{name}', [HomePageController::class, 'changeTheme'])->name('change-theme');
    Route::get('placement-test', [PlacementTestController::class, 'show'])->name('placement-test.show');
    Route::post('placement-test', [PlacementTestController::class, 'submit'])->name('placement-test.submit');

    /**
     * ============================================================================
     * Student Dashboard Routes
     * ============================================================================
     */

    Route::middleware(['auth', 'verified', 'default.locale:tr'])->get('invite', [StudentDashboardController::class, 'invite'])->name('student.invite');

    Route::group(['middleware' => ['auth', 'verified', 'default.locale:tr'], 'prefix' => 'student', 'as' => 'student.'], function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('instructors', [StudentDashboardController::class, 'instructors'])->name('instructors');
        Route::get('instructors/{instructor}/schedule', [StudentDashboardController::class, 'instructorSchedule'])->name('instructors.schedule');
        Route::post('instructors/{instructor}/schedule', [StudentDashboardController::class, 'storeInstructorSchedule'])->name('instructors.schedule.store');
        Route::get('plans', [StudentDashboardController::class, 'plans'])->name('plans.index');
        Route::post('plans/purchase', [StudentDashboardController::class, 'purchasePlan'])->name('plans.purchase');
        Route::post('trial/request', [StudentDashboardController::class, 'requestTrialLesson'])->name('trial.request');
        Route::post('plans/cart', [CartController::class, 'addStudentPlanToCart'])->name('plans.cart.add');
        Route::get('plans/cart/remove', [CartController::class, 'removeStudentPlanFromCart'])->name('plans.cart.remove');
        // Profile setting routes
        Route::get('setting', [StudentProfileSettingController::class, 'index'])->name('setting.index');
        Route::put('setting/profile', [StudentProfileSettingController::class, 'updateProfile'])->name('setting.profile.update');
        Route::put('setting/bio', [StudentProfileSettingController::class, 'updateBio'])->name('setting.bio.update');
        Route::put('setting/password', [StudentProfileSettingController::class, 'updatePassword'])->name('setting.password.update');
        Route::get('setting/experience-modal', [StudentProfileSettingController::class, 'showExperienceModal'])->name('setting.experience-modal');
        Route::get('setting/edit-experience-modal/{id}', [StudentProfileSettingController::class, 'editExperienceModal'])->name('setting.edit-experience-modal');

        Route::post('setting/experience', [StudentProfileSettingController::class, 'storeExperience'])->name('setting.experience.store');
        Route::put('setting/experience/{id}', [StudentProfileSettingController::class, 'updateExperience'])->name('setting.experience.update');
        Route::delete('setting/experience/{id}', [StudentProfileSettingController::class, 'destroyExperience'])->name('setting.experience.destroy');

        Route::get('setting/add-education-modal', [StudentProfileSettingController::class, 'addEducationModal'])->name('setting.add-education-modal');
        Route::post('setting/education', [StudentProfileSettingController::class, 'storeEducation'])->name('setting.education.store');
        Route::get('setting/edit-education-modal/{id}', [StudentProfileSettingController::class, 'editEducationModal'])->name('setting.edit-education-modal');
        Route::put('setting/education/{id}', [StudentProfileSettingController::class, 'updateEducation'])->name('setting.education.update');
        Route::delete('setting/education/{id}', [StudentProfileSettingController::class, 'destroyEducation'])->name('setting.education.destroy');

        Route::put('setting/address', [StudentProfileSettingController::class, 'updateAddress'])->name('setting.address.update');
        Route::put('setting/socials', [StudentProfileSettingController::class, 'updateSocials'])->name('setting.socials.update');

        /** Order Routes */
        Route::get('orders', [StudentOrderController::class, 'index'])->name('orders.index');
        Route::get('order-details/{id}', [StudentOrderController::class, 'show'])->name('order.show');
        Route::get('order/invoice/{id}', [StudentOrderController::class, 'printInvoice'])->name('order.print-invoice');

        Route::get('reviews', [StudentReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{id}', [StudentReviewController::class, 'show'])->name('reviews.show');
        Route::delete('reviews/{id}', [StudentReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::get('enrolled-courses', [StudentDashboardController::class, 'enrolledCourses'])->name('enrolled-courses');
        Route::get('quiz-attempts', [StudentDashboardController::class, 'quizAttempts'])->name('quiz-attempts');
        /** messages */
        Route::get('messages/{user?}', [StudentMessageController::class, 'index'])->name('messages.index');
        Route::post('messages/{user}', [StudentMessageController::class, 'store'])->name('messages.store');

        /** learning routes */
        Route::get('learning/{slug}', [LearningController::class, 'index'])->name('learning.index');
        Route::post('learning/get-file-info', [LearningController::class, 'getFileInfo'])->name('get-file-info');
        Route::post('learning/make-lesson-complete', [LearningController::class, 'makeLessonComplete'])->name('make-lesson-complete');
        Route::get('learning/resource-download/{id}', [LearningController::class, 'downloadResource'])->name('download-resource');

        Route::get('learning/quiz/{id}', [LearningController::class, 'quizIndex'])->name('quiz.index');
        Route::post('learning/quiz/{id}', [LearningController::class, 'quizStore'])->name('quiz.store');
        Route::get('learning/quiz-result/{id}/{result_id}', [LearningController::class, 'quizResult'])->name('quiz.result');
        Route::get('learning/{slug}/{lesson_id}', [LearningController::class, 'liveSession'])->name('learning.live');
        Route::get('live-lessons/{lesson}', [LearningController::class, 'studentLiveSession'])->name('live-lessons.join');
        Route::get('live-lessons/{lesson}/rate', [StudentDashboardController::class, 'showLiveLessonRating'])->name('live-lessons.rate');
        Route::post('live-lessons/{lesson}/rate', [StudentDashboardController::class, 'storeLiveLessonRating'])->name('live-lessons.rate.store');
        Route::post('live-lessons/{lesson}/cancel', [StudentDashboardController::class, 'cancelLiveLesson'])->name('live-lessons.cancel');

        /** qna routes */
        Route::post('create-question', [QnaController::class, 'create'])->name('qna.create');
        Route::get('fetch-lesson-questions', [QnaController::class, 'fetchLessonQuestions'])->name('fetch-lesson-questions');
        Route::post('create-reply', [QnaController::class, 'createReply'])->name('create-reply');
        Route::get('fetch-replies', [QnaController::class, 'fetchReply'])->name('fetch-replies');

        Route::delete('delete-question/{id}', [QnaController::class, 'destroyQuestion'])->name('destroy-question');
        Route::delete('delete-reply/{id}', [QnaController::class, 'destroyReply'])->name('destroy-reply');

        /** course review Routes */
        Route::post('add-review', [LearningController::class, 'addReview'])->name('add-review');
        Route::get('fetch-reviews/{course_id}', [LearningController::class, 'fetchReviews'])->name('fetch-reviews');

        /** download certificate route */
        Route::get('download-certificate/{id}', [StudentDashboardController::class, 'downloadCertificate'])->name('download-certificate');
        Route::view('wishlist', 'frontend.wishlist.index')->name('wishlist');

        /** custom student pages */
        Route::get('homeworks', [StudentHomeworkController::class, 'index'])->name('homeworks.index');
        Route::post('homeworks/{homework}/submit', [StudentHomeworkController::class, 'submit'])->name('homeworks.submit');
        Route::view('support', 'frontend.student-dashboard.support.index')->name('support.index');
        Route::view('guide', 'frontend.student-dashboard.guide.index')->name('guide.index');
        Route::get('library', [StudentLibraryController::class, 'index'])->name('library.index');
        Route::get('reports', [StudentDashboardController::class, 'reports'])->name('reports.index');

    });

    /**
     * ============================================================================
     * Instructor Dashboard Routes
     * ============================================================================
     */

	    Route::group(['middleware' => ['auth', 'verified', 'approved.instructor', 'role:instructor', 'force.locale:en'], 'prefix' => 'instructor', 'as' => 'instructor.'], function () {
	        Route::get('dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
	
	        /** custom instructor pages */
	        Route::get('messages/{user?}', [InstructorMessageController::class, 'index'])->name('messages.index');
	        Route::post('messages/{user}', [InstructorMessageController::class, 'store'])->name('messages.store');
	        Route::get('schedule', [InstructorScheduleController::class, 'index'])->name('schedule.index');
	        Route::post('schedule/availability', [InstructorScheduleController::class, 'storeAvailability'])->name('schedule.availability.store');
	        Route::delete('schedule/availability/{availability}', [InstructorScheduleController::class, 'destroyAvailability'])->name('schedule.availability.destroy');
	        Route::get('lessons', [InstructorLessonController::class, 'index'])->name('lessons.index');
	        Route::post('lessons/{lesson}/cancel', [InstructorLessonController::class, 'cancel'])->name('lessons.cancel');
	        Route::post('lessons/{lesson}/summary', [InstructorLessonController::class, 'updateSummary'])->name('lessons.summary');
	        Route::get('live-lessons/{lesson}', [LearningController::class, 'studentLiveSession'])->name('live-lessons.join');
	        Route::get('students', [InstructorStudentController::class, 'index'])->name('students.index');
	        Route::get('students/{student}/panel', [InstructorStudentController::class, 'panel'])->name('students.panel');
	        Route::get('homeworks', [InstructorHomeworkController::class, 'index'])->name('homeworks.index');
	        Route::post('homeworks', [InstructorHomeworkController::class, 'store'])->name('homeworks.store');
	        Route::post('homeworks/{homework}/archive', [InstructorHomeworkController::class, 'archive'])->name('homeworks.archive');
	        Route::view('guide', 'frontend.instructor-dashboard.guide.index')->name('guide.index');
	        Route::get('library', [InstructorLibraryController::class, 'index'])->name('library.index');
	        Route::post('library', [InstructorLibraryController::class, 'store'])->name('library.store');
	        Route::delete('library/{item}', [InstructorLibraryController::class, 'destroy'])->name('library.destroy');
	        Route::view('agreement', 'frontend.instructor-dashboard.agreement.index')->name('agreement.index');
	        Route::get('instructions', function () {
	            // Some deployments may still have older versions of this view that expect an
	            // `$instructors` variable. Provide a safe default to avoid a 500.
	            return view('frontend.instructor-dashboard.instructions.index', [
	                'instructors' => collect(),
	            ]);
	        })->name('instructions.index');
	        Route::get('reports', [InstructorReportController::class, 'index'])->name('reports.index');
	        // Profile setting routes
        Route::get('zoom-setting', [InstructorLiveCredentialController::class, 'index'])->name('zoom-setting.index');
        Route::put('zoom-setting', [InstructorLiveCredentialController::class, 'update'])->name('zoom-setting.update');
        Route::get('zoom-setting/connect', [InstructorLiveCredentialController::class, 'connect'])->name('zoom-setting.connect');
        Route::get('zoom-setting/callback', [InstructorLiveCredentialController::class, 'callback'])->name('zoom-setting.callback');
        Route::post('zoom-setting/disconnect', [InstructorLiveCredentialController::class, 'disconnect'])->name('zoom-setting.disconnect');
	        Route::get('jitsi-setting', [InstructorLiveCredentialController::class, 'jitsi_index'])->name('jitsi-setting.index');
        Route::put('jitsi-setting', [InstructorLiveCredentialController::class, 'jitsi_update'])->name('jitsi-setting.update');
        Route::get('setting', [InstructorProfileSettingController::class, 'index'])->name('setting.index');
        Route::put('setting/profile', [InstructorProfileSettingController::class, 'updateProfile'])->name('setting.profile.update');
        Route::put('setting/email', [InstructorProfileSettingController::class, 'updateEmail'])->name('setting.email.update');
        Route::put('setting/password', [InstructorProfileSettingController::class, 'updatePassword'])->name('setting.password.update');
        Route::post('setting/schedule', [InstructorScheduleController::class, 'updateAvailability'])->name('setting.schedule.update');

        /** Course Routes */
        Route::get('courses', [InstructorCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
        Route::get('courses/create/{id}/step/{step?}', [InstructorCourseController::class, 'edit'])->name('courses.edit');
        Route::get('courses/{id}/edit', [InstructorCourseController::class, 'editView'])->name('courses.edit-view');

        Route::get('courses/get-filters/{category_id}', [InstructorCourseController::class, 'getFiltersByCategory'])->name('courses.get-filters');
        Route::get('courses/get-instructors', [InstructorCourseController::class, 'getInstructors'])->name('courses.get-instructors');

        Route::post('courses/create', [InstructorCourseController::class, 'store'])->name('courses.store');
        Route::post('courses/update', [InstructorCourseController::class, 'update'])->name('courses.update');

        /** Course content routes */
        Route::post('course-chapter/{course_id?}/store', [CourseContentController::class, 'chapterStore'])->name('course-chapter.store');
        Route::get('course-chapter/sorting/{course_id}', [CourseContentController::class, 'chapterSorting'])->name('course-chapter.sorting.index');
        Route::get('course-chapter/edit/{chapter_id}', [CourseContentController::class, 'chapterEdit'])->name('course-chapter.edit');
        Route::put('course-chapter/update/{chapter_id}', [CourseContentController::class, 'chapterUpdate'])->name('course-chapter.update');
        Route::delete('course-chapter/delete/{chapter_id}', [CourseContentController::class, 'chapterDestroy'])->name('course-chapter.destroy');

        Route::post('course-chapter/sorting/{course_id}', [CourseContentController::class, 'chapterSortingStore'])->name('course-chapter.sorting.store');
        Route::get('course-chapter/lesson/create', [CourseContentController::class, 'lessonCreate'])->name('course-chapter.lesson.create');
        Route::post('course-chapter/lesson/create', [CourseContentController::class, 'lessonStore'])->name('course-chapter.lesson.store');
        Route::get('course-chapter/lesson/edit', [CourseContentController::class, 'lessonEdit'])->name('course-chapter.lesson.edit');

        Route::post('course-chapter/lesson/update', [CourseContentController::class, 'lessonUpdate'])->name('course-chapter.lesson.update');
        Route::delete('course-chapter/lesson/{chapter_item_id}/destroy', [CourseContentController::class, 'chapterLessonDestroy'])->name('course-chapter.lesson.destroy');
        Route::post('course-chapter/lesson/sorting/{chapter_id}', [CourseContentController::class, 'sortLessons'])->name('course-chapter.lesson.sorting');

        Route::get('course-chapter/quiz-question/create/{quiz_id}', [CourseContentController::class, 'createQuizQuestion'])->name('course-chapter.quiz-question.create');
        Route::post('course-chapter/quiz-question/create/{quiz_id}', [CourseContentController::class, 'storeQuizQuestion'])->name('course-chapter.quiz-question.store');
        Route::get('course-chapter/quiz-question/edit/{question_id}', [CourseContentController::class, 'editQuizQuestion'])->name('course-chapter.quiz-question.edit');
        Route::put('course-chapter/quiz-question/update/{question_id}', [CourseContentController::class, 'updateQuizQuestion'])->name('course-chapter.quiz-question.update');
        Route::delete('course-chapter/quiz-question/delete/{question_id}', [CourseContentController::class, 'destroyQuizQuestion'])->name('course-chapter.quiz-question.destroy');
        Route::get('course-delete-request/{course_id}', [InstructorCourseController::class, 'showDeleteRequest'])->name('course.delete-request.show');
        Route::post('course-delete-request', [InstructorCourseController::class, 'sendDeleteRequest'])->name('course.send-delete-request');

        /** payout routes */
        Route::get('payout', [InstructorPayoutController::class, 'index'])->name('payout.index');
        Route::get('payout/create', [InstructorPayoutController::class, 'create'])->name('payout.create');
        Route::post('payout/create', [InstructorPayoutController::class, 'store'])->name('payout.store');
        Route::delete('payout/delete/{id}', [InstructorPayoutController::class, 'destroy'])->name('payout.destroy');

        /** announcement routes */
        Route::resource('announcements', InstructorAnnouncementController::class);

        /** my sales routes */
        Route::get('my-sells', [InstructorDashboardController::class, 'mySells'])->name('my-sells.index');
        /** lessons qna routes */
        Route::get('lesson-question', [InstructorLessonQnaController::class, 'index'])->name('lesson-questions.index');
        Route::post('lesson-question/{id}', [InstructorLessonQnaController::class, 'createReply'])->name('lesson-question.reply');
        Route::delete('lesson-question/destroy/{id}', [InstructorLessonQnaController::class, 'destroyQuestion'])->name('lesson-question.destroy');
        Route::delete('lesson-question/reply/destroy/{id}', [InstructorLessonQnaController::class, 'destroyReply'])->name('lesson-reply.destroy');
        Route::put('lesson-question/seen-update/{id}', [InstructorLessonQnaController::class, 'markAsReadUnread'])->name('lesson-question.seen-update');

        Route::post('cloud/store', [CloudStorageController::class, 'store'])->name('cloud.store');

        Route::view('wishlist', 'frontend.wishlist.index')->name('wishlist');
    });
    /** wishlist routes */
    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::controller(FavoriteController::class)->group(function () {
            Route::get('wishlist/{course:slug}', 'update')->name('wishlist.update');
            Route::delete('wishlist/{course:slug}', 'destroy')->name('wishlist.remove');
        });
        /** secure-video route */
        Route::get('secure-video/{hash}', App\Http\Controllers\SecureLinkPreviewController::class)->name('secure.video')->middleware('signed');
    });

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('checkout', [CheckOutController::class, 'index'])->name('checkout.index');
        Route::post('tinymce-upload-image', [TinymceImageUploadController::class, 'upload']);
        Route::delete('tinymce-delete-image', [TinymceImageUploadController::class, 'destroy']);
    });
});

//maintenance mode route
Route::get('/maintenance-mode', function () {
    $setting = Illuminate\Support\Facades\Cache::get('setting', null);
    if (!$setting?->maintenance_mode) {
        return redirect()->route('home');
    }

    return view('global.maintenance');
})->name('maintenance.mode');

require __DIR__ . '/auth.php';

require __DIR__ . '/admin.php';
