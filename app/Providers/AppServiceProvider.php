<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Equb\EqubRepository;

use App\Repositories\User\UserRepository;
use App\Repositories\Equb\IEqubRepository;


use App\Repositories\User\IUserRepository;
use App\Repositories\Member\MemberRepository;

use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\PaymentRepository;

use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\EqubTypeRepository;

use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\EqubTaker\EqubTakerRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\ActivityLogRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;
use App\Repositories\MainEqub\MainEqubRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\RejectedDate\RejectedDateRepository;

use App\Repositories\Notification\INotificationRepository;
use App\Repositories\RejectedDate\IRejectedDateRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IEqubTypeRepository::class, EqubTypeRepository::class);
        $this->app->bind(IMemberRepository::class, MemberRepository::class);
        $this->app->bind(IPaymentRepository::class, PaymentRepository::class);
        $this->app->bind(IEqubRepository::class, EqubRepository::class);
        $this->app->bind(IEqubTakerRepository::class, EqubTakerRepository::class);
        $this->app->bind(IRejectedDateRepository::class, RejectedDateRepository::class);
        $this->app->bind(IActivityLogRepository::class, ActivityLogRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(INotificationRepository::class, NotificationRepository::class);
        $this->app->bind(MainEqubRepositoryInterface::class, MainEqubRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
        Schema::defaultStringLength(191);
    }
}
