<?php

namespace App\Providers;

use App\Models\Activity;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(RequestHandled::class, function (RequestHandled $event) {
            $this->logAdminActivity($event);
        });
    }

    private function logAdminActivity(RequestHandled $event): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $request = $event->request;
        $response = $event->response;

        if (! $request->routeIs('admin.*') && ! $request->is('admin/*')) {
            return;
        }

        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (! $user || $user->role !== 'admin') {
            return;
        }

        $method = strtoupper($request->method());

        if (! in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        if ($response->getStatusCode() >= 400) {
            return;
        }

        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return;
        }

        $routeParts = explode('.', $routeName);

        if (count($routeParts) < 2) {
            return;
        }

        array_shift($routeParts);
        $actionPart = array_pop($routeParts);
        $subject = trim(Str::headline(str_replace('-', ' ', implode(' ', $routeParts))));
        $actionKey = $this->resolveActionKey($actionPart, $method);
        $actionLabel = $this->resolveActionLabel($actionKey);
        $description = trim($actionLabel.' '.$subject);

        Activity::create([
            'user_id' => $user->id,
            'action' => $actionKey,
            'subject_type' => $subject ?: 'Admin action',
            'subject_id' => null,
            'description' => $description ?: $actionLabel,
        ]);
    }

    private function resolveActionKey(string $actionPart, string $method): string
    {
        $normalized = str_replace('_', '-', $actionPart);

        return match (true) {
            $normalized === 'store' => 'created',
            $normalized === 'update' => 'updated',
            $normalized === 'destroy' => 'deleted',
            str_contains($normalized, 'delete') => 'deleted',
            str_contains($normalized, 'publish') => 'published',
            str_contains($normalized, 'unpublish') => 'unpublished',
            str_contains($normalized, 'approve') => 'approved',
            str_contains($normalized, 'reject') => 'rejected',
            str_contains($normalized, 'verify') => 'verified',
            str_contains($normalized, 'pin') => 'pinned',
            str_contains($normalized, 'unpin') => 'unpinned',
            str_contains($normalized, 'activate') => 'activated',
            str_contains($normalized, 'deactivate') => 'deactivated',
            $method === 'POST' => 'created',
            $method === 'PUT', $method === 'PATCH' => 'updated',
            $method === 'DELETE' => 'deleted',
            default => 'updated',
        };
    }

    private function resolveActionLabel(string $actionKey): string
    {
        return match ($actionKey) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'published' => 'Published',
            'unpublished' => 'Unpublished',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'verified' => 'Verified',
            'pinned' => 'Pinned',
            'unpinned' => 'Unpinned',
            'activated' => 'Activated',
            'deactivated' => 'Deactivated',
            default => Str::headline($actionKey),
        };
    }
}
