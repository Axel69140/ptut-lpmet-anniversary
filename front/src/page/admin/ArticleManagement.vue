<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref, computed, watch, watchEffect } from "vue";
    import Footer from '../../components/Footer.vue';    
    import Loader from '../../components/Loader.vue';
    import { articleService } from '../../services/article.services';

    const searchValue = ref('');
    let articles = ref([]);
    const itemsSelected = ref<Item[]>([]);
    let isLoading = ref(true);
    let articleEdit = false;
    const id = ref('');
    const title = ref('');
    const content = ref('');
    const user = ref('');

    const headers: Header[] = [
        { text: "Utilisateur", value: "user", sortable: true },
        { text: "Titre", value: "title", sortable: true },
        { text: "Contenu", value: "content", sortable: true }
    ];

    const items: Item[] = articles.value;

    onMounted(async () => {
        // set datatable
        getArticles();
        isLoading.value = false;  
    });    

    const getArticles = () => {    
        articleService.getArticles().then((response) => { 
            items.splice(0, items.length);
            const articlesResponse = response.data;        
            articles.value.push(... articlesResponse);
            itemsSelected.value = [];  
        });
    };

    const getArticleById = (articleId) => {  
        articleService.getArticleById(articleId).then((response) => { 
            articleEdit = true;
            id.value = articleId;
            title.value = response.data.title;
            content.value = response.data.content;
            user.value = response.data.user;
        });
    };

    const createArticle = () => {        
        isLoading.value = true; 
        articleService.createArticle({
            title: title.value,
            content: content.value,
            user: user.value
        }).then(async (response) => { 
            await getArticles();  
            isLoading.value = false; 
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
            isLoading.value = false; 
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
        user.value = '';
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
                deleteArticles
            }
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des articles</h1>     

        <div class="container">      
            <div id="function-datatable">
                <input type="text" placeholder="Rechercher..." v-model="searchValue">
                <button type="button" data-bs-toggle="modal" data-bs-target="#clearModal">Vider la table article</button>
                <button @click="exportData()">Exporter la liste des articles</button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#formModal">Créer un article</button>

                <div v-if="itemsSelected.length === 1">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#formModal" @click="getArticleById(itemsSelected[0].id)">Modifier l'article</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer l'article</button>
                </div>

                <div v-if="itemsSelected.length > 1">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#deletesModal">Supprimer les articles</button>
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

        <!-- Pop-in d'ajout ou de modfication d'un article -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModal">Créer un article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm()"></button>
                    </div>
                    <div class="modal-body">
                        <input v-model="id" type="hidden"/>

                        <div class="form-row">
                            <input v-model="title" class="form-row__input" type="text" placeholder="Titre*"/>
                        </div>

                        <div class="form-row">
                            <input v-model="user" class="form-row__input" type="text" placeholder="Utilisateur*"/>
                        </div>

                        <div class="form-row">
                            <textarea v-model="content" class="form-row__input" placeholder="Contenu*"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetForm()">Fermer</button>
                        <button v-if="!articleEdit" type="button" class="btn btn-primary" @click="createArticle()" data-bs-dismiss="modal">Enregistrer</button>
                        <button v-else type="button" class="btn btn-primary" @click="editArticle()" data-bs-dismiss="modal">Enregistrer</button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" @click="deleteArticle()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" @click="deleteArticles()" data-bs-dismiss="modal">Supprimer</button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" @click="clearArticleTable()" data-bs-dismiss="modal">Vider</button>
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
    margin-bottom: 20px;
    display: flex;
}
</style>