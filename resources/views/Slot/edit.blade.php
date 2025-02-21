<div class="bg-gray-800 shadow-md rounded-lg p-6">
    <h2 class="text-xl text-white font-bold mb-4">Modifier un cours</h2>
    <form hx-post="/admin-club/slots/edit/{{ $slot->id }}" hx-target="#main-adminclub" hx-swap="innerHTML" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4 items-center">
            <div class="col-span-2">
                <label for="name" class="block text-sm font-medium text-white">Nom</label>
                <input id="name" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="name" required autofocus value="{{ old('name', $slot->name) }}" />
            </div>
            <div>
                <label for="day_of_week" class="block text-sm font-medium text-white">Jour</label>
                <select id="day_of_week" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" name="day_of_week" required>
                    <option value="1" {{ old('day_of_week', $slot->day_of_week) == 1 ? 'selected' : '' }}>Lundi</option>
                    <option value="2" {{ old('day_of_week', $slot->day_of_week) == 2 ? 'selected' : '' }}>Mardi</option>
                    <option value="3" {{ old('day_of_week', $slot->day_of_week) == 3 ? 'selected' : '' }}>Mercredi</option>
                    <option value="4" {{ old('day_of_week', $slot->day_of_week) == 4 ? 'selected' : '' }}>Jeudi</option>
                    <option value="5" {{ old('day_of_week', $slot->day_of_week) == 5 ? 'selected' : '' }}>Vendredi</option>
                    <option value="6" {{ old('day_of_week', $slot->day_of_week) == 6 ? 'selected' : '' }}>Samedi</option>
                    <option value="7" {{ old('day_of_week', $slot->day_of_week) == 7 ? 'selected' : '' }}>Dimanche</option>
                </select>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-white">Lieu</label>
                <input id="location" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="location" required value="{{ old('location', $slot->location) }}" />
            </div>
            <div>
                <label for="time_start" class="block text-sm font-medium text-white">Début</label>
                <input id="time_start" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="time" name="time_start" required value="{{ old('time_start', $slot->time_start) }}" />
            </div>
            <div>
                <label for="time_end" class="block text-sm font-medium text-white">Fin</label>
                <input id="time_end" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="time" name="time_end" required value="{{ old('time_end', $slot->time_end) }}" />
            </div>
            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-white">Description</label>
                <textarea id="description" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" name="description" rows="2" required>{{ old('description', $slot->description) }}</textarea>
            </div>
            <div class="col-span-2">
                <label for="capacity" class="block text-sm font-medium text-white">Capacité</label>
                <input id="capacity" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="number" name="capacity" required value="{{ old('capacity', $slot->capacity) }}" />
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
                Modifier
            </button>
        </div>
    </form>
</div>