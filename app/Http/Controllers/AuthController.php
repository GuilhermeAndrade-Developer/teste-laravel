<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Método para login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        return $this->respondWithToken($token);
    }

    // Método para registro de usuário
    public function register(Request $request)
    {
        // Validação dos dados do usuário
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Criação do usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Geração do token JWT
        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }

    // Método para obter o usuário autenticado
    public function me()
    {
        return response()->json(JWTAuth::user());
    }

    // Método para logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    // Método para atualizar o token
    public function refresh()
    {
        try {
            return $this->respondWithToken(Auth::guard('api')->refresh());
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado. Por favor, faça login novamente.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
    }

    // Método auxiliar para formatar a resposta com o token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }
}
