<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { participantService } from '../../services/participant.services';

    const searchValue = ref('');
    let participants = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let participantEdit = false;
    const id = ref('');
    const name = ref('');
    const email = ref('');
    const user = ref('');

    const headers: Header[] = [
        { text: "Nom", value: "name", sortable: true },
        { text: "Email", value: "email", sortable: true },
        { text: "Invité par", value: "invitedBy", sortable: true },
    ];

    const items: Item[] = participants.value;

    onMounted(async () => {
        // set datatable
        getParticipants();
        isLoading.value = false;  
    });    

    const getParticipants = () => {    
        participantService.getParticipants().then((response) => { 
            items.splice(0, items.length);
            const participantsResponse = response.data;  
            participants.value.push(... participantsResponse);
            itemsSelected.value = [];  
        });
    };

    const getParticipantByEMail = (participantMail) => {  
        participantService.getActivityById(participantMail).then((response) => { 
            participantEdit = true;     
            name.value = response.data.name;
            email.value = response.data.email;
            user.value = response.data.user;
        });
    };

    const createParticipant = () => {        
        isLoading.value = true; 
        participantService.createParticipant({
            name: name.value,
            email: email.value,
            user: user.value
        }).then(async (response) => { 
            await getParticipants();  
            isLoading.value = false; 
        });
    };

    const editParticipant = () => {        
        isLoading.value = true; 
        participantService.editParticipant(email.value, {
            name: name.value,
            email: email.value,
            user: user.value
        }).then(async (response) => { 
            await getParticipants();  
            isLoading.value = false; 
        }); 
    };

    const deleteParticipant = () => {        
        isLoading.value = true;         
        participantService.deleteParticipant(itemsSelected.value[0].id).then(async (response) => { 
            await getParticipants();
            isLoading.value = false;     
        });
    };

    const deleteParticipants = () => {        
        isLoading.value = true; 
        let emails = [];
        itemsSelected.value.forEach((participant) => {
            emails.push(participant.email);
        });    
        participantService.deleteParticipants(emails).then(async (response) => { 
            await getParticipants();
            isLoading.value = false;     
        });
    };

    const exportData = () => {
        participantService.exportParticipantData().then(async (response) => { 
            // upload le pdf reçu     
        })
    };

    const resetForm = () => {
        name.value = '';
        email.value = '';
        user.value = '';
    };
    
    defineComponent({
        name: 'participantManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                exportData,
                createParticipant,
                getParticipantByEMail,
                resetForm,
                editParticipant,
                deleteParticipant,
                deleteParticipants
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des participants</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des participants</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer un participant</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getParticipantByEMail(itemsSelected[0].email)">Modifier le participant</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer le participant</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les participants</a>
                </div>
            </div>

            <EasyDataTable
                :headers="headers"
                :items="items"
                :search-value="searchValue"
                :loading="isLoading"
                v-model:items-selected="itemsSelected"
                alternating
                border-cell
                buttons-pagination
                rows-per-page-message="Ligne par page"           
            >
                <template #loading>
                    <Loader/>
                </template>

                <template #empty-message>
                    <p>Aucun résultat</p>
                </template>

                <template #item-activeYears="item">
                    {{ item.activeYears.length > 0 ? item.activeYears[0] + "/" + item.activeYears[1] : "" }}                    
                </template>
            </EasyDataTable>
        </div>

        <!-- Pop-in d'ajout ou de modfication d'un participant -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">Créer un participant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="name" class="form-row__input" type="text" placeholder="Nom de l'participant*"/>
                        </div>

                        <div class="form-row">
                            <textarea v-model="email" class="form-row__input" placeholder="Description*"/>
                        </div>

                        <div class="form-row">
                            <input v-model="user" class="form-row__input" type="text" placeholder="Utilisateur*"/>
                        </div>   
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!participantEdit" type="button" class="btn btn-primary" @click="createParticipant()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn btn-primary" @click="editParticipant()" data-bs-dismiss="modal">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pop-in de confirmation de suppression -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Voulez-vous supprimez le participant ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" @click="deleteParticipant()" data-bs-dismiss="modal">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deletesModal" tabindex="-1" aria-labelledby="deletesModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Voulez-vous supprimez les participants ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" @click="deleteParticipants()" data-bs-dismiss="modal">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <Footer/>
</template>

<style scoped>
h1 {
    margin-bottom: 3.5rem;
}

#function-datatable {
    display: flex;
    flex-wrap: wrap;
}
</style>