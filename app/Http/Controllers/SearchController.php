<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\PerceptualHash;
use Jenssegers\ImageHash\Hash;
use GuzzleHttp\Client; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Necessary for logging errors

class SearchController extends Controller
{
    // Define the Hamming distance similarity threshold as a class constant.
    protected const HAMMING_THRESHOLD = 20;

    /**
     * Main search route handles text, image, and URL searches.
     */
    public function index(Request $request)
    {
        $mode = $request->input('mode', 'text'); // new hidden input from form

        if ($mode === 'image') {
            return $this->searchByImage($request);
        }

        if ($mode === 'url') {
            return $this->searchByImageUrl($request);
        }

        // ---------------------------
        // Text Search
        // ---------------------------
        $query = $request->input('query');
        $selectedCategories = $request->input('search_categories', []);
        $results = collect();

        if ($query) {
            if (in_array('products', $selectedCategories) || empty($selectedCategories)) {
                $products = Product::query()
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->with(['subCategory.category', 'supplier'])
                    ->get();

                $results = $results->concat(
                    // Ensure text search also uses the ['type' => 'product', 'data' => $item] structure
                    $products->map(fn($item) => ['type' => 'product', 'data' => $item])
                );
            }
        }

        return view('search.results', [
            'results' => $results,
            'query' => $query,
            'selectedCategories' => $selectedCategories
        ]);
    }

    /**
     * Efficiently calculates the Hamming distance between two binary hash strings (bytes).
     * This implementation uses bitwise operations (popcount) for efficiency.
     */
    private function hammingDistance(string $bin1, string $bin2): int {
        $distance = 0;
        $len = strlen($bin1);
        
        if ($len !== strlen($bin2)) {
            return PHP_INT_MAX; // incompatible lengths
        }

        for ($i = 0; $i < $len; $i++) {
            $byteXor = ord($bin1[$i]) ^ ord($bin2[$i]);
            
            while ($byteXor > 0) {
                $distance++;
                $byteXor &= ($byteXor - 1);
            }
        }
        
        return $distance;
    }


    public function searchByImage(Request $request)
    {
        $imageFile = $request->file('image_file');
        $imageUrl  = $request->input('image_url');

        if (is_array($imageFile)) {
            $imageFile = $imageFile[0] ?? null;
        }
        if (!$imageFile instanceof \Illuminate\Http\UploadedFile) {
            $imageFile = null;
        }

        $implementation = new PerceptualHash();
        $hasher = new ImageHash($implementation);

        $hash = null;
        $error = null;
        $matchedProducts = collect();
        $tempFilesToClean = [];
        $disk = 'public'; 
        $directory = 'search_images';

        // --- 1. Missing Input Check ---
        if (!$imageFile && !$imageUrl) {
            $error = 'الرجاء رفع صورة أو إدخال رابط صورة للبحث.';
        }

        // --- 2. Process Image (Unchanged from previous revision) ---
        if (!$error) {
            try {
                // A. Uploaded file
                if ($imageFile) {
                    $storedPath = $imageFile->store($directory, $disk);
                    $fullPath = Storage::disk($disk)->path($storedPath);
                    $hash = $hasher->hash($fullPath);
                    $tempFilesToClean[] = ['disk' => $disk, 'path' => $storedPath];

                // B. Remote URL
                } elseif ($imageUrl) {
                    $client = new Client(['timeout' => 10]); 
                    $response = $client->get($imageUrl);

                    if ($response->getStatusCode() === 200) {
                        $contentType = $response->getHeaderLine('Content-Type');
                        
                        if (!Str::startsWith($contentType, 'image/')) {
                            throw new \Exception("URL returned non-image content: {$contentType}");
                        }

                        $imageContent = $response->getBody()->getContents();
                        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                        
                        $tempFileName = 'remote_img_' . time() . '_' . Str::random(10) . '.' . ($extension ?: 'jpg'); 
                        $storedPath = $directory . '/' . $tempFileName;
                        
                        Storage::disk($disk)->put($storedPath, $imageContent);
                        $fullPath = Storage::disk($disk)->path($storedPath);
                        $tempFilesToClean[] = ['disk' => $disk, 'path' => $storedPath];
                        
                        $hash = $hasher->hash($fullPath);

                    } else {
                        throw new \Exception("Failed to retrieve image from URL (Status: {$response->getStatusCode()})");
                    }
                }

                // --- 3. Product Matching (Unchanged) ---
                if ($hash) {
                    $hashBinary = hex2bin((string) $hash); 
                    if ($hashBinary === false) {
                        throw new \Exception("Failed to convert uploaded image hash to binary");
                    }

                    Product::chunk(500, function ($products) use (&$matchedProducts, $hashBinary) {
                        foreach ($products as $product) {
                            if (empty($product->phashes)) continue;

                            $storedPhashes = json_decode($product->phashes, true);
                            if (!is_array($storedPhashes)) continue;

                            foreach ($storedPhashes as $storedHashHex) {
                                $storedBinary = hex2bin($storedHashHex);
                                if ($storedBinary === false) continue;

                                $distance = $this->hammingDistance($hashBinary, $storedBinary);

                                if ($distance <= self::HAMMING_THRESHOLD) { 
                                    $matchedProducts->push($product);
                                    break;
                                }
                            }
                        }
                    });
                    
                    $matchedProducts = $matchedProducts->unique('id');
                }

            } catch (\Exception $e) {
                Log::error('Image Search/Hashing Failed: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $error = 'فشل في معالجة الصورة. تأكد من صحة الملف و الرابط.';
            }
        }

        // --- 4. Cleanup Temporary Files (Unchanged) ---
        foreach ($tempFilesToClean as $file) {
             Storage::disk($file['disk'])->delete($file['path']);
        }

        // --- 5. Return Results (FIXED) ---
        $formattedResults = $matchedProducts->map(fn($product) => [
            'type' => 'product', // CRITICAL: Add the 'type' for the view logic
            'data' => $product,
        ]);

        return view('search.results', [
            'results' => $formattedResults,
            'searchError' => $error,
            // Pass a flag to indicate image search for the view title
            'isImageSearch' => true
        ]);
    }

    /**
     * Dedicated method for searching by a direct image URL (simple lookup)
     */
    public function searchByImageUrl(Request $request)
    {
        $url = $request->input('image_url');

        if ($url) {
            $results = Product::where('image_url', $url)->get();

            return response()->json([
                'success' => true,
                'image_url' => $url,
                'results' => $results
            ]);
        }

        return response()->json(['success' => false], 400);
    }
}
