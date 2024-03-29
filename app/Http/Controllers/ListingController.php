<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $listings = Listing::query()
            ->where(
                // Only show listings that are available or recently unavailable
                fn(Builder $query) => $query
                    ->where('available', true)
                    ->orWhereDate('updated_at', '>=', now()->subWeek())
            )
            ->whereHas('user',
                // If a user is shadow banned, hide their listings from everyone but themselves
                fn(Builder $query) => $query
                    ->where('marketplace_ban', false)
                    ->orWhere('user_id', $request->user()->id)
            )
            // Put the unavailable listings at the bottom
            ->orderBy('available', 'desc')
            // Put the most recently created listings at the top
            ->latest()
            // Put the current user's listings at the very top
            ->orderByRaw('user_id = ?', $request->user()->id)
            ->with('user')
            ->get();

        return ListingResource::collection($listings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0', 'max:5000'],
            'image' => ['required', 'image', 'max:20000'], // 20 MB
            'miles_from_campus' => ['required', 'integer', 'min:0', 'max:9']
        ]);

        $name = $data['image']->hashName();
        $path = $data['image']->storePubliclyAs('listings', $name);
        $data['image_url'] = Storage::url($path);
        unset($data['image']);

        $model = $request->user()->listings()->create($data);

        dispatch(function() use ($model, $data) {
            webhook_alert('New Marketplace Listing', [
                'Title' => $data['title'],
                'User' => $model->name,
                'Description' => $data['description'],
                'Price' => '$' . number_format($data['price'])
            ], $data['image_url']);
        })->afterResponse();

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
     * Display a basic page with the resource.
     */
    public function page(Listing $listing)
    {
        return view('listing', ['listing' => $listing]);
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
            'price' => ['integer', 'min:0', 'max:1000'],
            'available' => 'boolean',
            'miles_from_campus' => ['integer', 'min:0', 'max:9']
        ]);

        $listing->update($data);
        $listing = $listing->load('user');
        return new ListingResource($listing);
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
