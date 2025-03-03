<!-- Vue pour les non-connectés -->
<div class="relative bg-[#17252A] min-h-screen flex flex-col items-center justify-center text-white text-center px-6">
    <div class="max-w-4xl">
        <h1 class="text-5xl font-bold mb-4">Bienvenue sur Notre Plateforme de Réservation</h1>
        <p class="text-lg text-[#DEF2F1] mb-6">
            Réservez facilement vos créneaux et suivez votre planning en un clic.
            Une gestion simplifiée et intuitive, accessible à tout moment.
        </p>
        <a href="{{ route('register') }}" class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-white font-bold py-3 px-6 rounded-lg text-lg transition">
            Demander un compte
        </a>
        <a href="{{ route('login') }}" class="ml-4 border border-white text-white font-bold py-3 px-6 rounded-lg text-lg transition hover:bg-white hover:text-[#17252A]">
            Se connecter
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 max-w-6xl">
        <div class="bg-[#2B7A78] p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold mb-2">Réservation rapide</h3>
            <p class="text-[#DEF2F1]">Choisissez et réservez votre créneau en quelques secondes.</p>
        </div>
        <div class="bg-[#3AAFA9] p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold mb-2">Suivi en temps réel</h3>
            <p class="text-[#DEF2F1]">Gardez un œil sur vos sessions et consultez vos inscriptions.</p>
        </div>
        <div class="bg-[#DEF2F1] text-[#17252A] p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold mb-2">Expérience fluide</h3>
            <p>Interface moderne et intuitive pour une gestion simplifiée.</p>
        </div>
    </div>

    <div class="absolute bottom-4 text-sm text-[#DEF2F1]">
        © 2025 - Tous droits réservés
    </div>
</div>