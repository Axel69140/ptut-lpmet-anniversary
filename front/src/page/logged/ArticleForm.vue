<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';     
    import { accountService } from '../../services/account.services';
    import { articleService } from '../../services/article.services';

    export default {
        name: 'articleForm',
        data: () => ({ 
            users: [],
            idUser: 1,
            content: '',
            title: '', 
        }),       
        computed: {            
        },
        methods: {     
            async parameterArticle() {
                if (this.content !== undefined && this.content !== '' && this.title !== undefined && this.title !== '') {
                    await articleService.createArticle({
                        content: this.content,
                        title: this.title,
                        id_user: this.idUser,
                    });

                    this.content = '';
                }
            } 
        },
        async mounted() {
            this.idUser = await accountService.getId();
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Proposer un article</h1>
        <p class="informations"></p>
        <form class="formulaireArticle">
            <div class="divTitleTZ">
                <label class="labelTitle">Titre de l'article</label>
                <input type="text" class="titleTextZone" v-model="title">
            </div>
            <div class="divContentTZ">
                <label class="labelContent">Contenu de l'article</label>
                <textarea class="contentTextZone" rows="10" cols="100" v-model="content"></textarea>
            </div>
            <div class="divImage">
                <label class="labelImage">Image de l'article</label>
                <input class="inputImage" type="image">
            </div>
            <div class="sendButton">
                <button @click="parameterArticle()" class="btn-custom">Envoyer l'anecdote</button>
            </div>
        </form>
    </main>
    
    <Footer class="footer" />
</template>

<style scoped>

    .labelContent{
        margin:  20px 15px 0 0;
    }

    .labelTitle{
        margin-right: 48px;
    }

    .labelImage{
        margin-right: 5%;
    }

    .formulaireArticle{
        display: flex;
        flex-direction: column;
    }

    .divImage{
        display: flex;
        justify-content: center;
    }
    .divTitleTZ{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .divContentTZ{
        display: flex;
        justify-content: center;
    }
    .footer{
        position: inherit;
    }
    .divContentTZ{
        margin: 20px 0;
    }
    .contentTextZone, .titleTextZone{
        width: 60%;
        border-radius: 20px;
        padding: 1%;
        border: solid 4px var(--primary);
    }

    .contentTextZone:focus-visible, .titleTextZone:focus-visible{
        outline: var(--primary);
    }

</style>