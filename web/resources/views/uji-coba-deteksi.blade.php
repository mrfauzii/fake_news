<!-- resources/views/uji-coba-deteksi.blade.php -->

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Coba Deteksi</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-red-50 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- HEADER -->
        <div class="bg-red-800 text-white p-8 text-center">
            <h1 class="text-4xl font-bold mb-2">
                Uji Coba Deteksi
            </h1>

            <p class="text-red-100">
                Input teks untuk pengujian route
            </p>
        </div>

        <!-- FORM -->
        <div class="p-8">

            <form action="{{ route('detect.hoax') }}" method="POST">
                @csrf

                <div class="mb-6">

                    <label class="block text-lg font-semibold text-gray-700 mb-3">
                        Masukkan Teks
                    </label>

                    <textarea
                        name="input_text"
                        rows="8"
                        class="w-full border border-gray-300 rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-red-700"
                        placeholder="Masukkan teks berita di sini..."
                        required></textarea>

                </div>

                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="bg-red-800 hover:bg-red-900 text-white font-semibold px-8 py-3 rounded-xl transition">

                        Kirim

                    </button>

                </div>

            </form>

        </div>

    </div>

</body>

</html>