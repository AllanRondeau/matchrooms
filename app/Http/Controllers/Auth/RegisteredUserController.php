<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_hotel' => 'boolean',
        ];
        if ($request->input('is_hotel')) {
            $rules['company_name'] = 'required|string|max:255';
        }
        $validated = $request->validate($rules);

        $user = User::create([
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
        ]);

        if ($request->input('is_hotel')) {
            $hotelRole = Role::where('name', 'hotelier')->first();
            if ($hotelRole) {
                $user->roles()->attach($hotelRole->id);
            }
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(app('home.redirect')($request));
    }
}
