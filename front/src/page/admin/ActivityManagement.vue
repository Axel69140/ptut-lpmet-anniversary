<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { activityService } from '../../services/activity.services';
    import { accountService } from '../../services/account.services';

    const searchValue = ref('');
    let activities = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let activityEdit = false;
    const id = ref('');
    const name = ref('');
    const description = ref('');
    const isAllDay = ref(false);
    const durationHour = ref(0);
    const durationMinute = ref(0);
    let totalDuration = '';

    const headers: Header[] = [
        { text: "Nom de l'activité", value: "name", sortable: true },
        { text: "Utilisateur", value: "creator", sortable: true },
        { text: "Description", value: "description", sortable: true },
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
        });
    };

    const createActivity = async () => {        
        isLoading.value = true; 
        const idUser = await accountService.getId();

        if(isAllDay){
            totalDuration = "24:00:00";
        }
        else if(!durationHour.value == 0 || !durationMinute.value == 0){
            if(durationHour.value < 10 && durationMinute.value < 10){
                totalDuration = "0" + durationHour.value + ":0" + durationMinute.value + ":00";
            }else if(durationHour.value < 10){
                totalDuration = "0" + durationHour.value + ":" + durationMinute.value + ":00";
            }else if(durationMinute.value < 10){
                totalDuration = durationHour.value + ":0" + durationMinute.value + ":00";
            }else{
                totalDuration = durationHour.value + ":" + durationMinute.value + ":00";
            }
        }

        activityService.createActivity({
            name: name.value,
            description: description.value,
            duration: totalDuration,
            creator: idUser
        }).then(async (response) => { 
            await getActivities();  
            resetForm();
            isLoading.value = false; 
        });
    };

    const editActivity = () => {        
        isLoading.value = true; 
        activityService.editActivity(id.value, {
            name: name.value,
            description: description.value,
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
        durationHour.value = 0;
        durationMinute.value = 0;
        isAllDay.value = false;
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

                <template #item-creator="item">
                    {{ item.creator.firstName }} {{ item.creator.lastName }}                 
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

                        <div class="divDurée">
                            <h2>Durée de l'activité</h2>    

                            <div class="form-row">
                                <label for="isAllDay">Toute la journée</label>
                                <div class="cntr">
                                    <input v-model="isAllDay" type="checkbox" id="isAllDay" class="hidden-xs-up">
                                    <label for="isAllDay" class="cbx"></label>
                                </div>
                            </div>

                            <div v-if="!isAllDay" class="duration">
                                <div class="hour form-row">
                                    <label>Heures</label>
                                    <input type="number" name="monInput" min="0" max="60" class="form-row__input" v-model="durationHour">
                                </div>
                                <div class="minute form-row">
                                    <label>Minutes</label>
                                    <input type="number" name="monInput" min="0" max="24" class="form-row__input" v-model="durationMinute">
                                </div>
                            </div>                
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

.divDurée h2 {
    font-size: 20px;
    font-weight: bold;
    text-align: initial;
}

.divDurée {
    text-align: initial;
}

#isAllDay:checked ~ .cbx {
  border-color: transparent;
  background: var(--primary);
  animation: jelly 0.6s ease;
}

#isAllDay:checked ~ .cbx:after {
  opacity: 1;
  transform: rotate(45deg) scale(1);
}

.duration label {
    width: 60px;
}
</style>