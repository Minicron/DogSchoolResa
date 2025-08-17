@php ($days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'])
<div class="bg-[#17252A]/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-[#3AAFA9]/20">
    <!-- Messages de succès/erreur -->
    @if (isset($success))
        <div class="bg-[#3AAFA9]/20 border border-[#3AAFA9] text-[#3AAFA9] p-4 rounded-xl mb-6 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ $success }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#FEFFFF]">Membres du club</h2>
            <p class="text-[#DEF2F1] text-sm mt-1">Gérez les membres de votre club</p>
        </div>
        <button 
            class="bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] text-[#17252A] transition-all duration-200 rounded-xl px-6 py-3 font-medium flex items-center transform hover:scale-[1.02] shadow-lg"
            hx-get="/admin-club/members/invite"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML">    
            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
            </svg>
            Inviter un membre
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-[#2B7A78]/20 text-[#DEF2F1] w-full rounded-xl overflow-hidden">
            <thead class="bg-[#17252A]">
                <tr>
                    <th class="px-6 py-4 text-left font-medium">Nom</th>
                    <th class="px-6 py-4 text-left font-medium">Prénom</th>
                    <th class="px-6 py-4 text-left font-medium">Email</th>
                    <th class="px-6 py-4 text-left font-medium">Rôle</th>
                    <th class="px-6 py-4 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr class="border-t border-[#3AAFA9]/20 hover:bg-[#2B7A78]/10 transition-colors">
                        <td class="px-6 py-4">{{ $member->name }}</td>
                        <td class="px-6 py-4">{{ $member->firstname }}</td>
                        <td class="px-6 py-4">{{ $member->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $member->role === 'admin-club' ? 'bg-[#3AAFA9] text-[#17252A]' : 
                                   ($member->role === 'monitor' ? 'bg-[#2B7A78] text-[#DEF2F1]' : 'bg-[#17252A] text-[#DEF2F1]') }}">
                                {{ $member->role === 'admin-club' ? 'Admin' : 
                                   ($member->role === 'monitor' ? 'Moniteur' : 'Membre') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <button 
                                class="bg-[#3AAFA9] text-[#17252A] hover:bg-[#2B7A78] transition-all duration-200 rounded-lg p-2 font-medium flex items-center"
                                title="Modifier le membre"
                                hx-get="/admin-club/members/edit/{{ $member->id }}"
                                hx-target="#main-adminclub" 
                                hx-swap="innerHTML">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM4 12v4h4v-4H4z" />
                                </svg>
                            </button>
                            <button 
                                class="bg-[#FE4A49] text-white hover:bg-red-700 transition-all duration-200 rounded-lg p-2 font-medium flex items-center"
                                title="Supprimer le membre"
                                hx-trigger="confirmed"
                                hx-get="/admin-club/members/delete/{{ $member->id }}"
                                hx-target="#main-adminclub" 
                                hx-swap="innerHTML"
                                onClick="
                                    Swal.fire({
                                        title: 'Êtes-vous sûr ?', 
                                        text:'Cela supprimera définitivement ce membre du club !',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Oui, supprimer',
                                        cancelButtonText: 'Annuler',
                                        confirmButtonColor: '#FE4A49'
                                    }).then((result)=>{
                                        if (result.isConfirmed) {
                                            htmx.trigger(this, 'confirmed');
                                        }
                                    })">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>