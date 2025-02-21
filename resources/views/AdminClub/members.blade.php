@php ($days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'])
<div class="bg-gray-800 text-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Membres du club</h2>
        <button 
            class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-2 font-bold"
            hx-get="/admin-club/members/invite"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML">    
            Envoyer une invitation
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-gray-800 text-white w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Nom</th>
                    <th class="px-4 py-2">Prénom</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Rôle</th>
                    <th class="px-4 py-2">Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr class="border-t border-gray-700">
                        <td class="px-4 py-2">{{ $member->name }}</td>
                        <td class="px-4 py-2">{{ $member->firstname }}</td>
                        <td class="px-4 py-2">{{ $member->email }}</td>
                        <td class="px-4 py-2">{{ $member->role }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <button 
                                class="bg-blue-500 text-white hover:bg-blue-700 transition border rounded-lg shadow-lg p-2 font-bold flex items-center"
                                hx-get="/admin-club/slots/edit/{{ $member->id }}"
                                hx-target="#main-adminclub" 
                                hx-swap="innerHTML">    
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM4 12v4h4v-4H4z" />
                                </svg>                                
                            </button>
                            <button 
                                class="bg-red-500 text-white hover:bg-red-700 transition border rounded-lg shadow-lg p-2 font-bold flex items-center"
                                hx-trigger="confirmed"
                                hx-get="/admin-club/slots/delete/{{ $member->id }}"
                                hx-target="#main-adminclub" 
                                hx-swap="innerHTML"
                                onClick="
                                    Swal.fire({
                                        title: 'Êtes-vous sûr ?', text:'Cela supprimera définitivement ce créneau ainsi que toutes les inscriptions à ce dernier !',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Oui',
                                        cancelButtonText: 'Annuler'
                                    }).then((result)=>{
                                        if (result.isConfirmed) {
                                            htmx.trigger(this, 'confirmed');
                                        }
                                    })">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h1v10a2 2 0 002 2h8a2 2 0 002-2V6h1a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm3 4a1 1 0 112 0v8a1 1 0 11-2 0V6zm-3 1a1 1 0 011-1h1a1 1 0 110 2H7a1 1 0 01-1-1zm7 0a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>                                
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-end mt-4">
        <button 
            class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-2 font-bold"
            hx-get="/admin-club/members/invite"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML">    
            Envoyer une invitation
        </button>
    </div>
</div>