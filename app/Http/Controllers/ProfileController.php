<?php

namespace App\Http\Controllers;

use App\Hashes\AdminIdHash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = __('app.profile');
        $user_id = AdminIdHash::private($id);
        return view('dashboard', compact('page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $page_title = __('app.profile');
        $user_id = AdminIdHash::private($id);
        return view('dashboard', compact('page_title'));
    }
}
