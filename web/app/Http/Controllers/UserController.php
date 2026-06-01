<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WaController;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil keyword pencarian secara aman melalui request object
        $search = $request->input('search');

        // 2. Query ke database dengan klausa pencarian dinamis (kondisional)
        $usersFromDb = Users::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        })
            ->orderBy('name', 'asc') // Menambahkan sorting agar susunan tabel teratur rapi
            ->paginate(2)
            ->appends(['search' => $search]);

        // 3. Mapping data koleksi pagination tanpa memutus rantai pagination-nya
        $usersFromDb->through(function ($user) {
            return [
                'nama' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->phone_number,
            ];
        });

        // 4. Return data utuh ke view admin.user
        return view('admin.user', [
            'users' => $usersFromDb,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            /** @var Users|null $user */
            $user = Auth::user();

            Log::info('updateProfile request', $request->all());

            if (!$user) {
                session()->flash('error', 'User tidak ditemukan'); // <-- SET SESSION ERROR
                return response()->json(['success' => false], 404);
            }

            // =========================
            // VALIDATION
            // =========================
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                // Ambil pesan error pertama dari validasi untuk dimasukkan ke session
                session()->flash('error', $validator->errors()->first()); // <-- SET SESSION ERROR
                return response()->json(['success' => false], 422);
            }

            $validated = $validator->validated();
            $email = $validated['email'] ?? null;
            $phone = $validated['phone_number'] ?? null;

            // =========================
            // RULE CHECK - PHONE INPUT
            // =========================
            if ($phone) {
                $userWithPhone = Users::where('phone_number', $phone)->where('id', '!=', $user->id)->first();
                if ($userWithPhone && !empty($userWithPhone->email)) {
                    session()->flash('error', 'Nomor WA ini sudah terhubung dengan email, tidak bisa digunakan'); // <-- SET SESSION ERROR
                    return response()->json(['success' => false], 422);
                }
            }

            // =========================
            // RULE CHECK - EMAIL INPUT
            // =========================
            if ($email) {
                $userWithEmail = Users::where('email', $email)->where('id', '!=', $user->id)->first();
                if ($userWithEmail && !empty($userWithEmail->phone_number)) {
                    session()->flash('error', 'Email ini sudah terhubung dengan nomor WA, tidak bisa digunakan'); // <-- SET SESSION ERROR
                    return response()->json(['success' => false], 422);
                }
            }

            // =========================
            // TRIGGER VERIFICATION (SUKSES)
            // =========================
            if ($phone) {
                app(WaController::class)->linkWhatsApp($phone);
                session()->flash('success', 'Link verifikasi WhatsApp berhasil dikirim. Silakan cek WhatsApp Anda.');
                return response()->json(['success' => true]);
            }

            if ($email) {
                app(EmailController::class)->linkEmail($email);
                session()->flash('success', 'Link verifikasi Email berhasil dikirim. Silakan cek Email Anda.');
                return response()->json(['success' => true]);
            }

            session()->flash('success', 'Profile berhasil diperbarui');
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('updateProfile error', ['message' => $e->getMessage()]);

            session()->flash('error', 'Terjadi kesalahan pada server'); // <-- SET SESSION ERROR
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Return JSON data untuk kebutuhan API Admin/Eksternal jika diperlukan
     */
    public function getUserData()
    {
        $usersFromDb = Users::all();

        $userData = $usersFromDb->map(function ($user) {
            return [
                'nama' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->phone_number,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil dimuat.',
            'data' => $userData,
        ]);
    }
}
