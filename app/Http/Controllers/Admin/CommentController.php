<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Komentar/Index', [
            'comments' => Comment::with('post:id,judul')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        return back()->with('success', 'Komentar berhasil disetujui.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
