<?php

namespace App\Http\Controllers;

use App\Models\Term;

class CourseCatalogController extends Controller
{
    public function index(Term $term)
    {
        return $term->courses;

        // return $term->courses()->select([
        //     'id', 'name', 'attendance_required',
        //     'crn', 'number', 'term_id', 'subject_id',
        //     'credits', 'location', 'max_enrollment', 'enrollment',
        //     'professor', 'prerequisites',
        //     'days', 'start_time', 'end_time'
        // ])->with([
        //     'distRequirements:code',
        //     'crossListings:id',
        //     'labs:id',
        //     'labFor:id',
        // ])->get();
    }
}
