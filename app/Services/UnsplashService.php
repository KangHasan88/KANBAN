<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnsplashService
{
    protected $accessKey;
    protected $baseUrl = 'https://api.unsplash.com/';
    
    public function __construct()
    {
        $this->accessKey = config('unsplash.access_key');
    }
    
    // Search photos
    public function searchPhotos($query, $page = 1, $perPage = 20)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->accessKey,
            ])->get($this->baseUrl . 'search/photos', [
                'query' => $query,
                'page' => $page,
                'per_page' => $perPage,
                'orientation' => 'landscape'
            ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'results' => $response->json()['results'],
                    'total' => $response->json()['total'],
                    'total_pages' => $response->json()['total_pages']
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Unsplash API error: ' . $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Unsplash search error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get random photos
    public function getRandomPhotos($count = 10, $query = null)
    {
        try {
            $params = [
                'count' => min($count, 30),
                'orientation' => 'landscape'
            ];
            
            if ($query) {
                $params['query'] = $query;
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->accessKey,
            ])->get($this->baseUrl . 'photos/random', $params);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'photos' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Unsplash API error: ' . $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Unsplash random error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Download photo from URL
    public function downloadPhoto($imageUrl, $taskId, $photoId, $authorName, $authorLink)
    {
        try {
            $contents = file_get_contents($imageUrl);
            if ($contents === false) {
                return ['success' => false, 'message' => 'Failed to download image'];
            }
            
            $extension = 'jpg';
            $filename = time() . '_' . uniqid() . '_unsplash_' . $photoId . '.' . $extension;
            $path = 'attachments/' . $taskId . '/' . $filename;
            
            $fullPath = storage_path('app/public/' . $path);
            $directory = dirname($fullPath);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            file_put_contents($fullPath, $contents);
            
            return [
                'success' => true,
                'file_path' => '/storage/' . $path,
                'file_name' => $filename,
                'photo_id' => $photoId,
                'author_name' => $authorName,
                'author_link' => $authorLink
            ];
            
        } catch (\Exception $e) {
            Log::error('Download photo error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}