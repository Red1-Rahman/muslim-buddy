<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithCollection;
use App\Models\HadithChapter;
use App\Models\UserHadithProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class HadithController extends Controller
{
    public function index(Request $request): View
    {
        $collections = HadithCollection::with(['chapters' => function ($query) {
            $query->orderBy('chapter_number');
        }])->get();

        $selectedCollection = null;
        $chapters = collect();
        $hadiths = collect();

        if ($request->has('collection')) {
            $selectedCollection = HadithCollection::findOrFail($request->collection);
            $chapters = $selectedCollection->chapters()
                ->withCount('hadiths')
                ->orderBy('chapter_number')
                ->get();

            if ($request->has('chapter')) {
                $chapter = HadithChapter::findOrFail($request->chapter);
                $query = $chapter->hadiths()->with(['collection', 'chapter', 'section']);

                // Apply grade filter
                if ($request->filled('grade')) {
                    $query->where('english_grade', 'like', '%' . $request->grade . '%');
                }

                // Apply search filter
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('english_hadith', 'like', '%' . $search . '%')
                          ->orWhere('arabic_hadith', 'like', '%' . $search . '%')
                          ->orWhere('english_matn', 'like', '%' . $search . '%')
                          ->orWhere('arabic_matn', 'like', '%' . $search . '%');
                    });
                }

                $hadiths = $query->orderBy('hadith_number')->paginate(10);

                // Load user progress for authenticated users
                if (auth()->check()) {
                    $hadithIds = $hadiths->pluck('id');
                    $userProgress = UserHadithProgress::where('user_id', auth()->id())
                        ->whereIn('hadith_id', $hadithIds)
                        ->pluck('status', 'hadith_id');

                    foreach ($hadiths as $hadith) {
                        $hadith->user_progress_status = $userProgress->get($hadith->id, 'not_read');
                    }
                }
            }
        }

        return view('hadith.index', compact(
            'collections',
            'selectedCollection', 
            'chapters',
            'hadiths'
        ));
    }

    public function show(Hadith $hadith): View
    {
        $hadith->load(['collection', 'chapter', 'section']);
        
        $userProgress = null;
        if (auth()->check()) {
            $userProgress = UserHadithProgress::where('user_id', auth()->id())
                ->where('hadith_id', $hadith->id)
                ->first();
        }

        // Get previous and next hadith in the same chapter
        $previousHadith = Hadith::where('chapter_id', $hadith->chapter_id)
            ->where('hadith_number', '<', $hadith->hadith_number)
            ->orderBy('hadith_number', 'desc')
            ->first();

        $nextHadith = Hadith::where('chapter_id', $hadith->chapter_id)
            ->where('hadith_number', '>', $hadith->hadith_number)
            ->orderBy('hadith_number')
            ->first();

        return view('hadith.show', compact(
            'hadith',
            'userProgress',
            'previousHadith',
            'nextHadith'
        ));
    }

    public function updateProgress(Request $request, Hadith $hadith): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:not_read,read,memorized',
            'notes' => 'nullable|string|max:1000'
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $progress = UserHadithProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'hadith_id' => $hadith->id
            ],
            [
                'status' => $request->status,
                'notes' => $request->notes,
                'read_at' => in_array($request->status, ['read', 'memorized']) ? now() : null,
                'memorized_at' => $request->status === 'memorized' ? now() : null
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'status' => $progress->status
        ]);
    }

    public function dashboard(): View
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

        // Get reading statistics
        $totalHadiths = Hadith::count();
        $readHadiths = UserHadithProgress::where('user_id', $userId)
            ->whereIn('status', ['read', 'memorized'])
            ->count();
        $memorizedHadiths = UserHadithProgress::where('user_id', $userId)
            ->where('status', 'memorized')
            ->count();

        // Get recent progress
        $recentProgress = UserHadithProgress::with(['hadith.collection', 'hadith.chapter'])
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        // Get collection statistics
        $collectionStats = HadithCollection::withCount([
            'hadiths',
            'hadiths as read_hadiths_count' => function ($query) use ($userId) {
                $query->join('user_hadith_progress', 'hadiths.id', '=', 'user_hadith_progress.hadith_id')
                    ->where('user_hadith_progress.user_id', $userId)
                    ->whereIn('user_hadith_progress.status', ['read', 'memorized']);
            }
        ])->get();

        return view('hadith.dashboard', compact(
            'totalHadiths',
            'readHadiths',
            'memorizedHadiths',
            'recentProgress',
            'collectionStats'
        ));
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $query = $request->query;
        
        $hadiths = Hadith::with(['collection', 'chapter'])
            ->where(function ($q) use ($query) {
                $q->where('english_hadith', 'like', '%' . $query . '%')
                  ->orWhere('english_matn', 'like', '%' . $query . '%')
                  ->orWhere('arabic_hadith', 'like', '%' . $query . '%')
                  ->orWhere('arabic_matn', 'like', '%' . $query . '%');
            })
            ->limit(20)
            ->get();

        return response()->json([
            'hadiths' => $hadiths->map(function ($hadith) {
                return [
                    'id' => $hadith->id,
                    'number' => $hadith->formatted_number,
                    'collection' => $hadith->collection->name,
                    'chapter' => $hadith->chapter->chapter_name_english,
                    'preview' => Str::limit($hadith->english_matn, 150),
                    'grade' => $hadith->english_grade,
                    'url' => route('hadith.show', $hadith)
                ];
            })
        ]);
    }
}