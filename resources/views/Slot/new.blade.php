<div class="bg-gray-800 shadow-md rounded-lg p-6">
    <h2 class="text-xl text-white font-bold mb-4">Nouveau cours</h2>
    <form hx-post="/admin-club/slots/new" hx-target="#main-adminclub" hx-swap="innerHTML" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4 items-center">
            <div class="col-span-2">
                <label for="name" class="block text-sm font-medium text-white">Nom</label>
                <input id="name" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="name" required autofocus />
            </div>
            <div>
                <label for="day_of_week" class="block text-sm font-medium text-white">Jour</label>
                <select id="day_of_week" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" name="day_of_week" required>
                    <option value="1">Lundi</option>
                    <option value="2">Mardi</option>
                    <option value="3">Mercredi</option>
                    <option value="4">Jeudi</option>
                    <option value="5">Vendredi</option>
                    <option value="6">Samedi</option>
                    <option value="7">Dimanche</option>
                </select>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-white">Lieu</label>
                <input id="location" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="location" required />
            </div>
            <div>
                <label for="time_start" class="block text-sm font-medium text-white">Début</label>
                <input id="time_start" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="time" name="time_start" required />
            </div>
            <div>
                <label for="time_end" class="block text-sm font-medium text-white">Fin</label>
                <input id="time_end" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="time" name="time_end" required />
            </div>
            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-white">Description</label>
                <textarea id="description" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" name="description" rows="2" required></textarea>
            </div>
            <div class="col-span-2">
                <label for="capacity" class="block text-sm font-medium text-white">Capacité</label>
                <input id="capacity" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="number" name="capacity" required />
            </div>
        </div>
        <div class="flex justify-end mt-4 gap-4">
            <button 
                hx-get="/admin-club/slots"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML"
                class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold"
            >
                <span class="mt-2 font-bold">Annuler</span>
            </button>
            <button class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold" type="submit">
                Ajouter
            </button>
        </div>
    </form>
</div>