<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Listing::query()
            ->where('available', true)
            ->orWhereDate('updated_at', '>=', now()->subWeek())
            ->withCount('savedBy')
            ->latest()
            ->orderBy('saved_by_count', 'desc')
            ->orderBy('available', 'desc')
            ->get()
            ->map->only(['id', 'title', 'image_url', 'price', 'available']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'max_digits:4'],
            'image' => ['required', 'image', 'max:10000']
        ]);

        $name = $data['image']->hashName();
        $path = $data['image']->storePubliclyAs('listings', $name);
        $data['image_url'] = Storage::url($path);
        unset($data['image']);

        $model = $request->user()->listings()->create($data);
        $model = $model->load('user');
        return new ListingResource($model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->load('user');
        return new ListingResource($listing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 401);

        $data = $request->validate([
            'title' => 'string',
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['integer', 'max_digits:4'],
            'available' => 'boolean'
        ]);

        $listing->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 401);
        $listing->delete();
    }
}
