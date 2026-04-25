<?php

// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Laravel\Fortify\Fortify;

// class FortifyServiceProvider extends ServiceProvider
// {
//     /**
//      * Register services.
//      */
//     public function register(): void
//     {
//         //
//     }

//     /**
//      * Bootstrap services.
//      */
//     public function boot(): void
//     {
//         Fortify::authenticateUsing(function (Request $request) {
//             $user = User::where('email', $request->email)->first();

//             if ($user &&
//                 Hash::check($request->password, $user->password)) {
//                 return $user;
//             }
//         });
//     }
// }
