<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { activityService } from '../../services/activity.services';

    const searchValue = ref('');
    let activities = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let activityEdit = false;
    const id = ref('');
    const name = ref('');
    const description = ref('');
    const startHour = ref('');
    const duration = ref('');
    const user = ref('');

    const headers: Header[] = [
        { text: "Nom de l'activité", value: "name", sortable: true },
        { text: "Utilisateur", value: "user", sortable: true },
        { text: "Description", value: "description", sortable: true },
        { text: "Heure du début de l'activité", value: "startHour", sortable: true },
        { text: "Durée", value: "duration", sortable: true }
    ];

    const items: Item[] = activities.value;

    onMounted(() => {
        // set datatable
        getActivities();
    });    

    const getActivities = () => {    
        activityService.getActivities().then((response) => { 
            items.splice(0, items.length);
            const activitiesResponse = response.data;  
            activities.value.push(... activitiesResponse);
            itemsSelected.value = [];  
            isLoading.value = false;  
        });
    };

    const getActivityById = (activityId) => {  
        activityService.getActivityById(activityId).then((response) => { 
            activityEdit = true;     
            id.value = activityId;
            name.value = response.data.name;
            description.value = response.data.description;
            startHour.value = response.data.startHour;
            duration.value = response.data.duration;
            user.value = response.data.user;
        });
    };

    const createActivity = () => {        
        isLoading.value = true; 
        activityService.createActivity({
            name: name.value,
            description: description.value,
            startHour: startHour.value,
            duration: duration.value,
            user: user.value
        }).then(async (response) => { 
            await getActivities();  
            isLoading.value = false; 
        });
    };

    const editActivity = () => {        
        isLoading.value = true; 
        activityService.editActivity(id.value, {
            name: name.value,
            description: description.value,
            startHour: startHour.value,
            duration: duration.value,
            user: user.value
        }).then(async (response) => { 
            await getActivities();  
            isLoading.value = false; 
        }); 
    };

    const deleteActivity = () => {        
        isLoading.value = true;         
        activityService.deleteActivity(itemsSelected.value[0].id).then(async (response) => { 
            await getActivities();
            isLoading.value = false;     
        });
    };

    const deleteActivities = () => {        
        isLoading.value = true; 
        let ids = [];
        itemsSelected.value.forEach((activity) => {
            ids.push(activity.id);
        });    
        activityService.deleteActivities(ids).then(async (response) => { 
            await getActivities();
            isLoading.value = false;     
        });
    };

    const clearActivityTable = () => {
        isLoading.value = true; 
        activityService.clearActivityTable().then(async (response) => { 
            await getActivities();
            isLoading.value = false;     
        }).catch(err => {
            console.log("Error : Impossible de vider la table activité");
        });
    };    

    const exportData = () => {
        activityService.exportActivityData().then(async (response) => { 
            // upload le pdf reçu     
        })
    };

    const resetForm = () => {
        name.value = '';
        description.value = '';
        startHour.value = '';
        duration.value = '';
        user.value = '';
        activityEdit = false;
    };
    
    defineComponent({
        name: 'activityManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                clearActivityTable,
                exportData,
                createActivity,
                getActivityById,
                resetForm,
                editActivity,
                deleteActivity,
                deleteActivities
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des activités</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table activité</a>
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des activités</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer un activité</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getActivityById(itemsSelected[0].id)">Modifier l'activité</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer l'activité</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les activités</a>
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

                <template #item-activeYears="item">
                    {{ item.activeYears.length > 0 ? item.activeYears[0] + "/" + item.activeYears[1] : "" }}                    
                </template>
            </EasyDataTable>
        </div>

        <!-- Pop-in d'ajout ou de modfication d'un activité -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">{{ !activityEdit ? "Créer" : "Modifier" }}  un activité</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="name" class="form-row__input" type="text" placeholder="Nom de l'activité*"/>
                        </div>

                        <div class="form-row">
                            <textarea v-model="description" class="form-row__input" placeholder="Description*"/>
                        </div>

                        <div class="form-row">
                            <input v-model="startHour" class="form-row__input" type="text" placeholder="Heure du début de l'activité"/>
                        </div>

                        <div class="form-row">
                            <input v-model="duration" class="form-row__input" type="text" placeholder="Durée de l'activité"/>
                        </div>

                        <div class="form-row">
                            <input v-model="user" class="form-row__input" type="text" placeholder="Utilisateur*"/>
                        </div>   
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!activityEdit" type="button" class="btn-modal-valid btn-custom" @click="createActivity()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn-modal-valid btn-custom" @click="editActivity()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <p>Voulez-vous supprimez l'activité ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteActivity()" data-bs-dismiss="modal">Supprimer</button>
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
                        <p>Voulez-vous supprimez les activités ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteActivities()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn-modal-alert btn-custom" @click="clearActivityTable()" data-bs-dismiss="modal">Vider</button>
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