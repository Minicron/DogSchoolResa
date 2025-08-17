<!-- Vue pour les non-connectés - Club d'Éducation Canine de Condat-Sur-Vienne -->
<div class="relative min-h-screen bg-[#17252A]">

    <!-- Messages de succès/erreur -->
    @if (session('success'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-[#3AAFA9]/20 border border-[#3AAFA9] text-[#3AAFA9] p-4 rounded-xl flex items-center shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-[#FE4A49]/20 border border-[#FE4A49] text-[#FE4A49] p-4 rounded-xl flex items-center shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Section Hero -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Contenu principal -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl lg:text-6xl font-bold text-[#FEFFFF] mb-6">
                        Bienvenue au 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78]">
                            Club d'Éducation Canine
                        </span>
                        <br>de Condat-Sur-Vienne
                    </h1>
                    <p class="text-xl text-[#DEF2F1] mb-8 leading-relaxed">
                        Réservez vos séances d'entraînement et partagez des moments inoubliables avec votre compagnon à quatre pattes.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('login') }}" class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-[#17252A] font-bold py-4 px-8 rounded-xl text-lg transition transform hover:scale-105 shadow-lg">
                            Se connecter
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Section Fonctionnalités -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-[#2B7A78]">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-[#FEFFFF] mb-4">Pourquoi utiliser cette plateforme ?</h2>
                <p class="text-xl text-[#DEF2F1]">Une expérience complète pour l'éducation de votre chien</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Carte 1 -->
                <div class="bg-[#FEFFFF] p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[#17252A] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#FEFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#17252A] mb-4 text-center">Réservation Simple</h3>
                    <p class="text-[#17252A] text-center">
                        Réservez vos créneaux d'entraînement en quelques clics. 
                        Interface intuitive et rapide pour une expérience fluide.
                    </p>
                </div>
                
                <!-- Carte 2 -->
                <div class="bg-[#FEFFFF] p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[#17252A] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#FEFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#17252A] mb-4 text-center">Suivi en temps réel</h3>
                    <p class="text-[#17252A] text-center">
                        Gardez un œil sur vos sessions et consultez vos inscriptions.
                    </p>
                </div>
                
                <!-- Carte 3 -->
                <div class="bg-[#FEFFFF] p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[#17252A] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#FEFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#17252A] mb-4 text-center">Expérience fluide</h3>
                    <p class="text-[#17252A] text-center">
                        Interface moderne et intuitive pour une gestion simplifiée.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#17252A] text-[#FEFFFF] py-12 px-4 sm:px-6 lg:px-8 border-t border-[#2B7A78]">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div>
                            <h3 class="text-lg font-bold">CEC Condat</h3>
                            <p class="text-sm text-[#DEF2F1]">Éducation, Agility, Ecole du chiot</p>
                        </div>
                    </div>
                    <p class="text-[#DEF2F1]">
                        Une passion partagée pour l'éducation et le bien-être canin.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-[#DEF2F1]">
                        <p>📍 29 Rue Jules Ferry, 87100 Condat-Sur-Vienne, France</p>
                        <p>📧 cec.condat@yahoo.fr</p>
                        <p>📞 05.55.30.77.73</p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liens utiles</h4>
                    <div class="space-y-2">
                        <a href="{{ route('login') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition">Connexion</a>
                        <a href="{{ route('register') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition">Inscription</a>
                        <a href="#" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition">À propos</a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-[#2B7A78] mt-8 pt-8 text-center text-[#DEF2F1]">
                <p>&copy; 2025 Club d'Éducation Canine de Condat-Sur-Vienne. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>