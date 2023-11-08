<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotificationQueue;
use App\Policies\CategoryPolicy;
use App\Policies\OptionPolicy;
use App\Policies\OrderPerformerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductTypePolicy;
use App\Policies\PropertyPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        User::class => UserPolicy::class,
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        ProductType::class => ProductTypePolicy::class,
        OrderPerformer::class => OrderPerformerPolicy::class,
        Tag::class => TagPolicy::class,
        Category::class => CategoryPolicy::class,
        Option::class => OptionPolicy::class,
        Property::class => PropertyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
