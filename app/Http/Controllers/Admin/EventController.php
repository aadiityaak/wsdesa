<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Agenda/Index', [
            'events' => Event::with('category')
                ->orderBy('tanggal_mulai')
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Agenda/Form', [
            'categories' => EventCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_category_id' => ['nullable', 'exists:event_categories,id'],
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:upcoming,ongoing,done'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $baseSlug = Str::slug($validated['judul']);
        $slug = $baseSlug;
        $counter = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function edit(Event $event): Response
    {
        return Inertia::render('Admin/Agenda/Form', [
            'event' => $event->load('category'),
            'categories' => EventCategory::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'event_category_id' => ['nullable', 'exists:event_categories,id'],
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:upcoming,ongoing,done'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validated['judul'] !== $event->judul) {
            $baseSlug = Str::slug($validated['judul']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return back()->with('success', 'Agenda berhasil dihapus.');
    }
}
