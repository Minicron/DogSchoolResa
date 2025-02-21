<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;

class ClubController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Club::all();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        

        if ($request->isMethod('post')) {

            $club = new Club();
            $club->name = $request->name;
            $club->city = $request->city;
            $club->admin_id = auth()->user()->id;
            $club->affiliates = null;
            $club->logo = null;
            $club->description = $request->description;
            $club->website = null;
            $club->facebook = null;
            $club->save();


            return view('club.create');
        }

        return view('club.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Club::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function show(Club $club)
    {
        return $club;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Club $club)
    {
        $club->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function destroy(Club $club) 
    {
        $club->delete();
    }
}
