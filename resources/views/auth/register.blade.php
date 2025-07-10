<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('images/image2.jpg') }}')">
        <div class="h-screen flex justify-center items-center backdrop-blur-sm bg-black/30">
            <div class="bg-white mx-4 p-8 rounded shadow-md w-full md:w-1/2 lg:w-1/3">
                <h1 class="text-3xl font-bold mb-6 text-center">Créer un compte</h1>

                {{-- Message de succès --}}
                @if (session('success'))
                    <div id="success-message" class="bg-green-100 text-green-800 text-sm text-center font-semibold py-2 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "{{ route('login') }}";
                        }, 4000);
                    </script>
                @endif



                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="cin" class="block text-sm font-medium text-gray-700">CIN</label>
                        <input name="cin" type="text" id="cin" value="{{ old('cin') }}" required
                            class="w-full border rounded px-3 py-2">
                        @error('cin')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <input name="nom" type="text" id="nom" value="{{ old('nom') }}" required
                            class="w-full border rounded px-3 py-2">
                        @error('nom')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                        <input name="email" type="email" id="email" value="{{ old('email') }}" required
                            class="w-full border rounded px-3 py-2">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input name="password" type="password" id="password" required
                            class="w-full border rounded px-3 py-2">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <input name="password_confirmation" type="password" id="password_confirmation" required
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                        <select name="role" id="role" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Choisir un rôle --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="medecin" {{ old('role') == 'medecin' ? 'selected' : '' }}>Médecin</option>
                            <option value="secretaire" {{ old('role') == 'secretaire' ? 'selected' : '' }}>Secrétaire</option>
                        </select>
                        @error('role')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">
                        Créer un compte
                    </button>

                    <p class="text-center text-sm mt-4">
                        Vous avez déjà un compte ?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
