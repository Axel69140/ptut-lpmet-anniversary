<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { articleService } from '../../services/article.services';
    import { userService } from '../../services/user.services';

    const searchValue = ref('');
    let articles = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let articleEdit = false;
    let imageUrl = ref('');
    const id = ref('');
    const title = ref('');
    const content = ref('');
    const user = ref('');
    const selectedUser = ref('');
    let allUsers = [];

    const headers: Header[] = [
        { text: "Utilisateur", value: "creator", sortable: true },
        { text: "Titre", value: "title", sortable: true },
        { text: "Contenu", value: "content", sortable: true }
    ];

    const items: Item[] = articles.value;

    onMounted(() => {
        // set datatable
        getUsers();  
        getArticles();

    });    

    const getArticles = () => {    
        articleService.getArticles().then((response) => { 
            items.splice(0, items.length);
            const articlesResponse = response.data;        
            articles.value.push(... articlesResponse);
            itemsSelected.value = [];  
            isLoading.value = false; 
        });
    };

    const getUsers = () => {    
        userService.getUsers().then((response) => {             
            allUsers.push(... response.data);   
        });
    };

    const getArticleById = (articleId) => {  
        articleService.getArticleById(articleId).then((response) => { 
            articleEdit = true;
            id.value = articleId;
            title.value = response.data.title;
            content.value = response.data.content;            
            selectedUser.value = response.data.creator.id;
        });
    };

    const createArticle = () => {        
        isLoading.value = true; 
        articleService.createArticle({
            title: title.value,
            content: content.value,
            creator: selectedUser.value
        }).then(async (response) => { 
            await getArticles();  
            resetForm();
            isLoading.value = false; 
            imageUrl.value = '';
        });
    };

    const editArticle = () => {        
        isLoading.value = true; 
        articleService.editArticle(id.value, {
            title: title.value,
            content: content.value,
            user: user.value
        }).then(async (response) => { 
            await getArticles();  
            resetForm();
            isLoading.value = false; 
            imageUrl.value = '';
        }); 
    };

    const deleteArticle = () => {        
        isLoading.value = true;         
        articleService.deleteArticle(itemsSelected.value[0].id).then(async (response) => { 
            await getArticles();
            isLoading.value = false;     
        });
    };

    const deleteArticles = () => {        
        isLoading.value = true; 
        let ids = [];
        itemsSelected.value.forEach((article) => {
            ids.push(article.id);
        });    
        articleService.deleteArticles(ids).then(async (response) => { 
            await getArticles();
            isLoading.value = false;
        });
    };

    const clearArticleTable = () => {
        isLoading.value = true; 
        articleService.clearArticleTable().then(async (response) => { 
            await getArticles();
            isLoading.value = false;     
        }).catch(err => {
            console.log("Error : Impossible de vider la table article");
        });
    };    

    const exportData = () => {
        articleService.exportArticleData().then(async (response) => { 
            // upload le pdf reçu     
        })
    };

    const range = (start, end) => {
        return Array(end - start + 1).fill().map((_, index) => start + index);
    }  

    const resetForm = () => {
        title.value = '';
        content.value = '';
        selectedUser.value = '';
        imageUrl.value = '';
        document.getElementById("imageFile").value = "";
        articleEdit = false;
    };

    const previewImage = (event, isDrop) => {
        let file = null;
        if(isDrop){
            file = event.dataTransfer.files[0];
        }else{
            file = event.target.files[0];
        }
        console.log(file);
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (event) => {
            console.log(event.target.result);
            imageUrl.value = event.target.result;
        };
        reader.readAsDataURL(file);
    };

    const dropHandler = (event) => {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        previewImage(event,true);
    };
    
    defineComponent({
        name: 'articleManagement',
        components: {
            Footer,
            Loader
        },
        setup() {
            return {
                clearArticleTable,
                exportData,
                createArticle,
                getArticleById,
                resetForm,
                editArticle,
                deleteArticle,
                deleteArticles,
                getUsers
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des news</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input class="searchBar" type="text" placeholder="Rechercher..." v-model="searchValue">
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table news</a>
                <a class="btn-custom btn-datatable" @click="exportData()">Exporter la liste des news</a>
                <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getUsers()">Créer une news</a>

                <div v-if="itemsSelected.length === 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getArticleById(itemsSelected[0].id)">Modifier la news</a>
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer la news</a>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <a class="btn-custom btn-datatable" type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les news</a>
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

        <!-- Pop-in d'ajout ou de modfication d'un article -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">{{ !articleEdit ? "Créer" : "Modifier" }}  un article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="title" class="form-row__input" type="text" placeholder="Titre*"/>
                        </div>

                        <div class="form-row">
                            <select class="form-row__input" v-model="selectedUser" name="selectedUser" id="selectedUser">
                                <option value="" disabled selected hidden>Utilisateur*</option>
                                <option v-for="user in allUsers" :key="user" :value="user.id">{{ user.firstName }} {{ user.lastName }}</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <textarea v-model="content" class="form-row__input" placeholder="Contenu*"/>
                        </div>

                        <div class="form-row">
                            <label v-if="!imageUrl" for="imageFile" class="labelImage dropzone" @drop="dropHandler" @dragover.prevent>Ajouter une image à la news</label>
                            <label  v-if="imageUrl" for="imageFile" class="labelImage dropzone" @drop="dropHandler" @dragover.prevent>Changer d'image
                                <img :src="imageUrl" v-if="imageUrl" class="imagePreview">
                            </label>
                            <input class="upload" id="imageFile" name="imageFile" type="file" accept=".png, .jpeg, .jpg, .webp" @change="previewImage($event,false)">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!articleEdit" type="button" class="btn-modal-valid btn-custom" @click="createArticle()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn-modal-valid btn-custom" @click="editArticle()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <p>Voulez-vous supprimez l'article ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteArticle()" data-bs-dismiss="modal">Supprimer</button>
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
                        <p>Voulez-vous supprimez les articles ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-neutre btn-custom" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn-modal-alert btn-custom" @click="deleteArticles()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn-modal-alert btn-custom" @click="clearArticleTable()" data-bs-dismiss="modal">Vider</button>
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

select {
    height: 40px;
}

.placeholder {
    color: #afafaf;
}

.imagePreview{
    max-width: 50%;
    max-height: 80%;
    margin: 2%;
}
</style>