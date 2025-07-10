<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('images/image1.jpeg') }}')">
        <div class="h-screen flex justify-center items-center backdrop-blur-sm bg-black/30">
            <div class="bg-white mx-4 p-8 rounded shadow-md w-full md:w-1/2 lg:w-1/3">
                <h1 class="text-3xl font-bold mb-8 text-center">Connexion</h1>
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    @if ($errors->has('email'))
                        <p class="text-red-600 text-center font-semibold mt-2">{{ $errors->first('email') }}</p>
                    @endif
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2" for="email">Adresse e-mail</label>
                        <input name="email" type="email" id="email" required
                            placeholder="Entrez votre adresse e-mail"
                            class="border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2" for="password">Mot de passe</label>
                        <input name="password" type="password" id="password" required
                            placeholder="Entrez votre mot de passe"
                            class="border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" />
                        <a class="text-gray-600 hover:text-gray-800 text-sm" href="#">Mot de passe oublié ?</a>
                                            <p class="text-center text-sm mt-4">Crée un compte ? 
                        <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline">S'inscrire</a>.
                
                    </div>

                    <div class="mb-6">
                        <button
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit">
                            Se connecter
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>
