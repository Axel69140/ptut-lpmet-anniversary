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
        <div class="information">
            <p class="moreInformation">Les annecdotes sont anonymes</p>
            <p class="moreInformation">Elles seront projeté lors de l'</p>
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
    }

    .validButton{
        border: 1px solid black;
        background-color: green;
        border-radius: 15px;
        padding: 0.5%;
        transition: all .2s ease-in-out; 
    }

    .validButton:hover{
        transform: scale(1.1);
    }
</style>