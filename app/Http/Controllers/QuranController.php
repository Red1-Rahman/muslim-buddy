<?php

namespace App\Http\Controllers;

use App\Models\Surah;
use App\Models\Verse;
use App\Models\UserVerseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuranController extends Controller
{
    /**
     * Display list of surahs
     */
    public function index()
    {
        $user = Auth::user();
        $surahs = Surah::with(['verses' => function ($query) use ($user) {
            $query->select('id', 'surah_number');
        }])->get();

        // Calculate progress for each surah
        foreach ($surahs as $surah) {
            $totalVerses = $surah->total_verses;
            $readCount = UserVerseProgress::where('user_id', $user->id)
                ->whereHas('verse', function ($q) use ($surah) {
                    $q->where('surah_number', $surah->surah_number);
                })
                ->where('is_read', true)
                ->count();

            $surah->progress = $totalVerses > 0 ? round(($readCount / $totalVerses) * 100, 1) : 0;
            $surah->read_count = $readCount;
        }

        return view('quran.index', compact('surahs', 'user'));
    }

    /**
     * Display a specific surah with verses
     */
    public function show($surahNumber)
    {
        $user = Auth::user();
        $surah = Surah::where('surah_number', $surahNumber)->firstOrFail();
        $verses = Verse::where('surah_number', $surahNumber)
            ->orderBy('verse_number')
            ->get();

        // Load user progress for these verses
        $progressMap = UserVerseProgress::where('user_id', $user->id)
            ->whereIn('verse_id', $verses->pluck('id'))
            ->get()
            ->keyBy('verse_id');

        foreach ($verses as $verse) {
            $verse->user_progress = $progressMap->get($verse->id);
        }

        return view('quran.show', compact('surah', 'verses', 'user'));
    }

    /**
     * Display a specific verse with details
     */
    public function verse($surahNumber, $verseNumber)
    {
        $user = Auth::user();
        $verse = Verse::where('surah_number', $surahNumber)
            ->where('verse_number', $verseNumber)
            ->with('surah')
            ->firstOrFail();

        $progress = UserVerseProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'verse_id' => $verse->id,
            ]
        );

        return view('quran.verse', compact('verse', 'progress', 'user'));
    }

    /**
     * Mark verse as read
     */
    public function markAsRead(Request $request, $verseId)
    {
        $user = Auth::user();
        $verse = Verse::findOrFail($verseId);

        $progress = UserVerseProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'verse_id' => $verse->id,
            ]
        );

        if (!$progress->is_read) {
            $progress->markAsRead();
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'points' => $user->fresh()->total_points,
        ]);
    }

    /**
     * Mark verse as understood
     */
    public function markAsUnderstood(Request $request, $verseId)
    {
        $user = Auth::user();
        $verse = Verse::findOrFail($verseId);

        $progress = UserVerseProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'verse_id' => $verse->id,
            ]
        );

        if (!$progress->is_understood) {
            $progress->markAsUnderstood();
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'points' => $user->fresh()->total_points,
        ]);
    }

    /**
     * Mark verse as memorized
     */
    public function markAsMemorized(Request $request, $verseId)
    {
        $user = Auth::user();
        $verse = Verse::findOrFail($verseId);

        $progress = UserVerseProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'verse_id' => $verse->id,
            ]
        );

        if (!$progress->is_memorized) {
            $progress->markAsMemorized();
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'points' => $user->fresh()->total_points,
        ]);
    }

    /**
     * Review memorized verse
     */
    public function review(Request $request, $verseId)
    {
        $user = Auth::user();
        $progress = UserVerseProgress::where('user_id', $user->id)
            ->where('verse_id', $verseId)
            ->where('is_memorized', true)
            ->firstOrFail();

        $difficulty = $request->input('difficulty', 'easy');
        $progress->scheduleNextReview($difficulty);

        return response()->json([
            'success' => true,
            'next_review' => $progress->next_review_at->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Get verses due for review
     */
    public function dueReviews()
    {
        $user = Auth::user();
        
        try {
            $dueVerses = UserVerseProgress::where('user_id', $user->id)
                ->where('is_memorized', true)
                ->whereNotNull('next_review_at')
                ->where('next_review_at', '<=', now())
                ->with(['verse.surah'])
                ->orderBy('next_review_at')
                ->get();
        } catch (\Exception $e) {
            // If there's an error, return empty collection and log it
            \Log::error('Error in dueReviews: ' . $e->getMessage());
            $dueVerses = collect([]);
        }

        return view('quran.reviews', compact('dueVerses', 'user'));
    }

    /**
     * Search verses
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        if (empty($query)) {
            return view('quran.search', compact('query', 'user'))->with('verses', collect([]));
        }

        $verses = Verse::where(function ($q) use ($query) {
            $q->where('arabic_text', 'like', "%{$query}%")
                ->orWhere('translation_english', 'like', "%{$query}%")
                ->orWhere('translation_bengali', 'like', "%{$query}%")
                ->orWhere('transliteration', 'like', "%{$query}%");
        })
            ->with('surah')
            ->limit(50)
            ->get();

        // Load user progress for these verses
        $progressMap = UserVerseProgress::where('user_id', $user->id)
            ->whereIn('verse_id', $verses->pluck('id'))
            ->get()
            ->keyBy('verse_id');

        foreach ($verses as $verse) {
            $verse->user_progress = $progressMap->get($verse->id);
        }

        return view('quran.search', compact('verses', 'query', 'user'));
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_read' => UserVerseProgress::where('user_id', $user->id)
                ->where('is_read', true)
                ->count(),
            'total_understood' => UserVerseProgress::where('user_id', $user->id)
                ->where('is_understood', true)
                ->count(),
            'total_memorized' => UserVerseProgress::where('user_id', $user->id)
                ->where('is_memorized', true)
                ->count(),
            'total_verses' => 6236,
            'due_reviews' => UserVerseProgress::where('user_id', $user->id)
                ->where('is_memorized', true)
                ->where('next_review_at', '<=', now())
                ->count(),
        ];

        $stats['read_percentage'] = round(($stats['total_read'] / $stats['total_verses']) * 100, 2);
        $stats['memorized_percentage'] = round(($stats['total_memorized'] / $stats['total_verses']) * 100, 2);

        // Get progress by juz
        $juzProgress = DB::table('verses')
            ->leftJoin('user_verse_progress', function ($join) use ($user) {
                $join->on('verses.id', '=', 'user_verse_progress.verse_id')
                    ->where('user_verse_progress.user_id', '=', $user->id)
                    ->where('user_verse_progress.is_read', '=', true);
            })
            ->select('verses.juz', DB::raw('COUNT(*) as total'), DB::raw('COUNT(user_verse_progress.id) as `read`'))
            ->whereNotNull('verses.juz')
            ->groupBy('verses.juz')
            ->orderBy('verses.juz')
            ->get();

        return view('quran.statistics', compact('stats', 'juzProgress', 'user'));
    }
}
