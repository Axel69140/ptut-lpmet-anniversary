<script>
    import axios from 'axios';    

    export default {
        name: 'anecdoteForm',
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
    <h1>Proposer une anecdote</h1> 
    <div class="formulaire">
        <div class="information"><!--Récupérer via la version admin les p a mettre ici-->
            <p class="moreInformation">Les annecdotes sont anonymes</p>
            <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
            <p class="moreInformation">Elles doivent avoir un lien avec l'IUT</p>
            <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
        </div>
        <div class="divTextZone">
            <textarea class="textZone" rows="10" cols="100"></textarea>
        </div>
        <div class="sendButton">
            <button class="validButton">Envoyer l'anecdote</button>
        </div>
    </div>
</template>

<style scoped>

    .moreInformation{
        margin: 0;
        color: #777777;
    }
    .formulaire{
        display: flex;
        flex-direction: column;
    }

    .divTextZone{
        margin: 20px 0;
    }
    .textZone{
        border-radius: 20px;
        padding: 1%;
        border: solid 4px var(--primary);
    }

    .textZone:focus-visible{
        outline: var(--primary);
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