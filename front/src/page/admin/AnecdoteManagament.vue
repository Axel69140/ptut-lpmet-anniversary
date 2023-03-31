<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { anecdoteService } from '../../services/anecdote.services';

    const searchValue = ref('');
    let anecdotes = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let anecdoteEdit = false;
    const id = ref('');
    const user = ref('');
    const content = ref('');

    const headers: Header[] = [
        { text: "Utilisateur", value: "user", sortable: true },
        { text: "Contenu", value: "content", sortable: true },
    ];

    const items: Item[] = anecdotes.value;

    onMounted(async () => {
        // set datatable
        getAnecdotes();
        isLoading.value = false;  
    });    

    const getAnecdotes = () => {    
        anecdoteService.getAnecdotes().then((response) => { 
            items.splice(0, items.length);
            const anecdotesResponse = response.data;
            /*anecdotesResponse.forEach(user => {
                user.publicProfil = user.publicProfil == true ? "Oui" : "Non";
                user.participated = user.participated == true ? "Oui" : "Non";
            }); */        
            anecdotes.value.push(... anecdotesResponse);
            itemsSelected.value = [];  
        });
    };

    const getAnecdoteById = (anecdoteId) => {  
        anecdoteService.getAnecdoteById(anecdoteId).then((response) => { 
            anecdoteEdit = true;     
            id.value = anecdoteId;
            user.value = response.data.email;
            content.value = response.data.firstName;
        });
    };

    const createAnecdote = () => {        
        isLoading.value = true; 
        anecdoteService.createAnecdote({
            user: user.value,
            content: content.value
        }).then(async (response) => { 
            await getAnecdotes();  
            isLoading.value = false; 
        });
    };

    const editAnecdote = () => {        
        isLoading.value = true; 
        anecdoteService.editAnecdote(id.value, {
            user: user.value,
            content: content.value
        }).then(async (response) => { 
            await getAnecdotes();  
            isLoading.value = false; 
        }); 
    };

    const deleteAnecdote = () => {        
        isLoading.value = true;         
        anecdoteService.deleteAnecdote(itemsSelected.value[0].id).then(async (response) => { 
            await getAnecdotes();
            isLoading.value = false;     
        });
    };

    const deleteAnecdotes = () => {        
        isLoading.value = true; 
        let ids = [];
        itemsSelected.value.forEach((anecdote) => {
            ids.push(anecdote.id);
        });    
        anecdoteService.deleteAnecdotes(ids).then(async (response) => { 
            await getAnecdotes();
            isLoading.value = false;     
        });
    };

    const clearAnecdoteTable = () => {
        isLoading.value = true; 
        anecdoteService.clearAnecdoteTable().then(async (response) => { 
            await getAnecdotes();
            isLoading.value = false;     
        }).catch(err => {
            console.log("Error : Impossible de vider la table anecdote");
        });
    };    

    const exportData = () => {
        anecdoteService.exportAnecdoteData().then(async (response) => { 
            // upload le pdf reçu     
        })
    };

    const resetForm = () => {
        user.value = '';
        content.value = '';
    };
    
    defineComponent({
        name: 'anecdoteManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                clearAnecdoteTable,
                exportData,
                createAnecdote,
                getAnecdoteById,
                resetForm,
                editAnecdote,
                deleteAnecdote,
                deleteAnecdotes
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des anecdotes</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table anecdote</a>
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des anecdotes</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer un anecdote</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getAnecdoteById(itemsSelected[0].id)">Modifier l'anecdote</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer l'anecdote</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les anecdotes</a>
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
            </EasyDataTable>
        </div>

        <!-- Pop-in d'ajout ou de modfication d'un utilisateur -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">Créer une anecdote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="user" class="form-row__input" type="text" placeholder="Utilisateur*"/>
                        </div>

                        <div class="form-row">
                            <textarea v-model="content" class="form-row__input" placeholder="Contenu*"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!anecdoteEdit" type="button" class="btn-modal-valid btn-custom" @click="createAnecdote()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn-modal-valid btn-custom" @click="editAnecdote()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <p>Voulez-vous supprimez l'anecdote ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteAnecdote()" data-bs-dismiss="modal">Supprimer</button>
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
                        <p>Voulez-vous supprimez les anecdotes ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteAnecdotes()" data-bs-dismiss="modal">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="clearModal" tabindex="-1" aria-labelledby="clearModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Voulez êtes sur le point de vider la table, voulez-vous continuez ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="clearAnecdoteTable()" data-bs-dismiss="modal">Vider</button>
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