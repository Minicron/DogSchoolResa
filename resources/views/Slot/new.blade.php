<div class="bg-[#17252A] shadow-md rounded-lg p-6 max-w-3xl mx-auto">
    <h2 class="text-2xl text-[#DEF2F1] font-bold mb-6">Nouveau cours</h2>
    <form hx-post="/admin-club/slots/new" hx-target="#main-adminclub" hx-swap="innerHTML" 
          x-data="{ autoClose: false }" class="space-y-4">
        @csrf
        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-[#DEF2F1]">Nom</label>
            <input id="name" name="name" type="text" required autofocus
                   class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
        </div>

        <!-- Grille: Jour & Lieu -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="day_of_week" class="block text-sm font-medium text-[#DEF2F1]">Jour</label>
                <select id="day_of_week" name="day_of_week" required
                        class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]">
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
                <label for="location" class="block text-sm font-medium text-[#DEF2F1]">Lieu</label>
                <input id="location" name="location" type="text" required
                       class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
        </div>

        <!-- Plage horaire : début et fin regroupés -->
        <div>
            <label class="block text-sm font-medium text-[#DEF2F1]">Plage horaire</label>
            <div class="mt-1 flex items-center space-x-2">
                <input id="time_start" name="time_start" type="time" required
                       class="w-1/2 rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
                <span class="text-[#DEF2F1] font-bold">à</span>
                <input id="time_end" name="time_end" type="time" required
                       class="w-1/2 rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-[#DEF2F1]">Description</label>
            <textarea id="description" name="description" rows="3" required
                      class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]"></textarea>
        </div>

        <!-- Grille: Capacité & Alerte moniteurs -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="capacity" class="block text-sm font-medium text-[#DEF2F1]">Capacité</label>
                <input id="capacity" name="capacity" type="number" required
                       class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
            <div>
                <label for="alert_monitors" class="block text-sm font-medium text-[#DEF2F1]">
                    Alerter si le nombre de moniteurs est inférieur à
                </label>
                <input id="alert_monitors" name="alert_monitors" type="number"
                       class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
        </div>

        <!-- Nouvelle case à cocher pour limiter l'inscription -->
        <div class="flex items-center">
            <input type="checkbox" id="is_restricted" name="is_restricted"
                   class="h-4 w-4 rounded text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300" />
            <label for="is_restricted" class="ml-2 block text-sm font-medium text-[#DEF2F1]">
                Limiter qui peut s'inscrire à ce créneau
            </label>
        </div>

        <!-- Options de clôture -->
        <div class="space-y-2">
            <div class="flex items-center">
                <input id="auto_close" type="checkbox" x-model="autoClose" name="auto_close"
                       class="h-4 w-4 rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9]" />
                <label for="auto_close" class="ml-2 block text-sm font-medium text-[#DEF2F1]">
                    Clôture automatique des inscriptions
                </label>
            </div>
            <!-- Champ conditionnel -->
            <div x-show="autoClose" x-cloak>
                <label for="close_duration" class="block text-sm font-medium text-[#DEF2F1]">
                    Durée avant clôture (heures)
                </label>
                <select id="close_duration" name="close_duration"
                        class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]">
                    <option value="72">72</option>
                    <option value="48">48</option>
                    <option value="24">24</option>
                    <option value="12">12</option>
                </select>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end gap-4 mt-6">
            <button 
                type="button"
                hx-get="/admin-club/slots"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML"
                class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-6 rounded transition"
            >
                Annuler
            </button>
            <button 
                type="submit" 
                class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-6 rounded transition"
            >
                Ajouter
            </button>
        </div>
    </form>
</div>
