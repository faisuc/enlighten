<?php

namespace Styde\Enlighten\Http\Controllers;

use Illuminate\Http\Request;
use Styde\Enlighten\Models\Run;
use Styde\Enlighten\Models\Module;

class RunController extends Controller
{
    public function index()
    {
        if (Run::count() === 0) {
            return redirect(route('enlighten.intro'));
        }
        return view('enlighten::dashboard.index');
    }

    public function show(Request $request, ?Run $run = null)
    {
        $tabs = $this->getTabs();

        if ($request->route('area') === null) {
            $area = $tabs->first();
        } else {
            $area = $tabs->firstWhere('slug', $request->route('area'));
        }

        if ($area === null) {
            return redirect(route('enlighten.run.index'));
        }

        $groups = $run->groups()->with('stats')->filterByArea($area)->get();

        $modules = Module::all();

        $modules->addGroups($groups);

        return view('enlighten::area.show', [
            'modules' => $modules->whereHasGroups(),
            'title' => 'Dashboard',
            'area' => $area
        ]);
    }
}
