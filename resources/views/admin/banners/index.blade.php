@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">Manage Banners</h2>
    <a href="{{ route('admin.banners.create') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Add Banner</a>
</div>

<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <table class="w-full text-left text-sm text-slate-600">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
            <tr>
                <th class="px-6 py-4 font-medium">Image & Title</th>
                <th class="px-6 py-4 font-medium">Status</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($banners as $banner)
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($banner->image_path)
                            <img src="{{ $banner->image_path }}" class="h-10 w-16 rounded-md object-cover bg-slate-100">
                        @else
                            <div class="h-10 w-16 rounded-md bg-slate-100"></div>
                        @endif
                        <div>
                            <div class="font-medium text-slate-800">{{ $banner->title ?? 'No Title' }}</div>
                            <div class="text-xs text-slate-400">{{ $banner->subtitle }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    @if($banner->is_active)
                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline-block ml-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:underline" onclick="return confirm('Delete this banner?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($banners->isEmpty())
            <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400">No banners found.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $banners->links() }}
</div>
@endsection
