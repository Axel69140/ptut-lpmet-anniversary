<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { userService } from '../../services/user.services';
    import { settingService } from '../../services/setting.service';

    const searchValue = ref('');
    let users = ref([]);
    let settings = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let userEdit = false;
    const id = ref('');
    const email = ref('');
    const firstName = ref('');
    const lastName = ref('');
    const maidenName = ref('');
    const password = ref('');
    const password_confirmation = ref('');
    const phone = ref('');
    const activeYears = ref('');
    const activeYears2 = ref('');
    const _function = ref('');
    const link = ref('');
    const note = ref('');
    const isParticipated = ref('');
    const isPublic = ref('');
    let invalidMail = false;
    let invalidPassword = false;
    let notSimilarPassword = false;
    let alreadyUseMail = false;
    let invalidYears = false;

    const headers: Header[] = [
        { text: "Prénom", value: "firstName", sortable: true },
        { text: "Nom", value: "lastName", sortable: true },
        { text: "Nom de jeune fille", value: "maidenName", sortable: true },
        { text: "Email", value: "email", sortable: true },
        { text: "Numéro de téléphone", value: "phoneNumber", sortable: true },
        { text: "Année d'activité", value: "activeYears", sortable: true },
        { text: "Fonction", value: "function", sortable: true },
        { text: "Lien linkedIn", value: "link", sortable: true },
        { text: "Note", value: "note", sortable: true },
        { text: "Participe à l'évènement", value: "participated", sortable: true },
        { text: "Profil publique", value: "publicProfil", sortable: true }
    ];

    const items: Item[] = users.value;

    onMounted (async() => {
        await settingService.getSettings().then(response => {
            settings.value = response.data;
        });
        getUsers(); 
         
    });    

    const getUsers = () => {    
        userService.getUsers().then((response) => { 
            items.splice(0, items.length);
            const usersResponse = response.data;
            usersResponse.forEach(user => {
                user.publicProfil = user.publicProfil == true ? "Oui" : "Non";
                user.participated = user.participated == true ? "Oui" : "Non";
            });         
            users.value.push(... usersResponse);
            itemsSelected.value = [];  
            isLoading.value = false;
        });
    };

    const getUserById = (userId) => {  
        userService.getUserById(userId).then((response) => { 
            userEdit = true;     
            id.value = userId;
            email.value = response.data.email;
            firstName.value = response.data.firstName;
            lastName.value = response.data.lastName;
            maidenName.value = response.data.maidenName;
            phone.value = response.data.phoneNumber;
            activeYears.value = response.data.activeYears[0];
            activeYears2.value = response.data.activeYears[1];
            _function.value = response.data.function;
            link.value = response.data.link;
            note.value = response.data.note;
            isParticipated.value = response.data.participated;
            isPublic.value = response.data.publicProfil; 
        });
        
        console.log(settings.value[0].allowedFunctions);
    };

    const createUser = () => {    
        validateEmail();
        validatePassword();
        validateYears();

        if (!invalidMail && !invalidPassword && !notSimilarPassword && !invalidYears) {
            isLoading.value = true;         
            userService.createUser({
                email: email.value,
                lastName: lastName.value,
                firstName: firstName.value,
                password: password.value,
                roles: ["ROLE_USER"],
                maidenName: maidenName.value,
                phoneNumber: phone.value,
                note: note.value,
                isParticipated: isParticipated.value == true ? isParticipated.value : false,
                isPublicProfil: isPublic.value == true ? isPublic.value : false,
                activeYears: [activeYears.value, activeYears2.value],
                function: _function.value,
                link: link.value
            }).then(async (response) => { 
                await getUsers();  
                isLoading.value = false; 
            });
        }
    };

    const editUser = () => {        
        isLoading.value = true; 
        userService.editUser(id.value, {
            email: email.value,
            lastName: lastName.value,
            firstName: firstName.value,
            maidenName: maidenName.value,
            phoneNumber: phone.value,
            note: note.value,
            isParticipated: isParticipated.value == true ? isParticipated.value : false,
            isPublicProfil: isPublic.value == true ? isPublic.value : false,
            activeYears: [activeYears.value, activeYears2.value],
            function: _function.value,
            link: link.value
        }).then(async (response) => { 
            await getUsers();  
            resetForm();
            isLoading.value = false; 
        }); 
    };

    const deleteUser = () => {        
        isLoading.value = true;         
        userService.deleteUser(itemsSelected.value[0].id).then(async (response) => { 
            await getUsers();
            isLoading.value = false;     
        });
    };

    const deleteUsers = () => {        
        isLoading.value = true; 
        let ids = [];
        itemsSelected.value.forEach((user) => {
            ids.push(user.id);
        });    
        userService.deleteUsers(ids).then(async (response) => { 
            await getUsers();
            isLoading.value = false;     
        });
    };

    const clearUserTable = () => {
        isLoading.value = true; 
        userService.clearUserTable().then(async (response) => { 
            await getUsers();
            isLoading.value = false;     
        }).catch(err => {
            console.log("Error : Impossible de vider la table utilisateur");
        });
    };   

    const exportData = () => {
        userService.exportUserData().then(async (response) => {
            const downloadUrl = response.data.fileToDownload;
            const serverUrl = import.meta.env.VITE_URL_API;
            const fullDownloadUrl = serverUrl + '/' + downloadUrl;    
            const filename = 'export_user.xlsx';        
            const link = document.createElement('a');
            link.href = fullDownloadUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link); 
        });
    };

    const range = (start, end) => {
        return Array(end - start + 1).fill().map((_, index) => start + index);
    }  

    const validateEmail = () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email.value)) {
          invalidMail = true;
        } else {
          invalidMail = false;
        }
    }

    const validatePassword = () => {
        const regex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (password.value === password_confirmation.value) {
          notSimilarPassword = false;
          if (!regex.test(password.value)) {
            invalidPassword = true    
          } else {
            invalidPassword = false;
          }          
        } else {
          notSimilarPassword = true;
        }
    }
      
    const validateYears = () => {
        if (activeYears.value > activeYears2.value) {
          invalidYears = true;
        } else {
          invalidYears = false;
        }
    }

    const resetForm = () => {
        email.value = '';
        firstName.value = '';
        lastName.value = '';
        maidenName.value = '';
        password.value = '';
        password_confirmation.value = '';
        phone.value = '';
        activeYears.value = '';
        activeYears2.value = '';
        _function.value = '';
        link.value = '';
        note.value = '';
        isParticipated.value = '';
        isPublic.value = '';
        userEdit = false;
    };
    
    defineComponent({
        name: 'userManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                clearUserTable,
                exportData,
                createUser,
                getUserById,
                resetForm,
                editUser,
                deleteUser,
                deleteUsers
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des utilisateurs</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table utilisateur</a>
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des utilisateurs</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer un utilisateur</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getUserById(itemsSelected[0].id)">Modifier l'utilisateur</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer l'utilisateur</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les utilisateurs</a>
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

        <!-- Pop-in d'ajout ou de modfication d'un utilisateur -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">{{ !userEdit ? "Créer" : "Modifier" }} un utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Message d'erreur -->
                        <div class="alert alert-danger" role="alert" v-if="invalidMail">
                            Adresse mail invalide
                        </div>

                        <div class="alert alert-danger" role="alert" v-if="alreadyUseMail">
                            Adresse mail déjà utilisée
                        </div>

                        <div class="alert alert-danger" role="alert" v-if="invalidPassword">
                            Le mot de passe doit contenir au moins 8 caractères, une majuscule et un caractère spécial.
                        </div>

                        <div class="alert alert-danger" role="alert" v-if="notSimilarPassword">
                            Mots de passe différents
                        </div>

                        <div class="alert alert-danger" role="alert" v-if="invalidYears">
                            Année d'activité à l'IUT invalide
                        </div> 

                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="firstName" class="form-row__input" type="text" placeholder="Prénom*"/>
                            <input v-model="lastName" class="form-row__input" type="text" placeholder="Nom*"/>
                        </div>

                        <div class="form-row">
                            <input v-model="maidenName" class="form-row__input" type="text" placeholder="Nom de jeune fille"/>
                            <input v-model="phone" class="form-row__input" type="tel" placeholder="Numéro de téléphone"/>
                        </div>

                        <div class="form-row">
                            <input v-model="email" class="form-row__input" type="text" placeholder="Adresse mail*"/>
                        </div>

                        <div class="form-row" v-if="!userEdit">
                            <input v-model="password" class="form-row__input" type="password" placeholder="Mot de passe*"/>
                        </div>

                        <div class="form-row" v-if="!userEdit">
                            <input v-model="password_confirmation" class="form-row__input" type="password" placeholder="Confirmer mot de passe*"/>
                        </div>

                        <div class="form-row">
                            <label for="activeYears">Année d'activité à l'IUT*</label>
                            <select v-model="activeYears" name="activeYears" id="activeYears" class="form-row__input">
                                <option value="">-</option>
                                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
                            </select>

                            <span>/</span>

                            <select v-model="activeYears2" name="activeYears2" id="activeYears2" class="form-row__input">
                                <option value="">-</option>
                                <option v-for="year in range(1993, 2023)" v-bind:key="year" v-bind:value="year">{{ year }}</option>
                            </select>
                        </div>
                        
                        <div v-if="settings.length > 0" class="form-row">
                            <select v-model="_function" class="form-row__input" type="select" placeholder="Fonction">
                                <option v-for="fct in settings[0].allowedFunctions.slice(1)" :value="fct">{{ fct }}</option>
                            </select>
                            <input v-model="link" class="form-row__input" type="text" placeholder="Lien linkedIn"/>
                        </div>      

                        <div class="form-row">
                            <textarea v-model="note" class="form-row__input" placeholder="Note"/>
                        </div>      

                        <div class="form-row">
                            <label for="isParticipated">Je participe à l'événement</label>
                            <div class="cntr">
                                <input v-model="isParticipated" type="checkbox" id="isParticipated" class="hidden-xs-up">
                                <label for="isParticipated" class="cbx"></label>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="isPublic">Je souhaite afficher publiquement mes informations</label>
                            <div class="cntr">
                                <input v-model="isPublic" type="checkbox" id="isPublic" class="hidden-xs-up">
                                <label for="isPublic" class="cbx"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!userEdit" type="button" class="btn-modal-valid btn-custom" @click="createUser()" v-bind:data-bs-dismiss="!invalidMail && !invalidPassword && !notSimilarPassword && !invalidYears ? 'modal' : null">Enregistrer</button>
                        <button v-else type="button" class="btn-modal-valid btn-custom" @click="editUser()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <p>Voulez-vous supprimez l'utilisateur ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteUser()" data-bs-dismiss="modal">Supprimer</button>
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
                        <p>Voulez-vous supprimez les utilisateurs ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteUsers()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn-modal-alert btn-custom" @click="clearUserTable()" data-bs-dismiss="modal">Vider</button>
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

#isParticipated:checked ~ .cbx, #isPublic:checked ~ .cbx {
  border-color: transparent;
  background: var(--primary);
  animation: jelly 0.6s ease;
}

#isParticipated:checked ~ .cbx:after, #isPublic:checked ~ .cbx:after {
  opacity: 1;
  transform: rotate(45deg) scale(1);
}

span {
    font-size: 20px;
}
</style>