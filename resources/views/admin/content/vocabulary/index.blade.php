@extends('layouts.admin')

@section('title', 'Vocabulary')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Vocabulary
                    </h1>
                    <p class="mt-2 text-gray-600">Manage categories and bulk upload words.</p>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('admin.vocabulary.categories.index') }}"
                   class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                                <i class="fas fa-layer-group text-indigo-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-lg font-bold text-gray-900 group-hover:text-indigo-700">Categories</div>
                                <div class="mt-1 text-sm text-gray-600">Create, translate, and enable/disable categories.</div>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.vocabulary.upload-words.index') }}"
                   class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-file-upload text-emerald-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-lg font-bold text-gray-900 group-hover:text-emerald-700">Upload Words</div>
                                <div class="mt-1 text-sm text-gray-600">Bulk import words per category using CSV/XLSX.</div>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-emerald-600 transition-colors"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.vocabulary.words.index') }}"
                   class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-spell-check text-blue-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-lg font-bold text-gray-900 group-hover:text-blue-700">Manage Words</div>
                                <div class="mt-1 text-sm text-gray-600">Edit or delete words within any category.</div>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
