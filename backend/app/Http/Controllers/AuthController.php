<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'country_code'    => ['required', 'regex:/^\+[0-9]{1,4}$/'],
            'mobile_number'   => ['required', 'regex:/^[0-9]{6,15}$/'],
            'email'           => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'date_of_birth'   => ['nullable', 'date'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:4096'],
        ]);

        $fullMobile = $validated['country_code'] . $validated['mobile_number'];

        if (User::where('mobile_number', $fullMobile)->exists()) {
            throw ValidationException::withMessages([
                'mobile_number' => ['The mobile number has already been taken.'],
            ]);
        }

        $pictureName = null;

        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $pictureName = $this->storePicture($request, $validated['name'], $fullMobile);
        }

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'] ?? null,
            'date_of_birth'   => $validated['date_of_birth'] ?? null,
            'mobile_number'   => $fullMobile,
            'profile_picture' => $pictureName,
            'password'        => $validated['password'],
        ]);

        // Assign a member_id like KRWWF-mmyy-0001 (sequential per month-year)
        $now = now();
        $prefix = sprintf('KRWWF-%02d%02d-', $now->month, $now->year % 100);
        // Count existing users for this month-year to increment sequence
        $count = User::where('member_id', 'like', $prefix . '%')->count();
        $sequence = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        $user->member_id = $prefix . $sequence;
        $user->save();

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login_with'    => ['required', Rule::in(['mobile', 'email'])],
            'country_code'  => ['nullable', 'regex:/^\+[0-9]{1,4}$/', Rule::requiredIf(fn () => $request->input('login_with') === 'mobile')],
            'mobile_number' => ['nullable', 'regex:/^[0-9]{6,15}$/', Rule::requiredIf(fn () => $request->input('login_with') === 'mobile')],
            'email'         => ['nullable', 'email', Rule::requiredIf(fn () => $request->input('login_with') === 'email')],
            'password'      => ['required', 'string'],
        ]);

        $user = null;

        if ($credentials['login_with'] === 'mobile') {
            $fullMobile = $credentials['country_code'] . $credentials['mobile_number'];
            $user = User::where('mobile_number', $fullMobile)->first();
        }

        if ($credentials['login_with'] === 'email') {
            $user = User::where('email', $credentials['email'])->first();
        }

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            throw ValidationException::withMessages(['user' => ['Unauthenticated user.']]);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'regex:/^\+[0-9]{7,19}$/', Rule::unique('users', 'mobile_number')->ignore($user->id)],
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            // additional profile fields allowed in separate section saves
            'location' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:100'],
            'skills' => ['nullable', 'string'],
            'role_in_community' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'interests' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }

        if (array_key_exists('mobile_number', $validated)) {
            $user->mobile_number = $validated['mobile_number'];
        }

        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        if (array_key_exists('location', $validated)) {
            $user->location = $validated['location'];
        }

        if (array_key_exists('profession', $validated)) {
            $user->profession = $validated['profession'];
        }

        if (array_key_exists('company', $validated)) {
            $user->company = $validated['company'];
        }

        if (array_key_exists('experience', $validated)) {
            $user->experience = $validated['experience'];
        }

        if (array_key_exists('skills', $validated)) {
            $user->skills = $validated['skills'];
        }

        if (array_key_exists('role_in_community', $validated)) {
            $user->role_in_community = $validated['role_in_community'];
        }

        if (array_key_exists('blood_group', $validated)) {
            $user->blood_group = $validated['blood_group'];
        }

        if (array_key_exists('interests', $validated)) {
            $user->interests = $validated['interests'];
        }

        if (array_key_exists('date_of_birth', $validated)) {
            $user->date_of_birth = $validated['date_of_birth'];
        }

        $user->save();

        return response()->json($user);
    }

    public function uploadPicture(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            throw ValidationException::withMessages(['user' => ['Unauthenticated user.']]);
        }

        $request->validate([
            'profile_picture' => ['required', 'image', 'max:4096'],
        ]);

        if ($user->profile_picture) {
            $oldPath = public_path('user_pictures/' . $user->profile_picture);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $pictureName = $this->storePicture($request, $user->name, $user->mobile_number ?? 'nophone');
        $user->profile_picture = $pictureName;
        $user->save();

        return response()->json($user);
    }

    private function storePicture(Request $request, string $name, string $phone): string
    {
        $sanitizedName  = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
        $sanitizedPhone = preg_replace('/[^0-9]/', '', $phone);
        $timestamp      = time();
        $ext            = $request->file('profile_picture')->getClientOriginalExtension();
        $filename       = "{$sanitizedName}_{$sanitizedPhone}_{$timestamp}.{$ext}";

        $request->file('profile_picture')->move(public_path('user_pictures'), $filename);

        return $filename;
    }
}
