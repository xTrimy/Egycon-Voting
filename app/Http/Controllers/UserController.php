<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // view users 
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // create user
        $events = Event::all();
        $roles = Role::all();
        return view('users.create', compact('events', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'events' => 'required|array',
            'events.*' => 'required|exists:events,id',
            'email' => 'required|email|unique:users,email',
            'weight' => 'required|integer',
            'role' => 'required|string',
        ]);
        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'vote_weight' => (int)$request->weight,
        ]);
        $user->assignRole($request->role);
        $user->events()->attach($request->events);
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function viewSettings()
    {
        /** @var User $user */
        $user = auth()->user();
        $telegramNotificationsEnabled = $user->getTelegramChatId() ? true : false;
        $telegramCode = $user->getTelegramCode();
        $telegramCodeQR = $user->getTelegramCodeQR();
        return view('settings.all', compact('user', 'telegramNotificationsEnabled', 'telegramCode', 'telegramCodeQR'));
    }

    public function disableTelegramNotifications()
    {
        /** @var User $user */
        $user = auth()->user();
        $telegramChat = $user->getTelegramChatIdObject();
        if ($telegramChat) {
            $telegramChat->delete();
        }
        return redirect()->back()->with('success', 'Telegram notifications disabled');
    }
}
