<?php

namespace App\Jobs;

use App\Models\Professor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RefreshRateMyProfessorsData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $schoolId = base64_encode('School-550');

        $query = "
        {
            search: newSearch {
                teachers(query: {
                    schoolID: \"$schoolId\"
                }, first: 1000) {
                    edges {
                        node {
                            avgDifficultyRounded
                            avgRatingRounded
                            firstName
                            lastName
                            numRatings
                            wouldTakeAgainPercentRounded
                            department
                            mostUsefulRating { comment }
                            ratingsDistribution { r1 r2 r3 r4 r5 }
                        }
                    }
                }
            }
        }";

        $professors = Http::withToken(base64_encode('test:test'), 'Basic')
            ->post('https://www.ratemyprofessors.com/graphql', ['query' => $query])
            ->json('data.search.teachers.edges');

        foreach($professors as $professor) {
            $professor = $professor['node'];

            $review = $professor['mostUsefulRating']['comment'] ?? null;
            if(strlen($review) < 10) $review = null;

            Professor::where(
                'name', $professor['firstName'] . ' ' . $professor['lastName']
            )->update([
                'difficulty' => $professor['avgDifficultyRounded'],
                'rating' => $professor['avgRatingRounded'],
                'rating_count' => $professor['numRatings'],
                'take_again_percent' => $professor['wouldTakeAgainPercentRounded'],
                'featured_review' => $review,
                'ratings_distribution' => [
                    '1' => $professor['ratingsDistribution']['r1'],
                    '2' => $professor['ratingsDistribution']['r2'],
                    '3' => $professor['ratingsDistribution']['r3'],
                    '4' => $professor['ratingsDistribution']['r4'],
                    '5' => $professor['ratingsDistribution']['r5']
                ]
            ]);
        }
    }
}
