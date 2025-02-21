<div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
    <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
        <h1 class="text-2xl font-semibold">Nouveau club</h1>

        <form action="{{ url('/club/create') }}" method="POST" hx-post="/club/create" hx-swap="innerHTML" hx-target="#super_admin_dashboard">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold mb-2">Nom :</label>
                <input type="text" id="name" name="name" required class="w-full p-2 border rounded">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold mb-2">Description :</label>
                <textarea id="description" name="description" required class="w-full p-2 border rounded"></textarea>
            </div>

            <!-- City -->
            <div class="mb-4">
                <label for="city" class="block text-sm font-semibold mb-2">Ville :</label>
                <input type="text" id="city" name="city" required class="w-full p-2 border rounded">
            </div>

            <!-- Bouton de soumission -->
            <a href="{{ url('/super-admin') }}" class="p-2 hover:bg-gray-100 transition border text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
            <button type="submit" class="hover:bg-gray-100 transition border bg-[#22567d] text-white hover:text-[#22567d] font-bold py-2 px-4 rounded">Valider la création du club</button>
        </form>
    </div>
</div>