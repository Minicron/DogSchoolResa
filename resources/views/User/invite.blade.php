<div class="bg-gray-800 shadow-md rounded-lg p-6">
    <h2 class="text-xl text-white font-bold mb-4">Inviter un nouveau membre</h2>
    @if (isset($error) && $error == 'EMAIL_ALREADY_EXISTS')
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            L'adresse email existe déjà
        </div>
    @endif
    <form hx-post="/admin-club/members/invite" hx-target="#main-adminclub" hx-swap="innerHTML" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4 items-center">
            <div>
                <label for="name" class="block text-sm font-medium text-white">Nom</label>
                <input id="name" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="name" required value="{{ old('name', isset($user) ? $user->name : '') }}" />
            </div>
            <div>
                <label for="firstname" class="block text-sm font-medium text-white">Prénom</label>
                <input id="firstname" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="text" name="firstname" required value="{{ old('firstname', isset($user) ? $user->firstname : '') }}" />
            </div>            
            <div class="col-span-2">
                <label for="email" class="block text-sm font-medium text-white">Email</label>
                <input id="email" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" type="email" name="email" required autofocus value="{{ old('email', isset($user) ? $user->email : '') }}" />
            </div>
            <div class="col-span-2">
                <label for="role" class="block text-sm font-medium text-white">Rôle</label>
                <select id="role" class="mt-1 block w-full rounded bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500 p-2" name="role" required>
                    <option value="user" {{ old('role', isset($user) ? $user->role : '') == 'user' ? 'selected' : '' }}>Membre</option>
                    <option value="admin-club" {{ old('role', isset($user) ? $user->role : '') == 'admin-club' ? 'selected' : '' }}>Administrateur du club</option>                    
                </select>
            </div>
            <input type="hidden" name="club_id" value="{{ $idClub }}" />
        </div>
        <div class="flex justify-end mt-4 gap-4">
            <button 
                hx-get="/admin-club/members"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML"
                class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold"
            >
                <span class="mt-2 font-bold">Annuler</span>
            </button>
            <button class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold" type="submit">
                Envoyer l'invitation
            </button>
        </div>
    </form>
</div>
