@php ($days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'])
<div class="bg-gray-800 text-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div>
        Vos cours        
    </div>
    <div class="mt-4">
        @foreach ($slots as $slot)
            <div class="bg-gray-800 overflow-hidden border shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items center">
                        <div class="flex-1">
                            <div class="font-bold text-xl">{{ $slot->name }}</div>
                            <div class="text-sm">{{ $slot->description }}</div>
                            <div class="text-sm">{{ $days[$slot->day_of_week - 1] }} - De {{ $slot->start_time }} à {{ $slot->end_time }}</div>
                            <div class="text-sm">{{ $slot->capacity }} places</div>
                        </div>
                        <div class="flex justify-end mt-4 gap-4 ">
                            <button 
                                class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold"
                                hx-get="/admin-club/slots/edit/{{ $slot->id }}"
                                hx-target="#main-adminclub" 
                                hx-swap="innerHTML"
                            >    
                                Modifier
                            </button>
                            <button 
                                class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold"
                                hx-trigger="confirmed"
                                hx-get="/admin-club/slots/delete/{{ $slot->id }}"
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
                                Supprimer
                            </button>
                        </div>
                    </div>                    
                </div>
            </div>
        @endforeach     
        <div class="mt-4">
            <button 
                class="bg-[#22567d] text-white hover:bg-gray-700 transition border rounded-lg shadow-lg p-4 font-bold"
                href="{{ url('/admin-club/slots/new') }}" 
                hx-trigger="click"
                hx-get="/admin-club/slots/new"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML">    
                Ajouter un cours
            </button>
        </div>               
    </div>    
</div>