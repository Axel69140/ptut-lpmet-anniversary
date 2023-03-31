<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { timelineService } from '../../services/timeline.services';

    const searchValue = ref('');
    let timelineSteps = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let timelineEdit = false;
    const id = ref('');
    const title = ref('');
    const day = ref('');
    const month = ref('');
    const year = ref('');
    const content = ref('');
    const media = ref('');

    const headers: Header[] = [
        { text: "Titre", value: "title", sortable: true },
        { text: "Date", value: "date", sortable: true },
        { text: "Contenu", value: "content", sortable: true },
        { text: "Lien de l'image", value: "media", sortable: true }
    ];

    const items: Item[] = timelineSteps.value;

    onMounted(async () => {
        // set datatable
        getTimelineSteps();
        isLoading.value = false;  
    });    

    const getTimelineSteps = () => {    
        timelineService.getTimelineSteps().then((response) => { 
            items.splice(0, items.length);
            const timelinesResponse = response.data;
            /*timelinesResponse.forEach(timelineStep => {
                timelineStep.publicProfil = timelineStep.publicProfil == true ? "Oui" : "Non";
                timelineStep.participated = timelineStep.participated == true ? "Oui" : "Non";
            }); */        
            timelineSteps.value.push(... timelinesResponse);
            itemsSelected.value = [];  
        });
    };

    const getTimelineStepById = (timelineStepId) => {  
        timelineService.getTimelineStepById(timelineStepId).then((response) => { 
            timelineEdit = true;     
            id.value = timelineStepId;
            title.value = response.data.title;
            //day.value = response.data.date;
            //month.value = response.data.date;
            //year.value = response.data.date;
            content.value = response.data.content;
            media.value = response.data.media; 
        });
    };

    const createTimelineStep = () => {        
        isLoading.value = true; 
        timelineService.createTimelineStep({
            title: title.value,
            //date: date.value,
            media: media.value,
            content: content.value
        }).then(async (response) => { 
            await getTimelineSteps();  
            isLoading.value = false; 
        });
    };

    const editTimelineStep = () => {
        isLoading.value = true; 
        timelineService.editTimelineStep(id.value, {
            title: title.value,
            //date: date.value,
            media: media.value,
            content: content.value
        }).then(async (response) => { 
            await getTimelineSteps();  
            isLoading.value = false; 
        }); 
    };

    const deleteTimelineStep = () => {        
        isLoading.value = true;         
        timelineService.deleteTimelineStep(itemsSelected.value[0].id).then(async (response) => { 
            await getTimelineSteps();
            isLoading.value = false;     
        });
    };

    const deleteTimelineSteps = () => {        
        isLoading.value = true; 
        let ids = [];
        itemsSelected.value.forEach((timelineStep) => {
            ids.push(timelineStep.id);
        });    
        timelineService.deleteTimelineSteps(ids).then(async (response) => { 
            await getTimelineSteps();
            isLoading.value = false;     
        });
    };

    const clearTimelineTable = () => {
        isLoading.value = true; 
        timelineService.clearTimelineTable().then(async (response) => { 
            await getTimelineSteps();
            isLoading.value = false;     
        }).catch(err => {
            console.log("Error : Impossible de vider la table timeline");
        });
    };    

    const exportData = () => {
        timelineService.exportTimelineData().then(async (response) => { 
            // upload le pdf reçu     
        })
    };

    const range = (start, end) => {
        return Array(end - start + 1).fill().map((_, index) => start + index);
    }  

    const resetForm = () => {
        title.value = '';
        day.value = '';
        month.value = '';
        year.value = '';
        media.value = '';
        content.value = '';
        timelineEdit = false;
    };
    
    defineComponent({
        name: 'timelineManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                createTimelineStep,
                exportData,
                clearTimelineTable,
                getTimelineStepById,
                resetForm,
                editTimelineStep,
                deleteTimelineStep,
                deleteTimelineSteps
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion de la timeline</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table de la timeline</a>
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des étapes de la timeline</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer une étape de la timeline</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getTimelineStepById(itemsSelected[0].id)">Modifier l'étape</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer l'étape</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les étapes</a>
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
                        <h5 class="modal-title" id="formModal">{{ !timelineEdit ? "Créer" : "Modifier" }}  une étape</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="title" class="form-row__input" type="text" placeholder="Titre*"/>
                        </div>

                        <div class="form-row">
                            <textarea v-model="content" class="form-row__input" placeholder="Contenu*"/>
                        </div> 

                        <div class="form-row">
                            <input v-model="media" class="form-row__input" type="text" placeholder="Lien de l'image*"/>
                        </div>

                        <div class="form-row">
                            <label for="day">Date de l'étape</label>
                            <select v-model="day" name="day" id="day">
                                <option value="">-</option>
                                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
                            </select>

                            <span>/</span>

                            <select v-model="month" name="month" id="month">
                                <option value="">-</option>
                                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
                            </select>

                            <span>/</span>

                            <select v-model="year" name="year" id="year">
                                <option value="">-</option>
                                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
                            </select>

                            <span>/</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!timelineEdit" type="button" class="btn-modal-valid btn-custom" @click="createTimelineStep()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn-modal-valid btn-custom" @click="editTimelineStep()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <p>Voulez-vous supprimez l'étape ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteTimelineStep()" data-bs-dismiss="modal">Supprimer</button>
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
                        <p>Voulez-vous supprimez les étapes ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteTimelineSteps()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn-modal-alert btn-custom" @click="clearTimelineTable()" data-bs-dismiss="modal">Vider</button>
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