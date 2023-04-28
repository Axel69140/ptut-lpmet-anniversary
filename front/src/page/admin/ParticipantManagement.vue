<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { participantService } from '../../services/participant.services';
    import { userService } from '../../services/user.services';
    import { guestService } from '../../services/guest.service';
    import { accountService } from '../../services/account.services';

    const searchValue = ref('');
    let participants = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let participantEdit = false;
    const id = ref('');
    const name = ref('');
    const email = ref('');
    let mode = 'no_create';
    const selectedUser = ref('');
    let allUsers = [];

    const headers: Header[] = [
        { text: "Nom", value: "name", sortable: true },
        { text: "Email", value: "email", sortable: true },
        { text: "Invité par", value: "invitedBy", sortable: true },
    ];

    const items: Item[] = participants.value;

    onMounted(() => {
        // set datatable
        getUsers();
        getParticipants();        
    });    

    const getParticipants = () => {    
        participantService.getParticipants().then((response) => { 
            items.splice(0, items.length);
            const participantsResponse = response.data;  
            participants.value.push(... participantsResponse);
            itemsSelected.value = [];  
            isLoading.value = false;  
        });
    };

    const getUsers = () => {    
        userService.getUsers().then((response) => {             
            allUsers.push(... response.data);   
        });
    };

    const getParticipantByEMail = (participantMail) => {  
        participantService.getActivityById(participantMail).then((response) => { 
            participantEdit = true;     
            name.value = response.data.name;
            email.value = response.data.email;
        });
    };

    const createParticipantByUser = () => {        
        isLoading.value = true;
        userService.editUser(selectedUser.value , {
            isParticipated: true,
        }).then(async (response) => { 
            await getParticipants();  
            resetForm();
            isLoading.value = false; 
        });
    };

    const createGuest = () => {        
        isLoading.value = true; 
        guestService.createGuest({
            name: name.value,
            email: email.value,
            invitedBy: accountService.getId()
        }).then(async (response) => { 
            await getParticipants();  
            resetForm();
            isLoading.value = false; 
        });
    };

    const editParticipant = () => {        
        isLoading.value = true; 
        participantService.editParticipant(email.value, {
            name: name.value,
            email: email.value
        }).then(async (response) => { 
            await getParticipants();  
            resetForm();
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
        selectedUser.value = '';
        participantEdit = false;
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
                createParticipantByUser,
                createGuest,
                getParticipantByEMail,
                resetForm,
                editParticipant,
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
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getUsers()">Créer un participant</a>

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
                    <Loader :is-loading="isLoading"/>
                </template>

                <template #empty-message>
                    <p>Aucun résultat</p>
                </template>

                <template #item-name="item">
                    {{ item.firstName  }} {{ item.lastName  }}            
                </template>

                <template #item-invitedBy="item">
                    {{ item.invitedBy ? item.invitedBy.firstName + ' ' + item.invitedBy.lastName : '/' }}
                </template>
            </EasyDataTable>
        </div>

        <!-- Pop-in d'ajout ou de modfication d'un participant -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">{{ !participantEdit ? "Créer" : "Modifier" }}  un participant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>

                    <div class="modal-body">
                        <div id="nav-modal">
                            <a href="#" :class="{active: mode === 'no_create'}" @click="mode = 'no_create'" id="nav-modal-link-1">À partir d'un utilisateur existant</a>
                            <a href="#" :class="{active: mode === 'create'}" @click="mode = 'create'" id="nav-modal-link-2">Inviter une nouvelle personne</a>
                        </div>
                        
                        <input v-model="id" type="hidden"/>

                        <div v-if="mode === 'no_create'" class="form-row">
                            <select class="form-row__input" v-model="selectedUser" name="selectedUser" id="selectedUser">
                                <option value="" disabled selected hidden>Utilisateur*</option>
                                <option v-for="user in allUsers" :key="user" :value="user.id">{{ user.firstName }} {{ user.lastName }}</option>
                            </select>
                        </div>

                        <div v-if="mode === 'create'">
                            <div class="form-row">
                                <input v-model="name" class="form-row__input" type="text" placeholder="Nom du participant*"/>
                            </div>

                            <div class="form-row">
                                <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail*"/>
                            </div>
                        </div>   
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!participantEdit && mode === 'no_create'" type="button" class="btn-modal-valid btn-custom" @click="createParticipantByUser()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-if="!participantEdit && mode === 'create'" type="button" class="btn-modal-valid btn-custom" @click="createGuest()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-if="participantEdit" type="button" class="btn-modal-valid btn-custom" @click="editParticipant()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteParticipants()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteParticipants()" data-bs-dismiss="modal">Supprimer</button>
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

#nav-modal {
    display: flex;
}

#nav-modal-link-1 {
    width: 50%;
    text-decoration: none;    
    color: black;
    padding: 0 0 4px 0;
    margin: 0 4px 0 0;
    font-weight: bold;
}

.active {
    border-bottom: 3px solid black;
}

#nav-modal-link-2 {
    width: 50%;
    text-decoration: none;
    color: black;
    padding: 0 0 4px 0;
    margin: 0 0 0 4px;
    font-weight: bold;
}

select {
    height: 40px;
}
</style>