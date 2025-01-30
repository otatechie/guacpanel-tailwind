<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\PhoneBrand;
use Illuminate\Support\Arr;
use App\Models\MobileDevice;
use App\Models\PhoneVariant;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    private function getAuthUser(): User
    {
        return auth()->user();
    }


    public function index()
    {

        $user = $this->getAuthUser();

        return Inertia::render('UserAccount/IndexPage', [
            'user' => array_merge($user->only('name', 'email', 'location', 'region',), []),
        ]);
    }


    public function twoFactorAuthentication()
    {
        $user = $this->getAuthUser();

        $data = [
            'user' => $user,
            'qrCodeSvg' => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'recoveryCodes' => $user->two_factor_secret ? json_decode(decrypt($user->two_factor_recovery_codes, true)) : null,
        ];

        return Inertia::render('UserAccount/IndexTwoFactorAuthenticationPage', $data);
    }


    public function userListing()
    {
        $user = auth()->user();
        $userListings = MobileDevice::with('phoneBrand', 'phoneModel', 'phoneVariant')
            ->where('user_id', $user->id)->get();

        return Inertia::render('UserAccount/IndexPageListing', [
            'userListings' => $userListings,
        ]);
    }


    public function userProfile(User $user)
    {
        $userListings = MobileDevice::with('phoneBrand', 'phoneModel', 'phoneVariant')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($mobileDevice) {
                return array_merge($mobileDevice->toArray(), [
                    'media' => [
                        'images' => $mobileDevice->getMedia('images') ?: null,
                    ],
                ]);
            });

        return Inertia::render('UserAccount/IndexPageProfile', [
            'user' => array_merge($user->toArray(), [
                'created_at_human' => $user->created_at ? $user->created_at->diffForHumans() : null,
                'created_at_date' => $user->created_at ? $user->created_at->format('M d, Y') : null
            ]),
            'userListings' => $userListings
        ]);
    }


    public function userFavorites()
    {
        $user = $this->getAuthUser();

        $favorites = $user->favorites()
            ->with(['mobileDevice.phoneBrand', 'mobileDevice.phoneModel', 'mobileDevice.phoneVariant'])
            ->get()
            ->map(function ($favorite) {
                return $favorite->mobileDevice;
            });

        return Inertia::render('UserAccount/IndexPageFavorites', [
            'favorites' => $favorites
        ]);
    }
}
