<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }
    public function sucessRegister()
    {
        return Inertia::render('Auth/RegisterSucess');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Avatar opcional
            ]);

            // Criar usuário
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Criar pasta do usuário: storage/app/users/{id}/profile
            $userDirectory = "users/{$user->id}/profile";
            Storage::makeDirectory($userDirectory);

            // Definir URL do avatar
            $avatarUrl = null;

            // Se o usuário enviou uma foto de perfil
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->storeAs($userDirectory, 'photoUserProfile.png');
                $avatarUrl = Storage::url($avatarPath);
            } else {
                // Se não enviou, gera um avatar com a inicial do nome
                $initial = strtoupper(substr($user->name, 0, 1));
                $avatarUrl = "https://ui-avatars.com/api/?name=$initial&background=random&color=fff";
            }

            // Atualizar a coluna 'avatar' no banco
            $user->forceFill(['avatar' => $avatarUrl])->save();


            // Disparar evento e autenticar usuário
            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('register.sucess');

        } catch (ValidationException $e) {
            // Se for erro de validação (e-mail já cadastrado ou outros erros)
            return Inertia::render('Auth/Register', [
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ]);

        } catch (\Exception $e) {
            // Para outros erros inesperados
            return Inertia::render('Auth/Register', [
                'status' => 'error',
                'message' => 'Erro ao registrar usuário. Tente novamente.',
            ]);
        }
    }

}
