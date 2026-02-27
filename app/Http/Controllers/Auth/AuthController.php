<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PersonalDataSheet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function showCreatePersonnelForm(): View
    {
        return view('auth.register', ['forAdmin' => true]);
    }

    public function storePersonnel(Request $request): RedirectResponse
    {
        $authUser = auth()->user();
        
        // Define validation rules based on user role
        $roleRules = ['required', 'string'];
        if ($authUser->isAdmin()) {
            $roleRules[] = 'in:personnel,sub-admin';
        } else { // sub-admin
            $roleRules[] = 'in:personnel'; // sub-admins can only create personnel
        }
        
        $validated = $request->validate([
            'surname' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'employee_id' => ['required', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
            'role' => $roleRules,
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = $this->buildDisplayName($validated);

        $user = User::create($validated);
        $this->createPdsWithName($user, $validated);

        return redirect()->route('personnel.index')->with('success', 'Personnel added successfully.');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'surname' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'employee_id' => ['required', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = User::ROLE_PERSONNEL; // Regular registration always creates personnel
        $validated['name'] = $this->buildDisplayName($validated);

        $user = User::create($validated);
        $this->createPdsWithName($user, $validated);

        Auth::attempt([
            'email' => $validated['email'],
            'password' => $request->password,
        ]);

        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function buildDisplayName(array $validated): string
    {
        $first = trim($validated['first_name'] ?? '');
        $middle = trim($validated['middle_name'] ?? '');
        $surname = trim($validated['surname'] ?? '');
        $ext = trim($validated['name_extension'] ?? '');
        $parts = array_filter([$first, $middle, $surname]);
        $name = implode(' ', $parts);
        if ($ext !== '') {
            $name .= ' ' . $ext;
        }
        return $name;
    }

    private function createPdsWithName(User $user, array $validated): void
    {
        PersonalDataSheet::create([
            'user_id' => $user->id,
            'surname' => $validated['surname'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'email_address' => $validated['email'],
            'agency_employee_no' => $validated['employee_id'],
        ]);
    }
}
