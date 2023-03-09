<script>
    import axios from 'axios';    

    export default {
        name: 'articleForm',
        data: () => ({ users: [] }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
        }
    }
</script>


<template>
    <h1>Proposer un article</h1>
    <div class="formulaire">
        <div class="information"><!--Récupérer via la version admin les p a mettre ici-->
            <p class="moreInformation">Les annecdotes sont anonymes</p>
            <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
            <p class="moreInformation">Elles doivent avoir un lien avec l'IUT</p>
            <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
        </div>
        <form>
            <div class="titleArticle">
                <label class="titleArticleLabel">Titre de l'article</label>
                <textarea id="titleArticleInput" class="titleArticleInput" rows="1" cols="100"></textarea>
            </div>
            <div class="contentArticle">
                <label class="contentArticleLabel">Contenu de l'article</label>
                <textarea id="contentArticleInput" class="contentArticleInput" rows="10" cols="100"></textarea>
            </div>
            <div class="pictureArticle">
                <label class="pictureArticleLabel">Photo de l'article</label>
                <input type="file">
            </div>  
            <div class="buttonArticle">
                <button id="buttonArticleInput" class="validButton">Envoyer l'anecdote</button>
            </div>          
        </form>
        <div class="sendButton">
            
        </div>
    </div>
     
</template>

<style scoped>

.contentArticle{
    display: flex;
    align-items: flex-start;
    justify-content: center;
}

.titleArticle, .pictureArticle{
    display: flex;
    align-items: center;
    justify-content: center;
}

.contentArticleLabel, .pictureArticleLabel{
    margin-top: 10px;
    margin-right: 10px;
}
.titleArticleLabel{
    margin-right: 38px;
}

.pictureArticle{
    display: flex;
    justify-content: center;
}

form div{
    margin: 15px 0;
}

form{
    display: flex;
    margin-top: 50px;
    flex-direction: column;
}

.contentArticleInput, .titleArticleInput{
    border-radius: 20px;
    padding: 1%;
    border: solid 4px var(--primary);
}

.contentArticleInput:focus-visible, .titleArticleInput:focus-visible{
    outline: var(--primary);
}
.moreInformation{
    margin: 0;
    color: #777777;
}
.validButton{
    border: 3px solid #fff;
    color: #fff;
    background-color: var(--secondary);
    border-radius: 15px;
    padding: 0.5%;
    transition: all .2s ease-in-out; 
}

.validButton:hover{
    transform: scale(1.1);
    background-color: var(--primary);
    border: var(--primary);
}
</style>