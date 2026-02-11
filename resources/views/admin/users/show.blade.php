@extends('layouts.admin')

@section('title', 'View User')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
                    User Details
                </h1>
                <p class="mt-2 text-gray-600">Review all available user data.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Users
                </a>
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:via-purple-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>
                    Edit User
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user mr-3"></i>
                            Profile
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</div>
                                <div class="text-sm text-gray-600 truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Not Verified
                                </span>
                            @endif

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 border border-indigo-200">
                                <i class="fas fa-shield-alt mr-1"></i>
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>

                            @if($user->onboarding_completed_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-cyan-100 to-blue-100 text-cyan-800 border border-cyan-200">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    Onboarding Complete
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-gray-100 to-slate-100 text-gray-700 border border-gray-200">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Onboarding Pending
                                </span>
                            @endif
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-3">
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">User ID</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->id }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ optional($user->created_at)->format('M d, Y H:i') ?? '—' }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Updated</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ optional($user->updated_at)->format('M d, Y H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-id-card mr-3"></i>
                            Account Details
                        </h2>
                    </div>
                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900 break-words">{{ $user->name }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900 break-words">{{ $user->email }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->role ?? 'user' }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Verified At</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ optional($user->email_verified_at)->format('M d, Y H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-clipboard-list mr-3"></i>
                            Onboarding
                        </h2>
                    </div>
                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed At</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ optional($user->onboarding_completed_at)->format('M d, Y H:i') ?? '—' }}</div>
                            </div>
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->onboarding_completed_at ? 'Completed' : 'Not completed' }}</div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-database mr-2 text-indigo-500"></i>
                                Stored Onboarding Data
                            </div>
                            @php
                                $formatOnboardingLabel = fn ($text) => ucwords(str_replace(['_', '-'], ' ', (string) $text));

                                $formatOnboardingScalar = function ($key, $value) use ($formatOnboardingLabel) {
                                    if ($value === null) {
                                        return '—';
                                    }

                                    $keyString = strtolower((string) $key);
                                    $isNotificationKey = str_contains($keyString, 'notification') || str_contains($keyString, 'notifications');
                                    if ($isNotificationKey && (is_bool($value) || $value === 0 || $value === 1 || $value === '0' || $value === '1')) {
                                        return ((int) $value) === 1 ? 'On' : 'Off';
                                    }

                                    if (is_bool($value)) {
                                        return $value ? 'True' : 'False';
                                    }

                                    if (is_int($value) || is_float($value)) {
                                        return (string) $value;
                                    }

                                    $stringValue = trim((string) $value);
                                    if ($stringValue === '') {
                                        return '—';
                                    }

                                    if (filter_var($stringValue, FILTER_VALIDATE_EMAIL) || filter_var($stringValue, FILTER_VALIDATE_URL)) {
                                        return $stringValue;
                                    }

                                    if (preg_match('/^\d+(\.\d+)?$/', $stringValue)) {
                                        return $stringValue;
                                    }

                                    $looksLikeLabel = str_contains($stringValue, '_') || str_contains($stringValue, '-');
                                    if ($looksLikeLabel) {
                                        return $formatOnboardingLabel(strtolower($stringValue));
                                    }

                                    $hasLetters = preg_match('/[A-Za-z]/', $stringValue) === 1;
                                    $isAllLower = $hasLetters && mb_strtolower($stringValue) === $stringValue;
                                    if ($isAllLower) {
                                        return $formatOnboardingLabel($stringValue);
                                    }

                                    return $stringValue;
                                };
                            @endphp
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                @if($user->onboarding_data)
                                    @foreach($user->onboarding_data as $key => $value)
                                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                {{ $formatOnboardingLabel($key) }}
                                            </div>
                                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                                @if(is_array($value))
                                                    @php $isList = array_values($value) === $value; @endphp
                                                    @if($isList)
                                                        {{ implode(', ', array_map(fn ($v) => is_scalar($v) || $v === null ? $formatOnboardingScalar($key, $v) : '(complex value)', $value)) }}
                                                    @else
                                                        <div class="space-y-1">
                                                            @foreach($value as $subKey => $subValue)
                                                                <div class="flex gap-2">
                                                                    <div class="text-gray-500">{{ $formatOnboardingLabel($subKey) }}:</div>
                                                                    <div class="text-gray-900">
                                                                        {{ is_scalar($subValue) || $subValue === null ? $formatOnboardingScalar($subKey, $subValue) : '(complex value)' }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @else
                                                    {{ $formatOnboardingScalar($key, $value) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-1 sm:col-span-2 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                        <div class="text-sm font-semibold text-gray-700">No onboarding data available</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
