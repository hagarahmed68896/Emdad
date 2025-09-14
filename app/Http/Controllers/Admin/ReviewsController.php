<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewsController extends Controller
{
public function index(Request $request)
{
    // General statistics for the reviews
    $totalReviews = Review::count();
    $positiveRatedReviews = Review::where('rating', '>=', 4)->count();
    $negativeRatedReviews = Review::where('rating', '<', 4)->count();

    // Calculate percentages
    $positivePercent = $totalReviews > 0 ? round(($positiveRatedReviews / $totalReviews) * 100, 2) : 0;
    $negativePercent = $totalReviews > 0 ? round(($negativeRatedReviews / $totalReviews) * 100, 2) : 0;

    // Build the query
    $query = Review::with(['user', 'product']);

    // Filter by rating
    if ($request->filled('ratingFilter')) {
        $rating = $request->ratingFilter;

        if (is_numeric($rating)) {
            // If numeric, filter by exact star value
            $query->where('rating', $rating);
        } else {
            // If string (positive/negative/complain), filter accordingly
            switch ($rating) {
                case 'positive':
                    $query->where('rating', '>=', 4);
                    break;
                case 'negative':
                    $query->where('rating', '<', 4);
                    break;
                case 'complain':
                    $query->where('rating', '<', 2);
                    break;
            }
        }
    }

    // Filter by search in comment or user name
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('comment', 'like', '%' . $request->search . '%')
              ->orWhereHas('user', function ($q2) use ($request) {
                  $q2->where('name', 'like', '%' . $request->search . '%');
              });
        });
    }

    // Sorting
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'highest_rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest_rating':
                $query->orderBy('rating', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    // Execute the query with pagination
    $reviews = $query->paginate(10)->withQueryString();

    return view('admin.reviews.index', [
        'reviews' => $reviews,
        'totalReviews' => $totalReviews,
        'positiveRatedReviews' => $positiveRatedReviews,
        'negativeRatedReviews' => $negativeRatedReviews,
        'negativePercent' => $negativePercent,
        'positivePercent' => $positivePercent,
        'ratingFilter' => $request->input('ratingFilter'),
        'sortFilter' => $request->input('sort'),
        'search' => $request->input('search'),
    ]);
}





public function exportCsv(Request $request)
{
    $query = Review::query()->with(['user', 'product']);

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    $reviewsToExport = $query->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=reviews_export_" . now()->format('Ymd_His') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($reviewsToExport) {
        $file = fopen('php://output', 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, [
            'ID',
            'اسم العميل',
            'اسم المنتج',
            'التعليق',
            'التقييم',
            'الحالة',
            'تاريخ المراجعة',
        ]);

        foreach ($reviewsToExport as $review) {
            $status = 'غير محدد';
            if ($review->rating >= 4) {
                $status = 'إيجابي';
            } elseif ($review->rating >= 2) {
                $status = 'سلبي';
            } else {
                $status = 'شكوي';
            }

            fputcsv($file, [
                $review->id,
                $review->user->name ?? '-',
                $review->product->name ?? '-',
                $review->comment ?? '-',
                $review->rating . '/5',
                $status,
                $review->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);
    };

    return new StreamedResponse($callback, 200, $headers);
}


public function bulkDelete(Request $request)
{
    $ids = explode(',', $request->input('selected_ids'));

    Review::whereIn('id', $ids)->delete();

    return redirect()->route('admin.reviews.index')->with('success', 'تم حذف التقييمات المحددة بنجاح.');
}


}
