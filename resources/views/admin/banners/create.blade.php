@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Add New Banner</h2>
        <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Banner Title</label>
                <input type="text" name="title" class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Banner Image (Desktop Size)</label>
                <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Link URL (Optional)</label>
                <input type="url" name="link_url" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                <label for="is_active" class="text-sm text-slate-700">Set as Active</label>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-blue-500">Save Banner</button>
            </div>
        </div>
    </form>
</div>
@endsection
