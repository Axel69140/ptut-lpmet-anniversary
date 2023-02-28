<script>
    import axios from 'axios';    

    export default {
        name: 'home',
        data: () => ({}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
            axios.get('http://localhost:3000/teams/').then(response => {  
                if (response.data.teams.length > 0) {
                    for(let i = 0; i < response.data.teams.length; i++) {
                        this.teams.push({
                            id: response.data.teams[i].id,
                            name : response.data.teams[i].name, 
                            score : response.data.teams[i].score,
                            players : response.data.teams[i].players,
                        });
                    }                        
                }                          
            }).catch(err => {
                console.log(err);
            });     
        }
    }
</script>


<template>
    
</template>

<style scoped>

</style>